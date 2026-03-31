<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\Volunteer;

class MatchingService
{
    public function getCandidates(Meeting $meeting)
    {
        $candidates = Volunteer::where('is_active', true)
            ->with(['user', 'assignments', 'credentials'])
            ->get()
            ->map(fn($v) => $this->calculateMatchScore($v, $meeting))
            ->filter(fn($v) => $v['match_score'] > 0)
            ->sortByDesc('match_score')
            ->values();

        return $candidates;
    }

    public function autoAssign(Meeting $meeting)
    {
        $candidates = $this->getCandidates($meeting);
        return $candidates->isEmpty() ? null : Volunteer::find($candidates->first()['id']);
    }

    public function getSuggestions(Meeting $meeting)
    {
        return $this->getCandidates($meeting)->skip(1)->take(3)->values();
    }

    private function calculateMatchScore(Volunteer $volunteer, Meeting $meeting): array
    {
        $scores = [
            'availability' => $this->scoreAvailability($volunteer, $meeting),
            'certifications' => $this->scoreCertifications($volunteer, $meeting),
            'languages' => $this->scoreLanguages($volunteer, $meeting),
            'facility' => $this->scoreFacilityFamiliarity($volunteer, $meeting),
            'recency' => $this->scoreRecency($volunteer),
        ];

        $weights = ['availability' => 0.40, 'certifications' => 0.30, 'languages' => 0.15, 'facility' => 0.10, 'recency' => 0.05];

        $total = 0;
        foreach ($scores as $category => $score) {
            $total += $score * $weights[$category];
        }

        return [
            'id' => $volunteer->id,
            'name' => $volunteer->user->full_name,
            'match_score' => round($total, 2),
            'is_available' => $scores['availability'] > 0,
        ];
    }

    private function scoreAvailability(Volunteer $volunteer, Meeting $meeting): float
    {
        $availability = json_decode($volunteer->availability, true) ?? [];
        if (empty($availability)) return 0;

        $dayOfWeek = $meeting->date_scheduled->dayOfWeek;
        $weekNumber = intdiv($meeting->date_scheduled->day, 7);
        if ($dayOfWeek === 0) $dayOfWeek = 6; else $dayOfWeek--;

        $slotIndex = ($weekNumber * 7) + $dayOfWeek;
        if ($slotIndex >= count($availability)) return 50;

        return $availability[$slotIndex] ? 100 : 25;
    }

    private function scoreCertifications(Volunteer $volunteer, Meeting $meeting): float
    {
        $certs = json_decode($volunteer->certifications, true) ?? [];
        if (empty($certs)) return 50;

        $required = $this->getRequiredCerts($meeting->meeting_type);
        if (empty($required)) return 75;

        $matching = count(array_intersect($certs, $required));
        return 50 + (($matching / count($required)) * 50);
    }

    private function scoreLanguages(Volunteer $volunteer, Meeting $meeting): float
    {
        $langs = json_decode($volunteer->languages, true) ?? [];
        if (empty($langs) || in_array('English', $langs)) return 75;
        return count($langs) >= 2 ? 90 : 80;
    }

    private function scoreFacilityFamiliarity(Volunteer $volunteer, Meeting $meeting): float
    {
        $prior = $volunteer->assignments()
            ->whereHas('meeting', fn($q) => $q->where('facility_id', $meeting->facility_id))
            ->count();

        if ($prior >= 5) return 100;
        if ($prior >= 2) return 80;
        if ($prior === 1) return 60;
        return 50;
    }

    private function scoreRecency(Volunteer $volunteer): float
    {
        $last = $volunteer->assignments()->latest('created_at')->first();
        if (!$last) return 100;

        $days = now()->diffInDays($last->created_at);
        if ($days >= 30) return 100;
        if ($days >= 14) return 90;
        if ($days >= 7) return 80;
        return 60;
    }

    private function getRequiredCerts(string $type): array
    {
        $certs = [
            'peer_support' => ['peer support'],
            'workshop' => ['facilitator'],
            'individual' => ['training'],
            'group' => [],
        ];
        return $certs[$type] ?? [];
    }
}
