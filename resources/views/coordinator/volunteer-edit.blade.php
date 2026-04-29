@extends('layouts.app')

@section('title', 'Edit Volunteer - ChronoSync')

@section('extra-styles')
<style>
    .edit-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .edit-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }

    .edit-subtitle {
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }

    .back-link {
        color: #0099cc;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1rem;
    }

    .back-link:hover { color: #003366; }

    .card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-weight: 600;
        color: #003366;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .form-grid.three-col {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        font-size: 0.85rem;
        display: block;
        margin-bottom: 0.4rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.65rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        color: #333;
        background: white;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.2);
        outline: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e0e0e0;
        background: #f8f9fa;
    }

    .btn {
        padding: 0.65rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: background-color 0.2s;
    }

    .btn-primary {
        background: #0099cc;
        color: white;
    }

    .btn-primary:hover { background: #003366; color: white; }

    .btn-secondary {
        background: #e0e0e0;
        color: #333;
    }

    .btn-secondary:hover { background: #d0d0d0; color: #333; }

    .btn-danger {
        background: #dc3545;
        color: white;
        padding: 0.4rem 0.75rem;
        font-size: 0.82rem;
    }

    .btn-danger:hover { background: #c82333; }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover { background: #218838; }

    /* Credentials table */
    .credentials-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }

    .credentials-table th {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .credentials-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e0e0e0;
        vertical-align: middle;
    }

    .credentials-table tr:last-child td {
        border-bottom: none;
    }

    .status-pill {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-approved { background: #d4edda; color: #155724; }
    .status-pending  { background: #fff3cd; color: #856404; }
    .status-denied   { background: #f8d7da; color: #721c24; }

    .add-credential-form {
        background: #f8f9fa;
        border: 1px dashed #ccc;
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-top: 1rem;
        display: none;
    }

    .add-credential-form.show {
        display: block;
    }

    .alert {
        padding: 0.85rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Dark mode */
    html.dark .edit-title { color: #e2e8f0 !important; }
    html.dark .edit-subtitle { color: #94a3b8 !important; }
    html.dark .card { background: #1a2235 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }
    html.dark .card-header { background: #1e2a3a !important; border-color: #2a3a50 !important; }
    html.dark .card-title { color: #93c5fd !important; }
    html.dark .form-label { color: #e2e8f0 !important; }
    html.dark .form-control,
    html.dark .form-select {
        background: #1e2a3a !important;
        border-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }
    html.dark .form-control:focus,
    html.dark .form-select:focus { border-color: #0099cc !important; }
    html.dark .form-actions { background: #1e2a3a !important; border-color: #2a3a50 !important; }
    html.dark .btn-secondary { background: #2a3a50 !important; color: #e2e8f0 !important; }
    html.dark .btn-secondary:hover { background: #3a4a60 !important; }
    html.dark .credentials-table th { background: #1e2a3a !important; color: #93c5fd !important; border-color: #2a3a50 !important; }
    html.dark .credentials-table td { border-color: #2a3a50 !important; color: #e2e8f0 !important; }
    html.dark .add-credential-form { background: #1e2a3a !important; border-color: #2a3a50 !important; }

    @media (max-width: 768px) {
        .form-grid,
        .form-grid.three-col { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">

            <a href="{{ route('volunteers.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Volunteers
            </a>

            <div class="edit-header">
                <div class="volunteer-avatar-lg" style="width:52px;height:52px;border-radius:50%;background:#0099cc;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                    {{ strtoupper(substr($volunteer->first_name,0,1).substr($volunteer->last_name,0,1)) }}
                </div>
                <div>
                    <h1 class="edit-title">{{ $volunteer->first_name }} {{ $volunteer->last_name }}</h1>
                    <div class="edit-subtitle">{{ $volunteer->email }}</div>
                </div>
                <div style="margin-left:auto;">
                    <a href="{{ route('volunteers.show', $volunteer->volunteer_id) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View Profile
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
                    <ul style="margin: 0.5rem 0 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Personal Information ── --}}
            <form method="POST" action="{{ route('coordinator.volunteers.update', $volunteer->volunteer_id) }}">
                @csrf
                @method('PATCH')

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-user"></i> Personal Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control"
                                       value="{{ old('first_name', $volunteer->first_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control"
                                       value="{{ old('last_name', $volunteer->last_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="{{ old('email', $volunteer->email) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control"
                                       value="{{ old('phone', $volunteer->phone) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control"
                                       value="{{ old('dob', $volunteer->dob?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-select" required>
                                    @foreach(['Male','Female','Non-Binary','Transgender','Prefer Not to Say','Other'] as $g)
                                        <option value="{{ $g }}" {{ old('gender', $volunteer->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="clean_date">Clean Date</label>
                                <input type="date" id="clean_date" name="clean_date" class="form-control"
                                       value="{{ old('clean_date', $volunteer->clean_date?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="probation_status">Probation Status</label>
                                <select id="probation_status" name="probation_status" class="form-select" required>
                                    <option value="not_probation" {{ old('probation_status', $volunteer->probation_status) === 'not_probation' ? 'selected' : '' }}>Not on Probation</option>
                                    <option value="active_probation" {{ old('probation_status', $volunteer->probation_status) === 'active_probation' ? 'selected' : '' }}>Active Probation</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Location & Transport ── --}}
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-map-marker-alt"></i> Location & Transport</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="neighborhood">Neighborhood</label>
                                <input type="text" id="neighborhood" name="neighborhood" class="form-control"
                                       value="{{ old('neighborhood', $volunteer->neighborhood) }}" placeholder="e.g. Downtown">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="bus_line">Primary Bus Line</label>
                                <input type="text" id="bus_line" name="bus_line" class="form-control"
                                       value="{{ old('bus_line', $volunteer->bus_line) }}" placeholder="e.g. Line 5">
                            </div>
                            <div class="form-group">
                                <label class="form-label">SMS Deliverable</label>
                                <select name="is_sms_deliverable" class="form-select">
                                    <option value="1" {{ old('is_sms_deliverable', $volunteer->is_sms_deliverable) ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !old('is_sms_deliverable', $volunteer->is_sms_deliverable) ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Treatment Facility ── --}}
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-building"></i> Treatment Facility</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="treatment_facility">Facility Type / Program</label>
                                <input type="text" id="treatment_facility" name="treatment_facility" class="form-control"
                                       value="{{ old('treatment_facility', $volunteer->treatment_facility) }}" placeholder="e.g. Inpatient, Outpatient">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="facility_name">Facility Name</label>
                                <input type="text" id="facility_name" name="facility_name" class="form-control"
                                       value="{{ old('facility_name', $volunteer->facility_name) }}" placeholder="e.g. Harmony House">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="discharge_date">Discharge Date</label>
                                <input type="date" id="discharge_date" name="discharge_date" class="form-control"
                                       value="{{ old('discharge_date', $volunteer->discharge_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('volunteers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>

            {{-- ── Credentials ── --}}
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-id-card"></i> Credentials</h2>
                    <button type="button" class="btn btn-success" id="toggle-add-credential" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">
                        <i class="fas fa-plus"></i> Add Credential
                    </button>
                </div>
                <div class="card-body" style="padding-top: 0;">

                    {{-- Add credential form (hidden by default) --}}
                    <div class="add-credential-form" id="add-credential-form">
                        <form method="POST" action="{{ route('coordinator.volunteers.credentials.store', $volunteer->volunteer_id) }}">
                            @csrf
                            <div class="form-grid three-col" style="margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Credential Type</label>
                                    <select name="credential_type_id" class="form-select" required>
                                        <option value="">Select type…</option>
                                        @foreach($credentialTypes as $ct)
                                            <option value="{{ $ct->credential_type_id }}">{{ $ct->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Facility</label>
                                    <select name="facility_id" class="form-select" required>
                                        <option value="">Select facility…</option>
                                        @foreach($facilities as $f)
                                            <option value="{{ $f->facility_id }}">{{ $f->facility_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="denied">Denied</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Approval Date</label>
                                    <input type="date" name="approval_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Expiration Date</label>
                                    <input type="date" name="expiration_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Notes</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Optional notes…">
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.75rem;">
                                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">
                                    <i class="fas fa-save"></i> Save Credential
                                </button>
                                <button type="button" id="cancel-add-credential" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Existing credentials --}}
                    @if($volunteer->credentials->isEmpty())
                        <p style="color: #999; font-size: 0.9rem; padding: 1rem 0 0;">No credentials on file for this volunteer.</p>
                    @else
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="credentials-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Facility</th>
                                    <th>Status</th>
                                    <th>Approval Date</th>
                                    <th>Expiration Date</th>
                                    <th>Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($volunteer->credentials as $cred)
                                <tr>
                                    <td>{{ $cred->credentialType?->display_name ?? '—' }}</td>
                                    <td>{{ $cred->facility?->facility_name ?? '—' }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $cred->status }}">
                                            {{ ucfirst($cred->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $cred->approval_date ? \Carbon\Carbon::parse($cred->approval_date)->format('M d, Y') : '—' }}</td>
                                    <td>{{ $cred->expiration_date ? \Carbon\Carbon::parse($cred->expiration_date)->format('M d, Y') : '—' }}</td>
                                    <td>{{ $cred->notes ?? '—' }}</td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    style="padding: 0.35rem 0.7rem; font-size: 0.78rem;"
                                                    onclick="toggleEditCredential('{{ $cred->credential_id }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST"
                                                  action="{{ route('coordinator.volunteers.credentials.destroy', [$volunteer->volunteer_id, $cred->credential_id]) }}"
                                                  onsubmit="return confirm('Remove this credential?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        {{-- Inline edit form --}}
                                        <div id="edit-cred-{{ $cred->credential_id }}" style="display:none; margin-top:0.75rem;">
                                            <form method="POST"
                                                  action="{{ route('coordinator.volunteers.credentials.update', [$volunteer->volunteer_id, $cred->credential_id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
                                                    <div>
                                                        <label class="form-label" style="font-size:0.78rem;">Status</label>
                                                        <select name="status" class="form-select" style="padding:0.4rem 0.6rem; font-size:0.82rem;">
                                                            <option value="pending"  {{ $cred->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $cred->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="denied"   {{ $cred->status === 'denied'   ? 'selected' : '' }}>Denied</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="form-label" style="font-size:0.78rem;">Approval Date</label>
                                                        <input type="date" name="approval_date" class="form-control"
                                                               style="padding:0.4rem 0.6rem; font-size:0.82rem;"
                                                               value="{{ $cred->approval_date ? \Carbon\Carbon::parse($cred->approval_date)->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label" style="font-size:0.78rem;">Expiration Date</label>
                                                        <input type="date" name="expiration_date" class="form-control"
                                                               style="padding:0.4rem 0.6rem; font-size:0.82rem;"
                                                               value="{{ $cred->expiration_date ? \Carbon\Carbon::parse($cred->expiration_date)->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label" style="font-size:0.78rem;">Notes</label>
                                                        <input type="text" name="notes" class="form-control"
                                                               style="padding:0.4rem 0.6rem; font-size:0.82rem;"
                                                               value="{{ $cred->notes }}">
                                                    </div>
                                                </div>
                                                <div style="display:flex; gap:0.5rem;">
                                                    <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.85rem; font-size:0.8rem;">
                                                        <i class="fas fa-save"></i> Save
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" style="padding:0.35rem 0.85rem; font-size:0.8rem;"
                                                            onclick="toggleEditCredential('{{ $cred->credential_id }}')">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    const addForm   = document.getElementById('add-credential-form');
    const toggleBtn = document.getElementById('toggle-add-credential');
    const cancelBtn = document.getElementById('cancel-add-credential');

    toggleBtn.addEventListener('click', () => {
        addForm.classList.toggle('show');
        toggleBtn.innerHTML = addForm.classList.contains('show')
            ? '<i class="fas fa-times"></i> Cancel'
            : '<i class="fas fa-plus"></i> Add Credential';
    });

    cancelBtn.addEventListener('click', () => {
        addForm.classList.remove('show');
        toggleBtn.innerHTML = '<i class="fas fa-plus"></i> Add Credential';
    });

    function toggleEditCredential(id) {
        const el = document.getElementById('edit-cred-' + id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endsection
