<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Facility;
use App\Models\Volunteer;
use App\Models\AuditLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * List recurring meeting slots with optional filters.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Meeting::with('facility')->withoutTrashed();

        // Filter by facility
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter by status (active/inactive meeting slots)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by format
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Filter by day of week
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Filter by assignment status for the upcoming occurrence
        if ($request->filled('assignment_status')) {
            $today = now()->toDateString();
            if ($request->assignment_status === 'unassigned') {
                // Meetings whose next occurrence has fewer confirmed/pending assignments than needed
                $query->whereDoesntHave('assignments', function ($q) use ($today) {
                    $q->whereIn('status', ['confirmed', 'pending_confirmation'])
                      ->where('assignment_date', '>=', $today);
                });
            } elseif ($request->assignment_status === 'assigned') {
                $query->whereHas('assignments', function ($q) use ($today) {
                    $q->whereIn('status', ['confirmed', 'pending_confirmation'])
                      ->where('assignment_date', '>=', $today);
                });
            }
        }

        $meetings = $query
            ->orderBy('day_of_week')
            ->orderBy('week_of_month')
            ->orderBy('meeting_time')
            ->paginate(15);

        $facilities = Facility::where('status', 'active')
            ->orderBy('facility_name')
            ->get();

        return view('coordinator.matching', compact('meetings', 'facilities'));
    }

    /**
     * Store a new recurring meeting slot.
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
     * Show meeting slot details and upcoming occurrences with assignments.
     */
    public function show(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meeting->load('facility');

        // Build the next 3 months of occurrences with their assignments
        $occurrences = [];
        $now = now();
        for ($m = 0; $m < 3; $m++) {
            $year  = $now->copy()->addMonths($m)->year;
            $month = $now->copy()->addMonths($m)->month;
            $date  = $meeting->occurrenceInMonth($year, $month);

            if ($date && $date->gte($now->startOfDay())) {
                $dateStr     = $date->toDateString();
                $assignments = $meeting->assignmentsForDate($dateStr)
                    ->with('volunteer')
                    ->get();

                $occurrences[] = [
                    'date'        => $date,
                    'date_str'    => $dateStr,
                    'assignments' => $assignments,
                    'filled'      => $assignments->whereIn('status', ['confirmed', 'pending_confirmation'])->count(),
                    'needed'      => $meeting->volunteers_needed,
                ];
            }
        }

        // Available volunteers (for manual assignment UI)
        $volunteers = Volunteer::withoutTrashed()->orderBy('last_name')->get();

        return view('placeholder.coming-soon', compact('meeting', 'occurrences', 'volunteers'));
    }

    /**
     * Assign a volunteer to a specific occurrence (auto or manual).
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

        return DB::transaction(function () use ($validated, $meeting) {
            $dateStr  = $validated['assignment_date'];
            $type     = $validated['assignment_type'] ?? 'manual';

            // Enforce the 5-volunteer cap
            $current = $meeting->activeAssignmentsForDate($dateStr)->count();
            if ($current >= $meeting->volunteers_needed) {
                return back()->with('error', "This occurrence already has {$meeting->volunteers_needed} volunteer(s) assigned.");
            }

            // Prevent duplicate assignment
            $existing = MeetingAssignment::where('meeting_id', $meeting->meeting_id)
                ->where('volunteer_id', $validated['volunteer_id'])
                ->where('assignment_date', $dateStr)
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->first();

            if ($existing) {
                return back()->with('error', 'This volunteer is already assigned to this occurrence.');
            }

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

            // Send SMS confirmation request
            $volunteer = Volunteer::find($validated['volunteer_id']);
            if ($volunteer && $volunteer->is_sms_deliverable) {
                try {
                    $smsService = new SmsService();
                    $smsService->sendConfirmationRequest($assignment);
                } catch (\Exception $e) {
                    // SMS failure should not block assignment
                }
            }

            return back()->with('success', 'Volunteer assigned and confirmation request sent.');
        });
    }

    /**
     * Confirm an assignment (volunteer or coordinator confirming).
     */
    public function confirmAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeCoordinatorOrAdmin();
        $assignment = $meetingAssignment;

        if ($assignment->status !== 'pending_confirmation') {
            return back()->with('error', 'Assignment is not in a pending state.');
        }

        $assignment->status       = 'confirmed';
        $assignment->confirmed_at = now();
        $assignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'confirm_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $assignment->meeting_assignment_id,
            'change_details' => ['status' => ['old' => 'pending_confirmation', 'new' => 'confirmed']],
        ]);

        return back()->with('success', 'Assignment confirmed.');
    }

    /**
     * Decline an assignment (volunteer responding via SMS or coordinator removing).
     */
    public function declineAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeCoordinatorOrAdmin();
        $assignment = $meetingAssignment;

        $assignment->status = 'declined';
        $assignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'decline_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $assignment->meeting_assignment_id,
            'change_details' => ['status' => ['old' => $assignment->getOriginal('status'), 'new' => 'declined']],
        ]);

        return back()->with('success', 'Assignment declined.');
    }

    /**
     * Cancel an assignment (coordinator removing a volunteer from an occurrence).
     */
    public function cancelAssignment(Request $request, MeetingAssignment $meetingAssignment)
    {
        $this->authorizeCoordinatorOrAdmin();
        $assignment = $meetingAssignment;

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus         = $assignment->status;
        $assignment->status = 'cancelled';
        $assignment->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'cancel_assignment',
            'entity_type'    => 'meeting_assignments',
            'entity_id'      => $assignment->meeting_assignment_id,
            'change_details' => [
                'status' => ['old' => $oldStatus, 'new' => 'cancelled'],
                'reason' => $validated['reason'] ?? null,
            ],
        ]);

        // Notify volunteer of cancellation
        $volunteer = $assignment->volunteer;
        if ($volunteer && $volunteer->is_sms_deliverable) {
            try {
                $smsService = new SmsService();
                $smsService->sendCancellation($assignment);
            } catch (\Exception $e) {
                // SMS failure should not block cancellation
            }
        }

        return back()->with('success', 'Assignment cancelled and volunteer notified.');
    }

    /**
     * Mark an entire recurring meeting slot as inactive (deactivate it).
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
     * Reactivate a previously inactive meeting slot.
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
     * Soft-delete a recurring meeting slot and cancel future assignments.
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        return DB::transaction(function () use ($meeting) {
            $today = now()->toDateString();

            // Cancel all future pending/confirmed assignments
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

    /**
     * Authorize coordinator or admin only.
     */
    private function authorizeCoordinatorOrAdmin()
    {
        $user  = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }
}
