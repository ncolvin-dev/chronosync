<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Facility;
use App\Models\Meeting;
use App\Models\Volunteer;
use App\Models\VolunteerCredential;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * ReportingService
 *
 * Generates reports for volunteer management:
 * - Coverage report (meetings with/without assigned volunteers)
 * - Facility schedule report
 * - Credential expiration report
 */
class ReportingService
{
    /**
     * Generate coverage report for a date range.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getCoverageReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate ??= now()->startOfMonth();
        $endDate ??= now()->endOfMonth();

        $meetings = Meeting::query()
            ->scheduled()
            ->dateBetween($startDate, $endDate)
            ->get();

        $totalMeetings = $meetings->count();
        $assignedMeetings = $meetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id !== null)->count();
        $unassignedMeetings = $totalMeetings - $assignedMeetings;
        $confirmedMeetings = $meetings->filter(fn (Meeting $m) => $m->hasConfirmedVolunteer())->count();

        $coveragePercentage = $totalMeetings > 0
            ? round(($assignedMeetings / $totalMeetings) * 100, 2)
            : 0;

        $confirmationRate = $assignedMeetings > 0
            ? round(($confirmedMeetings / $assignedMeetings) * 100, 2)
            : 0;

        // Group by facility
        $byFacility = $meetings->groupBy('facility_id')->map(function (Collection $facilityMeetings) {
            $facility = $facilityMeetings->first()->facility;
            $assigned = $facilityMeetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id !== null)->count();
            $confirmed = $facilityMeetings->filter(fn (Meeting $m) => $m->hasConfirmedVolunteer())->count();
            $total = $facilityMeetings->count();

            return [
                'facility_id' => $facility->facility_id,
                'facility_name' => $facility->facility_name,
                'total_meetings' => $total,
                'assigned_meetings' => $assigned,
                'unassigned_meetings' => $total - $assigned,
                'confirmed_meetings' => $confirmed,
                'coverage_percentage' => $total > 0 ? round(($assigned / $total) * 100, 2) : 0,
            ];
        });

        // Group by day of week
        $byDayOfWeek = $meetings->groupBy('day_of_week')->map(function (Collection $dayMeetings) {
            $dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
            $dayOfWeek = $dayMeetings->first()->day_of_week;
            $assigned = $dayMeetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id !== null)->count();
            $total = $dayMeetings->count();

            return [
                'day_of_week' => $dayOfWeek,
                'day_name' => $dayNames[$dayOfWeek],
                'total_meetings' => $total,
                'assigned_meetings' => $assigned,
                'coverage_percentage' => $total > 0 ? round(($assigned / $total) * 100, 2) : 0,
            ];
        })->sortBy('day_of_week')->values();

        return [
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_meetings' => $totalMeetings,
                'assigned_meetings' => $assignedMeetings,
                'unassigned_meetings' => $unassignedMeetings,
                'confirmed_meetings' => $confirmedMeetings,
                'coverage_percentage' => $coveragePercentage,
                'confirmation_rate' => $confirmationRate,
            ],
            'by_facility' => $byFacility->values()->toArray(),
            'by_day_of_week' => $byDayOfWeek->toArray(),
        ];
    }

    /**
     * Generate facility schedule report.
     *
     * @param Facility $facility
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getFacilityScheduleReport(Facility $facility, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate ??= now()->startOfMonth();
        $endDate ??= now()->endOfMonth();

        $meetings = Meeting::query()
            ->where('facility_id', $facility->facility_id)
            ->dateBetween($startDate, $endDate)
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get();

        $upcomingMeetings = $meetings->filter(fn (Meeting $m) => $m->isUpcoming());
        $unassignedUpcoming = $upcomingMeetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id === null);

        $meetings_data = $meetings->map(function (Meeting $meeting) {
            return [
                'meeting_id' => $meeting->meeting_id,
                'date' => $meeting->meeting_date->toDateString(),
                'time' => $meeting->meeting_time,
                'format' => $meeting->format,
                'status' => $meeting->status,
                'assigned_volunteer' => $meeting->assignedVolunteer ? [
                    'volunteer_id' => $meeting->assignedVolunteer->volunteer_id,
                    'name' => $meeting->assignedVolunteer->getFullNameAttribute(),
                ] : null,
                'confirmed_status' => $meeting->confirmed_status,
                'notes' => $meeting->notes,
            ];
        })->toArray();

        return [
            'facility' => [
                'facility_id' => $facility->facility_id,
                'facility_name' => $facility->facility_name,
                'address' => $facility->getFullAddressAttribute(),
            ],
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_meetings' => $meetings->count(),
                'upcoming_meetings' => $upcomingMeetings->count(),
                'assigned_meetings' => $meetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id !== null)->count(),
                'unassigned_upcoming' => $unassignedUpcoming->count(),
            ],
            'meetings' => $meetings_data,
        ];
    }

    /**
     * Generate credential expiration report.
     *
     * @param Facility|null $facility
     * @return array
     */
    public function getCredentialExpirationReport(?Facility $facility = null): array
    {
        $credentialService = new CredentialService();

        $expiringCredentials = VolunteerCredential::expiringSoon();
        $expiredCredentials = VolunteerCredential::expired();

        if ($facility !== null) {
            $expiringCredentials = $expiringCredentials->where('facility_id', $facility->facility_id);
            $expiredCredentials = $expiredCredentials->where('facility_id', $facility->facility_id);
        }

        $expiring = $expiringCredentials->get();
        $expired = $expiredCredentials->get();

        // Categorize by urgency
        $critical = $expired;
        $urgent = $expiring->filter(fn (VolunteerCredential $c) => $c->daysUntilExpiration() <= 7);
        $warning = $expiring->filter(fn (VolunteerCredential $c) => $c->daysUntilExpiration() > 7);

        return [
            'facility' => $facility ? [
                'facility_id' => $facility->facility_id,
                'facility_name' => $facility->facility_name,
            ] : null,
            'summary' => [
                'total_expiring_soon' => $expiring->count(),
                'total_expired' => $expired->count(),
                'critical_count' => $critical->count(),
                'urgent_count' => $urgent->count(),
                'warning_count' => $warning->count(),
            ],
            'critical' => $critical->map(fn (VolunteerCredential $c) => $this->formatCredentialAlert($c))->values()->toArray(),
            'urgent' => $urgent->map(fn (VolunteerCredential $c) => $this->formatCredentialAlert($c))->values()->toArray(),
            'warning' => $warning->map(fn (VolunteerCredential $c) => $this->formatCredentialAlert($c))->values()->toArray(),
        ];
    }

