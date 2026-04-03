<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Facility;
use App\Models\Volunteer;
use App\Models\AuditLog;
use App\Services\SmsService;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * List meetings with filters.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Meeting::with('facility', 'assignedVolunteer');

        // Filter by facility
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('meeting_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('meeting_date', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assignment status
        if ($request->filled('assignment_status')) {
            if ($request->assignment_status === 'unassigned') {
                $query->doesntHave('assignments');
            } elseif ($request->assignment_status === 'assigned') {
                $query->has('assignments');
            }
        }

        $meetings = $query->orderBy('scheduled_time', 'desc')
            ->paginate(15);

        $facilities = Facility::where('status', 'active')
            ->orderBy('facility_name')
            ->get();

        return view('coordinator.matching', compact('meetings', 'facilities'));
    }

    /**
     * Store a new meeting.
     */
    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'meeting_date' => 'required|date|after:today',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'meeting_type' => 'required|in:group,individual,workshop,peer_support',
            'description' => 'nullable|string|max:1000',
            'expected_attendees' => 'nullable|integer|min:1',
            'special_requirements' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $dateTime = Carbon::parse($validated['meeting_date'] . ' ' . $validated['time_start']);

            $meeting = Meeting::create([
                'facility_id' => $validated['facility_id'],
                'meeting_date' => $dateTime,
                'time_end' => $dateTime->copy()->setTimeFromTimeString($validated['time_end']),
                'meeting_type' => $validated['meeting_type'],
                'description' => $validated['description'] ?? null,
                'expected_attendees' => $validated['expected_attendees'] ?? null,
                'special_requirements' => $validated['special_requirements'] ?? null,
                'status' => 'scheduled',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'meeting_created',
                'model_type' => Meeting::class,
                'model_id' => $meeting->id,
                'changes' => ['created' => $meeting->toArray()],
            ]);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting created successfully.');
        });
    }

    /**
     * Show meeting details.
     */
    public function show(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $assignments = $meeting->assignments()->with('volunteer.user')->get();

        return view('placeholder.coming-soon', compact('meeting', 'assignments'));
    }

    /**
     * Assign volunteer to meeting using matching algorithm.
     */
    public function assign(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $matchingService = new MatchingService();
        $candidate = $matchingService->autoAssign($meeting);

        if (!$candidate) {
            return back()->with('error', 'No suitable volunteers available for this meeting.');
        }

        return DB::transaction(function () use ($candidate, $meeting) {
            $assignment = MeetingAssignment::create([
                'meeting_id' => $meeting->id,
                'volunteer_id' => $candidate->id,
                'status' => 'pending_confirmation',
                'assignment_type' => 'auto',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'volunteer_assigned',
                'model_type' => MeetingAssignment::class,
                'model_id' => $assignment->id,
                'changes' => [
                    'volunteer_id' => $candidate->id,
                    'volunteer_name' => $candidate->user->full_name,
                    'meeting_id' => $meeting->id,
                ],
            ]);

            // Send SMS confirmation request
            $smsService = new SmsService();
            $smsService->sendConfirmationRequest($assignment);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', "Volunteer {$candidate->user->full_name} assigned. Awaiting confirmation.");
        });
    }

    /**
     * Manual assignment with coordinator override and confirmation dialog.
     */
    public function manualAssign(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'volunteer_id' => 'required|exists:volunteers,id',
            'override_reason' => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $meeting) {
            $volunteer = Volunteer::find($validated['volunteer_id']);

            // Check for existing assignment
            $existingAssignment = $meeting->assignments()
                ->where('volunteer_id', $volunteer->id)
                ->first();

            if ($existingAssignment) {
                return back()->with('error', 'This volunteer is already assigned to this meeting.');
            }

            $assignment = MeetingAssignment::create([
                'meeting_id' => $meeting->id,
                'volunteer_id' => $volunteer->id,
                'status' => 'confirmed',
                'assignment_type' => 'manual',
            ]);

            // Log the override
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'volunteer_manual_assigned',
                'model_type' => MeetingAssignment::class,
                'model_id' => $assignment->id,
                'changes' => [
                    'volunteer_id' => $volunteer->id,
                    'volunteer_name' => $volunteer->user->full_name,
                    'meeting_id' => $meeting->id,
                    'override_reason' => $validated['override_reason'],
                ],
            ]);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', "Volunteer {$volunteer->user->full_name} manually assigned.");
        });
    }

    /**
     * Unassign volunteer from meeting.
     */
    public function unassign(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'assignment_id' => 'required|exists:meeting_assignments,id',
            'reason' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $meeting) {
            $assignment = MeetingAssignment::findOrFail($validated['assignment_id']);

            if ($assignment->meeting_id !== $meeting->id) {
                abort(403);
            }

            $volunteerName = $assignment->volunteer->user->full_name;

            $assignment->status = 'cancelled';
            $assignment->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'volunteer_unassigned',
                'model_type' => MeetingAssignment::class,
                'model_id' => $assignment->id,
                'changes' => [
                    'status' => [
                        'old' => 'confirmed',
                        'new' => 'cancelled',
                    ],
                    'reason' => $validated['reason'] ?? null,
                ],
            ]);

            // Send SMS cancellation
            $smsService = new SmsService();
            $smsService->sendCancellation($assignment);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', "Assignment for {$volunteerName} cancelled and notification sent.");
        });
    }

    /**
     * Mark meeting as complete.
     */
    public function complete(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
            'attendees_count' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($validated, $meeting) {
            $oldStatus = $meeting->status;

            $meeting->status = 'completed';
            $meeting->notes = $validated['notes'] ?? null;
            $meeting->attendees_count = $validated['attendees_count'] ?? null;
            $meeting->completed_at = now();
            $meeting->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'meeting_completed',
                'model_type' => Meeting::class,
                'model_id' => $meeting->id,
                'changes' => [
                    'status' => [
                        'old' => $oldStatus,
                        'new' => 'completed',
                    ],
                    'notes' => $validated['notes'] ?? null,
                ],
            ]);

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting marked as completed.');
        });
    }

    /**
     * Cancel a meeting.
     */
    public function cancel(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $meeting) {
            $oldStatus = $meeting->status;

            $meeting->status = 'cancelled';
            $meeting->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'meeting_cancelled',
                'model_type' => Meeting::class,
                'model_id' => $meeting->id,
                'changes' => [
                    'status' => [
                        'old' => $oldStatus,
                        'new' => 'cancelled',
                    ],
                    'cancellation_reason' => $validated['cancellation_reason'],
                ],
            ]);

            // Send SMS to assigned volunteer
            $assignment = $meeting->assignments()
                ->where('status', 'confirmed')
                ->first();

            if ($assignment) {
                $smsService = new SmsService();
                $smsService->sendCancellation($assignment);
            }

            return redirect()->route('meetings.show', $meeting)
                ->with('success', 'Meeting cancelled and volunteer notified.');
        });
    }

    /**
     * Authorize coordinator or admin.
     */
    private function authorizeCoordinatorOrAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }
}
