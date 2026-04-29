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
        align-items: stretch;
    }

    .facility-card-footer > *,
    .facility-card-footer form {
        flex: 1;
        display: flex;
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
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
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

    /* ── Dark Mode ── */
    html.dark .facilities-title {
        color: #e2e8f0 !important;
    }

    html.dark .facility-card {
        background-color: #1a2235 !important;
        border-color: #2a3a50 !important;
    }

    html.dark .facility-card-body {
        background-color: #1a2235;
    }

    html.dark .facility-info-label {
        color: #94a3b8 !important;
    }

    html.dark .facility-info-value {
        color: #e2e8f0 !important;
    }

    html.dark .facility-info-item {
        border-color: #2a3a50 !important;
    }

    html.dark .facility-card-footer {
        background-color: #141d2e !important;
    }

    html.dark .facility-modal-dialog {
        background-color: #1a2235 !important;
        color: #e2e8f0;
    }

    html.dark .modal-body {
        background-color: #1a2235;
    }

    html.dark .modal-footer {
        background-color: #141d2e !important;
    }

    html.dark .form-label {
        color: #e2e8f0 !important;
    }

    html.dark .schedule-builder {
        background-color: #141d2e !important;
        border-color: #2a3a50 !important;
    }

    html.dark .meeting-slot {
        background-color: #1a2235 !important;
        border-color: #2a3a50 !important;
    }

    html.dark .meeting-slot-header {
        color: #93c5fd !important;
    }

    html.dark .btn-secondary {
        background-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }

    html.dark .btn-secondary:hover {
        background-color: #3a4a60 !important;
    }

    html.dark .empty-state {
        background-color: #1a2235 !important;
        color: #e2e8f0;
    }

    html.dark .empty-state-title {
        color: #94a3b8 !important;
    }

    html.dark .empty-state-text {
        color: #64748b !important;
    }

    html.dark .filter-section {
        background-color: #1a2235 !important;
        border-color: #2a3a50 !important;
    }

    html.dark .form-label {
        color: #e2e8f0 !important;
    }

    html.dark .form-control,
    html.dark .form-select {
        background-color: #141d2e !important;
        border-color: #2a3a50 !important;
        color: #e2e8f0 !important;
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

            {{-- Flash messages --}}
            @if(session('success'))
                <div style="background:#d4edda;color:#155724;border-radius:0.5rem;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-weight:500;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background:#f8d7da;color:#721c24;border-radius:0.5rem;padding:0.875rem 1.25rem;margin-bottom:1.5rem;font-weight:500;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('facilities.index') }}" style="margin-bottom:2rem;">
                <div class="filter-section" style="background:white;border-radius:0.75rem;padding:1.25rem 1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1rem;">
                        <div>
                            <label class="form-label" style="font-weight:600;color:#333;font-size:0.875rem;display:block;margin-bottom:0.4rem;">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                   placeholder="Name, city, or address…">
                        </div>
                        <div>
                            <label class="form-label" style="font-weight:600;color:#333;font-size:0.875rem;display:block;margin-bottom:0.4rem;">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                        <button type="submit" style="padding:0.65rem 1.4rem;background:#0099cc;color:white;border:none;border-radius:0.5rem;font-weight:600;cursor:pointer;">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('facilities.index') }}" style="padding:0.65rem 1.4rem;background:#e0e0e0;color:#333;border-radius:0.5rem;font-weight:600;text-decoration:none;display:inline-block;">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Facilities Grid -->
            <div class="facilities-grid">
                @forelse($facilities as $facility)
                @php
                    $meetingCount = $facility->meetings()->where('status','active')->count();
                    $cleanYears   = $facility->clean_time_requirement ?? 0;
                    $cleanLabel   = $cleanYears == 0
                        ? 'No requirement'
                        : ($cleanYears == 1 ? '1 year minimum' : "{$cleanYears} years minimum");
                @endphp
                <div class="facility-card">
                    <div class="facility-card-header">
                        <div class="facility-name">{{ $facility->facility_name }}</div>
                        <span class="facility-status">
                            @if($facility->status === 'active')
                                <i class="fas fa-check-circle"></i> Active
                            @else
                                <i class="fas fa-times-circle"></i> Inactive
                            @endif
                        </span>
                    </div>
                    <div class="facility-card-body">
                        <div class="facility-info-item">
                            <div class="facility-info-label">Address</div>
                            <div class="facility-info-value">{{ $facility->full_address }}</div>
                        </div>
                        @if($facility->contact1_name)
                        <div class="facility-info-item">
                            <div class="facility-info-label">Primary Contact</div>
                            <div class="facility-info-value">
                                {{ $facility->contact1_name }}
                                @if($facility->contact1_phone)| {{ $facility->contact1_phone }}@endif
                            </div>
                        </div>
                        @endif
                        <div class="facility-info-item">
                            <div class="facility-info-label">Clean Time Requirement</div>
                            <div class="facility-info-value">{{ $cleanLabel }}</div>
                        </div>
                        <div class="facility-info-item">
                            <div class="facility-info-label">Active Meeting Slots</div>
                            <div class="facility-info-value">{{ $meetingCount }} recurring {{ Str::plural('slot', $meetingCount) }}</div>
                        </div>
                    </div>
                    <div class="facility-card-footer">
                        {{-- Edit --}}
                        <div style="flex:1;display:flex;">
                            <button type="button" class="btn-small btn-edit" style="width:100%;"
                                    data-facility='@json($facility)'
                                    onclick="openEditFacilityModal(this)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                        {{-- Toggle active/inactive --}}
                        <form method="POST" action="{{ route('facilities.toggle-status', $facility) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-small"
                                    style="width:100%;background:{{ $facility->status === 'active' ? '#6c757d' : '#28a745' }};color:white;"
                                    title="{{ $facility->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $facility->status === 'active' ? 'pause' : 'play' }}-circle"></i>
                                {{ $facility->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        {{-- Delete --}}
                        <form method="POST" action="{{ route('facilities.destroy', $facility) }}"
                              onsubmit="return confirm('Delete {{ addslashes($facility->facility_name) }}? This will also remove all its meeting slots.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-small btn-delete" style="width:100%;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="grid-column:1/-1;">
                    <div class="empty-state-icon"><i class="fas fa-building"></i></div>
                    <div class="empty-state-title">No facilities found</div>
                    <div class="empty-state-text">
                        @if(request()->hasAny(['search','status']))
                            Try adjusting your filters or <a href="{{ route('facilities.index') }}">reset</a>.
                        @else
                            Add the first facility using the button above.
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($facilities->hasPages())
            <div style="display:flex;justify-content:center;margin-top:1.5rem;">
                {{ $facilities->appends(request()->query())->links() }}
            </div>
            @endif
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
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body">
                {{-- Server-side validation errors --}}
                @if($errors->any())
                <div id="modal-error-banner" style="background:#f8d7da;color:#721c24;border-radius:0.5rem;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.875rem;">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
                    <ul style="margin:0.4rem 0 0 1.25rem;padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Basic Information -->
                <h4 style="margin-bottom: 1rem; color: #003366; font-weight: 600;">Basic Information</h4>

                <div class="form-group">
                    <label for="name" class="form-label">Facility Name *</label>
                    <input type="text" class="form-control" id="name" name="facility_name" required>
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
                        <label for="state" class="form-label">State / Territory *</label>
                        <select class="form-control" id="state" name="state" required>
                            <option value="">— Select —</option>
                            @foreach(config('states.states') as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
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
                    <label for="contact_email" class="form-label">Contact Email</label>
                    <input type="email" class="form-control" id="contact_email" name="contact_email">
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
                            <input type="checkbox" id="gender_restriction" name="gender_restriction" value="1">
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
                    <input type="checkbox" id="probation_allowed" name="probation_allowed" value="1">
                    <label for="probation_allowed">Allow Volunteers on Probation</label>
                </div>

                <!-- Meeting Schedule — multiple recurring slots -->
                <div id="meetingScheduleSection">
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

                </div>{{-- #meetingScheduleSection --}}

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

    function openAddFacilityModal() {
        const form = document.getElementById('facilityForm');
        document.getElementById('modalTitle').textContent = 'Add New Facility';
        form.reset();
        form.action = '{{ route('facilities.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('meetingScheduleSection').style.display = '';
        document.getElementById('meetingSlotsContainer').innerHTML = '';
        slotCount = 0;
        addMeetingSlot();
        document.getElementById('facilityModal').classList.add('show');
    }

    function openEditFacilityModal(btn) {
        const f    = JSON.parse(btn.dataset.facility);
        const form = document.getElementById('facilityForm');

        document.getElementById('modalTitle').textContent = 'Edit Facility';
        form.action = '/facilities/' + f.facility_id;
        document.getElementById('formMethod').value = 'PATCH';

        // Basic info
        document.getElementById('name').value                   = f.facility_name  ?? '';
        document.getElementById('address').value               = f.address         ?? '';
        document.getElementById('city').value                  = f.city            ?? '';
        document.getElementById('state').value                 = (f.state          ?? '').toUpperCase();
        document.getElementById('zip').value                   = f.zip             ?? '';

        // Contact
        document.getElementById('main_phone').value            = f.main_phone      ?? '';
        document.getElementById('contact_email').value         = f.contact_email   ?? '';
        document.getElementById('contact1_name').value         = f.contact1_name   ?? '';
        document.getElementById('contact1_phone').value        = f.contact1_phone  ?? '';
        document.getElementById('contact1_email').value        = f.contact1_email  ?? '';
        document.getElementById('contact2_name').value         = f.contact2_name   ?? '';
        document.getElementById('contact2_phone').value        = f.contact2_phone  ?? '';
        document.getElementById('contact2_email').value        = f.contact2_email  ?? '';

        // Requirements
        document.getElementById('clean_time_requirement').value = f.clean_time_requirement ?? 0;

        const credTypes = f.credentialing_types ?? [];
        document.getElementById('cred_background').checked = credTypes.includes('background_check');
        document.getElementById('cred_reference').checked  = credTypes.includes('reference_check');
        document.getElementById('cred_training').checked   = credTypes.includes('training_certification');
        document.getElementById('cred_medical').checked    = credTypes.includes('medical_exam');

        document.getElementById('gender_restriction').checked = !!f.gender_restriction;
        document.getElementById('probation_allowed').checked  = !!f.probation_allowed;

        // Hide meeting schedule — update doesn't manage slots
        document.getElementById('meetingScheduleSection').style.display = 'none';

        document.getElementById('facilityModal').classList.add('show');
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

    // ── Client-side validation ──────────────────────────────────────────────
    const REQUIRED_FIELDS = [
        { id: 'name',                    label: 'Facility Name' },
        { id: 'address',                 label: 'Street Address' },
        { id: 'city',                    label: 'City' },
        { id: 'state',                   label: 'State' },
        { id: 'zip',                     label: 'ZIP Code' },
        { id: 'main_phone',              label: 'Main Phone Number' },
        { id: 'clean_time_requirement',  label: 'Minimum Clean Time' },
    ];

    function setFieldError(id, message) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (!el) return;
        el.style.borderColor = '#dc3545';
        if (err) { err.textContent = message; err.style.display = 'block'; }
    }

    function clearFieldError(id) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.style.borderColor = '';
        if (err) { err.textContent = ''; err.style.display = 'none'; }
    }

    function clearAllErrors() {
        REQUIRED_FIELDS.forEach(f => clearFieldError(f.id));
    }

    // Attach error spans after the DOM is ready
    REQUIRED_FIELDS.forEach(({ id }) => {
        const el = document.getElementById(id);
        if (!el) return;
        const span = document.createElement('span');
        span.id = 'err-' + id;
        span.style.cssText = 'color:#dc3545;font-size:0.78rem;display:none;margin-top:0.2rem;';
        el.insertAdjacentElement('afterend', span);
        el.addEventListener('input', () => clearFieldError(id));
        el.addEventListener('change', () => clearFieldError(id));
    });

    document.getElementById('facilityForm').addEventListener('submit', function(e) {
        clearAllErrors();
        let firstError = null;

        REQUIRED_FIELDS.forEach(({ id, label }) => {
            const el = document.getElementById(id);
            if (!el) return;
            if (el.value.trim() === '' || el.value === null) {
                setFieldError(id, label + ' is required.');
                if (!firstError) firstError = el;
            }
        });

        if (firstError) {
            e.preventDefault();
            firstError.focus();
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Clear errors when modal is closed
    const _origClose = closeFacilityModal;
    closeFacilityModal = function() { clearAllErrors(); _origClose(); };

    @if($errors->any())
    // ── Re-open the Add Facility modal with old() values after a validation failure ──
    (function () {
        const form = document.getElementById('facilityForm');
        document.getElementById('modalTitle').textContent = 'Add New Facility';
        form.action  = '{{ route('facilities.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('meetingScheduleSection').style.display = '';

        // Repopulate basic fields
        document.getElementById('name').value                    = @json(old('facility_name', ''));
        document.getElementById('address').value                 = @json(old('address', ''));
        document.getElementById('city').value                    = @json(old('city', ''));
        document.getElementById('state').value                   = @json(old('state', ''));
        document.getElementById('zip').value                     = @json(old('zip', ''));
        document.getElementById('main_phone').value              = @json(old('main_phone', ''));
        document.getElementById('contact_email').value           = @json(old('contact_email', ''));
        document.getElementById('clean_time_requirement').value  = @json(old('clean_time_requirement', 0));
        document.getElementById('contact1_name').value           = @json(old('contact1_name', ''));
        document.getElementById('contact1_phone').value          = @json(old('contact1_phone', ''));
        document.getElementById('contact1_email').value          = @json(old('contact1_email', ''));
        document.getElementById('contact2_name').value           = @json(old('contact2_name', ''));
        document.getElementById('contact2_phone').value          = @json(old('contact2_phone', ''));
        document.getElementById('contact2_email').value          = @json(old('contact2_email', ''));

        // Checkboxes
        document.getElementById('gender_restriction').checked = @json(old('gender_restriction') ? true : false);
        document.getElementById('probation_allowed').checked  = @json(old('probation_allowed') ? true : false);

        // Credentialing type checkboxes
        const oldCreds = @json(old('credentialing_types', []));
        document.getElementById('cred_background').checked = oldCreds.includes('background_check');
        document.getElementById('cred_reference').checked  = oldCreds.includes('reference_check');
        document.getElementById('cred_training').checked   = oldCreds.includes('training_certification');
        document.getElementById('cred_medical').checked    = oldCreds.includes('medical_exam');

        // Rebuild meeting slots from old input
        document.getElementById('meetingSlotsContainer').innerHTML = '';
        slotCount = 0;
        const oldSlots = @json(old('meetings', []));
        if (oldSlots && oldSlots.length > 0) {
            oldSlots.forEach(function (slot) {
                const idx  = slotCount;
                const html = buildSlotHTML(idx);
                document.getElementById('meetingSlotsContainer').insertAdjacentHTML('beforeend', html);
                slotCount++;
                // Restore values
                const el = id => document.querySelector(`[name="meetings[${idx}][${id}]"]`);
                if (el('week_of_month'))    el('week_of_month').value    = slot.week_of_month    ?? 1;
                if (el('day_of_week'))      el('day_of_week').value      = slot.day_of_week      ?? 0;
                if (el('meeting_time'))     el('meeting_time').value     = slot.meeting_time     ?? '';
                if (el('duration_minutes')) el('duration_minutes').value = slot.duration_minutes ?? 60;
                if (el('format'))           el('format').value           = slot.format           ?? 'in_person';
                if (el('volunteers_needed'))el('volunteers_needed').value= slot.volunteers_needed?? 1;
            });
        } else {
            addMeetingSlot();
        }
        updateRemoveButtons();

        document.getElementById('facilityModal').classList.add('show');
    })();
    @endif
</script>
@endsection
