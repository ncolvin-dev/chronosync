@extends('layouts.app')

@section('title', 'Coverage Report - ChronoSync')

@section('extra-styles')
<style>
    .report-header {
        margin-bottom: 2rem;
    }

    .report-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .filter-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        font-size: 0.875rem;
    }

    .form-control,
    .form-select {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        min-width: 150px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        outline: none;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-left: auto;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-action:hover {
        background-color: #003366;
        color: white;
    }

    .report-table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow-x: auto;
        margin-bottom: 2rem;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .report-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .report-table tr:hover {
        background-color: #f8f9fa;
    }

    .facility-name {
        font-weight: 600;
        color: #003366;
    }

    .coverage-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .coverage-excellent { background-color: #d4edda; color: #155724; }
    .coverage-good      { background-color: #d1ecf1; color: #0c5460; }
    .coverage-fair      { background-color: #fff3cd; color: #856404; }
    .coverage-poor      { background-color: #f8d7da; color: #721c24; }

    .coverage-bar-container {
        width: 100px;
        height: 24px;
        background-color: #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        display: inline-block;
    }

    .coverage-bar {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .coverage-bar.excellent { background-color: #28a745; }
    .coverage-bar.good      { background-color: #0099cc; }
    .coverage-bar.fair      { background-color: #ffc107; }
    .coverage-bar.poor      { background-color: #dc3545; }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        text-align: center;
    }

    .summary-label {
        color: #999;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        color: #003366;
    }

    .summary-subtitle {
        color: #666;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #999;
    }

    @media (max-width: 1024px) {
        .filter-section   { flex-direction: column; align-items: stretch; }
        .filter-group     { width: 100%; }
        .form-control,
        .form-select      { width: 100%; }
        .action-buttons   { margin-left: 0; flex-direction: column; }
        .btn-action       { width: 100%; justify-content: center; }
    }

    @media (max-width: 768px) {
        .report-title { font-size: 1.25rem; }
        .report-summary { grid-template-columns: 1fr; }
        .report-table th,
        .report-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="report-header">
                <h1 class="report-title">
                    <i class="fas fa-chart-bar"></i> Coverage Report
                </h1>
                <p style="color: #666; font-size: 0.9rem;">
                    Volunteer assignment coverage analysis by facility.
                </p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('reports.coverage-summary') }}" id="filter-form">
                <div class="filter-section">
                    <div class="filter-group">
                        <label for="date-from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date-from" name="date_from"
                               value="{{ $dateFrom->format('Y-m-d') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date-to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date-to" name="date_to"
                               value="{{ $dateTo->format('Y-m-d') }}">
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="btn-action">
                            <i class="fas fa-sync"></i> Generate Report
                        </button>
                        <a href="{{ route('reports.export-pdf', ['report_type' => 'coverage', 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}"
                           class="btn-action">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('reports.export-csv', ['report_type' => 'coverage', 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}"
                           class="btn-action">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </form>

            {{-- Summary Cards --}}
            <div class="report-summary">
                <div class="summary-card">
                    <div class="summary-label">Total Assignments</div>
                    <div class="summary-value">{{ $totalMeetings }}</div>
                    <div class="summary-subtitle">
                        {{ $dateFrom->format('M j') }} – {{ $dateTo->format('M j, Y') }}
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Covered</div>
                    <div class="summary-value">{{ $assignedMeetings }}</div>
                    <div class="summary-subtitle">
                        {{ $totalMeetings > 0 ? round(($assignedMeetings / $totalMeetings) * 100, 1) : 0 }}% confirmed or completed
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Pending / Declined</div>
                    <div class="summary-value">{{ $unassignedMeetings }}</div>
                    <div class="summary-subtitle">need attention</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Completed</div>
                    <div class="summary-value">{{ $completedMeetings }}</div>
                    <div class="summary-subtitle">meetings done</div>
                </div>
            </div>

            {{-- Coverage Table --}}
            <div class="report-table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Total Assignments</th>
                            <th>Covered</th>
                            <th>Uncovered</th>
                            <th>Coverage %</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coverageByFacility as $row)
                            @php
                                $pct = $row['coverage_percentage'];
                                if ($pct >= 90)      { $tier = 'excellent'; $label = 'Excellent'; }
                                elseif ($pct >= 75)  { $tier = 'good';      $label = 'Good'; }
                                elseif ($pct >= 50)  { $tier = 'fair';      $label = 'Fair'; }
                                else                 { $tier = 'poor';      $label = 'Poor'; }
                            @endphp
                            <tr>
                                <td class="facility-name">{{ $row['facility_name'] }}</td>
                                <td>{{ $row['total_meetings'] }}</td>
                                <td>{{ $row['assigned'] }}</td>
                                <td>{{ $row['unassigned'] }}</td>
                                <td>
                                    <div class="coverage-bar-container">
                                        <div class="coverage-bar {{ $tier }}" style="width: {{ $pct }}%;">
                                            {{ $pct }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="coverage-badge coverage-{{ $tier }}">
                                        {{ $label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    No assignment data for the selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Keep export links in sync with the date pickers without a full page reload
    document.getElementById('filter-form').addEventListener('change', function () {
        const from = document.getElementById('date-from').value;
        const to   = document.getElementById('date-to').value;

        document.querySelectorAll('a.btn-action').forEach(function (link) {
            const url = new URL(link.href);
            url.searchParams.set('date_from', from);
            url.searchParams.set('date_to', to);
            link.href = url.toString();
        });
    });
</script>
@endsection
