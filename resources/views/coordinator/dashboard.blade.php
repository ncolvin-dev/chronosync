@extends('layouts.app')

@section('title', 'Dashboard - ChronoSync')

@section('extra-styles')
<style>
    .dashboard-header {
        margin-bottom: 2rem;
    }

    .dashboard-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
        color: #666;
        font-size: 0.875rem;
    }

    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-top: 4px solid #0099cc;
    }

    .stat-card-label {
        color: #999;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .stat-card-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #003366;
        margin-bottom: 0.5rem;
    }

    .stat-card-change {
        font-size: 0.85rem;
        color: #28a745;
    }

    .stat-card-change.negative {
        color: #dc3545;
    }

    .dashboard-content {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 2rem;
    }

    .dashboard-main {
        flex: 1;
    }

    .dashboard-sidebar {
        width: 300px;
    }

    .section-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .section-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .schedule-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .schedule-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .schedule-table tr:hover {
        background-color: #f8f9fa;
    }

    .schedule-table tr.unfilled {
        background-color: #ffe6e6;
    }

    .schedule-table tr.unfilled td {
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

    .status-confirmed {
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

    .activity-feed {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        padding: 1rem 0;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        gap: 1rem;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0099cc;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-action {
        color: #333;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        color: #999;
        font-size: 0.8rem;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-action {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-align: left;
        font-size: 0.9rem;
    }

    .btn-action:hover {
        background-color: #003366;
    }

    .btn-action i {
        width: 20px;
        text-align: center;
    }

    .alerts-section {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .alerts-title {
        color: #856404;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .alert-item {
        color: #856404;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .alert-item:last-child {
        margin-bottom: 0;
    }

    .empty-row {
        text-align: center;
        padding: 2rem;
        color: #999;
    }

    /* ── Dark Mode ── */
    html.dark .dashboard-title,
    html.dark .dashboard-subtitle {
        color: #e2e8f0 !important;
    }

    html.dark .schedule-table th {
        background-color: #1e2a3a !important;
        color: #93c5fd !important;
        border-color: #2a3a50 !important;
    }

    html.dark .schedule-table td {
        border-color: #2a3a50 !important;
        color: #e2e8f0;
    }

    html.dark .schedule-table tr:hover {
        background-color: #1e2a3a !important;
    }

    html.dark .schedule-table tr.unfilled {
        background-color: #3b1219 !important;
    }

    html.dark .schedule-table tr.unfilled td {
        color: #fca5a5 !important;
    }

    html.dark .schedule-table tr.unfilled:hover {
        background-color: #4c1a24 !important;
    }

    html.dark .status-confirmed {
        background-color: #14532d;
        color: #bbf7d0;
    }

    html.dark .status-pending {
        background-color: #422006;
        color: #fde68a;
    }

    html.dark .status-unfilled {
        background-color: #450a0a;
        color: #fecaca;
    }

    html.dark .alerts-section {
        background-color: #422006 !important;
        border-color: #854d0e !important;
    }

    html.dark .alerts-title,
    html.dark .alert-item {
        color: #fde68a !important;
    }

    html.dark .activity-icon {
        background-color: #1e2a3a !important;
    }

    html.dark .activity-action {
        color: #e2e8f0 !important;
    }

    html.dark .section-card .section-title {
        border-color: #2a3a50 !important;
    }

    html.dark .activity-item {
        border-color: #2a3a50 !important;
    }

    html.dark .btn-action {
        background-color: #1e3a5f !important;
        color: #93c5fd !important;
    }

    html.dark .btn-action:hover {
        background-color: #2a4a72 !important;
        color: #bfdbfe !important;
    }

    @media (max-width: 1200px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }

        .dashboard-sidebar {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .stat-cards {
            grid-template-columns: 1fr;
        }

        .dashboard-title {
            font-size: 1.25rem;
        }

        .section-title {
            font-size: 1rem;
        }

        .schedule-table {
            font-size: 0.8rem;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 0.75rem 0.5rem;
        }

        .quick-actions {
            grid-template-columns: 1fr 1fr;
        }

        .btn-action {
            flex-direction: column;
            text-align: center;
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line"></i> Coordinator Dashboard
                </h1>
                <p class="dashboard-subtitle">
                    Overview of your coordination activities and upcoming meetings.
                </p>
            </div>

            <!-- Stat Cards -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-card-label">Active Volunteers</div>
                    <div class="stat-card-value">{{ $activeVolunteers }}</div>
                    <div class="stat-card-change">
                        @if($newVolunteersMonth > 0)
                            <i class="fas fa-arrow-up"></i> {{ $newVolunteersMonth }} new this month
                        @else
                            <i class="fas fa-arrow-right"></i> None added this month
                        @endif
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">Active Facilities</div>
                    <div class="stat-card-value">{{ $activeFacilities }}</div>
                    <div class="stat-card-change">
                        <i class="fas fa-arrow-right"></i> All active
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">Active Meetings</div>
                    <div class="stat-card-value">{{ $activeMeetings }}</div>
                    <div class="stat-card-change {{ $meetingsNeedingVolunteers > 0 ? 'negative' : '' }}">
                        @if($meetingsNeedingVolunteers > 0)
                            <i class="fas fa-exclamation-circle"></i> {{ $meetingsNeedingVolunteers }} need volunteers
                        @else
                            <i class="fas fa-check-circle"></i> All covered
                        @endif
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">Pending Confirmations</div>
                    <div class="stat-card-value">{{ $pendingConfirmations }}</div>
                    <div class="stat-card-change {{ $pendingConfirmations > 0 ? 'negative' : '' }}">
                        @if($pendingConfirmations > 0)
                            <i class="fas fa-exclamation-circle"></i> Awaiting response
                        @else
                            <i class="fas fa-check-circle"></i> All confirmed
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="dashboard-content">
                <!-- Schedule Section -->
                <div class="dashboard-main">
                    <div class="section-card">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-alt"></i> This Week's Schedule
                        </h2>

                        <div style="overflow-x: auto;">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Facility</th>
                                        <th>Volunteer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($weekAssignments as $assignment)
                                    @php
                                        $isPending = $assignment->status === 'pending_confirmation';
                                        $timeDisplay = $assignment->meeting->meeting_time
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $assignment->meeting->meeting_time)->format('g:i A')
                                            : ($assignment->meeting->scheduled_time
                                                ? \Carbon\Carbon::parse($assignment->meeting->scheduled_time)->format('g:i A')
                                                : '—');
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($assignment->assignment_date)->format('D, M j') }}</td>
                                        <td>{{ $timeDisplay }}</td>
                                        <td>{{ $assignment->meeting->facility->facility_name }}</td>
                                        <td>{{ $assignment->volunteer->first_name }} {{ $assignment->volunteer->last_name }}</td>
                                        <td>
                                            <span class="status-badge {{ $isPending ? 'status-pending' : 'status-confirmed' }}">
                                                {{ $isPending ? 'Pending' : 'Confirmed' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="empty-row">No assignments in the next 7 days.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 1rem; text-align: center;">
                            <a href="{{ route('coordinator.matching') }}" style="color: #0099cc; text-decoration: none; font-weight: 600;">
                                View all meetings &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="dashboard-sidebar">
                    <!-- Alerts -->
                    @if($expiringCredentials > 0 || $meetingsNeedingVolunteers > 0 || $pendingConfirmations > 0)
                    <div class="alerts-section">
                        <div class="alerts-title">
                            <i class="fas fa-exclamation-triangle"></i> Alerts
                        </div>
                        @if($expiringCredentials > 0)
                        <div class="alert-item">
                            <strong>{{ $expiringCredentials }} credential{{ $expiringCredentials !== 1 ? 's' : '' }}</strong> expiring within 30 days
                        </div>
                        @endif
                        @if($meetingsNeedingVolunteers > 0)
                        <div class="alert-item">
                            <strong>{{ $meetingsNeedingVolunteers }} meeting{{ $meetingsNeedingVolunteers !== 1 ? 's' : '' }}</strong> need volunteers
                        </div>
                        @endif
                        @if($pendingConfirmations > 0)
                        <div class="alert-item">
                            <strong>{{ $pendingConfirmations }} pending</strong> confirmation{{ $pendingConfirmations !== 1 ? 's' : '' }}
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="section-card">
                        <h2 class="section-title">Quick Actions</h2>
                        <div class="quick-actions">
                            <button class="btn-action" onclick="window.location='{{ route('coordinator.facilities') }}'">
                                <i class="fas fa-plus"></i>
                                <span>Add Facility</span>
                            </button>
                            <button class="btn-action" onclick="window.location='{{ route('coordinator.matching') }}'">
                                <i class="fas fa-link"></i>
                                <span>Run Matching</span>
                            </button>
                            <button class="btn-action">
                                <i class="fas fa-sms"></i>
                                <span>Send SMS</span>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="section-card">
                        <h2 class="section-title">Recent Activity</h2>
                        <ul class="activity-feed">
                            @forelse($recentActivity as $log)
                            @php
                                $icon = match($log->action) {
                                    'assign_volunteer'   => 'fa-link',
                                    'confirm_assignment' => 'fa-check-circle',
                                    'cancel_assignment'  => 'fa-times-circle',
                                    'create_volunteer'   => 'fa-user-plus',
                                    'update_volunteer'   => 'fa-user-edit',
                                    'approve_credential' => 'fa-certificate',
                                    'deny_credential'    => 'fa-times-circle',
                                    'create_facility'    => 'fa-building',
                                    'update_facility'    => 'fa-building',
                                    'create_meeting'     => 'fa-calendar-plus',
                                    'update_meeting'     => 'fa-calendar-alt',
                                    'send_reminder'      => 'fa-bell',
                                    default              => 'fa-cog',
                                };
                                $actor = $log->actor?->name ?? 'System';
                            @endphp
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-action">{{ $log->action_label }}</div>
                                    <div class="activity-time">
                                        {{ $actor }} &bull; {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-action" style="color:#999;font-style:italic;">No recent activity.</div>
                                </div>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
