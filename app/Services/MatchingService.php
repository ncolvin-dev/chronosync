<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Meeting;
use App\Models\Volunteer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Scores and ranks volunteers as candidates for a specific meeting occurrence.
 *
 * Scoring is multi-dimensional: availability, credentials, prior experience,
 * geographic proximity, and how recently the volunteer was last assigned.
 * Each dimension produces a 0–100 score. The dimensions are then combined
 * using configurable weights from config/chronosync.php so the balance can
 * be tuned without changing code.
 *
 * Results are cached per meeting to reduce database load. The cache is
 * invalidated whenever an assignment changes, keeping results accurate.
 */
class MatchingService
{
    /**
     * Get a ranked list of candidate volunteers for a meeting.
     *
     * Uses the Cache-Aside pattern: serve from cache when available, otherwise
     * query the database and populate the cache. TTL jitter (±30 seconds around
     * 5 minutes) spreads expiry times across meetings so they don't all expire
     * simultaneously and overwhelm the database (Thunder Herd prevention).
     *
     * @return Collection<int, array{volunteer_id: string, name: string, match_score: float, is_available: bool, scores: array<string, float>}>
     */
    public function getCandidates(Meeting $meeting): Collection
    {
        $cacheKey = "matching_candidates_{$meeting->meeting_id}";
        $ttl      = 300 + random_int(-30, 30);

        return Cache::remember($cacheKey, $ttl, fn() => $this->fetchAndScoreCandidates($meeting));
    }

    /**
     * Return the single best-matching volunteer for auto-assignment.
     *
     * Fetches the top-scored candidate from the ranked list and loads the
     * full Volunteer model so callers get an object they can work with directly.
     * Returns null when no volunteer scores above zero.
     */
    public function autoAssign(Meeting $meeting): ?Volunteer
    {
        $top = $this->getCandidates($meeting)->first();

        if ($top === null) {
            return null;
        }

        return Volunteer::find($top['volunteer_id']);
    }

    /**
     * Return the next 2–3 candidates for coordinator review.
     *
     * Skips the top pick (reserved for auto-assign) and returns the runners-up
     * so a coordinator can choose a good alternative or compare options.
     */
    public function getSuggestions(Meeting $meeting): Collection
    {
        return $this->getCandidates($meeting)->skip(1)->take(3)->values();
    }

    /**
     * Clear the candidate cache for a meeting.
     *
     * Should be called any time an assignment is created, confirmed, declined,
     * or cancelled so the next getCandidates() call reflects the current pool.
     */
    public function invalidateCandidateCache(Meeting $meeting): void
    {
        Cache::forget("matching_candidates_{$meeting->meeting_id}");
    }

    // -------------------------------------------------------------------------
    // Private — candidate loading and scoring pipeline
    // -------------------------------------------------------------------------

    /**
     * Load all active volunteers with their related data, score each one,
     * filter out zero-score volunteers, and return the list ranked highest first.
     *
     * Eager-loading availability, credentials, and assignments in a single query
     * prevents N+1 problems when the scoring methods inspect those relationships.
     */
    private function fetchAndScoreCandidates(Meeting $meeting): Collection
    {
        return Volunteer::withoutTrashed()
            ->with(['assignments', 'credentials', 'availability'])
            ->get()
            ->map(fn(Volunteer $volunteer) => $this->scoreCandidate($volunteer, $meeting))
            ->filter(fn(array $candidate) => $candidate['match_score'] > 0)
            ->sortByDesc('match_score')
            ->values();
    }

    /**
     * Calculate a weighted composite score for one volunteer against one meeting.
     *
     * Each dimension is scored 0–100. The scores are multiplied by their weights
     * (from config/chronosync.php) and summed to produce the final match_score.
     * Storing the per-dimension scores in the result lets the UI show coordinators
     * why a volunteer ranked where they did.
     *
     * @return array{volunteer_id: string, name: string, match_score: float, is_available: bool, scores: array<string, float>}
     */
    private function scoreCandidate(Volunteer $volunteer, Meeting $meeting): array
    {
        $scores = [
            'availability_match'   => $this->scoreAvailability($volunteer, $meeting),
            'credential_match'     => $this->scoreCredentials($volunteer, $meeting),
            'previous_assignments' => $this->scorePreviousAssignments($volunteer, $meeting),
            'geographic_proximity' => $this->scoreGeographicProximity($volunteer),
            'volunteer_preference' => $this->scoreVolunteerPreference($volunteer),
        ];

        $weights = config('chronosync.matching.weights');

        // Multiply each dimension score by its configured weight and sum the results.
        $totalScore = array_sum(array_map(
            fn(string $dimension, float $score) => $score * ($weights[$dimension] ?? 0),
            array_keys($scores),
            $scores
        ));

        return [
            'volunteer_id' => $volunteer->volunteer_id,
            'name'         => $volunteer->full_name,
            'match_score'  => round($totalScore, 2),
            'is_available' => $scores['availability_match'] > 0,
            'scores'       => $scores,
        ];
    }

