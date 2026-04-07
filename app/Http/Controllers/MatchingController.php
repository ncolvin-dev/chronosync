<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exposes the volunteer matching algorithm as JSON API endpoints.
 *
 * These endpoints are consumed by the coordinator dashboard via AJAX.
 * All three actions are protected by role middleware in routes/web.php
 * and additionally guarded by the inherited authorizeCoordinatorOrAdmin() check.
 *
 * MatchingService is injected through the constructor so the controller
 * depends on the abstraction, not the concrete implementation.
 */
class MatchingController extends Controller
{
    public function __construct(private readonly MatchingService $matchingService) {}

    /**
     * Return a ranked list of candidate volunteers for a meeting.
     *
     * Each candidate includes a score breakdown so the coordinator can see
     * which dimensions drove their ranking. The 'already_assigned' flag lets
     * the UI disable the assign button for volunteers already on this occurrence.
     *
     * getCandidates() returns an array collection (not Eloquent models), so
     * each item is accessed with array syntax: $candidate['key'].
     */
    public function getCandidates(Meeting $meeting): JsonResponse
    {
        $this->authorizeCoordinatorOrAdmin();

        $today      = now()->toDateString();
        $candidates = $this->matchingService->getCandidates($meeting);

        $payload = $candidates->map(function (array $candidate) use ($meeting, $today) {
            // Check whether this volunteer already has an active assignment for today's date.
            $alreadyAssigned = $meeting->assignments()
                ->where('volunteer_id', $candidate['volunteer_id'])
                ->where('assignment_date', $today)
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->exists();

            return [
                'volunteer_id'    => $candidate['volunteer_id'],
                'name'            => $candidate['name'],
                'match_score'     => $candidate['match_score'],
                'is_available'    => $candidate['is_available'],
                'already_assigned'=> $alreadyAssigned,
                'score_breakdown' => $candidate['scores'],
            ];
        });

        return response()->json(['candidates' => $payload]);
    }

    /**
     * Automatically assign the top-scoring volunteer to a specific meeting occurrence.
     *
     * Validates that a target date is provided, checks the volunteer cap, creates
     * the assignment, and queues an SMS confirmation. The cache is invalidated so
     * the next getCandidates() call reflects the updated pool.
     *
     * Models use ULID primary keys — always reference meeting_id and volunteer_id,
     * never the generic ->id accessor which returns null for non-standard PKs.
     */
    public function autoAssign(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'assignment_date' => 'required|date|after_or_equal:today',
        ]);

        $dateStr   = $validated['assignment_date'];
        $candidate = $this->matchingService->autoAssign($meeting);

        // Guard: no candidates means the volunteer pool is empty or no one scored above zero.
        if ($candidate === null) {
            return response()->json(['success' => false, 'message' => 'No suitable volunteers available.'], 400);
        }

        // Guard: respect the per-occurrence volunteer cap before creating the assignment.
        $currentCount = $meeting->activeAssignmentsForDate($dateStr)->count();

        if ($currentCount >= $meeting->volunteers_needed) {
            return response()->json([
                'success' => false,
                'message' => "This occurrence already has {$meeting->volunteers_needed} volunteer(s) assigned.",
            ], 422);
        }

        // Both models use ULIDs as their primary keys — use the named key columns,
        // not ->id, which returns null when the PK column has a non-standard name.
        $assignment = MeetingAssignment::create([
            'meeting_id'      => $meeting->meeting_id,
            'volunteer_id'    => $candidate->volunteer_id,
            'assignment_date' => $dateStr,
            'status'          => 'pending_confirmation',
            'assignment_type' => 'auto',
        ]);

        // Invalidate the cache so the next candidate list reflects the assignment.
        $this->matchingService->invalidateCandidateCache($meeting);

        // Dispatch SMS as a queued job so the HTTP response returns immediately.
        // Provider latency and failures are handled by the job's retry logic.
        if ($candidate->is_sms_deliverable) {
            SendSmsJob::dispatch($assignment, 'confirmation_request');
        }

        return response()->json([
            'success'        => true,
            'message'        => "Volunteer {$candidate->full_name} assigned.",
            'assignment_id'  => $assignment->meeting_assignment_id,
            'volunteer_id'   => $candidate->volunteer_id,
            'volunteer_name' => $candidate->full_name,
        ]);
    }

    /**
     * Return the next 2–3 best candidates after the top pick.
     *
     * Gives the coordinator alternatives to review when they want to choose
     * manually rather than accepting the auto-assign recommendation.
     * Candidates are arrays — access with bracket syntax, not object notation.
     */
    public function getSuggestions(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeCoordinatorOrAdmin();

        $suggestions = $this->matchingService->getSuggestions($meeting);

        $payload = $suggestions->map(fn(array $candidate) => [
            'volunteer_id' => $candidate['volunteer_id'],
            'name'         => $candidate['name'],
            'match_score'  => $candidate['match_score'],
            'is_available' => $candidate['is_available'],
        ]);

        return response()->json(['suggestions' => $payload]);
    }
}
