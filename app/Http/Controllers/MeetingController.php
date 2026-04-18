<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Volunteer;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Manages recurring meeting slots and individual occurrence assignments.
 *
 * Recurring meetings are templates (day-of-week + week-of-month pattern).
 * MeetingAssignments link volunteers to specific calendar dates derived from those templates.
 * SMS notifications are dispatched as queued jobs so they never block the HTTP response.
 */
class MeetingController extends Controller
{
    /**
     * SmsService is injected by Laravel's container.
     * Injecting through the constructor decouples the controller from the concrete
     * implementation and makes it straightforward to swap or mock in tests.
     */
    public function __construct(private readonly SmsService $smsService) {}

    /**
     * List recurring meeting slots with optional filters.
     *
     * Supports filtering by facility, status, format, day of week, and whether
     * upcoming occurrences are already assigned. Results are paginated.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Meeting::with('facility')->withoutTrashed();

        // Each filter is applied only when a value is present in the request.
        // This keeps the query clean — unset filters add no WHERE clauses.
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Assignment status filter checks whether upcoming occurrences have
        // active assignments (confirmed or awaiting confirmation).
        if ($request->filled('assignment_status')) {
            $today = now()->toDateString();
            $this->applyAssignmentStatusFilter($query, $request->assignment_status, $today);
        }

        $meetings = $query
            ->orderBy('day_of_week')
            ->orderBy('week_of_month')
            ->orderBy('meeting_time')
            ->paginate(15);

        $facilities = Facility::where('status', 'active')
            ->orderBy('facility_name')
            ->get();

        $volunteers = Volunteer::withoutTrashed()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['volunteer_id', 'first_name', 'last_name']);

        return view('coordinator.matching', compact('meetings', 'facilities', 'volunteers'));
    }

    /**
     * Store a new recurring meeting slot.
     *
     * The meeting is wrapped in a transaction so the audit log record is created
     * atomically with the meeting itself.
     */
    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'facility_id'       => 'required|exists:facilities,facility_id',
            'day_of_week'       => 'required|integer|between:0,6',
            'week_of_month'     => 'required|integer|between:1,5',
            'meeting_time'      => 'required|date_format:H:i',
            'duration_minutes'  => 'nullable|integer|min:15|max:480',
            'format'            => 'required|in:in_person,virtual,hybrid',
            'volunteers_needed' => 'required|integer|between:1,5',
            'notes'             => 'nullable|string|max:2000',
        ]);

        return DB::transaction(function () use ($validated) {
            $meeting = Meeting::create([
                'facility_id'       => $validated['facility_id'],
                'day_of_week'       => $validated['day_of_week'],
                'week_of_month'     => $validated['week_of_month'],
                'meeting_time'      => $validated['meeting_time'],
                'duration_minutes'  => $validated['duration_minutes'] ?? 60,
                'format'            => $validated['format'],
                'volunteers_needed' => $validated['volunteers_needed'],
                'notes'             => $validated['notes'] ?? null,
                'status'            => 'active',
            ]);

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'create_meeting',
                'entity_type'    => 'meetings',
                'entity_id'      => $meeting->meeting_id,
                'change_details' => ['created' => $meeting->toArray()],
            ]);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Recurring meeting slot created successfully.');
        });
    }

    /**
     * Show meeting slot details and its upcoming occurrences with assignments.
     *
     * Calculates the next three months of occurrence dates from the recurring pattern
     * and loads the assignments already recorded for each one.
     */
    public function show(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meeting->load('facility');

        $occurrences = $this->buildUpcomingOccurrences($meeting);

        // Load all active volunteers for the manual assignment dropdown in the view.
        $volunteers = Volunteer::withoutTrashed()->orderBy('last_name')->get();

        return view('placeholder.coming-soon', compact('meeting', 'occurrences', 'volunteers'));
    }

    /**
     * Assign a volunteer to a specific occurrence of a recurring meeting.
     *
     * Enforces the volunteer cap and prevents duplicate assignments for the same date.
     * The DB transaction commits both the assignment and the audit log together.
     * SMS is dispatched after the transaction so a provider failure can never
     * roll back a successful assignment.
     */
    public function assign(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'volunteer_id'    => 'required|exists:volunteers,volunteer_id',
            'assignment_date' => 'required|date|after_or_equal:today',
            'assignment_type' => 'in:auto,manual',
            'override_reason' => 'nullable|string|max:500',
        ]);

        $dateStr = $validated['assignment_date'];
        $type    = $validated['assignment_type'] ?? 'manual';

        // Guard: check the volunteer cap before touching the database.
        $current = $meeting->activeAssignmentsForDate($dateStr)->count();
        if ($current >= $meeting->volunteers_needed) {
            return back()->with('error', "This occurrence already has {$meeting->volunteers_needed} volunteer(s) assigned.");
        }

        // Guard: prevent assigning the same volunteer to the same date twice.
        $alreadyAssigned = MeetingAssignment::where('meeting_id', $meeting->meeting_id)
            ->where('volunteer_id', $validated['volunteer_id'])
            ->where('assignment_date', $dateStr)
            ->whereIn('status', ['pending_confirmation', 'confirmed'])
            ->exists();

        if ($alreadyAssigned) {
            return back()->with('error', 'This volunteer is already assigned to this occurrence.');
        }

        // Wrap both DB writes in a transaction so they succeed or fail together.
        $assignment = DB::transaction(function () use ($validated, $meeting, $dateStr, $type) {
            $assignment = MeetingAssignment::create([
                'meeting_id'      => $meeting->meeting_id,
                'volunteer_id'    => $validated['volunteer_id'],
                'assignment_date' => $dateStr,
                'status'          => 'pending_confirmation',
                'assignment_type' => $type,
                'override_reason' => $validated['override_reason'] ?? null,
            ]);

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'assign_volunteer',
                'entity_type'    => 'meeting_assignments',
                'entity_id'      => $assignment->meeting_assignment_id,
                'change_details' => [
                    'volunteer_id'    => $validated['volunteer_id'],
                    'meeting_id'      => $meeting->meeting_id,
                    'assignment_date' => $dateStr,
                    'assignment_type' => $type,
                ],
            ]);

            return $assignment;
        });

        // SMS is dispatched after the transaction commits.
        // A queued job means provider latency does not affect response time,
        // and a provider outage cannot roll back the successfully saved assignment.
        if ($assignment->volunteer?->is_sms_deliverable) {
            SendSmsJob::dispatch($assignment, 'confirmation_request');
        }

        return back()->with('success', 'Volunteer assigned and confirmation request sent.');
    }

    /**
     * Confirm a pending assignment.
     *
     * Status can only move from pending_confirmation → confirmed.
     * Any other starting status is rejected with an error message.
     */
    public function confirmAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeAssignmentOwnerOrCoordinator($meetingAssignment);

        // Guard: only pending assignments can be confirmed.
        if ($meetingAssignment->status !== 'pending_confirmation') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Assignment is not in a pending state.'], 422);
            }
            return back()->with('error', 'Assignment is not in a pending state.');
        }

        $meetingAssignment->status       = 'confirmed';
        $meetingAssignment->confirmed_at = now();
        $meetingAssignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'confirm_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $meetingAssignment->meeting_assignment_id,
            'change_details' => ['status' => ['old' => 'pending_confirmation', 'new' => 'confirmed']],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Assignment confirmed.']);
        }
        return back()->with('success', 'Assignment confirmed.');
    }

    /**
     * Mark an assignment as declined.
     *
     * Used when a volunteer responds NO via SMS or a coordinator manually records
     * a refusal. Triggers no SMS — the volunteer's response is the trigger.
     */
    public function declineAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeAssignmentOwnerOrCoordinator($meetingAssignment);

        $oldStatus                 = $meetingAssignment->status;
        $meetingAssignment->status = 'declined';
        $meetingAssignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'decline_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $meetingAssignment->meeting_assignment_id,
            'change_details' => ['status' => ['old' => $oldStatus, 'new' => 'declined']],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Assignment declined.']);
        }
        return back()->with('success', 'Assignment declined.');
    }

    /**
     * Cancel an assignment and notify the volunteer.
     *
     * The cancellation is saved first so it succeeds even if the SMS fails.
     * The SMS job is dispatched after the save — failure there does not undo the cancellation.
     */
    public function cancelAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeAssignmentOwnerOrCoordinator($meetingAssignment);

        $isCoordinatorOrAdmin = auth()->user()->hasAnyRole(['coordinator', 'admin']);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus                 = $meetingAssignment->status;
        $meetingAssignment->status = 'cancelled';
        $meetingAssignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'cancel_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $meetingAssignment->meeting_assignment_id,
            'change_details' => [
                'status' => ['old' => $oldStatus, 'new' => 'cancelled'],
                'reason' => $validated['reason'] ?? null,
            ],
        ]);

        // Only SMS the volunteer when a coordinator/admin is cancelling on their behalf.
        if ($isCoordinatorOrAdmin && $meetingAssignment->volunteer?->is_sms_deliverable) {
            SendSmsJob::dispatch($meetingAssignment, 'cancellation');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Assignment cancelled.']);
        }
        return back()->with('success', 'Assignment cancelled.');
    }

    /**
     * Reinstate a declined or cancelled assignment back to pending confirmation.
     *
     * Allows a volunteer to reverse a decline or cancellation — for example if
     * they change their mind or the cancellation was made in error. The status
     * moves back to pending_confirmation so the volunteer must explicitly
     * re-confirm, keeping the confirmation audit trail intact.
     *
     * Only declined or cancelled assignments can be reinstated; any other
     * starting status is rejected to prevent invalid state transitions.
     */
    public function reinstateAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeAssignmentOwnerOrCoordinator($meetingAssignment);

        // Guard: only declined or cancelled assignments can be reinstated.
        if (!in_array($meetingAssignment->status, ['declined', 'cancelled'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Assignment cannot be reinstated from its current status.'], 422);
            }
            return back()->with('error', 'Assignment cannot be reinstated from its current status.');
        }

        $oldStatus = $meetingAssignment->status;
        $meetingAssignment->status = 'pending_confirmation';
        $meetingAssignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'reinstate_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $meetingAssignment->meeting_assignment_id,
            'change_details' => ['status' => ['old' => $oldStatus, 'new' => 'pending_confirmation']],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Assignment reinstated.']);
        }
        return back()->with('success', 'Assignment reinstated.');
    }

    /**
     * Mark a recurring meeting slot as inactive.
     *
     * Inactive slots remain in the database for historical reporting but no
     * longer appear in the active scheduling queue.
     */
    public function deactivate(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meeting->status = 'inactive';
        $meeting->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'deactivate_meeting',
            'entity_type'    => 'meetings',
            'entity_id'      => $meeting->meeting_id,
            'change_details' => ['status' => ['old' => 'active', 'new' => 'inactive']],
        ]);

        return back()->with('success', 'Meeting slot deactivated.');
    }

    /**
     * Restore a previously deactivated meeting slot to active scheduling.
     */
    public function activate(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meeting->status = 'active';
        $meeting->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'activate_meeting',
            'entity_type'    => 'meetings',
            'entity_id'      => $meeting->meeting_id,
            'change_details' => ['status' => ['old' => 'inactive', 'new' => 'active']],
        ]);

        return back()->with('success', 'Meeting slot reactivated.');
    }

    /**
     * Soft-delete a recurring meeting slot and cancel all future assignments.
     *
     * Both operations are wrapped in a transaction so the database never ends up
     * with a deleted meeting that still has live assignments attached.
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        return DB::transaction(function () use ($meeting) {
            $today = now()->toDateString();

            // Cancel upcoming assignments before deleting so volunteers' records
            // reflect the cancellation rather than pointing at a deleted meeting.
            $meeting->assignments()
                ->where('assignment_date', '>=', $today)
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->update(['status' => 'cancelled']);

            $meeting->delete();

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'delete_meeting',
                'entity_type'    => 'meetings',
                'entity_id'      => $meeting->meeting_id,
                'change_details' => ['deleted' => true],
            ]);

            return redirect()->route('meetings.index')
                ->with('success', 'Meeting slot deleted and future assignments cancelled.');
        });
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Apply an assignment-status filter to the meeting query.
     *
     * Extracted into its own method to keep index() easy to scan — the filter
     * logic would otherwise add significant nesting to an already busy method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status 'unassigned' or 'assigned'
     * @param string $today  Date string in Y-m-d format.
     */
    private function applyAssignmentStatusFilter($query, string $status, string $today): void
    {
        if ($status === 'unassigned') {
            // Meetings whose next occurrence has no active assignments yet.
            $query->whereDoesntHave('assignments', function ($q) use ($today) {
                $q->whereIn('status', ['confirmed', 'pending_confirmation'])
                  ->where('assignment_date', '>=', $today);
            });
            return;
        }

        if ($status === 'assigned') {
            // Meetings that have at least one active assignment on an upcoming date.
            $query->whereHas('assignments', function ($q) use ($today) {
                $q->whereIn('status', ['confirmed', 'pending_confirmation'])
                  ->where('assignment_date', '>=', $today);
            });
        }
    }

    /**
     * Build the list of upcoming occurrence data for the show view.
     *
     * Calculates the occurrence date for each of the next three calendar months
     * using the meeting's day-of-week + week-of-month pattern, then loads the
     * assignments already recorded for each date.
     *
     * @return array<int, array{date: \Carbon\Carbon, date_str: string, assignments: mixed, filled: int, needed: int}>
     */
    private function buildUpcomingOccurrences(Meeting $meeting): array
    {
        $now         = now();
        $occurrences = [];

        for ($monthOffset = 0; $monthOffset < 3; $monthOffset++) {
            $year  = $now->copy()->addMonths($monthOffset)->year;
            $month = $now->copy()->addMonths($monthOffset)->month;
            $date  = $meeting->occurrenceInMonth($year, $month);

            // Skip months where the occurrence has already passed.
            if (!$date || $date->lt($now->startOfDay())) {
                continue;
            }

            $dateStr     = $date->toDateString();
            $assignments = $meeting->assignmentsForDate($dateStr)->with('volunteer')->get();

            $occurrences[] = [
                'date'        => $date,
                'date_str'    => $dateStr,
                'assignments' => $assignments,
                'filled'      => $assignments->whereIn('status', ['confirmed', 'pending_confirmation'])->count(),
                'needed'      => $meeting->volunteers_needed,
            ];
        }

        return $occurrences;
    }
}