    /**
     * Score whether the volunteer is free at the meeting's scheduled time.
     *
     * Looks up the volunteer's Availability records for the matching week-of-month
     * and day-of-week, then checks whether the meeting's start hour falls inside
     * an available slot. Returns:
     *   100 — slot exists and volunteer marked it as available
     *    25 — slot exists but volunteer marked it as unavailable
     *     0 — no slot defined for this time (no data to indicate availability)
     */
    private function scoreAvailability(Volunteer $volunteer, Meeting $meeting): float
    {
        // meeting_time is stored as HH:MM:SS — extract just the hour for comparison.
        $meetingHour = (int) substr($meeting->meeting_time, 0, 2);

        $slot = $volunteer->availability
            ->where('week_of_month', $meeting->week_of_month)
            ->where('day_of_week', $meeting->day_of_week)
            ->first(fn($s) => $s->hour_start <= $meetingHour && $s->hour_end > $meetingHour);

        // Guard: no slot means we have no information — score zero rather than assume.
        if ($slot === null) {
            return 0;
        }

        return $slot->is_available ? 100 : 25;
    }

    /**
     * Score the volunteer's credentials for the meeting's specific facility.
     *
     * Only approved, non-expired credentials for the correct facility count.
     * The score increases with the number of valid credentials because more
     * credentials means the volunteer is cleared for more types of work there.
     */
    private function scoreCredentials(Volunteer $volunteer, Meeting $meeting): float
    {
        $today = now()->toDateString();

        $validCount = $volunteer->credentials
            ->where('facility_id', $meeting->facility_id)
            ->where('status', 'approved')
            ->filter(fn($credential) => $credential->expiration_date === null
                || $credential->expiration_date->toDateString() >= $today
            )
            ->count();

        return match (true) {
            $validCount === 0 => 0,    // no valid credentials — likely not cleared for this facility
            $validCount === 1 => 60,   // minimum clearance
            $validCount === 2 => 80,   // well credentialed
            default           => 100,  // fully credentialed (3 or more)
        };
    }

    /**
     * Score the volunteer's prior assignment history at this specific facility.
     *
     * Volunteers who have been to this facility before are familiar with the
     * location, staff, and format — reducing the chance of confusion or no-shows.
     */
    private function scorePreviousAssignments(Volunteer $volunteer, Meeting $meeting): float
    {
        $priorCount = $volunteer->assignments
            ->whereIn('status', ['confirmed', 'completed'])
            ->filter(fn($assignment) => $assignment->meeting?->facility_id === $meeting->facility_id)
            ->count();

        return match (true) {
            $priorCount >= 5  => 100,
            $priorCount >= 2  => 80,
            $priorCount === 1 => 60,
            default           => 50,  // no prior experience is not disqualifying, just lower priority
        };
    }

    /**
     * Estimate how accessible the facility is for this volunteer.
     *
     * Uses bus_line and neighborhood as lightweight proxies for transit access.
     * A proper geo-distance calculation would require coordinates that are not
     * currently stored in the database.
     */
    private function scoreGeographicProximity(Volunteer $volunteer): float
    {
        // A listed bus line is a strong signal the volunteer can reach any facility via transit.
        if (!empty($volunteer->bus_line)) {
            return 80;
        }

        // A listed neighborhood provides partial geographic context.
        if (!empty($volunteer->neighborhood)) {
            return 60;
        }

        // No location data — use a neutral mid-point rather than penalising the volunteer.
        return 50;
    }

    /**
     * Prefer volunteers who have not been assigned recently.
     *
     * Spreading assignments across the active volunteer pool prevents a small
     * group from bearing the entire load. Volunteers who haven't been assigned
     * in a month or more are treated as highest priority.
     */
    private function scoreVolunteerPreference(Volunteer $volunteer): float
    {
        $lastAssignment = $volunteer->assignments->sortByDesc('created_at')->first();

        // Guard: never assigned means they are very available and eager — highest score.
        if ($lastAssignment === null) {
            return 100;
        }

        $daysSinceLast = now()->diffInDays($lastAssignment->created_at);

        return match (true) {
            $daysSinceLast >= 30 => 100,
            $daysSinceLast >= 14 => 90,
            $daysSinceLast >= 7  => 80,
            default              => 60,
        };
    }
}
