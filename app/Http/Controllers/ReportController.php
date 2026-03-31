<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Facility;
use App\Models\Credential;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Coverage report with date range filter.
     */
    public function coverageSummary(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        $query = Meeting::whereBetween('date_scheduled', [$dateFrom, $dateTo])
            ->with('facility', 'assignments');

        $totalMeetings = $query->count();
        $assignedMeetings = $query->clone()->has('assignments')->count();
        $unassignedMeetings = $totalMeetings - $assignedMeetings;
        $completedMeetings = $query->clone()->where('status', 'completed')->count();
        $cancelledMeetings = $query->clone()->where('status', 'cancelled')->count();

        // Coverage by facility
        $coverageByFacility = Facility::withoutTrashed()
            ->with([
                'meetings' => function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date_scheduled', [$dateFrom, $dateTo]);
                },
                'meetings.assignments',
            ])
            ->get()
            ->map(function ($facility) {
                $total = $facility->meetings->count();
                $assigned = $facility->meetings
                    ->filter(fn($m) => $m->assignments->count() > 0)
                    ->count();

                return [
                    'facility_name' => $facility->name,
                    'total_meetings' => $total,
                    'assigned' => $assigned,
                    'unassigned' => $total - $assigned,
                    'coverage_percentage' => $total > 0 ? round(($assigned / $total) * 100, 1) : 0,
                ];
            });

        return view('reports.coverage-summary', compact(
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
     */
    public function facilitySchedule(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $facility = Facility::findOrFail($request->facility_id);

        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        $meetings = $facility->meetings()
            ->whereBetween('date_scheduled', [$dateFrom, $dateTo])
            ->with('assignments.volunteer.user')
            ->orderBy('date_scheduled')
            ->get()
            ->map(function ($meeting) {
                return [
                    'date' => $meeting->date_scheduled->format('Y-m-d H:i'),
                    'type' => $meeting->meeting_type,
                    'assigned_volunteer' => $meeting->assignments->first()?->volunteer->user->full_name ?? 'Unassigned',
                    'status' => $meeting->status,
                    'notes' => $meeting->description,
                ];
            });

        return view('reports.facility-schedule', compact('facility', 'meetings', 'dateFrom', 'dateTo'));
    }

    /**
     * Expiring credentials report.
     */
    public function credentialExpiration(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $daysUntilExpiry = $request->input('days_until', 30);

        $expiringCredentials = Credential::where('status', 'active')
            ->whereDate('expiration_date', '<=', now()->addDays($daysUntilExpiry))
            ->whereDate('expiration_date', '>', now())
            ->with('volunteer.user')
            ->orderBy('expiration_date')
            ->get()
            ->map(function ($credential) {
                $daysLeft = now()->diffInDays($credential->expiration_date);
                return [
                    'volunteer_name' => $credential->volunteer->user->full_name,
                    'email' => $credential->volunteer->user->email,
                    'credential_type' => $credential->credential_type,
                    'expiration_date' => $credential->expiration_date->format('Y-m-d'),
                    'days_until_expiry' => $daysLeft,
                    'urgency' => $daysLeft <= 7 ? 'critical' : ($daysLeft <= 14 ? 'high' : 'medium'),
                ];
            });

        $expiredCredentials = Credential::where('status', 'active')
            ->whereDate('expiration_date', '<=', now())
            ->with('volunteer.user')
            ->get()
            ->count();

        return view('reports.credential-expiration', compact(
            'expiringCredentials',
            'expiredCredentials',
            'daysUntilExpiry'
        ));
    }

    /**
     * Unfilled meetings sorted by urgency.
     */
    public function unfilledMeetings(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meetings = Meeting::where('date_scheduled', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->doesntHave('assignments')
            ->with('facility')
            ->orderBy('date_scheduled')
            ->get()
            ->map(function ($meeting) {
                $hoursUntilMeeting = now()->diffInHours($meeting->date_scheduled);
                return [
                    'meeting_id' => $meeting->id,
                    'facility_name' => $meeting->facility->name,
                    'scheduled_for' => $meeting->date_scheduled->format('Y-m-d H:i'),
                    'hours_until' => $hoursUntilMeeting,
                    'urgency' => $hoursUntilMeeting <= 24 ? 'critical' : ($hoursUntilMeeting <= 72 ? 'high' : 'medium'),
                    'meeting_type' => $meeting->meeting_type,
                ];
            })
            ->sortBy(fn($m) => $m['hours_until'])
            ->values()
            ->all();

        return view('reports.unfilled-meetings', compact('meetings'));
    }

    /**
     * Export coverage report as CSV.
     */
    public function exportCsv(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $reportType = $request->input('report_type', 'coverage');
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        $filename = "chronosync-{$reportType}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
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
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        $data = [];
        $view = null;

        if ($reportType === 'coverage') {
            $query = Meeting::whereBetween('date_scheduled', [$dateFrom, $dateTo])->with('facility', 'assignments');
            $data['totalMeetings'] = $query->count();
            $data['assignedMeetings'] = $query->clone()->has('assignments')->count();
            $data['unassignedMeetings'] = $data['totalMeetings'] - $data['assignedMeetings'];
            $data['dateFrom'] = $dateFrom;
            $data['dateTo'] = $dateTo;
            $view = 'reports.coverage-summary-pdf';
        } elseif ($reportType === 'credentials') {
            $data['credentials'] = Credential::where('status', 'active')
                ->whereDate('expiration_date', '<=', now()->addDays(30))
                ->with('volunteer.user')
                ->orderBy('expiration_date')
                ->get();
            $view = 'reports.credentials-pdf';
        }

        if (!$view) {
            return back()->with('error', 'Invalid report type.');
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->download("chronosync-{$reportType}-" . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Convert array to CSV format.
     */
    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://memory', 'r+');

        // Write headers
        fputcsv($output, array_keys($data[0]));

        // Write rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Get meetings data for export.
     */
    private function getMeetingsData(Carbon $dateFrom, Carbon $dateTo): array
    {
        return Meeting::whereBetween('date_scheduled', [$dateFrom, $dateTo])
            ->with('facility', 'assignments.volunteer.user')
            ->get()
            ->map(function ($meeting) {
                $assigned = $meeting->assignments->first();
                return [
                    'Facility' => $meeting->facility->name,
                    'Date' => $meeting->date_scheduled->format('Y-m-d H:i'),
                    'Type' => $meeting->meeting_type,
                    'Status' => $meeting->status,
                    'Volunteer' => $assigned?->volunteer->user->full_name ?? 'Unassigned',
                ];
            })
            ->toArray();
    }

    /**
     * Get credentials data for export.
     */
    private function getCredentialsData(): array
    {
        return Credential::where('status', 'active')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->with('volunteer.user')
            ->get()
            ->map(function ($credential) {
                return [
                    'Volunteer' => $credential->volunteer->user->full_name,
                    'Type' => $credential->credential_type,
                    'Expires' => $credential->expiration_date->format('Y-m-d'),
                    'Days Until' => now()->diffInDays($credential->expiration_date),
                ];
            })
            ->toArray();
    }

    /**
     * Get coverage data for export.
     */
    private function getCoverageData(Carbon $dateFrom, Carbon $dateTo): array
    {
        return Facility::withoutTrashed()
            ->with([
                'meetings' => function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date_scheduled', [$dateFrom, $dateTo]);
                },
                'meetings.assignments',
            ])
            ->get()
            ->map(function ($facility) {
                $total = $facility->meetings->count();
                $assigned = $facility->meetings
                    ->filter(fn($m) => $m->assignments->count() > 0)
                    ->count();

                return [
                    'Facility' => $facility->name,
                    'Total Meetings' => $total,
                    'Assigned' => $assigned,
                    'Unassigned' => $total - $assigned,
                    'Coverage %' => $total > 0 ? round(($assigned / $total) * 100, 1) : 0,
                ];
            })
            ->toArray();
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
