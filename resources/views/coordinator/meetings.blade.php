@extends('layouts.app')

@section('title', 'Meetings - ChronoSync')

@section('extra-styles')
<style>
    .meetings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .meetings-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .btn-add {
        background-color: #0099cc;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        font-size: 0.9rem;
    }

    .btn-add:hover { background-color: #003366; }

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
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .meetings-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .meetings-table th {
        background-color: #f8f9fa;
        color: #003366;
        font-weight: 600;
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
    }

    .meetings-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .meetings-table tr:last-child td { border-bottom: none; }
    .meetings-table tr:hover td { background-color: #f8f9fa; }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active   { background: #d4edda; color: #155724; }
    .status-inactive { background: #f8d7da; color: #721c24; }

    .action-btns {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 0.3rem 0.65rem;
        border: none;
        border-radius: 0.4rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        color: white;
    }

    .btn-sm:hover { opacity: 0.85; }
    .btn-edit     { background: #0099cc; }
    .btn-toggle   { background: #6c757d; }
    .btn-activate { background: #28a745; }
    .btn-delete   { background: #dc3545; }

    /* Sort headers */
    .sort-link { color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; white-space: nowrap; }
    .sort-link:hover { color: #0099cc; }
    .sort-icon { font-size: 0.7rem; opacity: 0.35; }
    .sort-icon.active { opacity: 1; color: #0099cc; }
    html.dark .sort-link:hover { color: #38bdf8; }
    html.dark .sort-icon.active { color: #38bdf8; }

    /* Reminder section */
    .reminder-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .reminder-select {
        flex: 1;
        min-width: 260px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        color: #333;
    }

    .btn-remind {
        background: #0099cc;
        color: white;
        border: none;
        padding: 0.55rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background-color 0.2s;
        white-space: nowrap;
    }

    .btn-remind:hover { background: #003366; }
    .btn-remind:disabled { background: #adb5bd; cursor: not-allowed; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        overflow-y: auto;
        padding: 2rem 1rem;
    }

    .modal-overlay.show { display: flex; align-items: flex-start; justify-content: center; }

    .meeting-dialog {
        background: white;
        border-radius: 0.75rem;
        width: 100%;
        max-width: 560px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.15rem;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #666;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }

    .modal-body { padding: 1.5rem; }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .form-group { margin-bottom: 1rem; }

    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.875rem;
        color: #333;
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        color: #333;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #0099cc;
        box-shadow: 0 0 0 3px rgba(0,153,204,0.15);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .type-toggle {
        display: flex;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .type-toggle label {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        color: #666;
        transition: all 0.15s;
    }

    .type-toggle input[type=radio] { display: none; }

    .type-toggle input[type=radio]:checked + label {
        background: #0099cc;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.55rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: #0099cc;
        color: white;
        border: none;
        padding: 0.55rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-primary:hover   { background: #003366; }
    .btn-secondary:hover { background: #5a6268; }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #888;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        color: #ccc;
    }

    /* Dark mode */
    html.dark .section-card,
    html.dark .meeting-dialog { background: #1e293b; }

    html.dark .section-title,
    html.dark .modal-title { background: #0f172a !important; color: #93c5fd !important; border-bottom-color: #334155 !important; }

    html.dark .meetings-table th,
    html.dark .form-label { color: #93c5fd; }

    html.dark .meetings-table th { background: #0f172a; border-color: #334155; }
    html.dark .meetings-table td { border-color: #1e293b; color: #cbd5e1; }
    html.dark .meetings-table tr:hover td { background: #0f172a; }
    html.dark .form-control,
    html.dark .reminder-select { background: #0f172a; border-color: #334155; color: #cbd5e1; }
    html.dark .modal-header,
    html.dark .modal-footer { border-color: #334155; }
    html.dark .type-toggle { border-color: #334155; }
    html.dark .type-toggle label { color: #94a3b8; }

    .req-star {
        color: #dc3545;
        margin-left: 0.15em;
        font-weight: 700;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: none;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220,53,69,0.15);
    }

    @media (max-width: 640px) {
        .form-row { grid-template-columns: 1fr; }
        .meetings-table { font-size: 0.8rem; }
        .meetings-table th, .meetings-table td { padding: 0.5rem 0.6rem; }
    }
</style>
@endsection

@section('content')
<div class="container" style="max-width:1100px;margin:0 auto;padding:2rem 1rem;">

    <!-- Header -->
    <div class="meetings-header">
        <h1 class="meetings-title"><i class="fas fa-calendar-alt"></i> Meetings</h1>
        <button class="btn-add" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add Meeting
        </button>
    </div>

    <!-- Send Reminder -->
    <div class="section-card">
        <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-bell"></i> Send Reminder</span>
            <button type="button" onclick="loadRemindable()" id="refreshBtn"
                    style="background:none;border:none;color:#0099cc;cursor:pointer;font-size:0.85rem;padding:0;">
                <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh
            </button>
        </div>

        <form method="POST" id="reminderForm" action="" onsubmit="return validateReminderForm(event)">
            @csrf
            <div class="reminder-row">
                <select class="reminder-select" id="reminderSelect"
                        onchange="document.getElementById('reminderForm').action = this.value; document.getElementById('reminderBtn').disabled = !this.value;">
                    <option value="">Loading…</option>
                </select>
                <button type="submit" class="btn-remind" id="reminderBtn" disabled>
                    <i class="fas fa-paper-plane"></i> Send Reminder
                </button>
            </div>
        </form>

        <p id="noMeetingsMsg" style="color:#888;margin:0.75rem 0 0;font-size:0.9rem;display:none;">
            No meetings currently have confirmed volunteers.
            Confirm volunteers on the <a href="{{ route('coordinator.matching') }}" style="color:#0099cc;">Matching</a> page first, then click Refresh.
        </p>
    </div>

    <!-- Meetings Table -->
    <div class="section-card">
        <div class="section-title"><i class="fas fa-list"></i> All Meeting Slots</div>

        @if($meetings->isEmpty())
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <div style="font-weight:600;margin-bottom:0.5rem;">No meetings yet</div>
                <div>Use the <strong>Add Meeting</strong> button to create the first slot.</div>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="meetings-table">
                    <thead>
                        <tr>
                            @php
                                $mkUrl = fn($col) => route('meetings.index', array_merge(
                                    request()->except(['sort','dir','page']),
                                    ['sort'=>$col, 'dir'=>($sort===$col && $dir==='asc') ? 'desc' : 'asc']
                                ));
                            @endphp
                            <th>
                                <a href="{{ $mkUrl('facility_name') }}" class="sort-link">
                                    Facility
                                    @if($sort === 'facility_name')
                                        <span class="sort-icon active">{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="sort-icon">⇅</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ $mkUrl('schedule') }}" class="sort-link">
                                    Schedule
                                    @if($sort === 'schedule')
                                        <span class="sort-icon active">{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="sort-icon">⇅</span>
                                    @endif
                                </a>
                            </th>
                            <th>Next Occurrence</th>
                            <th>Type</th>
                            <th>Format</th>
                            <th style="text-align:center;">Volunteers</th>
                            <th>
                                <a href="{{ $mkUrl('status') }}" class="sort-link">
                                    Status
                                    @if($sort === 'status')
                                        <span class="sort-icon active">{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                    @else
                                        <span class="sort-icon">⇅</span>
                                    @endif
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $meeting)
                        <tr>
                            <td style="font-weight:600;">{{ $meeting->facility->facility_name ?? '—' }}</td>
                            <td>{{ $meeting->schedule_label }}</td>
                            <td style="white-space:nowrap;color:#555;font-size:0.875rem;">
                                @php $next = $meeting->nextOccurrence(); @endphp
                                @if($next)
                                    {{ $next->format('D, M j, Y') }}
                                @elseif($meeting->isOneOff() && $meeting->scheduled_time)
                                    <span style="color:#aaa;font-style:italic;">Past</span>
                                @else
                                    <span style="color:#aaa;">—</span>
                                @endif
                            </td>
                            <td>{{ $meeting->isRecurring() ? 'Recurring' : 'One-off' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $meeting->format)) }}</td>
                            <td style="text-align:center;">{{ $meeting->volunteers_needed }}</td>
                            <td>
                                <span class="status-badge {{ $meeting->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-sm btn-edit"
                                            data-meeting='@json($meeting)'
                                            onclick="openEditModal(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    @if($meeting->status === 'active')
                                        <form method="POST" action="{{ route('meetings.deactivate', $meeting) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm btn-toggle"
                                                    onclick="return confirm('Deactivate this meeting slot?')">
                                                <i class="fas fa-pause"></i> Deactivate
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('meetings.activate', $meeting) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm btn-activate">
                                                <i class="fas fa-play"></i> Activate
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-delete"
                                                onclick="return confirm('Delete this meeting? Future assignments will be cancelled.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($meetings->hasPages())
                <div style="display:flex;justify-content:center;margin-top:1.5rem;">
                    {{ $meetings->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- Add / Edit Modal -->
<div class="modal-overlay" id="meetingModal">
    <div class="meeting-dialog">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Add Meeting</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('meetings.store') }}" id="meetingForm" novalidate>
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">

                <!-- Meeting type toggle (hidden on edit for existing meetings) -->
                <div id="typeSection">
                    <label class="form-label">Meeting Type</label>
                    <div class="type-toggle">
                        <input type="radio" name="meeting_type" id="typeRecurring" value="recurring" checked>
                        <label for="typeRecurring" onclick="showRecurring()">Recurring</label>
                        <input type="radio" name="meeting_type" id="typeOneOff" value="one_off">
                        <label for="typeOneOff" onclick="showOneOff()">One-off</label>
                    </div>
                </div>

                <!-- Facility -->
                <div class="form-group">
                    <label for="facility_id" class="form-label">Facility <span class="req-star" aria-hidden="true">*</span></label>
                    <select class="form-control" id="facility_id" name="facility_id">
                        <option value="">Select a facility…</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}">{{ $facility->facility_name }}</option>
                        @endforeach
                    </select>
                    <div id="error_facility_id" class="field-error" role="alert" aria-live="polite"></div>
                </div>

                <!-- Recurring fields -->
                <div id="recurringFields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="week_of_month" class="form-label">Week of Month <span class="req-star" aria-hidden="true">*</span></label>
                            <select class="form-control" id="week_of_month" name="week_of_month">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">Last</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="day_of_week" class="form-label">Day of Week <span class="req-star" aria-hidden="true">*</span></label>
                            <select class="form-control" id="day_of_week" name="day_of_week">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meeting_time" class="form-label">Meeting Time <span class="req-star" aria-hidden="true">*</span></label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time">
                        <div id="error_meeting_time" class="field-error" role="alert" aria-live="polite"></div>
                    </div>
                </div>

                <!-- One-off fields -->
                <div id="oneOffFields" style="display:none;">
                    <div class="form-group">
                        <label for="scheduled_time" class="form-label">Date &amp; Time <span class="req-star" aria-hidden="true">*</span></label>
                        <input type="datetime-local" class="form-control" id="scheduled_time" name="scheduled_time">
                        <div id="error_scheduled_time" class="field-error" role="alert" aria-live="polite"></div>
                    </div>
                </div>

                <!-- Shared fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="format" class="form-label">Format <span class="req-star" aria-hidden="true">*</span></label>
                        <select class="form-control" id="format" name="format" required>
                            <option value="in_person">In Person</option>
                            <option value="virtual">Virtual</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="volunteers_needed" class="form-label">Volunteers Needed <span class="req-star" aria-hidden="true">*</span></label>
                        <select class="form-control" id="volunteers_needed" name="volunteers_needed" required>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                    <input type="number" class="form-control" id="duration_minutes"
                           name="duration_minutes" min="15" max="480" value="60">
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                              maxlength="2000" style="resize:vertical;"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary" id="submitBtn">Add Meeting</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // -------------------------------------------------------------------------
    // Reminder dropdown — loaded and refreshed via AJAX
    // -------------------------------------------------------------------------
    async function loadRemindable() {
        const select      = document.getElementById('reminderSelect');
        const btn         = document.getElementById('reminderBtn');
        const noMsg       = document.getElementById('noMeetingsMsg');
        const icon        = document.getElementById('refreshIcon');
        const prevVal     = select.value;

        icon.classList.add('fa-spin');

        try {
            const resp     = await fetch('{{ route('meetings.remindable') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const meetings = await resp.json();

            select.innerHTML = '<option value="">Select a meeting with confirmed volunteers…</option>';
            meetings.forEach(m => {
                const opt      = document.createElement('option');
                opt.value      = m.url;
                opt.textContent = m.label;
                select.appendChild(opt);
            });

            // Restore prior selection if still in the list
            if (prevVal && select.querySelector(`option[value="${CSS.escape(prevVal)}"]`)) {
                select.value = prevVal;
            }

            btn.disabled  = !select.value;
            noMsg.style.display   = meetings.length ? 'none' : '';
        } catch (err) {
            console.error('Failed to load remindable meetings', err);
        } finally {
            icon.classList.remove('fa-spin');
        }
    }

    // Load on page ready
    document.addEventListener('DOMContentLoaded', loadRemindable);

    // Reload when returning to this tab (e.g. after confirming on Matching page)
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) loadRemindable();
    });

    function validateReminderForm(e) {
        if (!document.getElementById('reminderSelect').value) {
            e.preventDefault();
            alert('Please select a meeting first.');
            return false;
        }
        return confirm('Send a reminder to all confirmed volunteers for this meeting?');
    }

    // -------------------------------------------------------------------------
    // Validation helpers
    // -------------------------------------------------------------------------
    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(function (el) {
            el.textContent = '';
            el.style.display = 'none';
        });
        document.querySelectorAll('.form-control.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById('error_' + fieldId);
        if (field) field.classList.add('is-invalid');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = '';
        }
    }

    function validateMeetingForm() {
        clearErrors();
        let valid = true;

        // Facility is always required
        if (!document.getElementById('facility_id').value) {
            showFieldError('facility_id', 'Please select a facility.');
            valid = false;
        }

        const isRecurring = document.getElementById('recurringFields').style.display !== 'none';

        if (isRecurring) {
            // meeting_time is required for recurring
            if (!document.getElementById('meeting_time').value) {
                showFieldError('meeting_time', 'Please enter a meeting time.');
                valid = false;
            }
        } else {
            // scheduled_time is required for one-off
            if (!document.getElementById('scheduled_time').value) {
                showFieldError('scheduled_time', 'Please enter a date and time.');
                valid = false;
            }
        }

        return valid;
    }

    // Intercept submit — validate first; keep modal open on failure.
    document.getElementById('meetingForm').addEventListener('submit', function (e) {
        if (!validateMeetingForm()) {
            e.preventDefault();
            // Scroll to first error inside the modal body
            const firstError = document.querySelector('.field-error[style*="block"], .field-error:not([style*="none"])');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

    // -------------------------------------------------------------------------
    // Modal
    // -------------------------------------------------------------------------
    function openAddModal() {
        const form = document.getElementById('meetingForm');
        document.getElementById('modalTitle').textContent = 'Add Meeting';
        document.getElementById('submitBtn').textContent  = 'Add Meeting';
        form.reset();
        clearErrors();
        form.action = '{{ route('meetings.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('typeSection').style.display = '';
        document.getElementById('duration_minutes').value = '60';
        // Pre-populate time so the browser's native time picker has a real value
        // (avoids the empty "--:-- AM/PM" placeholder being mistaken for a filled field).
        document.getElementById('meeting_time').value = '09:00';
        showRecurring();
        document.getElementById('meetingModal').classList.add('show');
    }

    function openEditModal(btn) {
        const m    = JSON.parse(btn.dataset.meeting);
        const form = document.getElementById('meetingForm');

        document.getElementById('modalTitle').textContent = 'Edit Meeting';
        document.getElementById('submitBtn').textContent  = 'Save Changes';
        form.action = '/meetings/' + m.meeting_id;
        document.getElementById('formMethod').value = 'PUT';
        clearErrors();

        // Hide type toggle on edit — meeting type is immutable
        document.getElementById('typeSection').style.display = 'none';

        // Populate facility
        document.getElementById('facility_id').value = m.facility_id ?? '';

        // Show correct fields based on type
        if (m.scheduled_time) {
            showOneOff();
            // datetime-local expects "YYYY-MM-DDTHH:MM"
            const dt = m.scheduled_time.replace(' ', 'T').substring(0, 16);
            document.getElementById('scheduled_time').value = dt;
        } else {
            showRecurring();
            document.getElementById('day_of_week').value   = m.day_of_week  ?? 0;
            document.getElementById('week_of_month').value = m.week_of_month ?? 1;
            // meeting_time may be "HH:MM:SS" — trim to HH:MM for the time input
            document.getElementById('meeting_time').value  = m.meeting_time
                ? m.meeting_time.substring(0, 5)
                : '09:00';
        }

        // Shared fields
        document.getElementById('format').value             = m.format             ?? 'in_person';
        document.getElementById('volunteers_needed').value  = m.volunteers_needed  ?? 1;
        document.getElementById('duration_minutes').value   = m.duration_minutes   ?? 60;
        document.getElementById('notes').value              = m.notes              ?? '';

        document.getElementById('meetingModal').classList.add('show');
    }

    function closeModal() {
        clearErrors();
        document.getElementById('meetingModal').classList.remove('show');
    }

    // Close only when clicking the dark backdrop, not anything inside the dialog.
    document.getElementById('meetingModal').addEventListener('click', function (e) {
        if (!e.target.closest('.meeting-dialog')) {
            closeModal();
        }
    });

    function showRecurring() {
        document.getElementById('recurringFields').style.display = '';
        document.getElementById('oneOffFields').style.display    = 'none';
    }

    function showOneOff() {
        document.getElementById('recurringFields').style.display = 'none';
        document.getElementById('oneOffFields').style.display    = '';
    }


</script>
@endsection
