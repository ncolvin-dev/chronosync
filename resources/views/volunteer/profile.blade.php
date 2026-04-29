@extends('layouts.app')

@section('title', 'Profile - ChronoSync')

@section('extra-styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@include('volunteer.profile-styles')
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">
            @if($readOnly ?? false)
            <div style="margin-bottom: 1rem; display: flex; gap: 0.75rem; align-items: center;">
                <a href="{{ route('volunteers.index') }}" style="color: #0099cc; font-size: 0.9rem; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back to Volunteers
                </a>
                <a href="{{ route('volunteers.edit', $volunteer->volunteer_id) }}"
                   style="margin-left: auto; padding: 0.5rem 1rem; background: #003366; color: white; border-radius: 0.5rem; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-edit"></i> Edit Volunteer
                </a>
            </div>
            @endif

            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($volunteer->first_name, 0, 1) . substr($volunteer->last_name, 0, 1)) }}
                </div>
                <h1 class="profile-name">{{ $volunteer->first_name }} {{ $volunteer->last_name }}</h1>
                <p class="profile-email"><i class="fas fa-envelope"></i> {{ $volunteer->email }}</p>
                <div class="status-badges">
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                    <span class="badge badge-info">
                        <i class="fas fa-calendar"></i> Clean Time:
                        @php
                            $cleanDate = $volunteer->clean_date;
                            $years = (int)\Carbon\Carbon::parse($cleanDate)->diffInYears(\Carbon\Carbon::now());
                            $months = (int)\Carbon\Carbon::parse($cleanDate)->addYears($years)->diffInMonths(\Carbon\Carbon::now());
                        @endphp
                        {{ $years }} yrs {{ $months }} mo
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
                                <div class="info-value">{{ $volunteer->first_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value">{{ $volunteer->last_name }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $volunteer->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ $volunteer->phone }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($volunteer->dob)->format('M d, Y') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ ucfirst($volunteer->gender) }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Clean Date</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($volunteer->clean_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @unless($readOnly ?? false)
                        <button class="edit-button-inline" data-edit-section="personal-info">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        @endunless
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="personal-info-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $volunteer->first_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $volunteer->last_name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $volunteer->email }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ $volunteer->phone }}" required>
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="text" class="form-control datepicker" id="date_of_birth" name="date_of_birth" value="{{ $volunteer->dob?->format('Y-m-d') }}" placeholder="YYYY-MM-DD" required>
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" {{ $volunteer->gender === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $volunteer->gender === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="non-binary" {{ $volunteer->gender === 'non-binary' ? 'selected' : '' }}>Non-Binary</option>
                                <option value="transgender" {{ $volunteer->gender === 'transgender' ? 'selected' : '' }}>Transgender</option>
                                <option value="prefer-not-to-say" {{ $volunteer->gender === 'prefer-not-to-say' ? 'selected' : '' }}>Prefer Not to Say</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="clean_date" class="form-label">Clean Date</label>
                            <input type="text" class="form-control datepicker" id="clean_date" name="clean_date" value="{{ $volunteer->clean_date?->format('Y-m-d') }}" placeholder="YYYY-MM-DD" required>
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
                                <div class="info-value">{{ $volunteer->neighborhood ?? 'Not provided' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Primary Bus Line</div>
                                <div class="info-value">{{ $volunteer->bus_line ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        @unless($readOnly ?? false)
                        <button class="edit-button-inline" data-edit-section="transportation">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        @endunless
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="transportation-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="neighborhood" class="form-label">Neighborhood</label>
                            <input type="text" class="form-control" id="neighborhood" name="neighborhood" value="{{ $volunteer->neighborhood }}" placeholder="e.g., Downtown, Eastside">
                        </div>

                        <div class="form-group">
                            <label for="bus_line" class="form-label">Primary Bus Line</label>
                            <input type="text" class="form-control" id="bus_line" name="bus_line" value="{{ $volunteer->bus_line }}" placeholder="e.g., Line 5, Line 12">
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
                                    @if($volunteer->treatment_facility)
                                        <span class="badge badge-success">Yes</span>
                                        @if($volunteer->facility_name)
                                            <div style="margin-top: 0.5rem; font-size: 0.9rem;">{{ $volunteer->facility_name }}</div>
                                        @endif
                                    @else
                                        <span class="badge" style="background-color: #6c757d; color: white;">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Probation Status</div>
                                <div class="info-value">
                                    @if($volunteer->isOnProbation())
                                        <span class="badge" style="background-color: #ffc107; color: #333;">Currently on Probation</span>
                                    @else
                                        <span class="badge badge-success">Not on Probation</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @unless($readOnly ?? false)
                        <button class="edit-button-inline" data-edit-section="facility-probation">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        @endunless
                    </div>

                    <form method="POST" action="{{ route('volunteer.profile.update') }}" class="edit-form" id="facility-probation-form" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Treatment Facility Experience</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="has_treatment_yes" name="has_treatment_facility" value="1" {{ $volunteer->treatment_facility ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_treatment_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="has_treatment_no" name="has_treatment_facility" value="0" {{ !$volunteer->treatment_facility ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_treatment_no">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="treatment-detail-field" style="display: {{ $volunteer->treatment_facility ? 'block' : 'none' }};">
                            <label for="treatment_facility_name" class="form-label">Facility Name</label>
                            <input type="text" class="form-control" id="treatment_facility_name" name="treatment_facility_name" value="{{ $volunteer->facility_name }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Probation Status</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="probation_yes" name="on_probation" value="1" {{ $volunteer->isOnProbation() ? 'checked' : '' }}>
                                    <label class="form-check-label" for="probation_yes">Currently on Probation</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="probation_no" name="on_probation" value="0" {{ !$volunteer->isOnProbation() ? 'checked' : '' }}>
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

            <!-- Credentials Section (coordinator read-only view) -->
            @if($readOnly ?? false)
            <div class="collapsible-section">
                <div class="collapsible-header collapsed" data-target="credentials-section">
                    <div class="collapsible-header-title">
                        <i class="fas fa-id-card"></i> Credentials
                    </div>
                    <i class="fas fa-chevron-down collapsible-header-icon"></i>
                </div>
                <div class="collapsible-content" id="credentials-section">
                    @forelse($volunteer->credentials as $cred)
                    <div style="display: flex; gap: 2rem; padding: 1rem 0; border-bottom: 1px solid #e0e0e0; flex-wrap: wrap;">
                        <div style="min-width: 160px;">
                            <div class="info-label">Type</div>
                            <div class="info-value">{{ $cred->credentialType?->display_name ?? '—' }}</div>
                        </div>
                        <div style="min-width: 100px;">
                            <div class="info-label">Status</div>
                            <div class="info-value">{{ ucfirst($cred->status) }}</div>
                        </div>
                        <div style="min-width: 140px;">
                            <div class="info-label">Approval Date</div>
                            <div class="info-value">{{ $cred->approval_date ? \Carbon\Carbon::parse($cred->approval_date)->format('M d, Y') : '—' }}</div>
                        </div>
                        <div style="min-width: 140px;">
                            <div class="info-label">Expiration Date</div>
                            <div class="info-value">{{ $cred->expiration_date ? \Carbon\Carbon::parse($cred->expiration_date)->format('M d, Y') : '—' }}</div>
                        </div>
                    </div>
                    @empty
                    <p style="color: #999; font-size: 0.9rem; margin: 0;">No credentials on file.</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Audit Trail -->
            <div class="audit-trail">
                <div style="font-weight: 600; margin-bottom: 1rem; color: #333;">
                    <i class="fas fa-history"></i> Recent Changes
                </div>
                <div class="audit-item">
                    <span class="audit-date">Profile Last Updated:</span> {{ $volunteer->updated_at->format('M j, Y g:i A') }}
                </div>
                <div class="audit-item">
                    <span class="audit-date">Account Created:</span> {{ $volunteer->created_at->format('M j, Y g:i A') }}
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

    // Date pickers
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
        allowInput: true,
        yearSelectorType: 'input',
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
