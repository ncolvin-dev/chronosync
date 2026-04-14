@extends('layouts.app')

@section('title', 'Assignments - ChronoSync')

@section('extra-styles')
<style>
    .assignments-header {
        margin-bottom: 2rem;
    }

    .assignments-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .assignments-subtitle {
        color: #666;
        font-size: 0.875rem;
    }

    .filter-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid #e0e0e0;
        overflow-x: auto;
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        background: none;
        border: none;
        color: #666;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
        transition: all 0.3s;
    }

    .filter-tab:hover {
        color: #003366;
    }

    .filter-tab.active {
        color: #0099cc;
        border-bottom-color: #0099cc;
    }

    .assignments-list {
        display: grid;
        gap: 1.5rem;
    }

    .assignment-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid #d0d0d0;
        transition: all 0.3s;
    }

    .assignment-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .assignment-card.confirmed {
        border-left-color: #28a745;
    }

    .assignment-card.pending {
        border-left-color: #ffc107;
    }

    .assignment-card.scheduled {
        border-left-color: #0099cc;
    }

    .assignment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .assignment-facility {
        font-size: 1.1rem;
        font-weight: 700;
        color: #003366;
    }

    .assignment-status {
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .assignment-status.confirmed {
        background-color: #d4edda;
        color: #155724;
    }

    .assignment-status.pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .assignment-status.scheduled {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .assignment-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .assignment-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .assignment-detail-icon {
        color: #0099cc;
        width: 20px;
        text-align: center;
        margin-top: 0.25rem;
        flex-shrink: 0;
    }

    .assignment-detail-content {
        flex: 1;
    }

    .assignment-detail-label {
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .assignment-detail-value {
        color: #333;
        font-weight: 500;
    }

    .assignment-location {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        border-left: 3px solid #0099cc;
    }

    .assignment-location-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .assignment-location-address {
        color: #666;
        font-size: 0.9rem;
    }

    .assignment-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    .btn-confirm {
        background-color: #28a745;
        color: white;
    }

    .btn-confirm:hover {
        background-color: #218838;
    }

    .btn-decline {
        background-color: #dc3545;
        color: white;
    }

    .btn-decline:hover {
        background-color: #c82333;
    }

    .btn-view {
        background-color: #0099cc;
        color: white;
    }

    .btn-view:hover {
        background-color: #003366;
    }

    .sms-reminder {
        background-color: #e7f3ff;
        border: 1px solid #0099cc;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .sms-reminder-icon {
        color: #0099cc;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .sms-reminder-content {
        flex: 1;
    }

    .sms-reminder-title {
        font-weight: 600;
        color: #003366;
        margin-bottom: 0.25rem;
    }

    .sms-reminder-text {
        color: #666;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
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

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* ── Dark Mode ── */
    html.dark .assignments-title {
        color: #93c5fd;
    }

    html.dark .assignments-subtitle {
        color: #94a3b8;
    }

    html.dark .filter-tabs {
        border-bottom-color: #2a3a50;
    }

    html.dark .filter-tab {
        color: #64748b;
    }

    html.dark .filter-tab:hover {
        color: #cbd5e1;
    }

    html.dark .filter-tab.active {
        color: #38bdf8;
        border-bottom-color: #38bdf8;
    }

    html.dark .assignment-card {
        background-color: #1a2235;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        border-left-color: #2a3a50;
    }

    html.dark .assignment-card.confirmed {
        border-left-color: #28a745;
    }

    html.dark .assignment-card.pending {
        border-left-color: #d97706;
    }

    html.dark .assignment-card.scheduled {
        border-left-color: #0099cc;
    }

    html.dark .assignment-facility {
        color: #93c5fd;
    }

    /* Status badges */
    html.dark .assignment-status.confirmed {
        background-color: #14532d;
        color: #bbf7d0;
    }

    html.dark .assignment-status.pending {
        background-color: #78350f;
        color: #fde68a;
    }

    html.dark .assignment-status.scheduled {
        background-color: #164e63;
        color: #a5f3fc;
    }

    html.dark .assignment-detail-label {
        color: #64748b;
    }

    html.dark .assignment-detail-value {
        color: #e2e8f0;
    }

    html.dark .assignment-location {
        background-color: #141d2e;
        border-left-color: #0099cc;
    }

    html.dark .assignment-location-title {
        color: #93c5fd;
    }

    html.dark .assignment-location-address {
        color: #94a3b8;
    }

    html.dark .sms-reminder {
        background-color: #0f2235;
        border-color: #0099cc;
    }

    html.dark .sms-reminder-title {
        color: #93c5fd;
    }

    html.dark .sms-reminder-text {
        color: #94a3b8;
    }

    html.dark .empty-state-title {
        color: #94a3b8;
    }

    html.dark .empty-state-text {
        color: #64748b;
    }

    html.dark .empty-state-icon {
        color: #2a3a50;
    }

    html.dark .tab-content > p {
        color: #94a3b8 !important;
    }

    @media (max-width: 768px) {
        .assignments-title {
            font-size: 1.25rem;
        }

        .assignment-details {
            grid-template-columns: 1fr;
        }

        .assignment-header {
            flex-direction: column;
        }

        .assignment-actions {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .filter-tabs {
            margin-bottom: 1.5rem;
        }

        .sms-reminder {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">
            <div class="assignments-header">
                <h1 class="assignments-title">
                    <i class="fas fa-tasks"></i> My Assignments
                </h1>
                <p class="assignments-subtitle">
                    View your upcoming and past meeting assignments.
                </p>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All Assignments</button>
                <button class="filter-tab" data-filter="upcoming">Upcoming</button>
                <button class="filter-tab" data-filter="confirmed">Confirmed</button>
                <button class="filter-tab" data-filter="pending">Pending</button>
                <button class="filter-tab" data-filter="past">Past</button>
            </div>

            <!-- All Assignments -->
            <div class="tab-content active" id="all-assignments">
                <div class="assignments-list">
                    <!-- Confirmed Assignment Example -->
                    <div class="assignment-card confirmed">
                        <div class="assignment-header">
                            <div class="assignment-facility">Harmony House Treatment Center</div>
                            <span class="assignment-status confirmed">
                                <i class="fas fa-check-circle"></i> Confirmed
                            </span>
                        </div>

                        <div class="assignment-details">
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Date</div>
                                    <div class="assignment-detail-value">Tuesday, April 2, 2026</div>
                                </div>
                            </div>
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Time</div>
                                    <div class="assignment-detail-value">6:30 PM - 8:00 PM</div>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-location">
                            <div class="assignment-location-title">
                                <i class="fas fa-map-marker-alt"></i> Meeting Location
                            </div>
                            <div class="assignment-location-address">
                                Harmony House Treatment Center<br>
                                123 Recovery Lane<br>
                                Downtown, Metro City 12345
                            </div>
                        </div>

                        <div class="assignment-actions">
                            <button class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn-action btn-decline">
                                <i class="fas fa-times"></i> Cancel Commitment
                            </button>
                        </div>
                    </div>

                    <!-- Pending Assignment Example -->
                    <div class="assignment-card pending">
                        <div class="assignment-header">
                            <div class="assignment-facility">New Path Recovery Center</div>
                            <span class="assignment-status pending">
                                <i class="fas fa-clock"></i> Pending Response
                            </span>
                        </div>

                        <div class="assignment-details">
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Date</div>
                                    <div class="assignment-detail-value">Wednesday, April 3, 2026</div>
                                </div>
                            </div>
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Time</div>
                                    <div class="assignment-detail-value">7:00 PM - 8:30 PM</div>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-location">
                            <div class="assignment-location-title">
                                <i class="fas fa-map-marker-alt"></i> Meeting Location
                            </div>
                            <div class="assignment-location-address">
                                New Path Recovery Center<br>
                                456 Hope Street<br>
                                Eastside, Metro City 12346
                            </div>
                        </div>

                        <div class="assignment-actions">
                            <button class="btn-action btn-confirm">
                                <i class="fas fa-check"></i> Confirm Attendance
                            </button>
                            <button class="btn-action btn-decline">
                                <i class="fas fa-times"></i> Decline
                            </button>
                            <button class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>

                    <!-- Scheduled Assignment Example -->
                    <div class="assignment-card scheduled">
                        <div class="assignment-header">
                            <div class="assignment-facility">Sunrise Community Center</div>
                            <span class="assignment-status scheduled">
                                <i class="fas fa-calendar-check"></i> Scheduled
                            </span>
                        </div>

                        <div class="assignment-details">
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Date</div>
                                    <div class="assignment-detail-value">Thursday, April 4, 2026</div>
                                </div>
                            </div>
                            <div class="assignment-detail-item">
                                <div class="assignment-detail-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="assignment-detail-content">
                                    <div class="assignment-detail-label">Meeting Time</div>
                                    <div class="assignment-detail-value">5:00 PM - 6:30 PM</div>
                                </div>
                            </div>
                        </div>

                        <div class="assignment-location">
                            <div class="assignment-location-title">
                                <i class="fas fa-map-marker-alt"></i> Meeting Location
                            </div>
                            <div class="assignment-location-address">
                                Sunrise Community Center<br>
                                789 Main Avenue<br>
                                Midtown, Metro City 12347
                            </div>
                        </div>

                        <div class="assignment-actions">
                            <button class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Assignments -->
            <div class="tab-content" id="upcoming-assignments">
                <p style="color: #666; text-align: center; padding: 2rem;">
                    You have 3 upcoming assignments. Use the "All Assignments" tab to view them.
                </p>
            </div>

            <!-- Confirmed Assignments -->
            <div class="tab-content" id="confirmed-assignments">
                <p style="color: #666; text-align: center; padding: 2rem;">
                    You have 1 confirmed assignment.
                </p>
            </div>

            <!-- Pending Assignments -->
            <div class="tab-content" id="pending-assignments">
                <p style="color: #666; text-align: center; padding: 2rem;">
                    You have 1 pending assignment awaiting your response.
                </p>
            </div>

            <!-- Past Assignments -->
            <div class="tab-content" id="past-assignments">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="empty-state-title">No Past Assignments</div>
                    <div class="empty-state-text">You haven't attended any meetings yet.</div>
                </div>
            </div>

            <!-- SMS Reminder Notice -->
            <div class="sms-reminder">
                <div class="sms-reminder-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="sms-reminder-content">
                    <div class="sms-reminder-title">SMS Reminders</div>
                    <div class="sms-reminder-text">
                        You will receive SMS text message reminders for upcoming meetings. Standard message rates may apply. You can opt out in your profile settings.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Update active content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(filter + '-assignments').classList.add('active');
        });
    });

    // Action buttons
    document.querySelectorAll('.btn-confirm').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const facility = card.querySelector('.assignment-facility').textContent;
            if (confirm('Confirm your attendance at ' + facility + '?')) {
                // Handle confirmation
                alert('Your attendance has been confirmed!');
            }
        });
    });

    document.querySelectorAll('.btn-decline').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const facility = card.querySelector('.assignment-facility').textContent;
            if (confirm('Are you sure you want to decline or cancel your attendance at ' + facility + '?')) {
                // Handle decline
                alert('Your response has been recorded.');
            }
        });
    });

    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            // Open details modal or navigate to details page
            alert('Opening assignment details...');
        });
    });
</script>
@endsection
