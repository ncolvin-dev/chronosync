@extends('layouts.app')

@section('title', 'Facilities - ChronoSync')

@section('extra-styles')
<style>
    .facilities-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .facilities-title {
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
        transition: all 0.3s;
    }

    .btn-add:hover {
        background-color: #003366;
    }

    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .facility-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s;
    }

    .facility-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .facility-card-header {
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        padding: 1.5rem;
    }

    .facility-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .facility-status {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .facility-card-body {
        padding: 1.5rem;
    }

    .facility-info-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .facility-info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .facility-info-label {
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .facility-info-value {
        color: #333;
        font-weight: 500;
    }

    .facility-card-footer {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        display: flex;
        gap: 0.75rem;
    }

    .btn-small {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
        text-align: center;
    }

    .btn-edit {
        background-color: #0099cc;
        color: white;
    }

    .btn-edit:hover {
        background-color: #003366;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Modal Styles */
    .facility-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .facility-modal-overlay.show {
        display: block;
    }

    .facility-modal-dialog {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .form-group {
        margin-bottom: 1.5rem;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .checkbox-item input {
        margin: 0;
    }

    .checkbox-item label {
        margin: 0;
    }

    .schedule-builder {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .meeting-slot {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .meeting-slot:last-child {
        margin-bottom: 0;
    }

    .meeting-slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #003366;
        font-size: 0.875rem;
    }

    .btn-remove-slot {
        background: none;
        border: 1px solid #dc3545;
        color: #dc3545;
        padding: 0.25rem 0.6rem;
        border-radius: 0.4rem;
        font-size: 0.75rem;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-remove-slot:hover {
        background-color: #dc3545;
        color: white;
    }

    .schedule-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .schedule-row-wide {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 1rem;
    }

    .btn-add-slot {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: none;
        border: 2px dashed #0099cc;
        color: #0099cc;
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 0.75rem;
        width: 100%;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-add-slot:hover {
        background-color: #e8f7fc;
    }

    .btn-primary {
        background-color: #0099cc;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #003366;
    }

    .btn-secondary {
        background-color: #e0e0e0;
        color: #333;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background-color: #d0d0d0;
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
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .facilities-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .facilities-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .schedule-row {
            grid-template-columns: 1fr;
        }

        .facility-modal-dialog {
            width: 95%;
            max-height: 95vh;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="facilities-header">
                <h1 class="facilities-title">
                    <i class="fas fa-building"></i> Facilities Management
                </h1>
                <button class="btn-add" onclick="openAddFacilityModal()">
                    <i class="fas fa-plus"></i> Add Facility
                </button>
            </div>

            <!-- Facilities Grid -->
            <div class="facilities-grid">
                <!-- Facility Card 1 -->
                <div class="facility-card">
                    <div class="facility-card-header">
                        <div class="facility-name">Harmony House Treatment Center</div>
                        <span class="facility-status"><i class="fas fa-check-circle"></i> Active</span>
                    </div>
                    <div class="facility-card-body">
                        <div class="facility-info-item">
                            <div class="facility-info-label">Address</div>
                            <div class="facility-info-value">123 Recovery Lane, Downtown, Metro City 12345</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Primary Contact</div>
                            <div class="facility-info-value">Sarah Mitchell | (555) 123-4567</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Clean Time Requirement</div>
                            <div class="facility-info-value">2 years minimum</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Meetings This Month</div>
                            <div class="facility-info-value">8 scheduled</div>
                        </div>
                    </div>
                    <div class="facility-card-footer">
                        <button class="btn-small btn-edit" onclick="openEditFacilityModal(1)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-small btn-delete" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- Facility Card 2 -->
                <div class="facility-card">
                    <div class="facility-card-header">
                        <div class="facility-name">New Path Recovery Center</div>
                        <span class="facility-status"><i class="fas fa-check-circle"></i> Active</span>
                    </div>
                    <div class="facility-card-body">
                        <div class="facility-info-item">
                            <div class="facility-info-label">Address</div>
                            <div class="facility-info-value">456 Hope Street, Eastside, Metro City 12346</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Primary Contact</div>
                            <div class="facility-info-value">Michael Chen | (555) 234-5678</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Clean Time Requirement</div>
                            <div class="facility-info-value">1 year minimum</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Meetings This Month</div>
                            <div class="facility-info-value">6 scheduled</div>
                        </div>
                    </div>
                    <div class="facility-card-footer">
                        <button class="btn-small btn-edit" onclick="openEditFacilityModal(2)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-small btn-delete" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- Facility Card 3 -->
                <div class="facility-card">
                    <div class="facility-card-header">
                        <div class="facility-name">Sunrise Community Center</div>
                        <span class="facility-status"><i class="fas fa-check-circle"></i> Active</span>
                    </div>
                    <div class="facility-card-body">
                        <div class="facility-info-item">
                            <div class="facility-info-label">Address</div>
                            <div class="facility-info-value">789 Main Avenue, Midtown, Metro City 12347</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Primary Contact</div>
                            <div class="facility-info-label">Jennifer Lopez | (555) 345-6789</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Clean Time Requirement</div>
                            <div class="facility-info-value">6 months minimum</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Meetings This Month</div>
                            <div class="facility-info-value">5 scheduled</div>
                        </div>
                    </div>
                    <div class="facility-card-footer">
                        <button class="btn-small btn-edit" onclick="openEditFacilityModal(3)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-small btn-delete" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Facility Modal -->
<div class="facility-modal-overlay" id="facilityModal">
    <div class="facility-modal-dialog">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Add New Facility</h2>
            <button class="modal-close" onclick="closeFacilityModal()">×</button>
        </div>

        <form method="POST" action="{{ route('facilities.store') }}" id="facilityForm" novalidate>
            @csrf

            <div class="modal-body">
                <!-- Basic Information -->
                <h4 style="margin-bottom: 1rem; color: #003366; font-weight: 600;">Basic Information</h4>

                <div class="form-group">
                    <label for="name" class="form-label">Facility Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address" class="form-label">Street Address *</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="state" class="form-label">State *</label>
                        <select class="form-select" id="state" name="state" required>
                            <option value="">Select State</option>
                            <option value="CA">California</option>
                            <option value="NY">New York</option>
                            <option value="TX">Texas</option>
                            <option value="FL">Florida</option>
                            <option value="PA">Pennsylvania</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="zip" class="form-label">ZIP Code *</label>
                        <input type="text" class="form-control" id="zip" name="zip" required>
                    </div>
                </div>

                <!-- Contact Information -->
                <h4 style="margin: 1.5rem 0 1rem; color: #003366; font-weight: 600;">Contact Information</h4>

                <div class="form-group">
                    <label for="main_phone" class="form-label">Main Phone Number *</label>
                    <input type="tel" class="form-control" id="main_phone" name="main_phone" required>
                </div>

                <div class="form-group">
                    <label for="contact_email" class="form-label">Contact Email *</label>
                    <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                </div>

                <!-- Primary Contact -->
                <h4 style="margin: 1.5rem 0 1rem; color: #003366; font-weight: 600;">Primary Contact</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact1_name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="contact1_name" name="contact1_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact1_phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="contact1_phone" name="contact1_phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact1_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="contact1_email" name="contact1_email">
                </div>

                <!-- Secondary Contact -->
                <h4 style="margin: 1.5rem 0 1rem; color: #003366; font-weight: 600;">Secondary Contact (Optional)</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact2_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="contact2_name" name="contact2_name">
                    </div>
                    <div class="form-group">
                        <label for="contact2_phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="contact2_phone" name="contact2_phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact2_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="contact2_email" name="contact2_email">
                </div>

                <!-- Requirements & Settings -->
                <h4 style="margin: 1.5rem 0 1rem; color: #003366; font-weight: 600;">Requirements & Settings</h4>

                <div class="form-group">
                    <label for="clean_time_requirement" class="form-label">Minimum Clean Time (Years) *</label>
                    <input type="number" class="form-control" id="clean_time_requirement" name="clean_time_requirement" min="0" step="0.5" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Credentialing Types</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="cred_background" name="credentialing_types[]" value="background_check">
                            <label for="cred_background">Background Check</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="cred_reference" name="credentialing_types[]" value="reference_check">
                            <label for="cred_reference">Reference Check</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="cred_training" name="credentialing_types[]" value="training_certification">
                            <label for="cred_training">Training Certification</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="cred_medical" name="credentialing_types[]" value="medical_exam">
                            <label for="cred_medical">Medical Exam</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" id="gender_restriction" name="gender_restriction">
                            Gender Restriction
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="gender_type" class="form-label" style="display: none;" id="genderTypeLabel">Gender</label>
                        <select class="form-select" id="gender_type" name="gender_type" style="display: none;">
                            <option value="male">Male Only</option>
                            <option value="female">Female Only</option>
                        </select>
                    </div>
                </div>

                <div class="checkbox-item">
                    <input type="checkbox" id="probation_allowed" name="probation_allowed">
                    <label for="probation_allowed">Allow Volunteers on Probation</label>
                </div>

                <!-- Meeting Schedule — multiple recurring slots -->
                <h4 style="margin: 1.5rem 0 0.5rem; color: #003366; font-weight: 600;">Meeting Schedule</h4>
                <p style="font-size: 0.8rem; color: #666; margin-bottom: 1rem;">
                    Add one or more recurring meeting slots for this facility. Each slot defines when the meeting happens
                    each month and how many volunteers it needs (up to 5).
                </p>

                <div class="schedule-builder" id="meetingSlotsContainer">
                    <!-- Slots are injected here by JavaScript; first one added on page load -->
                </div>

                <button type="button" class="btn-add-slot" id="addMeetingSlotBtn" onclick="addMeetingSlot()">
                    <i class="fas fa-plus-circle"></i> Add Another Meeting Slot
                </button>

                <div class="checkbox-item">
                    <input type="checkbox" id="active" name="active" checked>
                    <label for="active">Active</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeFacilityModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Facility</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    // -------------------------------------------------------------------------
    // Meeting slot builder — dynamic, supports multiple recurring slots
    // -------------------------------------------------------------------------
    let slotCount = 0;

    function buildSlotHTML(index) {
        return `
        <div class="meeting-slot" id="slot-${index}">
            <div class="meeting-slot-header">
                <span><i class="fas fa-clock"></i> Meeting Slot #${index + 1}</span>
                <button type="button" class="btn-remove-slot" onclick="removeSlot(${index})" title="Remove this slot">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>

            <div class="schedule-row">
                <div class="form-group">
                    <label class="form-label">Week of Month *</label>
                    <select class="form-select" name="meetings[${index}][week_of_month]" required>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                        <option value="3">3rd</option>
                        <option value="4">4th</option>
                        <option value="5">Last</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Day of Week *</label>
                    <select class="form-select" name="meetings[${index}][day_of_week]" required>
                        <option value="0">Sunday</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Time *</label>
                    <input type="time" class="form-control" name="meetings[${index}][meeting_time]" required>
                </div>
            </div>

            <div class="schedule-row-wide">
                <div class="form-group">
                    <label class="form-label">Duration (min)</label>
                    <input type="number" class="form-control" name="meetings[${index}][duration_minutes]"
                           value="60" min="15" max="480" step="15">
                </div>
                <div class="form-group">
                    <label class="form-label">Format *</label>
                    <select class="form-select" name="meetings[${index}][format]" required>
                        <option value="in_person">In Person</option>
                        <option value="virtual">Virtual</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Volunteers Needed *</label>
                    <select class="form-select" name="meetings[${index}][volunteers_needed]" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; padding-bottom:0.25rem;">
                    <small style="color:#666; font-size:0.75rem; line-height:1.4;">
                        Max 5 volunteers per occurrence
                    </small>
                </div>
            </div>
        </div>`;
    }

    function addMeetingSlot() {
        const container = document.getElementById('meetingSlotsContainer');
        container.insertAdjacentHTML('beforeend', buildSlotHTML(slotCount));
        updateRemoveButtons();
        slotCount++;
    }

    function removeSlot(index) {
        const slot = document.getElementById('slot-' + index);
        if (slot) slot.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        // Hide remove button when only one slot remains
        const slots = document.querySelectorAll('.meeting-slot');
        slots.forEach(slot => {
            const btn = slot.querySelector('.btn-remove-slot');
            if (btn) btn.style.display = slots.length <= 1 ? 'none' : 'inline-flex';
        });
    }

    // Add the first slot automatically when the modal opens
    function openAddFacilityModal() {
        document.getElementById('modalTitle').textContent = 'Add New Facility';
        document.getElementById('facilityForm').reset();
        // Clear and rebuild slots
        document.getElementById('meetingSlotsContainer').innerHTML = '';
        slotCount = 0;
        addMeetingSlot();
        document.getElementById('facilityModal').classList.add('show');
    }

    function openEditFacilityModal(id) {
        document.getElementById('modalTitle').textContent = 'Edit Facility';
        document.getElementById('facilityModal').classList.add('show');
        // Load facility data (in real app)
    }

    function closeFacilityModal() {
        document.getElementById('facilityModal').classList.remove('show');
    }

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this facility? This action cannot be undone.')) {
            alert('Facility deleted successfully.');
        }
    }

    // Gender restriction toggle
    document.getElementById('gender_restriction').addEventListener('change', function() {
        const genderTypeField = document.getElementById('gender_type');
        const genderTypeLabel = document.getElementById('genderTypeLabel');
        if (this.checked) {
            genderTypeField.style.display = 'block';
            genderTypeLabel.style.display = 'block';
        } else {
            genderTypeField.style.display = 'none';
            genderTypeLabel.style.display = 'none';
        }
    });

    // Close modal when clicking outside
    document.getElementById('facilityModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFacilityModal();
        }
    });
</script>
@endsection
