@extends('layouts.app')

@section('title', 'SMS Configuration - ChronoSync')

@section('extra-styles')
<style>
    .sms-header {
        margin-bottom: 2rem;
    }

    .sms-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .sms-subtitle {
        color: #666;
        font-size: 0.875rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .config-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .card-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
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

    .form-text {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.25rem;
    }

    .template-editor {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .editor-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .editor-textarea {
        width: 100%;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.9rem;
        min-height: 120px;
        resize: vertical;
    }

    .editor-textarea:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        outline: none;
    }

    .placeholder-list {
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .placeholder-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }

    .placeholder-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .placeholder-item {
        background: white;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s;
    }

    .placeholder-item:hover {
        background-color: #f0f8ff;
        border-color: #0099cc;
    }

    .placeholder-code {
        font-family: 'Monaco', 'Courier New', monospace;
        background-color: #f0f0f0;
        padding: 0.4rem 0.6rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #003366;
    }

    .placeholder-desc {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.25rem;
    }

    .character-count {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.5rem;
    }

    .character-count.warning {
        color: #ffc107;
    }

    .character-count.error {
        color: #dc3545;
    }

    .activity-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .activity-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }

    .activity-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .activity-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .activity-table tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-sent      { background-color: #d4edda; color: #155724; }
    .status-delivered { background-color: #d1ecf1; color: #0c5460; }
    .status-failed    { background-color: #f8d7da; color: #721c24; }
    .status-pending   { background-color: #fff3cd; color: #856404; }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-save {
        background-color: #0099cc;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-save:hover {
        background-color: #003366;
    }

    .btn-reset {
        background-color: #e0e0e0;
        color: #333;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #d0d0d0;
    }

    @media (max-width: 1024px) {
        .content-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .sms-title { font-size: 1.25rem; }

        .placeholder-grid { grid-template-columns: 1fr; }

        .button-group { flex-direction: column; }

        .btn-save,
        .btn-reset { width: 100%; }

        .activity-table th,
        .activity-table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="sms-header">
                <h1 class="sms-title">
                    <i class="fas fa-mobile-alt"></i> SMS Reminder Configuration
                </h1>
                <p class="sms-subtitle">
                    Configure automated SMS reminders for upcoming volunteer meetings.
                </p>
            </div>

            @if(session('success'))
                <div style="background:#d4edda;color:#155724;border-radius:0.5rem;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-weight:500;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#f8d7da;color:#721c24;border-radius:0.5rem;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-weight:500;">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin:0.5rem 0 0 1rem;padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sms.configure.save') }}" id="configForm">
                @csrf

                <!-- Configuration Cards -->
                <div class="content-grid">
                    <!-- Reminder Timing -->
                    <div class="config-card">
                        <div class="card-title">
                            <i class="fas fa-clock"></i> Reminder Timing
                        </div>

                        <div class="form-group">
                            <label for="hours_before" class="form-label">Hours Before Meeting</label>
                            <input
                                type="number"
                                class="form-control"
                                id="hours_before"
                                name="hours_before"
                                value="{{ old('hours_before', $config->hours_before_meeting) }}"
                                min="1"
                                max="72"
                                required
                            >
                            <div class="form-text">Send reminder 1–72 hours before the meeting</div>
                        </div>

                        <div class="form-group">
                            <label for="minutes_buffer" class="form-label">Buffer Time (minutes)</label>
                            <input
                                type="number"
                                class="form-control"
                                id="minutes_buffer"
                                name="minutes_buffer"
                                value="{{ old('minutes_buffer', $config->buffer_minutes) }}"
                                min="0"
                                max="120"
                                required
                            >
                            <div class="form-text">Avoid sending duplicate reminders within this window</div>
                        </div>
                    </div>

                    <!-- Daytime Window -->
                    <div class="config-card">
                        <div class="card-title">
                            <i class="fas fa-calendar-alt"></i> Daytime Window
                        </div>

                        <div class="form-group">
                            <label for="window_start" class="form-label">Start Time</label>
                            <input
                                type="time"
                                class="form-control"
                                id="window_start"
                                name="window_start"
                                value="{{ old('window_start', $config->window_start) }}"
                                required
                            >
                            <div class="form-text">Don't send messages before this time</div>
                        </div>

                        <div class="form-group">
                            <label for="window_end" class="form-label">End Time</label>
                            <input
                                type="time"
                                class="form-control"
                                id="window_end"
                                name="window_end"
                                value="{{ old('window_end', $config->window_end) }}"
                                required
                            >
                            <div class="form-text">Don't send messages after this time</div>
                        </div>
                    </div>
                </div>

                <!-- Message Template Editor -->
                <div class="template-editor">
                    <div class="editor-title">
                        <i class="fas fa-edit"></i> SMS Message Template
                    </div>

                    <label for="message_template" class="form-label" style="margin-top: 1rem;">
                        Message Template
                    </label>
                    <textarea
                        id="message_template"
                        name="message_template"
                        class="editor-textarea"
                        placeholder="Enter your SMS template here..."
                        required
                    >{{ old('message_template', $config->message_template) }}</textarea>

                    <div class="character-count">
                        <span id="charCount">0</span> / 160 characters
                    </div>

                    <div class="placeholder-list">
                        <div class="placeholder-title">Available Placeholders</div>
                        <div class="placeholder-grid">
                            <div class="placeholder-item" onclick="insertPlaceholder('facility_name')">
                                <div class="placeholder-code">{facility_name}</div>
                                <div class="placeholder-desc">Meeting facility name</div>
                            </div>
                            <div class="placeholder-item" onclick="insertPlaceholder('meeting_date')">
                                <div class="placeholder-code">{meeting_date}</div>
                                <div class="placeholder-desc">Meeting date</div>
                            </div>
                            <div class="placeholder-item" onclick="insertPlaceholder('meeting_time')">
                                <div class="placeholder-code">{meeting_time}</div>
                                <div class="placeholder-desc">Meeting time</div>
                            </div>
                            <div class="placeholder-item" onclick="insertPlaceholder('facility_address')">
                                <div class="placeholder-code">{facility_address}</div>
                                <div class="placeholder-desc">Facility address</div>
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save Configuration
                        </button>
                        <button type="reset" class="btn-reset">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>

            </form>

            <!-- Recent SMS Activity -->
            <div class="activity-section">
                <div class="activity-title">
                    <i class="fas fa-history"></i> Recent SMS Activity
                </div>

                <div style="overflow-x: auto;">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Meeting</th>
                                <th>Sent</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" style="text-align:center;color:#999;padding:2rem;">
                                    SMS activity log coming soon.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    const templateTextarea = document.getElementById('message_template');
    const charCount = document.getElementById('charCount');

    function updateCharCount() {
        const len = templateTextarea.value.length;
        charCount.textContent = len;
        charCount.parentElement.classList.remove('warning', 'error');
        if (len > 160) charCount.parentElement.classList.add('error');
        else if (len > 140) charCount.parentElement.classList.add('warning');
    }

    templateTextarea.addEventListener('input', updateCharCount);
    updateCharCount();

    function insertPlaceholder(placeholder) {
        const pos    = templateTextarea.selectionStart;
        const text   = '{' + placeholder + '}';
        const before = templateTextarea.value.substring(0, pos);
        const after  = templateTextarea.value.substring(pos);

        templateTextarea.value = before + text + after;
        templateTextarea.focus();
        templateTextarea.selectionStart = pos + text.length;
        templateTextarea.selectionEnd   = pos + text.length;
        updateCharCount();
    }
</script>
@endsection
