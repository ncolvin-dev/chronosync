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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .time-input-group {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }

    .time-input-group .form-control {
        flex: 1;
    }

    .time-separator {
        color: #999;
        font-weight: 600;
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

    .status-sent {
        background-color: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-failed {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

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

    .empty-state {
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        color: #999;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sms-title {
            font-size: 1.25rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .time-input-group {
            flex-direction: column;
            align-items: stretch;
        }

        .time-separator {
            display: none;
        }

        .placeholder-grid {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
        }

        .btn-save,
        .btn-reset {
            width: 100%;
        }

        .activity-table th,
        .activity-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }
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

            <!-- Configuration Cards -->
            <div class="content-grid">
                <!-- Reminder Timing -->
                <div class="config-card">
                    <div class="card-title">
                        <i class="fas fa-clock"></i> Reminder Timing
                    </div>

                    <form method="POST" action="{{ route('coordinator.sms-config.store') }}" id="configForm" novalidate>
                        @csrf

                        <div class="form-group">
                            <label for="hours_before" class="form-label">Hours Before Meeting *</label>
                            <input
                                type="number"
                                class="form-control"
                                id="hours_before"
                                name="hours_before"
                                value="24"
                                min="1"
                                max="72"
                                required
                            >
                            <div class="form-text">Send reminder 1-72 hours before meeting</div>
                        </div>

                        <div class="form-group">
                            <label for="minutes_buffer" class="form-label">Buffer Time (minutes) *</label>
                            <input
                                type="number"
                                class="form-control"
                                id="minutes_buffer"
                                name="minutes_buffer"
                                value="15"
                                min="0"
                                max="120"
                                required
                            >
                            <div class="form-text">Avoid sending multiple reminders within this window</div>
                        </div>
                    </form>
                </div>

                <!-- Time Window -->
                <div class="config-card">
                    <div class="card-title">
                        <i class="fas fa-calendar-alt"></i> Daytime Window
                    </div>

                    <div class="form-group">
                        <label for="window_start" class="form-label">Start Time *</label>
                        <input
                            type="time"
                            class="form-control"
                            id="window_start"
                            name="window_start"
                            value="08:00"
                            required
                        >
                        <div class="form-text">Don't send messages before this time</div>
                    </div>

                    <div class="form-group">
                        <label for="window_end" class="form-label">End Time *</label>
                        <input
                            type="time"
                            class="form-control"
                            id="window_end"
                            name="window_end"
                            value="21:00"
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
                    Message Template *
                </label>
                <textarea
                    id="message_template"
                    name="message_template"
                    class="editor-textarea"
                    placeholder="Enter your SMS template here..."
                    required
                >Hi {volunteer_first_name}, this is a reminder about your volunteer meeting at {facility_name} on {meeting_date} at {meeting_time}. Please reply CONFIRM or DECLINE. Thank you!</textarea>

                <div class="character-count">
                    <span id="charCount">0</span> / 160 characters
                </div>

                <div class="placeholder-list">
                    <div class="placeholder-title">Available Placeholders</div>
                    <div class="placeholder-grid">
                        <div class="placeholder-item" onclick="insertPlaceholder('volunteer_first_name')">
                            <div class="placeholder-code">{volunteer_first_name}</div>
                            <div class="placeholder-desc">Volunteer's first name</div>
                        </div>
                        <div class="placeholder-item" onclick="insertPlaceholder('volunteer_last_name')">
                            <div class="placeholder-code">{volunteer_last_name}</div>
                            <div class="placeholder-desc">Volunteer's last name</div>
                        </div>
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

                <div class="button-group" style="margin-top: 1.5rem;">
                    <button type="submit" form="configForm" class="btn-save">
                        <i class="fas fa-save"></i> Save Configuration
                    </button>
                    <button type="reset" form="configForm" class="btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Recent SMS Activity -->
            <div class="activity-section">
                <div class="activity-title">
                    <i class="fas fa-history"></i> Recent SMS Activity
                </div>

                <div style="overflow-x: auto;">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Volunteer</th>
                                <th>Meeting</th>
                                <th>Send Date</th>
                                <th>Status</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Alex Johnson</strong></td>
                                <td>Harmony House - Tue, Apr 2 6:30 PM</td>
                                <td>Apr 1, 2026 3:45 PM</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
                                <td>CONFIRM</td>
                            </tr>
                            <tr>
                                <td><strong>Morgan Davis</strong></td>
                                <td>New Path - Wed, Apr 3 7:00 PM</td>
                                <td>Apr 2, 2026 4:20 PM</td>
                                <td><span class="status-badge status-sent">Sent</span></td>
                                <td>Awaiting response</td>
                            </tr>
                            <tr>
                                <td><strong>Jordan Taylor</strong></td>
                                <td>Sunrise - Thu, Apr 4 5:00 PM</td>
                                <td>Apr 3, 2026 2:15 PM</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
                                <td>DECLINE</td>
                            </tr>
                            <tr>
                                <td><strong>Casey Miller</strong></td>
                                <td>Recovery Plus - Fri, Apr 5 6:00 PM</td>
                                <td>Apr 4, 2026 5:30 PM</td>
                                <td><span class="status-badge status-sent">Sent</span></td>
                                <td>Awaiting response</td>
                            </tr>
                            <tr>
                                <td><strong>Riley Thompson</strong></td>
                                <td>Harmony House - Sat, Apr 6 5:00 PM</td>
                                <td>Apr 5, 2026 3:00 PM</td>
                                <td><span class="status-badge status-failed">Failed</span></td>
                                <td>Invalid number</td>
                            </tr>
                            <tr>
                                <td><strong>Sam Anderson</strong></td>
                                <td>New Path - Sun, Apr 7 7:00 PM</td>
                                <td>Apr 5, 2026 4:45 PM</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>Scheduled</td>
                            </tr>
                            <tr>
                                <td><strong>Taylor Jackson</strong></td>
                                <td>Sunrise - Mon, Apr 8 6:00 PM</td>
                                <td>Mar 31, 2026 2:30 PM</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
                                <td>CONFIRM</td>
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
    // Character counter
    const templateTextarea = document.getElementById('message_template');
    const charCount = document.getElementById('charCount');

    templateTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;

        if (this.value.length > 160) {
            charCount.parentElement.classList.add('error');
            charCount.parentElement.classList.remove('warning');
        } else if (this.value.length > 140) {
            charCount.parentElement.classList.add('warning');
            charCount.parentElement.classList.remove('error');
        } else {
            charCount.parentElement.classList.remove('warning', 'error');
        }
    });

    // Initialize character count
    charCount.textContent = templateTextarea.value.length;

    // Insert placeholder
    function insertPlaceholder(placeholder) {
        const textarea = document.getElementById('message_template');
        const cursorPos = textarea.selectionStart;
        const beforeText = textarea.value.substring(0, cursorPos);
        const afterText = textarea.value.substring(cursorPos);

        const placeholderText = '{' + placeholder + '}';
        textarea.value = beforeText + placeholderText + afterText;

        // Update character count
        charCount.textContent = textarea.value.length;
        if (textarea.value.length > 160) {
            charCount.parentElement.classList.add('error');
        }

        // Focus back to textarea
        textarea.focus();
        textarea.selectionStart = cursorPos + placeholderText.length;
        textarea.selectionEnd = cursorPos + placeholderText.length;
    }

    // Form submission
    document.getElementById('configForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const hoursefore = document.getElementById('hours_before').value;
        const windowStart = document.getElementById('window_start').value;
        const windowEnd = document.getElementById('window_end').value;
        const messageTemplate = document.getElementById('message_template').value;

        if (!hoursefore || !windowStart || !windowEnd || !messageTemplate) {
            alert('Please fill in all required fields.');
            return;
        }

        if (messageTemplate.length > 160) {
            alert('Message template exceeds 160 characters. SMS may be split into multiple messages.');
        }

        alert('SMS Configuration saved successfully!');
    });
</script>
@endsection
