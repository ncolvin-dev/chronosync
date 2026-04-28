@extends('layouts.app')

@section('title', 'Matching - ChronoSync')

@section('extra-styles')
<style>
    .matching-header {
        margin-bottom: 2rem;
    }

    .matching-title {
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
    }

    .filter-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
    }

    .filter-controls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 0;
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
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        outline: none;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-filter {
        padding: 0.75rem 1.5rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-filter:hover {
        background-color: #003366;
    }

    .btn-reset {
        padding: 0.75rem 1.5rem;
        background-color: #e0e0e0;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #d0d0d0;
    }

    /* Meetings Table */
    .meetings-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
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

    .meeting-row {
        display: flex;
        gap: 2rem;
        padding: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .meeting-row:last-child {
        border-bottom: none;
    }

    .meeting-info {
        flex: 1;
        min-width: 300px;
    }

    .meeting-info-row {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .meeting-info-item {
        flex: 1;
    }

    .meeting-info-label {
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .meeting-info-value {
        color: #333;
        font-weight: 500;
    }

    .meeting-status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-filled {
        background-color: #d4edda;
        color: #155724;
    }

    .status-candidates {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-unfilled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .volunteers-section {
        flex: 1;
        min-width: 300px;
    }

    .volunteers-title {
        font-weight: 600;
        color: #003366;
        margin-bottom: 0.75rem;
    }

    .volunteer-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .volunteer-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.8rem;
        background-color: #f0f0f0;
        border-radius: 9999px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .volunteer-chip:hover {
        background-color: #e0e0e0;
    }

    .volunteer-chip.assigned {
        background-color: #d4edda;
        color: #155724;
    }

    .chip-score {
        background-color: #0099cc;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .volunteer-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-assign {
        padding: 0.5rem 1rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-assign:hover {
        background-color: #218838;
    }

    .btn-change {
        padding: 0.5rem 1rem;
        background-color: #ffc107;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-change:hover {
        background-color: #e0a800;
        color: white;
    }

    .btn-override {
        padding: 0.5rem 1rem;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-override:hover {
        background-color: #c82333;
    }

    .no-candidates {
        color: #999;
        font-size: 0.9rem;
        font-style: italic;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #666;
        margin-bottom: 1.5rem;
    }

    /* ── Dark Mode ── */
    html.dark .matching-title {
        color: #93c5fd;
    }

    html.dark .filter-section {
        background-color: #1a2235;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }

    html.dark .filter-title {
        color: #e2e8f0;
    }

    html.dark .form-label {
        color: #cbd5e1;
    }

    html.dark .form-control,
    html.dark .form-select {
        background-color: #141d2e;
        border-color: #2a3a50;
        color: #e2e8f0;
    }

    html.dark .btn-reset {
        background-color: #2a3a50;
        color: #cbd5e1;
    }

    html.dark .btn-reset:hover {
        background-color: #3a4a60;
    }

    html.dark .meetings-container {
        background-color: #1a2235;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }

    html.dark .meeting-row {
        border-bottom: 1px solid #2a3a50;
    }

    html.dark .meeting-info-label {
        color: #64748b;
    }

    html.dark .meeting-info-value {
        color: #e2e8f0;
    }

    html.dark .volunteers-title {
        color: #93c5fd;
    }

    html.dark .no-candidates {
        color: #64748b;
    }

    /* Status badges — brighter on dark backgrounds */
    html.dark .status-filled {
        background-color: #166534;
        color: #bbf7d0;
    }

    html.dark .status-candidates {
        background-color: #78350f;
        color: #fde68a;
    }

    html.dark .status-unfilled {
        background-color: #7f1d1d;
        color: #fecaca;
    }

    /* Volunteer chips on dark */
    html.dark .volunteer-chip {
        background-color: #2a3a50;
        color: #cbd5e1;
    }

    html.dark .volunteer-chip:hover {
        background-color: #3a4a60;
    }

    html.dark .volunteer-chip.assigned {
        background-color: #14532d;
        color: #bbf7d0;
    }

    /* Dividers between meeting rows */
    html.dark .meeting-row {
        border-bottom-color: #2a3a50;
    }

    html.dark #manualAssignOverlay > div {
        background-color: #1a2235 !important;
        color: #e2e8f0;
    }

    html.dark #manualAssignOverlay label {
        color: #e2e8f0 !important;
    }

    html.dark #manualAssignOverlay select,
    html.dark #manualAssignOverlay input[type="date"],
    html.dark #manualAssignOverlay textarea {
        background-color: #141d2e !important;
        border-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }

    html.dark #manualAssignOverlay p {
        color: #94a3b8 !important;
    }

    @media (max-width: 1024px) {
        .meeting-row {
            flex-direction: column;
            gap: 1rem;
        }

        .meeting-info-row {
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-controls {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .matching-title {
            font-size: 1.25rem;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-filter,
        .btn-reset {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="matching-header">
                <h1 class="matching-title">
                    <i class="fas fa-link"></i> Volunteer Matching
                </h1>
                <p style="color: #666; font-size: 0.9rem;">
                    View upcoming meetings and match available volunteers based on availability, experience, and facility requirements.
                </p>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('meetings.index') }}">
            <div class="filter-section">
                <div class="filter-title">Filter Meetings</div>

                <div class="filter-controls">
                    <div class="form-group">
                        <label for="facility_id" class="form-label">Facility</label>
                        <select class="form-select" id="facility_id" name="facility_id">
                            <option value="">All Facilities</option>
                            @foreach($facilities as $f)
                                <option value="{{ $f->facility_id }}" {{ request('facility_id') == $f->facility_id ? 'selected' : '' }}>
                                    {{ $f->facility_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="day_of_week" class="form-label">Day of Week</label>
                        <select class="form-select" id="day_of_week" name="day_of_week">
                            <option value="">Any Day</option>
                            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                                <option value="{{ $i }}" {{ request('day_of_week') == $i ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-select" id="format" name="format">
                            <option value="">All Formats</option>
                            <option value="in_person" {{ request('format') === 'in_person' ? 'selected' : '' }}>In Person</option>
                            <option value="virtual"   {{ request('format') === 'virtual'   ? 'selected' : '' }}>Virtual</option>
                            <option value="hybrid"    {{ request('format') === 'hybrid'    ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Meeting Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All</option>
                            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('meetings.index') }}" class="btn-reset" style="text-decoration:none;display:inline-block;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
            </form>

            {{-- Meetings List --}}
            <div class="meetings-container">
                @forelse($meetings as $meeting)
                @php
                    $days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                    $weeks = ['1st','2nd','3rd','4th','Last'];

                    // Compute next occurrence date
                    $nextDate = null;
                    if ($meeting->scheduled_time) {
                        $dt = \Carbon\Carbon::parse($meeting->scheduled_time);
                        if ($dt->isFuture()) {
                            $nextDate = $dt;
                        }
                    } elseif ($meeting->day_of_week !== null && $meeting->week_of_month !== null && $meeting->meeting_time) {
                        $targetDow  = (int) $meeting->day_of_week;
                        $targetWeek = (int) $meeting->week_of_month;
                        [$h, $m]    = explode(':', $meeting->meeting_time);

                        // Try current month, then next month
                        foreach ([\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->addMonth()->startOfMonth()] as $start) {
                            $candidate = $start->copy();
                            // Advance to the target day of week
                            while ($candidate->dayOfWeek !== $targetDow) {
                                $candidate->addDay();
                            }
                            // Add (week - 1) more weeks, unless it's week 5 (last)
                            if ($targetWeek === 5) {
                                // Last occurrence: keep adding weeks while still in the month
                                $last = $candidate->copy();
                                while ($last->copy()->addWeek()->month === $candidate->month) {
                                    $last->addWeek();
                                }
                                $candidate = $last;
                            } else {
                                $candidate->addWeeks($targetWeek - 1);
                            }
                            $candidate->setTime((int)$h, (int)$m, 0);
                            if ($candidate->isFuture()) {
                                $nextDate = $candidate;
                                break;
                            }
                        }
                    }

                    // Load all upcoming active assignments for this meeting
                    $upcomingAssignments = $meeting->assignments()
                        ->whereIn('status', ['scheduled', 'confirmed', 'pending_confirmation'])
                        ->where('assignment_date', '>=', now()->toDateString())
                        ->with('volunteer')
                        ->orderBy('assignment_date')
                        ->get();
                    $assignedCount  = $upcomingAssignments->count();
                    $spotsRemaining = max(0, ($meeting->volunteers_needed ?? 1) - $assignedCount);
                @endphp
                <div class="meeting-row">
                    <div class="meeting-info">
                        <div class="meeting-info-row">
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Facility</div>
                                <div class="meeting-info-value">{{ $meeting->facility->facility_name }}</div>
                            </div>
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Schedule</div>
                                <div class="meeting-info-value">
                                    @if($meeting->day_of_week !== null)
                                        {{ $weeks[($meeting->week_of_month ?? 1) - 1] }}
                                        {{ $days[$meeting->day_of_week] }}
                                        @if($meeting->meeting_time)
                                            at {{ \Carbon\Carbon::createFromFormat('H:i:s', $meeting->meeting_time)->format('g:i A') }}
                                        @endif
                                    @elseif($meeting->scheduled_time)
                                        {{ \Carbon\Carbon::parse($meeting->scheduled_time)->format('D, M j, Y \a\t g:i A') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            @if($nextDate)
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Next Occurrence</div>
                                <div class="meeting-info-value">{{ $nextDate->format('D, M j, Y \a\t g:i A') }}</div>
                            </div>
                            @endif
                        </div>
                        <div style="margin-top: 0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                            @if($assignedCount > 0 && $spotsRemaining === 0)
                                <span class="meeting-status-badge status-filled">
                                    <i class="fas fa-check-circle"></i> Fully Assigned
                                </span>
                            @elseif($assignedCount > 0)
                                <span class="meeting-status-badge status-candidates">
                                    <i class="fas fa-user-check"></i> {{ $assignedCount }}/{{ $meeting->volunteers_needed }} Assigned
                                </span>
                            @else
                                <span class="meeting-status-badge status-unfilled">
                                    <i class="fas fa-user-times"></i> Needs Volunteer
                                </span>
                            @endif
                            @if($meeting->format)
                                <span class="meeting-status-badge" style="background:#e8f4fd;color:#0099cc;">
                                    {{ ucfirst(str_replace('_',' ',$meeting->format)) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="volunteers-section">
                        @php $nextDateStr = $nextDate ? $nextDate->toDateString() : ''; @endphp

                        {{-- Show all assigned volunteers --}}
                        @if($upcomingAssignments->isNotEmpty())
                            <div class="volunteers-title">
                                Assigned ({{ $assignedCount }}/{{ $meeting->volunteers_needed }})
                            </div>
                            <div class="volunteer-chips">
                                @foreach($upcomingAssignments as $assignment)
                                @php
                                    $statusColor = match($assignment->status) {
                                        'confirmed'           => '#28a745',
                                        'pending_confirmation'=> '#ffc107',
                                        default               => '#6c757d',
                                    };
                                    $statusIcon = match($assignment->status) {
                                        'confirmed'           => '✓',
                                        'pending_confirmation'=> '?',
                                        default               => '–',
                                    };
                                @endphp
                                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;width:100%;">
                                    <div class="volunteer-chip assigned" style="flex:1;">
                                        <span>{{ $assignment->volunteer->first_name }} {{ $assignment->volunteer->last_name }}</span>
                                        <span style="font-size:0.75rem;color:#666;margin-left:0.25rem;">
                                            &bull; {{ \Carbon\Carbon::parse($assignment->assignment_date)->format('M j') }}
                                        </span>
                                        <div class="chip-score" style="background-color:{{ $statusColor }};margin-left:auto;"
                                             title="{{ ucfirst(str_replace('_',' ',$assignment->status)) }}">
                                            {{ $statusIcon }}
                                        </div>
                                    </div>
                                    @if($assignment->status === 'pending_confirmation')
                                        <form method="POST"
                                              action="{{ route('meeting-assignments.confirm', $assignment) }}"
                                              onsubmit="return confirm('Mark {{ $assignment->volunteer->first_name }} as confirmed?');">
                                            @csrf
                                            <button type="submit"
                                                    style="padding:0.3rem 0.6rem;background:#28a745;color:white;border:none;border-radius:0.4rem;font-size:0.75rem;cursor:pointer;"
                                                    title="Confirm assignment">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST"
                                          action="{{ route('meeting-assignments.cancel', $assignment) }}"
                                          onsubmit="return confirm('Cancel {{ $assignment->volunteer->first_name }}\'s assignment?');">
                                        @csrf
                                        <button type="submit"
                                                style="padding:0.3rem 0.6rem;background:#dc3545;color:white;border:none;border-radius:0.4rem;font-size:0.75rem;cursor:pointer;"
                                                title="Cancel assignment">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>

                            @if($upcomingAssignments->where('status', 'confirmed')->count() > 0)
                                <form method="POST"
                                      action="{{ route('meetings.send-reminder', $meeting) }}"
                                      onsubmit="return confirm('Send reminder to all confirmed volunteers for this meeting?');"
                                      style="margin-top:0.5rem;">
                                    @csrf
                                    <button type="submit"
                                            style="padding:0.4rem 0.9rem;background:#0099cc;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:pointer;">
                                        <i class="fas fa-bell"></i> Send Reminder
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Show assign controls if spots remain --}}
                        @if($spotsRemaining > 0)
                            @if($upcomingAssignments->isEmpty())
                                <div class="volunteers-title">Assign Volunteer</div>
                                <div class="no-candidates" style="margin-bottom:0.75rem;">
                                    Auto-assign picks the top-scored match, or choose manually.
                                </div>
                            @else
                                <div style="font-size:0.8rem;color:#888;margin:0.5rem 0 0.75rem;">
                                    {{ $spotsRemaining }} spot{{ $spotsRemaining > 1 ? 's' : '' }} still open
                                </div>
                            @endif
                            <div class="volunteer-actions">
                                <button class="btn-assign"
                                        onclick="autoAssign('{{ $meeting->meeting_id }}', '{{ $nextDateStr }}', this)">
                                    <i class="fas fa-magic"></i> Auto-Assign
                                </button>
                                <button class="btn-change"
                                        onclick="openManualAssign('{{ $meeting->meeting_id }}', '{{ $nextDateStr }}')">
                                    <i class="fas fa-hand-pointer"></i> Manual Assign
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:3rem 2rem;color:#999;">
                    <i class="fas fa-calendar-times" style="font-size:2.5rem;display:block;margin-bottom:1rem;color:#ddd;"></i>
                    No meetings match your filters.
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($meetings->hasPages())
            <div style="display:flex;justify-content:center;margin-top:1.5rem;">
                {{ $meetings->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Manual Assign Modal --}}
<div id="manualAssignOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;"
     onclick="if(event.target===this) closeManualAssign();">
    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
                background:white;border-radius:0.75rem;box-shadow:0 10px 40px rgba(0,0,0,0.2);
                width:90%;max-width:480px;overflow:hidden;">

        <div style="background:linear-gradient(135deg,#003366,#0099cc);color:white;
                    padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="margin:0;font-size:1.1rem;font-weight:700;">
                <i class="fas fa-user-check"></i> Manually Assign Volunteer
            </h3>
            <button onclick="closeManualAssign()"
                    style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;line-height:1;">×</button>
        </div>

        <form id="manualAssignForm" method="POST" style="padding:1.5rem;">
            @csrf
            <input type="hidden" name="assignment_type" value="manual">

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-weight:600;color:#333;margin-bottom:0.4rem;font-size:0.875rem;">
                    Volunteer *
                </label>
                <select name="volunteer_id" required
                        style="width:100%;padding:0.75rem;border:1px solid #ddd;border-radius:0.5rem;font-size:0.9rem;">
                    <option value="">— Select a volunteer —</option>
                    @foreach($volunteers as $v)
                        <option value="{{ $v->volunteer_id }}">
                            {{ $v->last_name }}, {{ $v->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" id="modalAssignDate" name="assignment_date">

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-weight:600;color:#333;margin-bottom:0.4rem;font-size:0.875rem;">
                    Note (optional)
                </label>
                <textarea name="override_reason" rows="2" maxlength="500"
                          placeholder="e.g. volunteer requested this date"
                          style="width:100%;padding:0.75rem;border:1px solid #ddd;border-radius:0.5rem;font-size:0.9rem;resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
                <button type="button" onclick="closeManualAssign()"
                        style="padding:0.65rem 1.25rem;background:#e0e0e0;color:#333;border:none;border-radius:0.5rem;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:0.65rem 1.25rem;background:#28a745;color:white;border:none;border-radius:0.5rem;font-weight:600;cursor:pointer;">
                    <i class="fas fa-user-check"></i> Assign
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    /**
     * Auto-assign the best available volunteer to a meeting via the API,
     * then refresh the page so the new assignment is reflected.
     *
     * @param {string} meetingId   - The meeting's ULID
     * @param {string} dateStr     - ISO date string for the target occurrence (YYYY-MM-DD)
     * @param {HTMLElement} btn    - The clicked button (disabled during request)
     */
    function autoAssign(meetingId, dateStr, btn) {
        if (!dateStr) {
            showAlert('No upcoming occurrence date found for this meeting. Use Manual Assign to pick a date.', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning…';

        fetch(`/api/meetings/${meetingId}/auto-assign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ assignment_date: dateStr }),
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (ok) {
                // Refresh to show the new assignment
                window.location.reload();
            } else {
                const msg = data?.message || 'No eligible volunteer found. Try again later.';
                showAlert(msg, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Auto-Assign';
            }
        })
        .catch(() => {
            showAlert('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Auto-Assign';
        });
    }

    /**
     * Show a transient status message at the top of the meetings container.
     */
    function showAlert(message, type) {
        const existing = document.getElementById('matching-alert');
        if (existing) existing.remove();

        const color = type === 'error' ? '#f8d7da' : '#d4edda';
        const text  = type === 'error' ? '#721c24'  : '#155724';

        const el = document.createElement('div');
        el.id = 'matching-alert';
        el.style.cssText = `
            background:${color}; color:${text}; border-radius:0.5rem;
            padding:0.875rem 1.25rem; margin-bottom:1rem; font-weight:500;
            display:flex; align-items:center; gap:0.5rem;
        `;
        el.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${message}
            <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:1.1rem;color:inherit;">×</button>`;

        document.querySelector('.meetings-container').insertAdjacentElement('beforebegin', el);
        if (type !== 'error') setTimeout(() => el.remove(), 4000);
    }

    /**
     * Open the manual assign modal, pre-filling the form for the given meeting.
     *
     * @param {string} meetingId - The meeting's ULID
     * @param {string} dateStr   - ISO date string (YYYY-MM-DD) for the next occurrence
     */
    function openManualAssign(meetingId, dateStr) {
        const form = document.getElementById('manualAssignForm');
        form.action = `/meetings/${meetingId}/assign`;

        const dateInput = document.getElementById('modalAssignDate');
        dateInput.value = dateStr || '';

        // Reset the volunteer select to the placeholder
        form.querySelector('select[name="volunteer_id"]').value = '';
        form.querySelector('textarea[name="override_reason"]').value = '';

        document.getElementById('manualAssignOverlay').style.display = 'block';
    }

    function closeManualAssign() {
        document.getElementById('manualAssignOverlay').style.display = 'none';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeManualAssign();
    });
</script>
@endsection