    /**
     * Generate volunteer activity report.
     *
     * @param Volunteer $volunteer
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getVolunteerActivityReport(Volunteer $volunteer, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate ??= now()->subMonths(3)->startOfMonth();
        $endDate ??= now()->endOfMonth();

        $meetings = Meeting::query()
            ->where('assigned_volunteer_id', $volunteer->volunteer_id)
            ->dateBetween($startDate, $endDate)
            ->orderBy('meeting_date', 'desc')
            ->get();

        $completedMeetings = $meetings->filter(fn (Meeting $m) => $m->isCompleted());
        $scheduledMeetings = $meetings->filter(fn (Meeting $m) => $m->isScheduled());
        $cancelledMeetings = $meetings->filter(fn (Meeting $m) => $m->isCancelled());

        $credentials = $volunteer->credentials()->get();
        $validCredentials = $credentials->filter(fn (VolunteerCredential $c) => $c->isValid());
        $expiringCredentials = $credentials->filter(fn (VolunteerCredential $c) => $c->isExpiringSoon());
        $expiredCredentials = $credentials->filter(fn (VolunteerCredential $c) => $c->isExpired());

        return [
            'volunteer' => [
                'volunteer_id' => $volunteer->volunteer_id,
                'name' => $volunteer->getFullNameAttribute(),
                'email' => $volunteer->email,
                'years_clean' => $volunteer->years_clean,
            ],
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'meeting_summary' => [
                'total_assigned' => $meetings->count(),
                'completed' => $completedMeetings->count(),
                'scheduled' => $scheduledMeetings->count(),
                'cancelled' => $cancelledMeetings->count(),
                'completion_rate' => $meetings->count() > 0
                    ? round(($completedMeetings->count() / $meetings->count()) * 100, 2)
                    : 0,
            ],
            'credential_summary' => [
                'total_credentials' => $credentials->count(),
                'valid_credentials' => $validCredentials->count(),
                'expiring_soon' => $expiringCredentials->count(),
                'expired' => $expiredCredentials->count(),
            ],
            'credentials' => $credentials->map(function (VolunteerCredential $c) {
                return [
                    'credential_id' => $c->credential_id,
                    'facility_name' => $c->facility->facility_name,
                    'credential_type' => $c->credential_type,
                    'status' => $c->status,
                    'expiration_date' => $c->expiration_date?->toDateString(),
                    'is_valid' => $c->isValid(),
                ];
            })->toArray(),
            'recent_meetings' => $meetings->take(10)->map(function (Meeting $m) {
                return [
                    'meeting_id' => $m->meeting_id,
                    'facility_name' => $m->facility->facility_name,
                    'date' => $m->meeting_date->toDateString(),
                    'time' => $m->meeting_time,
                    'status' => $m->status,
                    'confirmed' => $m->hasConfirmedVolunteer(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Generate capacity report by facility.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getCapacityReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate ??= now()->startOfMonth();
        $endDate ??= now()->endOfMonth();

        $facilities = Facility::active()->get();

        $capacityData = $facilities->map(function (Facility $facility) use ($startDate, $endDate) {
            $meetings = Meeting::query()
                ->where('facility_id', $facility->facility_id)
                ->dateBetween($startDate, $endDate)
                ->get();

            $totalMeetings = $meetings->count();
            $assignedMeetings = $meetings->filter(fn (Meeting $m) => $m->assigned_volunteer_id !== null)->count();
            $confirmedMeetings = $meetings->filter(fn (Meeting $m) => $m->hasConfirmedVolunteer())->count();

            return [
                'facility_id' => $facility->facility_id,
                'facility_name' => $facility->facility_name,
                'city' => $facility->city,
                'state' => $facility->state,
                'total_meetings' => $totalMeetings,
                'assigned_meetings' => $assignedMeetings,
                'unassigned_meetings' => $totalMeetings - $assignedMeetings,
                'confirmed_meetings' => $confirmedMeetings,
                'coverage_percentage' => $totalMeetings > 0 ? round(($assignedMeetings / $totalMeetings) * 100, 2) : 0,
                'confirmation_rate' => $assignedMeetings > 0 ? round(($confirmedMeetings / $assignedMeetings) * 100, 2) : 0,
            ];
        });

        // Calculate totals
        $totals = [
            'total_meetings' => $capacityData->sum('total_meetings'),
            'assigned_meetings' => $capacityData->sum('assigned_meetings'),
            'unassigned_meetings' => $capacityData->sum('unassigned_meetings'),
            'confirmed_meetings' => $capacityData->sum('confirmed_meetings'),
        ];

        $totals['coverage_percentage'] = $totals['total_meetings'] > 0
            ? round(($totals['assigned_meetings'] / $totals['total_meetings']) * 100, 2)
            : 0;

        $totals['confirmation_rate'] = $totals['assigned_meetings'] > 0
            ? round(($totals['confirmed_meetings'] / $totals['assigned_meetings']) * 100, 2)
            : 0;

        return [
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'totals' => $totals,
            'by_facility' => $capacityData->sortByDesc('coverage_percentage')->values()->toArray(),
        ];
    }

    /**
     * Format credential alert for display.
     *
     * @param VolunteerCredential $credential
     * @return array
     */
    private function formatCredentialAlert(VolunteerCredential $credential): array
    {
        return [
            'credential_id' => $credential->credential_id,
            'volunteer_name' => $credential->volunteer->getFullNameAttribute(),
            'volunteer_id' => $credential->volunteer->volunteer_id,
            'facility_name' => $credential->facility->facility_name,
            'facility_id' => $credential->facility->facility_id,
            'credential_type' => $credential->credential_type,
            'status' => $credential->status,
            'expiration_date' => $credential->expiration_date?->toDateString(),
            'days_until_expiration' => $credential->daysUntilExpiration(),
            'is_expired' => $credential->isExpired(),
        ];
    }
}
