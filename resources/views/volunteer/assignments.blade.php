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

            <!-- Assignments List (filtered by JS) -->
            @if($assignments->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="empty-state-title">No Assignments Yet</div>
                <div class="empty-state-text">You have no meeting assignments at this time.</div>
            </div>
            @else
            <div class="assignments-list" id="assignments-list">
                @foreach($assignments as $assignment)
                @php
                    $meeting = $assignment->meeting;
                    $facility = $meeting?->facility;
                    $isPast = $assignment->assignment_date->lt(now()->startOfDay());
                    $cardClass = match($assignment->status) {
                        'confirmed' => 'confirmed',
                        'pending_confirmation' => 'pending',
                        default => 'scheduled',
                    };
                @endphp
                <div class="assignment-card {{ $cardClass }}"
                     data-assignment-id="{{ $assignment->meeting_assignment_id }}"
                     data-status="{{ $assignment->status }}"
                     data-past="{{ $isPast ? '1' : '0' }}">

                    <div class="assignment-header">
                        <div class="assignment-facility">{{ $facility?->facility_name ?? 'Unknown Facility' }}</div>
                        <span class="assignment-status {{ $cardClass }}">
                            @if($assignment->status === 'confirmed')
                                <i class="fas fa-check-circle"></i> Confirmed
                            @elseif($assignment->status === 'pending_confirmation')
                                <i class="fas fa-clock"></i> Pending Response
                            @elseif($assignment->status === 'declined')
                                <i class="fas fa-times-circle"></i> Declined
                            @elseif($assignment->status === 'cancelled')
                                <i class="fas fa-ban"></i> Cancelled
                            @else
                                <i class="fas fa-calendar-check"></i> Scheduled
                            @endif
                        </span>
                    </div>

                    <div class="assignment-details">
                        <div class="assignment-detail-item">
                            <div class="assignment-detail-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="assignment-detail-content">
                                <div class="assignment-detail-label">Meeting Date</div>
                                <div class="assignment-detail-value">{{ $assignment->assignment_date->format('l, F j, Y') }}</div>
                            </div>
                        </div>
                        @if($meeting)
                        <div class="assignment-detail-item">
                            <div class="assignment-detail-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="assignment-detail-content">
                                <div class="assignment-detail-label">Meeting Time</div>
                                <div class="assignment-detail-value">
                                    {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('g:i A') }}
                                    ({{ $meeting->duration_minutes }} min)
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($facility)
                    <div class="assignment-location">
                        <div class="assignment-location-title">
                            <i class="fas fa-map-marker-alt"></i> Meeting Location
                        </div>
                        <div class="assignment-location-address">
                            {{ $facility->facility_name }}<br>
                            {{ $facility->address }}<br>
                            {{ $facility->city }}, {{ $facility->state }} {{ $facility->zip }}
                        </div>
                    </div>
                    @endif

                    <div class="assignment-actions">
                        @if($assignment->status === 'pending_confirmation')
                            <button class="btn-action btn-confirm">
                                <i class="fas fa-check"></i> Confirm Attendance
                            </button>
                            <button class="btn-action btn-decline">
                                <i class="fas fa-times"></i> Decline
                            </button>
                        @elseif($assignment->status === 'confirmed' && !$isPast)
                            <button class="btn-action btn-cancel">
                                <i class="fas fa-times"></i> Cancel Commitment
                            </button>
                        @elseif(in_array($assignment->status, ['declined', 'cancelled']) && !$isPast)
                            <button class="btn-action btn-reinstate">
                                <i class="fas fa-undo"></i> Reinstate
                            </button>
                        @endif
                        <button class="btn-action btn-view">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>

                    <div class="assignment-extra-details" style="display:none; margin-top:1rem; padding-top:1rem; border-top:1px solid #e0e0e0; font-size:0.875rem; color:#555;">
                        @if($meeting)
                        <p style="margin:0;">
                            <strong>Schedule:</strong> {{ $meeting->schedule_label }}<br>
                            <strong>Format:</strong> {{ ucfirst(str_replace('_', ' ', $meeting->format)) }}<br>
                            <strong>Assignment Type:</strong> {{ ucfirst($assignment->assignment_type) }}
                            @if($assignment->confirmed_at)
                            <br><strong>Confirmed At:</strong> {{ $assignment->confirmed_at->format('M j, Y g:i A') }}
                            @endif
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div id="no-results-message" style="display:none; text-align:center; padding:2rem; color:#666;">
                No assignments found for this filter.
            </div>
            @endif

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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const noResults = document.getElementById('no-results-message');

    // Filter tabs — show/hide cards by status and date
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            let visible = 0;
            document.querySelectorAll('.assignment-card').forEach(card => {
                const status = card.getAttribute('data-status');
                const isPast = card.getAttribute('data-past') === '1';
                let show = false;
                if (filter === 'all') show = true;
                else if (filter === 'upcoming') show = !isPast && (status === 'pending_confirmation' || status === 'confirmed');
                else if (filter === 'confirmed') show = status === 'confirmed';
                else if (filter === 'pending') show = status === 'pending_confirmation';
                else if (filter === 'past') show = isPast;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
        });
    });

    function postAssignmentAction(id, action, successMessage) {
        fetch('/meeting-assignments/' + id + '/' + action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(res => {
            if (!res.ok) return res.json().then(data => Promise.reject(data));
            return res;
        })
        .then(() => {
            alert(successMessage);
            location.reload();
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }

    document.querySelectorAll('.btn-confirm').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const id = card.getAttribute('data-assignment-id');
            const facility = card.querySelector('.assignment-facility').textContent.trim();
            if (confirm('Confirm your attendance at ' + facility + '?')) {
                postAssignmentAction(id, 'confirm', 'Your attendance has been confirmed!');
            }
        });
    });

    document.querySelectorAll('.btn-decline').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const id = card.getAttribute('data-assignment-id');
            const facility = card.querySelector('.assignment-facility').textContent.trim();
            if (confirm('Decline your assignment at ' + facility + '?')) {
                postAssignmentAction(id, 'decline', 'You have declined this assignment.');
            }
        });
    });

    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const id = card.getAttribute('data-assignment-id');
            const facility = card.querySelector('.assignment-facility').textContent.trim();
            if (confirm('Cancel your commitment at ' + facility + '?')) {
                postAssignmentAction(id, 'cancel', 'Your commitment has been cancelled.');
            }
        });
    });

    document.querySelectorAll('.btn-reinstate').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const id = card.getAttribute('data-assignment-id');
            const facility = card.querySelector('.assignment-facility').textContent.trim();
            if (confirm('Reinstate your assignment at ' + facility + '?')) {
                postAssignmentAction(id, 'reinstate', 'Your assignment has been reinstated.');
            }
        });
    });

    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.assignment-card');
            const extra = card.querySelector('.assignment-extra-details');
            const isVisible = extra.style.display !== 'none';
            extra.style.display = isVisible ? 'none' : 'block';
            this.innerHTML = isVisible
                ? '<i class="fas fa-eye"></i> View Details'
                : '<i class="fas fa-eye-slash"></i> Hide Details';
        });
    });
</script>
@endsection
