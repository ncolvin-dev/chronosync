@extends('layouts.app')

@section('title', 'Facility Detail Report - ChronoSync')

@section('extra-styles')
<style>
    .report-header {
        margin-bottom: 2rem;
    }

    .facility-info-card {
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .facility-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .facility-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .facility-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .facility-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .facility-detail-item {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .facility-detail-label {
        font-size: 0.75rem;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .facility-detail-value {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .print-button {
        padding: 0.75rem 1.5rem;
        background-color: white;
        color: #003366;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .print-button:hover {
        background-color: #f0f0f0;
    }

    /* Meetings Table */
    .meetings-section {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .section-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .meetings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .meetings-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .meetings-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .meetings-table tr:hover {
        background-color: #f8f9fa;
    }

    .meetings-table tr.unfilled {
        background-color: #ffe6e6;
    }

    .meetings-table tr.unfilled td {
        color: #dc3545;
        font-weight: 500;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-assigned {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-unfilled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-confirmed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .credential-badge {
        display: inline-block;
        padding: 0.3rem 0.6rem;
        background-color: #f0f0f0;
        color: #333;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        margin-right: 0.25rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f0f0f0;
    }

    .summary-item {
        text-align: center;
    }

    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #003366;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        font-size: 0.85rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        color: #999;
    }

    .no-volunteer {
        font-style: italic;
        color: #999;
    }

    /* Print Styles */
    @media print {
        body {
            background: white;
        }

        .print-button {
            display: none;
        }

        .facility-info-card {
            page-break-after: avoid;
            box-shadow: none;
            border: 2px solid #003366;
        }

        .meetings-section {
            page-break-after: avoid;
            box-shadow: none;
            border: 1px solid #e0e0e0;
        }

        .meetings-table tr {
            page-break-inside: avoid;
        }
    }

    @media (max-width: 768px) {
        .facility-header-top {
            flex-direction: column;
        }

        .facility-name {
            font-size: 1.5rem;
        }

        .facility-details-grid {
            grid-template-columns: 1fr;
        }

        .meetings-table th,
        .meetings-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .meetings-section {
            padding: 1rem;
        }

        .section-title {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Facility Info Card -->
            <div class="facility-info-card">
                <div class="facility-header-top">
                    <div>
                        <div class="facility-name">
                            <i class="fas fa-building"></i> Harmony House Treatment Center
                        </div>
                        <span class="facility-badge">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    </div>
                    <button class="print-button" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>

                <div class="facility-details-grid">
                    <div class="facility-detail-item">
                        <div class="facility-detail-label">Address</div>
                        <div class="facility-detail-value">123 Recovery Lane<br>Downtown, Metro City 12345</div>
                    </div>
                    <div class="facility-detail-item">
                        <div class="facility-detail-label">Contact</div>
                        <div class="facility-detail-value">Sarah Mitchell<br>(555) 123-4567</div>
                    </div>
                    <div class="facility-detail-item">
                        <div class="facility-detail-label">Clean Time Requirement</div>
                        <div class="facility-detail-value">2 years minimum</div>
                    </div>
                    <div class="facility-detail-item">
                        <div class="facility-detail-label">Report Period</div>
                        <div class="facility-detail-value">March 1 - April 30, 2026</div>
                    </div>
                </div>
            </div>

            <!-- Meetings Detail -->
            <div class="meetings-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i> Meeting Detail
                </h2>

                <div style="overflow-x: auto;">
                    <table class="meetings-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Volunteer</th>
                                <th>Clean Time</th>
                                <th>Credentials</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, Mar 31, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Alex Johnson</strong></td>
                                <td>4 years 6 months</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                    <span class="credential-badge">Reference ✓</span>
                                </td>
                                <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, Apr 7, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Morgan Davis</strong></td>
                                <td>3 years 2 months</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                </td>
                                <td><span class="status-badge status-assigned">Assigned</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, Apr 14, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Jordan Taylor</strong></td>
                                <td>2 years 8 months</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                    <span class="credential-badge">Reference ✓</span>
                                    <span class="credential-badge">Training ✓</span>
                                </td>
                                <td><span class="status-badge status-pending">Pending Response</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, Apr 21, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Casey Miller</strong></td>
                                <td>5 years 1 month</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                </td>
                                <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, Apr 28, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Riley Thompson</strong></td>
                                <td>2 years 4 months</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                    <span class="credential-badge">Reference ✓</span>
                                </td>
                                <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, May 5, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Sam Anderson</strong></td>
                                <td>1 year 11 months</td>
                                <td>
                                    <span class="credential-badge">Background ✗</span>
                                </td>
                                <td><span class="status-badge status-assigned">Assigned</span></td>
                            </tr>

                            <!-- Filled Meeting -->
                            <tr>
                                <td>Tuesday, May 12, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><strong>Taylor Jackson</strong></td>
                                <td>3 years 7 months</td>
                                <td>
                                    <span class="credential-badge">Background ✓</span>
                                    <span class="credential-badge">Reference ✓</span>
                                </td>
                                <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            </tr>

                            <!-- Unfilled Meeting -->
                            <tr class="unfilled">
                                <td>Tuesday, May 19, 2026</td>
                                <td>6:30 PM - 8:00 PM</td>
                                <td><span class="no-volunteer"><em>Unfilled</em></span></td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="status-badge status-unfilled">Unfilled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-value">8</div>
                        <div class="summary-label">Total Meetings</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">7</div>
                        <div class="summary-label">Filled</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">1</div>
                        <div class="summary-label">Unfilled</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">87.5%</div>
                        <div class="summary-label">Coverage</div>
                    </div>
                </div>
            </div>

            <!-- Assigned Volunteers -->
            <div class="meetings-section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i> Assigned Volunteers This Period
                </h2>

                <div style="overflow-x: auto;">
                    <table class="meetings-table">
                        <thead>
                            <tr>
                                <th>Volunteer</th>
                                <th>Assignments</th>
                                <th>Clean Time</th>
                                <th>Gender</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Alex Johnson</strong></td>
                                <td>1</td>
                                <td>4 years 6 months</td>
                                <td>Male</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Morgan Davis</strong></td>
                                <td>1</td>
                                <td>3 years 2 months</td>
                                <td>Female</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Jordan Taylor</strong></td>
                                <td>1</td>
                                <td>2 years 8 months</td>
                                <td>Non-Binary</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Casey Miller</strong></td>
                                <td>1</td>
                                <td>5 years 1 month</td>
                                <td>Female</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Riley Thompson</strong></td>
                                <td>1</td>
                                <td>2 years 4 months</td>
                                <td>Male</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Sam Anderson</strong></td>
                                <td>1</td>
                                <td>1 year 11 months</td>
                                <td>Male</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Taylor Jackson</strong></td>
                                <td>1</td>
                                <td>3 years 7 months</td>
                                <td>Female</td>
                                <td><span class="status-badge status-confirmed">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
