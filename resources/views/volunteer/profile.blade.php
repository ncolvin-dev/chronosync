@extends('layouts.app')

@section('title', 'Profile - ChronoSync')

@section('extra-styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1rem;
        font-weight: 700;
        border: 3px solid white;
    }

    .profile-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .profile-email {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .status-badges {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-info {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .collapsible-section {
        margin-bottom: 2rem;
    }

    .collapsible-header {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
        transition: background-color 0.3s;
        border: 1px solid #e0e0e0;
    }

    .collapsible-header:hover {
        background-color: #e9ecef;
    }

    .collapsible-header.collapsed {
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .collapsible-header-title {
        font-weight: 600;
        color: #003366;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .collapsible-header-icon {
        color: #0099cc;
        transition: transform 0.3s;
    }

    .collapsible-header.collapsed .collapsible-header-icon {
        transform: rotate(-90deg);
    }

    .collapsible-content {
        display: none;
        border: 1px solid #e0e0e0;
        border-top: none;
        padding: 1.5rem;
        background-color: white;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .collapsible-content.show {
        display: block;
    }

    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .info-row.full {
        grid-template-columns: 1fr;
    }

    .info-item {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: #333;
        font-size: 1rem;
    }

    .edit-form {
        display: none;
    }

    .edit-form.show {
        display: block;
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
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 0.75rem;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .button-group button {
        flex: 1;
        padding: 0.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #0099cc;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #003366;
    }

    .btn-secondary {
        background-color: #e0e0e0;
        color: #333;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #d0d0d0;
    }

    .audit-trail {
        background-color: #f8f9fa;
        border-left: 4px solid #0099cc;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

    .audit-item {
        margin-bottom: 0.75rem;
        color: #666;
    }

    .audit-item:last-child {
        margin-bottom: 0;
    }

    .audit-date {
        font-weight: 600;
        color: #333;
    }

    .edit-button-inline {
        background-color: transparent;
        color: #0099cc;
        border: 1px solid #0099cc;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .edit-button-inline:hover {
        background-color: #0099cc;
        color: white;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 2rem 1rem;
        }

        .status-badges {
            flex-direction: column;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .button-group {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                </div>
                <h1 class="profile-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h1>
                <p class="profile-email"><i class="fas fa-envelope"></i> {{ auth()->user()->email }}</p>
                <div class="status-badges">
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                    <span class="badge badge-info">
                        <i class="fas fa-calendar"></i> Clean Time:
                        @php
                            $cleanDate = auth()->user()->clean_date;
                            $years = \Carbon\Carbon::parse($cleanDate)->diffInYears(\Carbon\Carbon::now());
                            $months = \Carbon\Carbon::parse($cleanDate)->addYears($years)->diffInMonths(\Carbon\Carbon::now());
                        @endphp
                        {{ $years }}y {{ $months }}m
                    </span>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="collapsible-section">
                <div class="collapsible-header collapsed" data-target="personal-info">
                    <div class="collapsible-header-title">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    <i class="fas fa-chevron-down collapsible-header-icon"></i>
                </div>

                <div class="collapsible-content" id="personal-info">
                    <div class="info-content" id="personal-info-display">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">First Name</div>
                                <div class="info-value">{{ auth()->user()->first_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value">{{ auth()->user()->last_name }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ auth()->user()->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ auth()->user()->phone }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('M d, Y') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ ucfirst(auth()->user()->gender) }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Clean Date</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse(auth()->user()->clean_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <button class="edit-button-inline" data-edit-section="personal-info">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="personal-info-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ auth()->user()->first_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ auth()->user()->last_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone }}" required>
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ auth()->user()->date_of_birth }}" required>
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" {{ auth()->user()->gender === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ auth()->user()->gender === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="non-binary" {{ auth()->user()->gender === 'non-binary' ? 'selected' : '' }}>Non-Binary</option>
                                <option value="transgender" {{ auth()->user()->gender === 'transgender' ? 'selected' : '' }}>Transgender</option>
                                <option value="prefer-not-to-say" {{ auth()->user()->gender === 'prefer-not-to-say' ? 'selected' : '' }}>Prefer Not to Say</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="clean_date" class="form-label">Clean Date</label>
                            <input type="date" class="form-control" id="clean_date" name="clean_date" value="{{ auth()->user()->clean_date }}" required>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn-secondary" data-cancel-edit="personal-info">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transportation & Location Section -->
            <div class="collapsible-section">
                <div class="collapsible-header collapsed" data-target="transportation">
                    <div class="collapsible-header-title">
                        <i class="fas fa-map-marker-alt"></i> Transportation & Location
                    </div>
                    <i class="fas fa-chevron-down collapsible-header-icon"></i>
                </div>

                <div class="collapsible-content" id="transportation">
                    <div class="info-content" id="transportation-display">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Neighborhood</div>
                                <div class="info-value">{{ auth()->user()->neighborhood ?? 'Not provided' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Primary Bus Line</div>
                                <div class="info-value">{{ auth()->user()->bus_line ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        <button class="edit-button-inline" data-edit-section="transportation">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="transportation-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="neighborhood" class="form-label">Neighborhood</label>
                            <input type="text" class="form-control" id="neighborhood" name="neighborhood" value="{{ auth()->user()->neighborhood }}" placeholder="e.g., Downtown, Eastside">
                        </div>

                        <div class="form-group">
                            <label for="bus_line" class="form-label">Primary Bus Line</label>
                            <input type="text" class="form-control" id="bus_line" name="bus_line" value="{{ auth()->user()->bus_line }}" placeholder="e.g., Line 5, Line 12">
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn-secondary" data-cancel-edit="transportation">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Facility & Probation Section -->
            <div class="collapsible-section">
                <div class="collapsible-header collapsed" data-target="facility-probation">
                    <div class="collapsible-header-title">
                        <i class="fas fa-building"></i> Facility & Probation Status
                    </div>
                    <i class="fas fa-chevron-down collapsible-header-icon"></i>
                </div>

                <div class="collapsible-content" id="facility-probation">
                    <div class="info-content" id="facility-probation-display">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Treatment Facility Experience</div>
                                <div class="info-value">
                                    @if(auth()->user()->has_treatment_facility)
                                        <span class="badge badge-success">Yes</span>
                                        @if(auth()->user()->treatment_facility_name)
                                            <div style="margin-top: 0.5rem; font-size: 0.9rem;">{{ auth()->user()->treatment_facility_name }}</div>
                                        @endif
                                    @else
                                        <span class="badge" style="background-color: #6c757d; color: white;">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Probation Status</div>
                                <div class="info-value">
                                    @if(auth()->user()->on_probation)
                                        <span class="badge" style="background-color: #ffc107; color: #333;">Currently on Probation</span>
                                    @else
                                        <span class="badge badge-success">Not on Probation</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button class="edit-button-inline" data-edit-section="facility-probation">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="facility-probation-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Treatment Facility Experience</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="has_treatment_yes" name="has_treatment_facility" value="1" {{ auth()->user()->has_treatment_facility ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_treatment_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="has_treatment_no" name="has_treatment_facility" value="0" {{ !auth()->user()->has_treatment_facility ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_treatment_no">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="treatment-detail-field" style="display: {{ auth()->user()->has_treatment_facility ? 'block' : 'none' }};">
                            <label for="treatment_facility_name" class="form-label">Facility Name</label>
                            <input type="text" class="form-control" id="treatment_facility_name" name="treatment_facility_name" value="{{ auth()->user()->treatment_facility_name }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Probation Status</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="probation_yes" name="on_probation" value="1" {{ auth()->user()->on_probation ? 'checked' : '' }}>
                                    <label class="form-check-label" for="probation_yes">Currently on Probation</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="probation_no" name="on_probation" value="0" {{ !auth()->user()->on_probation ? 'checked' : '' }}>
                                    <label class="form-check-label" for="probation_no">Not on Probation</label>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn-secondary" data-cancel-edit="facility-probation">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="audit-trail">
                <div style="font-weight: 600; margin-bottom: 1rem; color: #333;">
                    <i class="fas fa-history"></i> Recent Changes
                </div>
                <div class="audit-item">
                    <span class="audit-date">Profile Last Updated:</span> {{ auth()->user()->updated_at->format('M d, Y at h:i A') }}
                </div>
                <div class="audit-item">
                    <span class="audit-date">Account Created:</span> {{ auth()->user()->created_at->format('M d, Y at h:i A') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Collapsible sections
    document.querySelectorAll('.collapsible-header').forEach(header => {
        header.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const content = document.getElementById(target);

            this.classList.toggle('collapsed');
            content.classList.toggle('show');
        });
    });

    // Edit button handlers
    document.querySelectorAll('[data-edit-section]').forEach(button => {
        button.addEventListener('click', function() {
            const section = this.getAttribute('data-edit-section');
            const display = document.getElementById(section + '-display');
            const form = document.getElementById(section + '-form');

            display.style.display = 'none';
            form.classList.add('show');
        });
    });

    // Cancel edit handlers
    document.querySelectorAll('[data-cancel-edit]').forEach(button => {
        button.addEventListener('click', function() {
            const section = this.getAttribute('data-cancel-edit');
            const display = document.getElementById(section + '-display');
            const form = document.getElementById(section + '-form');

            display.style.display = 'block';
            form.classList.remove('show');
        });
    });

    // Toggle treatment facility field
    const hasTreatmentYes = document.getElementById('has_treatment_yes');
    const hasTreatmentNo = document.getElementById('has_treatment_no');
    const treatmentDetailField = document.getElementById('treatment-detail-field');

    if (hasTreatmentYes && hasTreatmentNo && treatmentDetailField) {
        hasTreatmentYes.addEventListener('change', function() {
            treatmentDetailField.style.display = 'block';
        });

        hasTreatmentNo.addEventListener('change', function() {
            treatmentDetailField.style.display = 'none';
        });
    }
</script>
@endsection
