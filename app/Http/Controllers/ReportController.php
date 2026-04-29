<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Facility;
use App\Models\VolunteerCredential;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Coverage report with date range filter.
     *
     * Meetings in ChronoSync are recurring patterns, not dated instances.
     * The dated unit is MeetingAssignment (assignment_date). All date-range
     * queries here run against assignment_date, not a non-existent date_scheduled.
     */
    public function coverageSummary(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $dateFrom = Carbon::parse($request->input('date_from', now()->startOfMonth()));
        $dateTo   = Carbon::parse($request->input('date_to', now()->endOfMonth()));

        $assignments = MeetingAssignment::whereBetween('assignment_date', [$dateFrom, $dateTo])
            ->with('meeting.facility')
            ->get();

        $totalMeetings      = $assignments->count();
        $assignedMeetings   = $assignments->whereIn('status', ['confirmed', 'completed'])->count();
        $unassignedMeetings = $assignments->whereIn('status', ['pending_confirmation', 'declined'])->count();
        $completedMeetings  = $assignments->where('status', 'completed')->count();
        $cancelledMeetings  = $assignments->where('status', 'cancelled')->count();

        // Coverage by facility
        $coverageByFacility = Facility::withoutTrashed()
            ->with(['meetings.assignments' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('assignment_date', [$dateFrom, $dateTo]);
            }])
            ->get()
            ->map(function ($facility) {
                $facilityAssignments = $facility->meetings->flatMap->assignments;
                $total    = $facilityAssignments->count();
                $covered  = $facilityAssignments->whereIn('status', ['confirmed', 'completed'])->count();

                return [
                    'facility_name'       => $facility->facility_name,
                    'total_meetings'      => $total,
                    'assigned'            => $covered,
                    'unassigned'          => $total - $covered,
                    'coverage_percentage' => $total > 0 ? round(($covered / $total) * 100, 1) : 0,
                ];
            });

        return view('reports.coverage', compact(
            'totalMeetings',
            'assignedMeetings',
            'unassignedMeetings',
            'completedMeetings',
            'cancelledMeetings',
            'coverageByFacility',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Facility detail report.
     *
     * Shows individual assignment occurrences for a facility in a date range.
     */
    public function facilitySchedule(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $facility = Facility::findOrFail($request->facility_id);

        $dateFrom = Carbon::parse($request->input('date_from', now()->startOfMonth()));
        $dateTo   = Carbon::parse($request->input('date_to', now()->endOfMonth()));

        $meetings = MeetingAssignment::whereHas('meeting', function ($q) use ($facility) {
                $q->where('facility_id', $facility->facility_id);
            })
            ->whereBetween('assignment_date', [$dateFrom, $dateTo])
            ->with('meeting', 'volunteer')
            ->orderBy('assignment_date')
            ->get()
            ->map(function ($assignment) {
                return [
                    'date'               => $assignment->assignment_date->format('Y-m-d'),
                    'time'               => substr($assignment->meeting->meeting_time, 0, 5),
                    'type'               => $assignment->meeting->format,
                    'assigned_volunteer' => $assignment->volunteer?->full_name ?? 'Unassigned',
                    'status'             => $assignment->status,
                    'notes'              => $assignment->meeting->notes,
                ];
            });

        return view('reports.facility-detail', compact('facility', 'meetings', 'dateFrom', 'dateTo'));
    }

    /**
     * Expiring credentials report.
     */
    public function credentialExpiration(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $daysUntilExpiry = $request->input('days_until', 30);

        $expiringCredentials = VolunteerCredential::where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($daysUntilExpiry))
            ->whereDate('expiration_date', '>', now())
            ->with('volunteer', 'credentialType')
            ->orderBy('expiration_date')
            ->get()
            ->map(function ($credential) {
                $daysLeft = now()->diffInDays($credential->expiration_date);
                return [
                    'volunteer_name'    => $credential->volunteer->full_name,
                    'email'             => $credential->volunteer->email,
                    'credential_type'   => $credential->credentialType->name,
                    'expiration_date'   => $credential->expiration_date->format('Y-m-d'),
                    'days_until_expiry' => $daysLeft,
                    'urgency'           => $daysLeft <= 7 ? 'critical' : ($daysLeft <= 14 ? 'high' : 'medium'),
                ];
            });

        $expiredCredentials = VolunteerCredential::where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now())
            ->count();

        return view('placeholder.coming-soon', compact(
            'expiringCredentials',
            'expiredCredentials',
            'daysUntilExpiry'
        ));
    }

    /**
     * Unfilled meetings sorted by urgency.
     *
     * Shows active recurring meeting slots with no upcoming assignments.
     */
    public function unfilledMeetings(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meetings = Meeting::where('status', 'active')
            ->whereDoesntHave('assignments', function ($q) {
                $q->where('assignment_date', '>=', now()->toDateString());
            })
            ->with('facility')
            ->get()
            ->map(function ($meeting) {
                $pattern = (Meeting::WEEK_LABELS[$meeting->week_of_month] ?? $meeting->week_of_month)
                    . ' ' . (Meeting::DAY_NAMES[$meeting->day_of_week] ?? $meeting->day_of_week)
                    . ' at ' . substr($meeting->meeting_time, 0, 5);

                return [
                    'meeting_id'    => $meeting->meeting_id,
                    'facility_name' => $meeting->facility->facility_name,
                    'pattern'       => $pattern,
                    'format'        => $meeting->format,
                ];
            })
            ->all();

        return view('placeholder.coming-soon', compact('meetings'));
    }

    /**
     * Export coverage report as CSV.
     */
    public function exportCsv(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $reportType = $request->input('report_type', 'coverage');
        $dateFrom   = Carbon::parse($request->input('date_from', now()->startOfMonth()));
        $dateTo     = Carbon::parse($request->input('date_to', now()->endOfMonth()));

        $filename = "chronosync-{$reportType}-" . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        if ($reportType === 'meetings') {
            $data = $this->getMeetingsData($dateFrom, $dateTo);
        } elseif ($reportType === 'credentials') {
            $data = $this->getCredentialsData();
        } else {
            $data = $this->getCoverageData($dateFrom, $dateTo);
        }

        $csv = $this->arrayToCsv($data);

        return response($csv, 200, $headers);
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $reportType = $request->input('report_type', 'coverage');
        $dateFrom   = Carbon::parse($request->input('date_from', now()->startOfMonth()));
        $dateTo     = Carbon::parse($request->input('date_to', now()->endOfMonth()));

        $data = [];
        $view = null;

        if ($reportType === 'coverage') {
            $assignments = MeetingAssignment::whereBetween('assignment_date', [$dateFrom, $dateTo])
                ->with('meeting.facility')
                ->get();

            $data['totalMeetings']      = $assignments->count();
            $data['assignedMeetings']   = $assignments->whereIn('status', ['confirmed', 'completed'])->count();
            $data['unassignedMeetings'] = $data['totalMeetings'] - $data['assignedMeetings'];
            $data['dateFrom']           = $dateFrom;
            $data['dateTo']             = $dateTo;
            $view = 'reports.coverage-summary-pdf';
        } elseif ($reportType === 'credentials') {
            $data['credentials'] = VolunteerCredential::where('status', 'approved')
                ->whereNotNull('expiration_date')
                ->whereDate('expiration_date', '<=', now()->addDays(30))
                ->with('volunteer', 'credentialType')
                ->orderBy('expiration_date')
                ->get();
            $view = 'reports.credentials-pdf';
        }

        if (!$view) {
            return back()->with('error', 'Invalid report type.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data);
        return $pdf->download("chronosync-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://memory', 'r+');
        fputcsv($output, array_keys($data[0]));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function getMeetingsData(Carbon $dateFrom, Carbon $dateTo): array
    {
        return MeetingAssignment::whereBetween('assignment_date', [$dateFrom, $dateTo])
            ->with('meeting.facility', 'volunteer')
            ->get()
            ->map(function ($assignment) {
                return [
                    'Facility'  => $assignment->meeting->facility->facility_name,
                    'Date'      => $assignment->assignment_date->format('Y-m-d'),
                    'Time'      => substr($assignment->meeting->meeting_time, 0, 5),
                    'Format'    => $assignment->meeting->format,
                    'Status'    => $assignment->status,
                    'Volunteer' => $assignment->volunteer?->full_name ?? 'Unassigned',
                ];
            })
            ->toArray();
    }

    private function getCredentialsData(): array
    {
        return VolunteerCredential::where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->with('volunteer', 'credentialType')
            ->get()
            ->map(function ($credential) {
                return [
                    'Volunteer' => $credential->volunteer->full_name,
                    'Type'      => $credential->credentialType->name,
                    'Expires'   => $credential->expiration_date->format('Y-m-d'),
                    'Days Until' => now()->diffInDays($credential->expiration_date),
                ];
            })
            ->toArray();
    }

    private function getCoverageData(Carbon $dateFrom, Carbon $dateTo): array
    {
        return Facility::withoutTrashed()
            ->with(['meetings.assignments' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('assignment_date', [$dateFrom, $dateTo]);
            }])
            ->get()
            ->map(function ($facility) {
                $assignments = $facility->meetings->flatMap->assignments;
                $total   = $assignments->count();
                $covered = $assignments->whereIn('status', ['confirmed', 'completed'])->count();

                return [
                    'Facility'        => $facility->facility_name,
                    'Total Meetings'  => $total,
                    'Covered'         => $covered,
                    'Uncovered'       => $total - $covered,
                    'Coverage %'      => $total > 0 ? round(($covered / $total) * 100, 1) : 0,
                ];
            })
            ->toArray();
    }
}
