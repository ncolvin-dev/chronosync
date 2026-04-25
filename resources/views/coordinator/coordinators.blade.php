@extends('layouts.app')

@section('title', 'Coordinators - ChronoSync')

@section('extra-styles')
<style>
    .coordinators-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .coordinators-title {
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
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-add:hover { background-color: #003366; }

    /* ── Coordinator Modal ── */
    .coord-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1080;
        overflow-y: auto;
        padding: 5rem 1rem 2rem;
    }

    .coord-modal-overlay.show { display: block; }

    .coord-modal-dialog {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
    }

    .coord-modal-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .coord-modal-title { font-size: 1.1rem; font-weight: 700; }

    .coord-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }

    .coord-modal-body { padding: 1.5rem; }

    .coord-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .coord-form-group { margin-bottom: 1.1rem; }

    .coord-form-label {
        font-weight: 600;
        color: #333;
        font-size: 0.875rem;
        display: block;
        margin-bottom: 0.35rem;
    }

    .coord-form-control,
    .coord-form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        height: 2.6rem;
        transition: border-color 0.2s;
    }

    .coord-form-control:focus,
    .coord-form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0,153,204,0.2);
        outline: none;
    }

    .coord-form-text {
        font-size: 0.72rem;
        color: #999;
        margin-top: 0.2rem;
    }

    .coord-error-msg {
        font-size: 0.75rem;
        color: #dc3545;
        margin-top: 0.2rem;
        display: none;
    }

    .coord-pw-strength {
        height: 5px;
        background: #e0e0e0;
        border-radius: 3px;
        margin-top: 0.4rem;
        overflow: hidden;
    }

    .coord-pw-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: all 0.3s;
    }

    .coord-pw-bar.weak   { width: 33%; background: #dc3545; }
    .coord-pw-bar.fair   { width: 66%; background: #ffc107; }
    .coord-pw-bar.strong { width: 100%; background: #28a745; }

    .coord-pw-text { font-size: 0.72rem; font-weight: 600; margin-top: 0.2rem; }
    .coord-pw-text.weak   { color: #dc3545; }
    .coord-pw-text.fair   { color: #ffc107; }
    .coord-pw-text.strong { color: #28a745; }

    .btn-coord-primary {
        background: #0099cc;
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-coord-primary:hover { background: #003366; }

    .btn-coord-secondary {
        background: #e0e0e0;
        color: #333;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-coord-secondary:hover { background: #ccc; }

    /* Dark mode modal */
    html.dark .coord-modal-dialog { background: #1a2235; }
    html.dark .coord-modal-footer { border-top-color: #2a3a50; }
    html.dark #c_email_display { background: #0f1929 !important; border-color: #2a3a50 !important; color: #94a3b8 !important; }
    html.dark .coord-form-label { color: #cbd5e1; }
    html.dark .coord-form-text  { color: #64748b; }
    html.dark .coord-form-control,
    html.dark .coord-form-select {
        background: #141d2e;
        border-color: #2a3a50;
        color: #e2e8f0;
    }
    html.dark .coord-form-control::placeholder { color: #4a5a6a; }
    html.dark .coord-form-control:focus,
    html.dark .coord-form-select:focus {
        background: #141d2e;
        border-color: #0099cc;
        color: #e2e8f0;
    }
    html.dark .coord-form-select option { background: #1a2235; color: #e2e8f0; }
    html.dark .coord-pw-strength { background: #2a3a50; }
    html.dark .btn-coord-primary { background: #0099cc !important; color: #ffffff !important; }
    html.dark .btn-coord-primary:hover { background: #003366 !important; color: #ffffff !important; }
    html.dark .btn-coord-secondary { background: #2a3a50; color: #cbd5e1; }
    html.dark .btn-coord-secondary:hover { background: #374f6b; }

    /* Search & filter */
    .search-and-filter {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .filter-title { font-weight: 600; color: #333; margin-bottom: 1.5rem; }

    .filter-controls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group { margin-bottom: 0; }

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

    .btn-filter:hover { background-color: #003366; }

    .btn-reset {
        padding: 0.75rem 1.5rem;
        background-color: #e0e0e0;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset:hover { background-color: #d0d0d0; }

    /* Coordinator list */
    .coordinators-list-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .coordinator-row {
        display: flex;
        gap: 2rem;
        padding: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        align-items: center;
        transition: background-color 0.15s;
    }

    .coordinator-row:last-child { border-bottom: none; }
    .coordinator-row:hover { background-color: #f8f9fa; }

    .coordinator-identity {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex: 2;
        min-width: 0;
    }

    .coordinator-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: #003366;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .coordinator-email {
        font-weight: 600;
        color: #003366;
        font-size: 0.95rem;
        word-break: break-all;
    }

    .coordinator-details {
        display: flex;
        gap: 2rem;
        flex: 2;
        flex-wrap: wrap;
        align-items: center;
    }

    .coord-info-item { min-width: 100px; }

    .coord-info-label {
        font-size: 0.7rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .coord-info-value {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .coordinator-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .btn-small {
        padding: 0.45rem 0.9rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.82rem;
        white-space: nowrap;
        transition: background-color 0.2s;
    }

    .btn-edit {
        background-color: #e8f0fe;
        color: #003366;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-edit:hover {
        background-color: #003366;
        color: white;
    }

    .btn-danger-sm {
        background-color: #fde8e8;
        color: #b91c1c;
    }

    .btn-danger-sm:hover {
        background-color: #b91c1c;
        color: white;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .role-coordinator { background-color: #dbeafe; color: #1e40af; }
    .role-admin       { background-color: #fce7f3; color: #9d174d; }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.35rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .pagination li a,
    .pagination li span {
        display: inline-block;
        padding: 0.5rem 0.85rem;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        transition: all 0.2s;
        color: #003366;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        line-height: 1;
    }

    .pagination li a:hover {
        background-color: #0099cc;
        border-color: #0099cc;
        color: white;
    }

    .pagination li.active span {
        background-color: #0099cc;
        border-color: #0099cc;
        color: white;
    }

    .pagination li.disabled span {
        opacity: 0.45;
        cursor: not-allowed;
        color: #999;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .empty-state-icon { font-size: 3rem; color: #d0d0d0; margin-bottom: 1rem; }
    .empty-state-title { color: #666; font-weight: 600; margin-bottom: 0.5rem; }
    .empty-state-text  { color: #999; font-size: 0.875rem; }

    /* ── Dark Mode ── */
    html.dark .coordinators-title { color: #e2e8f0 !important; }
    html.dark .search-and-filter { background-color: #1a2235 !important; border-color: #2a3a50 !important; }
    html.dark .filter-title { color: #e2e8f0 !important; }
    html.dark .form-label { color: #e2e8f0 !important; }
    html.dark .form-control,
    html.dark .form-select {
        background-color: #0f1117 !important;
        border-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }
    html.dark .coordinators-list-container { background-color: #1a2235 !important; }
    html.dark .coordinator-row { border-color: #2a3a50 !important; }
    html.dark .coordinator-row:hover { background-color: #1e2a3a !important; }
    html.dark .coordinator-email { color: #93c5fd !important; }
    html.dark .coord-info-label { color: #64748b !important; }
    html.dark .coord-info-value { color: #e2e8f0 !important; }
    html.dark .role-coordinator { background-color: #1e3a6e; color: #93c5fd; }
    html.dark .role-admin       { background-color: #4c1a36; color: #f9a8d4; }
    html.dark .btn-edit { background-color: #1e2a3a !important; color: #93c5fd !important; }
    html.dark .btn-edit:hover { background-color: #003366 !important; color: white !important; }
    html.dark .btn-danger-sm { background-color: #450a0a !important; color: #fca5a5 !important; }
    html.dark .btn-danger-sm:hover { background-color: #b91c1c !important; color: white !important; }
    html.dark .btn-reset { background-color: #2a3a50 !important; color: #e2e8f0 !important; }
    html.dark .btn-reset:hover { background-color: #3a4a60 !important; }
    html.dark .pagination li a,
    html.dark .pagination li span {
        background-color: #1a2235 !important;
        border-color: #2a3a50 !important;
        color: #93c5fd !important;
    }
    html.dark .pagination li a:hover {
        background-color: #1e3a5f !important;
        border-color: #2a4a72 !important;
        color: #bfdbfe !important;
    }
    html.dark .pagination li.active span {
        background-color: #1e3a5f !important;
        border-color: #2a4a72 !important;
        color: #bfdbfe !important;
    }
    html.dark .pagination li.disabled span { color: #475569 !important; }
    html.dark .empty-state { background-color: #1a2235 !important; }
    html.dark .empty-state-title { color: #94a3b8 !important; }
    html.dark .empty-state-text  { color: #64748b !important; }

    @media (max-width: 768px) {
        .coordinator-row { flex-wrap: wrap; gap: 1rem; }
        .coordinator-details { gap: 1rem; }
        .filter-controls { grid-template-columns: 1fr; }
        .filter-actions { flex-direction: column; }
        .btn-filter, .btn-reset { width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="coordinators-header">
                <h1 class="coordinators-title">
                    <i class="fas fa-user-tie"></i> Coordinator Management
                </h1>
                <button class="btn-add" onclick="openAddCoordinatorModal()">
                    <i class="fas fa-plus"></i> Add Coordinator
                </button>
            </div>

            <!-- Search and Filters -->
            <form method="GET" action="{{ route('coordinators.index') }}" id="filter-form">
            <div class="search-and-filter">
                <div class="filter-title">Search & Filter</div>
                <div class="filter-controls">
                    <div class="form-group">
                        <label for="search" class="form-label">Search by Email</label>
                        <input
                            type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Enter email address..."
                        >
                    </div>
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All</option>
                            <option value="coordinator" {{ request('role') === 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                            <option value="admin"       {{ request('role') === 'admin'       ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('coordinators.index') }}" class="btn-reset" style="display:inline-block; text-decoration:none;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
            </form>

            <!-- Coordinator List -->
            <div class="coordinators-list-container">
                @forelse($coordinators as $coordinator)
                @php
                    $initial = strtoupper(substr($coordinator->email, 0, 1));
                    $isAdmin = in_array('admin', $coordinator->roles ?? []);
                    $isCoord = in_array('coordinator', $coordinator->roles ?? []);
                    $isSelf  = auth()->id() === $coordinator->user_id;
                @endphp
                <div class="coordinator-row">
                    <div class="coordinator-identity">
                        <div class="coordinator-avatar">{{ $initial }}</div>
                        <div>
                            <div class="coordinator-email">{{ $coordinator->email }}</div>
                            <div style="margin-top: 0.3rem; display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                @if($isAdmin)
                                    <span class="role-badge role-admin"><i class="fas fa-shield-halved"></i> Admin</span>
                                @endif
                                @if($isCoord)
                                    <span class="role-badge role-coordinator"><i class="fas fa-user-tie"></i> Coordinator</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="coordinator-details">
                        <div class="coord-info-item">
                            <div class="coord-info-label">Last Login</div>
                            <div class="coord-info-value">
                                {{ $coordinator->last_login ? $coordinator->last_login->format('M j, Y') : 'Never' }}
                            </div>
                        </div>
                        <div class="coord-info-item">
                            <div class="coord-info-label">Member Since</div>
                            <div class="coord-info-value">{{ $coordinator->created_at->format('M j, Y') }}</div>
                        </div>
                    </div>
                    @php $canEdit = auth()->user()->hasRole('admin') || !$isAdmin; @endphp
                    @if($canEdit)
                    <div class="coordinator-actions">
                        <button
                            class="btn-small btn-edit"
                            onclick="openEditCoordinatorModal({{ json_encode(['user_id' => $coordinator->user_id, 'email' => $coordinator->email, 'role' => $isAdmin ? 'admin' : 'coordinator']) }})"
                        >Edit</button>
                        @if(auth()->user()->hasRole('admin') && !$isSelf)
                        <form method="POST" action="{{ route('coordinators.destroy', $coordinator->user_id) }}"
                              onsubmit="return confirm('Delete coordinator {{ addslashes($coordinator->email) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-small btn-danger-sm">Delete</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="empty-state-title">No coordinators found</div>
                        <div class="empty-state-text">Try adjusting your filters or add a new coordinator.</div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($coordinators->hasPages())
            <div class="coordinators-list-container" style="box-shadow: none; padding: 0;">
                <div class="pagination">
                    {{ $coordinators->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add / Edit Coordinator Modal -->
<div class="coord-modal-overlay" id="coordinatorModal">
    <div class="coord-modal-dialog">
        <div class="coord-modal-header">
            <span class="coord-modal-title" id="coordModalTitle"><i class="fas fa-user-plus"></i> Add Coordinator</span>
            <button class="coord-modal-close" onclick="closeCoordinatorModal()">×</button>
        </div>

        <form method="POST" id="coordinatorForm" novalidate>
            @csrf
            <input type="hidden" name="_method" id="coordFormMethod" value="POST">

            <div class="coord-modal-body">

                {{-- Promote mode: volunteer select --}}
                <div class="coord-form-group" id="c_volunteer_group">
                    <label class="coord-form-label">Volunteer <span style="color:#dc3545">*</span></label>
                    <select class="coord-form-select" name="volunteer_id" id="c_volunteer">
                        <option value="">Select a volunteer to promote…</option>
                        @foreach($promotableVolunteers as $v)
                            <option value="{{ $v->volunteer_id }}">
                                {{ $v->last_name }}, {{ $v->first_name }} — {{ $v->email }}
                            </option>
                        @endforeach
                    </select>
                    <div class="coord-error-msg" id="err-c_volunteer"></div>
                </div>

                {{-- Edit mode: read-only email display --}}
                <div class="coord-form-group" id="c_email_group" style="display:none;">
                    <label class="coord-form-label">Coordinator</label>
                    <div id="c_email_display" style="padding:0.5rem 0.75rem;background:#f0f4f8;border:1px solid #ddd;border-radius:0.5rem;font-size:0.875rem;color:#555;"></div>
                </div>

                <div class="coord-form-group">
                    <label class="coord-form-label">Role <span style="color:#dc3545">*</span></label>
                    <select class="coord-form-select" name="role" id="c_role" required>
                        <option value="coordinator">Coordinator</option>
                        @if(auth()->user()->hasRole('admin'))
                        <option value="admin">Admin</option>
                        @endif
                    </select>
                    <div class="coord-error-msg" id="err-c_role"></div>
                </div>

            </div>

            <div class="coord-modal-footer">
                <button type="button" class="btn-coord-secondary" onclick="closeCoordinatorModal()">Cancel</button>
                <button type="submit" class="btn-coord-primary" id="coordSubmitBtn">
                    <i class="fas fa-user-plus"></i> <span id="coordSubmitLabel">Add Coordinator</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    const ADD_URL   = '{{ route('coordinators.store') }}';
    const isEditing = { value: false };

    function showGroup(id) { const el = document.getElementById(id); if (el) el.style.display = ''; }
    function hideGroup(id) { const el = document.getElementById(id); if (el) el.style.display = 'none'; }

    function openAddCoordinatorModal() {
        isEditing.value = false;
        document.getElementById('coordModalTitle').innerHTML = '<i class="fas fa-arrow-up"></i> Promote Volunteer';
        document.getElementById('coordSubmitLabel').textContent = 'Promote';
        document.getElementById('coordFormMethod').value = 'POST';
        document.getElementById('coordinatorForm').action = ADD_URL;
        document.getElementById('coordinatorForm').reset();
        showGroup('c_volunteer_group');
        hideGroup('c_email_group');
        clearCoordErrors();
        document.getElementById('coordinatorModal').classList.add('show');
    }

    function openEditCoordinatorModal(data) {
        isEditing.value = true;
        document.getElementById('coordModalTitle').innerHTML = '<i class="fas fa-user-edit"></i> Edit Role';
        document.getElementById('coordSubmitLabel').textContent = 'Save';
        document.getElementById('coordFormMethod').value = 'PATCH';

        const url = '{{ url('/coordinators') }}/' + data.user_id;
        document.getElementById('coordinatorForm').action = url;

        document.getElementById('c_email_display').textContent = data.email;
        document.getElementById('c_role').value = data.role;
        hideGroup('c_volunteer_group');
        showGroup('c_email_group');
        clearCoordErrors();
        document.getElementById('coordinatorModal').classList.add('show');
    }

    function closeCoordinatorModal() {
        document.getElementById('coordinatorModal').classList.remove('show');
        clearCoordErrors();
    }

    document.getElementById('coordinatorModal').addEventListener('click', function(e) {
        if (e.target === this) closeCoordinatorModal();
    });

    // ── Validation ──
    function setCoordError(id, msg) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.style.borderColor = '#dc3545';
        if (err) { err.textContent = msg; err.style.display = 'block'; }
    }

    function clearCoordError(id) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.style.borderColor = '';
        if (err) { err.textContent = ''; err.style.display = 'none'; }
    }

    function clearCoordErrors() {
        ['c_volunteer', 'c_role'].forEach(clearCoordError);
    }

    ['c_volunteer', 'c_role'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => clearCoordError(id));
    });

    document.getElementById('coordinatorForm').addEventListener('submit', function(e) {
        clearCoordErrors();
        let first = null;

        if (!isEditing.value) {
            if (!document.getElementById('c_volunteer').value) {
                setCoordError('c_volunteer', 'Please select a volunteer to promote.');
                if (!first) first = document.getElementById('c_volunteer');
            }
        }

        if (!document.getElementById('c_role').value) {
            setCoordError('c_role', 'Role is required.');
            if (!first) first = document.getElementById('c_role');
        }

        if (first) {
            e.preventDefault();
            first.focus();
        }
    });
</script>
@endsection
