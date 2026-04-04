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
        gap: 1rem;
        align-items: flex-end;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
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
    }

    .btn-action:hover {
        background-color: #003366;
    }

    /* Coverage Report Table */
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

    .coverage-excellent {
        background-color: #d4edda;
        color: #155724;
    }

    .coverage-good {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .coverage-fair {
        background-color: #fff3cd;
        color: #856404;
    }

    .coverage-poor {
        background-color: #f8d7da;
        color: #721c24;
    }

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

    .coverage-bar.excellent {
        background-color: #28a745;
    }

    .coverage-bar.good {
        background-color: #0099cc;
    }

    .coverage-bar.fair {
        background-color: #ffc107;
    }

    .coverage-bar.poor {
        background-color: #dc3545;
    }

    /* Alerts Section -->
    .alerts-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        border-left: 4px solid #ffc107;
    }

    .alerts-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .alert-item {
        padding: 1rem;
        background-color: #fff3cd;
        border-left: 3px solid #ffc107;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
    }

    .alert-item:last-child {
        margin-bottom: 0;
    }

    .alert-item-title {
        font-weight: 600;
        color: #856404;
        margin-bottom: 0.25rem;
    }

    .alert-item-text {
        color: #856404;
        font-size: 0.9rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        color: #999;
    }

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

    @media (max-width: 1024px) {
        .filter-section {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .form-control,
        .form-select {
            width: 100%;
        }

        .action-buttons {
            margin-left: 0;
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .report-title {
            font-size: 1.25rem;
        }

        .report-summary {
            grid-template-columns: 1fr;
        }

        .report-table th,
        .report-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .coverage-badge {
            display: block;
            margin-top: 0.5rem;
        }
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
                    Volunteer coverage analysis by facility.
                </p>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <div class="filter-group">
                    <label for="date-from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date-from" value="2026-03-01">
                </div>

                <div class="filter-group">
                    <label for="date-to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date-to" value="2026-04-30">
                </div>

                <div class="filter-group">
                    <label for="facility" class="form-label">Facility</label>
                    <select class="form-select" id="facility">
                        <option value="">All Facilities</option>
                        <option value="harmony">Harmony House</option>
                        <option value="newpath">New Path Center</option>
                        <option value="sunrise">Sunrise Community</option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button class="btn-action" onclick="generateReport()">
                        <i class="fas fa-sync"></i> Generate Report
                    </button>
                    <button class="btn-action" onclick="exportPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn-action" onclick="exportCSV()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="report-summary">
                <div class="summary-card">
                    <div class="summary-label">Total Meetings</div>
                    <div class="summary-value">34</div>
                    <div class="summary-subtitle">March - April 2026</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Filled Meetings</div>
                    <div class="summary-value">29</div>
                    <div class="summary-subtitle">85.3% coverage</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Unfilled Meetings</div>
                    <div class="summary-value">5</div>
                    <div class="summary-subtitle">14.7% gap</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Average Coverage</div>
                    <div class="summary-value">86%</div>
                    <div class="summary-subtitle">All facilities</div>
                </div>
            </div>

            <!-- Coverage Table -->
            <div class="report-table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Total Meetings</th>
                            <th>Filled</th>
                            <th>Unfilled</th>
                            <th>Coverage %</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="facility-name">Harmony House Treatment Center</td>
                            <td>8</td>
                            <td>8</td>
                            <td>0</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar excellent" style="width: 100%;">100%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-excellent">
                                    <i class="fas fa-check-circle"></i> Excellent
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">New Path Recovery Center</td>
                            <td>7</td>
                            <td>6</td>
                            <td>1</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar good" style="width: 85.7%;">85.7%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-good">
                                    <i class="fas fa-check"></i> Good
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">Sunrise Community Center</td>
                            <td>6</td>
                            <td>5</td>
                            <td>1</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar good" style="width: 83.3%;">83.3%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-good">
                                    <i class="fas fa-check"></i> Good
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">Recovery Plus</td>
                            <td>5</td>
                            <td>4</td>
                            <td>1</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar fair" style="width: 80%;">80%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-fair">
                                    <i class="fas fa-exclamation-circle"></i> Fair
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">Hope Treatment Center</td>
                            <td>4</td>
                            <td>4</td>
                            <td>0</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar excellent" style="width: 100%;">100%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-excellent">
                                    <i class="fas fa-check-circle"></i> Excellent
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">Healing Community</td>
                            <td>3</td>
                            <td>2</td>
                            <td>1</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar fair" style="width: 66.7%;">66.7%</div>
                                </div>
                            </td>
                            <td>
                                <span class="coverage-badge coverage-fair">
                                    <i class="fas fa-exclamation-circle"></i> Fair
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="facility-name">Second Chance Center</td>
                            <td>1</td>
                            <td>0</td>
                            <td>1</td>
                            <td>
                                <div class="coverage-bar-container">
                                    <div class="coverage-bar poor" style="width: 0%;"></div>
                                </div>
                                0%
                            </td>
                            <td>
                                <span class="coverage-badge coverage-poor">
                                    <i class="fas fa-times-circle"></i> Poor
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Credential Expiration Alerts -->
            <div class="alerts-section">
                <h2 class="alerts-title">
                    <i class="fas fa-exclamation-triangle"></i> Credential Expiration Alerts
                </h2>

                <div class="alert-item">
                    <div class="alert-item-title">
                        <i class="fas fa-calendar"></i> Expiring Within 30 Days
                    </div>
                    <div class="alert-item-text">
                        2 volunteer credentials expire within the next 30 days. Review and renew them to maintain facility compliance.
                    </div>
                    <ul style="margin-top: 0.75rem; margin-left: 1.5rem; color: #856404; font-size: 0.9rem;">
                        <li><strong>Morgan Davis</strong> - Background Check - Expires April 8, 2026</li>
                        <li><strong>Jordan Taylor</strong> - Reference Check - Expires April 15, 2026</li>
                    </ul>
                </div>

                <div class="alert-item" style="background-color: #f8d7da; border-left-color: #dc3545; color: #721c24;">
                    <div class="alert-item-title" style="color: #721c24;">
                        <i class="fas fa-exclamation-circle"></i> Expired Credentials
                    </div>
                    <div class="alert-item-text" style="color: #721c24;">
                        1 volunteer has an expired credential. This volunteer should not be assigned to meetings requiring this credential.
                    </div>
                    <ul style="margin-top: 0.75rem; margin-left: 1.5rem; color: #721c24; font-size: 0.9rem;">
                        <li><strong>Sam Anderson</strong> - Background Check - Expired January 10, 2025</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function generateReport() {
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;
        const facility = document.getElementById('facility').value;

        alert('Generating report for ' + dateFrom + ' to ' + dateTo + '...');
    }

    function exportPDF() {
        alert('Exporting report as PDF...');
        // In a real application, this would trigger a PDF download
    }

    function exportCSV() {
        alert('Exporting report as CSV...');
        // In a real application, this would trigger a CSV download
    }
</script>
@endsection
