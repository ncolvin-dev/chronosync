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
    html.dark .modal-title,
    html.dark .meetings-table tr.facility-group-header td {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-bottom-color: #334155 !important;
    }

    html.dark .meetings-table tr.meeting-type-header td {
        background: #0d1424 !important;
        border-bottom-color: #1e293b !important;
    }

    html.dark .meetings-table tr.meeting-type-header td span {
        color: #64748b !important;
    }

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

    <!-- Flash messages -->
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:0.85rem 1.25rem;border-radius:0.5rem;margin-bottom:1.5rem;border:1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:0.85rem 1.25rem;border-radius:0.5rem;margin-bottom:1.5rem;border:1px solid #f5c6cb;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#f8d7da;color:#721c24;padding:0.85rem 1.25rem;border-radius:0.5rem;margin-bottom:1.5rem;border:1px solid #f5c6cb;">
            @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

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
                            <th>Schedule</th>
                            <th>Format</th>
                            <th style="text-align:center;">Volunteers</th>
                            <th>Status</th>
                            <th colspan="2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grouped = $meetings->getCollection()->groupBy('facility_id'); @endphp
                        @foreach($grouped as $facilityId => $facilityMeetings)
                        {{-- Facility group header --}}
                        <tr class="facility-group-header">
                            <td colspan="6" style="background:#f0f4f8;padding:0.6rem 1rem;border-bottom:2px solid #d0dce8;">
                                <strong style="color:#003366;font-size:0.95rem;">
                                    <i class="fas fa-building" style="margin-right:0.4rem;color:#0099cc;"></i>
                                    {{ $facilityMeetings->first()->facility->facility_name ?? '—' }}
                                </strong>
                                <span style="color:#666;font-size:0.82rem;margin-left:0.75rem;">
                                    {{ $facilityMeetings->count() }} meeting{{ $facilityMeetings->count() !== 1 ? 's' : '' }}
                                </span>
                            </td>
                        </tr>
                        {{-- Split into recurring and one-off sub-groups --}}
                        @php
                            $recurring = $facilityMeetings
                                ->filter(fn($m) => $m->isRecurring())
                                ->sortBy([['day_of_week','asc'],['week_of_month','asc'],['meeting_time','asc']]);
                            $oneOff = $facilityMeetings
                                ->filter(fn($m) => $m->isOneOff())
                                ->sortBy(fn($m) => optional($m->scheduled_time)->timestamp ?? 0);
                        @endphp

                        @foreach([['Recurring', 'fas fa-sync-alt', $recurring], ['One-off', 'fas fa-calendar-day', $oneOff]] as [$label, $icon, $group])
                        @if($group->isNotEmpty())
                        {{-- Type sub-header --}}
                        <tr class="meeting-type-header">
                            <td colspan="6" style="padding:0.35rem 1.5rem;background:#fafbfc;border-bottom:1px solid #e4eaf0;">
                                <span style="font-size:0.78rem;font-weight:700;color:#5a7a99;text-transform:uppercase;letter-spacing:0.05em;">
                                    <i class="{{ $icon }}" style="margin-right:0.35rem;"></i>{{ $label }}
                                    <span style="font-weight:500;text-transform:none;letter-spacing:0;margin-left:0.4rem;color:#8aa;">
                                        ({{ $group->count() }})
                                    </span>
                                </span>
                            </td>
                        </tr>
                        @foreach($group as $meeting)
                        <tr>
                            <td>{{ $meeting->schedule_label }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $meeting->format)) }}</td>
                            <td style="text-align:center;">{{ $meeting->volunteers_needed }}</td>
                            <td colspan="1">
                                <span class="status-badge {{ $meeting->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </td>
                            <td colspan="2">
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
                        @endif
                        @endforeach

                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($meetings->hasPages())
                <div style="display:flex;justify-content:center;margin-top:1.5rem;">
                    {{ $meetings->links() }}
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
                    <label for="facility_id" class="form-label">Facility *</label>
                    <select class="form-control" id="facility_id" name="facility_id" required>
                        <option value="">Select a facility…</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}">{{ $facility->facility_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Recurring fields -->
                <div id="recurringFields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="week_of_month" class="form-label">Week of Month *</label>
                            <select class="form-control" id="week_of_month" name="week_of_month">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">Last</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="day_of_week" class="form-label">Day of Week *</label>
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
                        <label for="meeting_time" class="form-label">Meeting Time *</label>
                        <input type="time" class="form-control" id="meeting_time" name="meeting_time">
                    </div>
                </div>

                <!-- One-off fields -->
                <div id="oneOffFields" style="display:none;">
                    <div class="form-group">
                        <label for="scheduled_time" class="form-label">Date &amp; Time *</label>
                        <input type="datetime-local" class="form-control" id="scheduled_time" name="scheduled_time">
                    </div>
                </div>

                <!-- Shared fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="format" class="form-label">Format *</label>
                        <select class="form-control" id="format" name="format" required>
                            <option value="in_person">In Person</option>
                            <option value="virtual">Virtual</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="volunteers_needed" class="form-label">Volunteers Needed *</label>
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
    // Modal
    // -------------------------------------------------------------------------
    function openAddModal() {
        const form = document.getElementById('meetingForm');
        document.getElementById('modalTitle').textContent = 'Add Meeting';
        document.getElementById('submitBtn').textContent  = 'Add Meeting';
        form.reset();
        form.action = '{{ route('meetings.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('typeSection').style.display = '';
        document.getElementById('duration_minutes').value = '60';
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
                : '';
        }

        // Shared fields
        document.getElementById('format').value             = m.format             ?? 'in_person';
        document.getElementById('volunteers_needed').value  = m.volunteers_needed  ?? 1;
        document.getElementById('duration_minutes').value   = m.duration_minutes   ?? 60;
        document.getElementById('notes').value              = m.notes              ?? '';

        document.getElementById('meetingModal').classList.add('show');
    }

    function closeModal() {
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
