<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Volunteer;
use App\Services\MatchingService;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Get ordered list of matching candidates for a meeting.
     */
    public function getCandidates(Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $candidates = $this->matchingService->getCandidates($meeting);

        return response()->json([
            'candidates' => $candidates->map(function ($volunteer) use ($meeting) {
                $assignment = $meeting->assignments()
                    ->where('volunteer_id', $volunteer->id)
                    ->first();

                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->user->full_name,
                    'email' => $volunteer->user->email,
                    'phone' => $volunteer->phone,
                    'match_score' => $volunteer->match_score ?? 0,
                    'is_available' => $volunteer->is_available ?? false,
                    'languages' => json_decode($volunteer->languages, true) ?? [],
                    'certifications' => json_decode($volunteer->certifications, true) ?? [],
                    'already_assigned' => (bool) $assignment,
                ];
            }),
        ]);
    }

    /**
     * Auto-assign top matching candidate to meeting.
     */
    public function autoAssign(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $candidate = $this->matchingService->autoAssign($meeting);

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'No suitable volunteers available.',
            ], 400);
        }

        $assignment = MeetingAssignment::create([
            'meeting_id' => $meeting->id,
            'volunteer_id' => $candidate->id,
            'status' => 'pending_confirmation',
            'assignment_type' => 'auto',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Volunteer {$candidate->user->full_name} assigned.",
            'assignment_id' => $assignment->id,
            'volunteer_id' => $candidate->id,
            'volunteer_name' => $candidate->user->full_name,
        ]);
    }

    /**
     * Get 2-3 alternate suggestions for coordinator.
     */
    public function getSuggestions(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $suggestions = $this->matchingService->getSuggestions($meeting);

        return response()->json([
            'suggestions' => $suggestions->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->user->full_name,
                    'email' => $volunteer->user->email,
                    'phone' => $volunteer->phone,
                    'match_score' => $volunteer->match_score ?? 0,
                    'reason' => $volunteer->match_reason ?? 'Good match for this meeting',
                    'languages' => json_decode($volunteer->languages, true) ?? [],
                    'certifications' => json_decode($volunteer->certifications, true) ?? [],
                ];
            }),
        ]);
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
