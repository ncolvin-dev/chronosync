@extends('layouts.app')

@section('title', 'Credentials - ChronoSync')

@section('extra-styles')
<style>
    .credentials-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .credentials-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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
    }

    .btn-action:hover {
        background-color: #003366;
    }

    .alert-banner {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .alert-icon {
        color: #ffc107;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 600;
        color: #856404;
        margin-bottom: 0.25rem;
    }

    .alert-text {
        color: #856404;
        font-size: 0.9rem;
    }

    .alert-list {
        margin-top: 0.75rem;
        padding-left: 1.5rem;
    }

    .alert-list li {
        margin-bottom: 0.25rem;
    }

    .credentials-table {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
        white-space: nowrap;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .table tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-expired {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-small {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .btn-approve {
        background-color: #28a745;
        color: white;
    }

    .btn-approve:hover {
        background-color: #218838;
    }

    .btn-deny {
        background-color: #dc3545;
        color: white;
    }

    .btn-deny:hover {
        background-color: #c82333;
    }

    .btn-renew {
        background-color: #0099cc;
        color: white;
    }

    .btn-renew:hover {
        background-color: #003366;
    }

    .expiration-soon {
        background-color: #fffaf0;
        font-weight: 600;
        color: #ff6b35;
    }

    .expiration-soon td {
        color: #ff6b35;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        color: #666;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state-text {
        color: #999;
        font-size: 0.875rem;
    }

    @media (max-width: 1024px) {
        .credentials-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-small {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .credentials-title {
            font-size: 1.25rem;
        }

        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="credentials-header">
                <h1 class="credentials-title">
                    <i class="fas fa-certificate"></i> Credential Management
                </h1>
                <div class="header-actions">
                    <button class="btn-action" onclick="exportCSV()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    <button class="btn-action" onclick="exportPDF()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Expiration Alert -->
            <div class="alert-banner">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Credentials Expiring Soon</div>
                    <div class="alert-text">
                        The following credentials will expire within 30 days:
                    </div>
                    <ul class="alert-list">
                        <li><strong>Morgan Davis</strong> - Background Check - Expires April 8, 2026</li>
                        <li><strong>Jordan Taylor</strong> - Reference Check - Expires April 15, 2026</li>
                    </ul>
                </div>
            </div>

            <!-- Credentials Table -->
            <div class="credentials-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Volunteer</th>
                            <th>Facility</th>
                            <th>Credential Type</th>
                            <th>Approval Date</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Approved Credential -->
                        <tr>
                            <td>
                                <strong>Alex Johnson</strong><br>
                                <small style="color: #999;">(555) 123-4567</small>
                            </td>
                            <td>Harmony House</td>
                            <td>Background Check</td>
                            <td>Mar 15, 2024</td>
                            <td>Mar 15, 2026</td>
                            <td><span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-renew" onclick="renewCredential()">Renew</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Pending Credential -->
                        <tr>
                            <td>
                                <strong>Morgan Davis</strong><br>
                                <small style="color: #999;">(555) 234-5678</small>
                            </td>
                            <td>New Path Center</td>
                            <td>Reference Check</td>
                            <td>Pending</td>
                            <td>Pending</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-approve" onclick="approveCredential()">Approve</button>
                                    <button class="btn-small btn-deny" onclick="denyCredential()">Deny</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expiring Soon Credential -->
                        <tr class="expiration-soon">
                            <td>
                                <strong>Jordan Taylor</strong><br>
                                <small style="color: #ff6b35;">(555) 345-6789</small>
                            </td>
                            <td>Sunrise Community</td>
                            <td>Training Certification</td>
                            <td>Apr 15, 2023</td>
                            <td><strong>Apr 15, 2026</strong></td>
                            <td><span class="status-badge status-approved"><i class="fas fa-exclamation"></i> Expires Soon</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-renew" onclick="renewCredential()">Renew</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Approved Credential -->
                        <tr>
                            <td>
                                <strong>Casey Miller</strong><br>
                                <small style="color: #999;">(555) 456-7890</small>
                            </td>
                            <td>Recovery Plus</td>
                            <td>Background Check</td>
                            <td>Feb 20, 2024</td>
                            <td>Feb 20, 2026</td>
                            <td><span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-renew" onclick="renewCredential()">Renew</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Pending Credential -->
                        <tr>
                            <td>
                                <strong>Riley Thompson</strong><br>
                                <small style="color: #999;">(555) 567-8901</small>
                            </td>
                            <td>Harmony House</td>
                            <td>Medical Exam</td>
                            <td>Pending</td>
                            <td>Pending</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-approve" onclick="approveCredential()">Approve</button>
                                    <button class="btn-small btn-deny" onclick="denyCredential()">Deny</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expired Credential -->
                        <tr>
                            <td>
                                <strong>Sam Anderson</strong><br>
                                <small style="color: #999;">(555) 678-9012</small>
                            </td>
                            <td>New Path Center</td>
                            <td>Background Check</td>
                            <td>Jan 10, 2023</td>
                            <td>Jan 10, 2025</td>
                            <td><span class="status-badge status-expired"><i class="fas fa-times"></i> Expired</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-renew" onclick="renewCredential()">Renew</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Approved Credential -->
                        <tr>
                            <td>
                                <strong>Taylor Jackson</strong><br>
                                <small style="color: #999;">(555) 789-0123</small>
                            </td>
                            <td>Sunrise Community</td>
                            <td>Reference Check</td>
                            <td>May 08, 2024</td>
                            <td>May 08, 2026</td>
                            <td><span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-renew" onclick="renewCredential()">Renew</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function approveCredential() {
        if (confirm('Approve this credential?')) {
            alert('Credential approved successfully!');
        }
    }

    function denyCredential() {
        if (confirm('Are you sure you want to deny this credential?')) {
            alert('Credential denied. Volunteer has been notified.');
        }
    }

    function renewCredential() {
        const date = new Date();
        date.setFullYear(date.getFullYear() + 1);
        const formattedDate = date.toLocaleDateString();
        alert('Credential renewed until ' + formattedDate);
    }

    function exportCSV() {
        alert('Exporting credentials to CSV...');
        // In a real app, this would trigger a download
    }

    function exportPDF() {
        alert('Exporting credentials to PDF...');
        // In a real app, this would trigger a PDF download
    }
</script>
@endsection
