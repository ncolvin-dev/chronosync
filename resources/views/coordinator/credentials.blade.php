@extends('layouts.app')

@section('title', 'Credentials - ChronoSync')

@section('extra-styles')
<style>
    .credentials-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .credentials-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-action:hover {
        background-color: #003366;
    }

    .alert-banner {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .alert-icon {
        color: #ffc107;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 600;
        color: #856404;
        margin-bottom: 0.25rem;
    }

    .alert-text {
        color: #856404;
        font-size: 0.9rem;
    }

    .alert-list {
        margin-top: 0.75rem;
        padding-left: 1.5rem;
    }

    .alert-list li {
        margin-bottom: 0.25rem;
    }

    .credentials-table {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
        white-space: nowrap;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .table tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-expired {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-small {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .btn-approve {
        background-color: #28a745;
        color: white;
    }

    .btn-approve:hover {
        background-color: #218838;
    }

    .btn-deny {
        background-color: #dc3545;
        color: white;
    }

    .btn-deny:hover {
        background-color: #c82333;
    }

    .btn-renew {
        background-color: #0099cc;
        color: white;
    }

    .btn-renew:hover {
        background-color: #003366;
    }

    .expiration-soon {
        background-color: #fffaf0;
        font-weight: 600;
        color: #ff6b35;
    }

    .expiration-soon td {
        color: #ff6b35;
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
    }

    .btn-edit-cred {
        background-color: #e8f0fe;
        color: #003366;
    }

    .btn-edit-cred:hover {
        background-color: #003366;
        color: white;
    }

    .btn-delete-cred {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete-cred:hover {
        background-color: #c82333;
    }

    /* Add Credential Modal */
    .cred-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1080;
        overflow-y: auto;
        padding: 4rem 1rem 2rem;
    }

    .cred-modal-overlay.show { display: block; }

    .cred-modal-dialog {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 680px;
        margin: 0 auto;
    }

    .cred-modal-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 0.75rem 0.75rem 0 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .cred-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }

    .cred-modal-body { padding: 1.5rem; }

    .cred-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .cred-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .cred-form-group label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #333;
        display: block;
        margin-bottom: 0.35rem;
    }

    .cred-form-group .form-control,
    .cred-form-group .form-select {
        font-size: 0.875rem;
    }

    .btn-modal-primary {
        background: #0099cc;
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-modal-primary:hover { background: #003366; }

    .btn-modal-secondary {
        background: #e0e0e0;
        color: #333;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-modal-secondary:hover { background: #ccc; }

    @media (max-width: 576px) {
        .cred-form-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 1024px) {
        .credentials-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-small {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .credentials-title {
            font-size: 1.25rem;
        }

        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .action-buttons {
            flex-direction: column;
        }
    }

    /* ── Dark Mode ── */
    html.dark .credentials-title {
        color: #e2e8f0 !important;
    }

    html.dark .credentials-table {
        background: #1a2235 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4) !important;
    }

    /* Bootstrap CSS variables — covers cell-level bg Bootstrap applies */
    html.dark .table {
        --bs-table-bg: #1a2235;
        --bs-table-striped-bg: #1e2738;
        --bs-table-hover-bg: #1e2a3a;
        --bs-table-border-color: #2a3a50;
        --bs-table-color: #e2e8f0;
        --bs-table-accent-bg: transparent;
        color: #e2e8f0;
    }

    html.dark .table th {
        background-color: #141d2e !important;
        color: #93c5fd !important;
        border-bottom-color: #2a3a50 !important;
    }

    html.dark .table > tbody > tr {
        background-color: #1a2235 !important;
    }

    /* Volunteer group header row */
    html.dark .table > tbody > tr.volunteer-group-header > td {
        background-color: #141d2e !important;
        color: #93c5fd !important;
        border-bottom-color: #2a3a50 !important;
    }

    html.dark .table > tbody > tr > td {
        background-color: #1a2235 !important;
        color: #e2e8f0 !important;
        border-bottom-color: #2a3a50 !important;
    }

    html.dark .table tr:hover,
    html.dark .table > tbody > tr:hover {
        background-color: #1e2a3a !important;
    }

    html.dark .table > tbody > tr:hover > td {
        background-color: #1e2a3a !important;
        color: #e2e8f0 !important;
    }

    html.dark .expiration-soon,
    html.dark .table > tbody > tr.expiration-soon {
        background-color: #2a1f0a !important;
    }

    html.dark .expiration-soon td,
    html.dark .table > tbody > tr.expiration-soon > td {
        background-color: #2a1f0a !important;
        color: #fbbf24 !important;
    }

    /* Filter card */
    html.dark .credentials-table + div,
    html.dark div[style*="background:white"],
    html.dark div[style*="background: white"] {
        background: #1a2235 !important;
        color: #e2e8f0 !important;
    }

    html.dark .form-control,
    html.dark .form-select {
        background-color: #141d2e !important;
        border-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }

    html.dark .form-control::placeholder {
        color: #4a5a6a !important;
    }

    html.dark .form-control:focus,
    html.dark .form-select:focus {
        background-color: #141d2e !important;
        border-color: #0099cc !important;
        color: #e2e8f0 !important;
        box-shadow: 0 0 0 0.2rem rgba(0,153,204,0.25) !important;
    }

    html.dark .form-select option {
        background: #1a2235;
        color: #e2e8f0;
    }

    /* Alert banner */
    html.dark .alert-banner {
        background-color: #2a1f0a !important;
        border-color: #78490a !important;
    }

    html.dark .alert-title,
    html.dark .alert-text,
    html.dark .alert-list li {
        color: #fde68a !important;
    }

    /* Status badges */
    html.dark .status-approved {
        background-color: #14532d !important;
        color: #bbf7d0 !important;
    }

    html.dark .status-pending {
        background-color: #422006 !important;
        color: #fde68a !important;
    }

    html.dark .status-expired {
        background-color: #450a0a !important;
        color: #fecaca !important;
    }

    /* Action buttons */
    html.dark .btn-edit-cred {
        background-color: #1e2a3a !important;
        color: #93c5fd !important;
    }

    html.dark .btn-edit-cred:hover {
        background-color: #003366 !important;
        color: white !important;
    }

    /* Inline edit form background */
    html.dark div[id^="edit-"] {
        background: #141d2e !important;
    }

    html.dark div[id^="edit-"] label {
        color: #cbd5e1 !important;
    }

    /* Empty state */
    html.dark .empty-state {
        background: #1a2235 !important;
    }

    html.dark .empty-state-title {
        color: #94a3b8 !important;
    }

    html.dark .empty-state-text {
        color: #64748b !important;
    }

    /* Pagination */
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

    html.dark .pagination li.disabled span {
        color: #475569 !important;
    }

    /* Add Credential Modal */
    html.dark .cred-modal-dialog {
        background: #1a2235 !important;
    }

    html.dark .cred-modal-footer {
        border-top-color: #2a3a50 !important;
    }

    html.dark .cred-modal-body {
        background: #1a2235 !important;
    }

    html.dark .cred-form-group label {
        color: #cbd5e1 !important;
    }

    html.dark .btn-modal-secondary {
        background: #2a3a50 !important;
        color: #cbd5e1 !important;
    }

    html.dark .btn-modal-secondary:hover {
        background: #374f6b !important;
    }

    /* "Expires Soon" badge keeps amber in dark mode (has yellow inline style overriding status-approved) */
    html.dark .status-badge[style*="background:#fff3cd"] {
        background-color: #422006 !important;
        color: #fde68a !important;
    }

    /* Deny / Renew inline form labels */
    html.dark div[id^="deny-"] label,
    html.dark div[id^="renew-"] label {
        color: #cbd5e1 !important;
    }

    /* Filter card (inline-styled) */
    html.dark div[style*="background:white;border-radius:0.75rem;padding:1.25rem 1.5rem"] {
        background: #1a2235 !important;
        color: #e2e8f0 !important;
    }

    html.dark div[style*="font-weight:600;color:#333;margin-bottom:1rem"] {
        color: #e2e8f0 !important;
    }

    html.dark label[style*="font-size:0.82rem;font-weight:600;color:#333"],
    html.dark label[style*="font-size:0.875rem;font-weight:600;color:#333"] {
        color: #cbd5e1 !important;
    }

    html.dark input[type="checkbox"] {
        accent-color: #0099cc;
        color-scheme: dark;
    }

    /* Success flash (inline-styled) */
    html.dark div[style*="background:#d4edda"] {
        background: #14532d !important;
        border-color: #166534 !important;
        color: #bbf7d0 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="credentials-header">
                <h1 class="credentials-title">
                    <i class="fas fa-certificate"></i> Credential Management
                </h1>
                <button type="button" class="btn-action" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Credential
                </button>
            </div>

            @if(session('success'))
                <div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:0.5rem;padding:0.85rem 1.25rem;margin-bottom:1.5rem;font-weight:500;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Expiring Soon Alert --}}
            @if($expiringSoon->isNotEmpty())
            <div class="alert-banner">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">
                    <div class="alert-title">{{ $expiringSoon->count() }} Credential(s) Expiring Within 30 Days</div>
                    <ul class="alert-list">
                        @foreach($expiringSoon as $exp)
                        <li>
                            <strong>{{ $exp->volunteer->first_name }} {{ $exp->volunteer->last_name }}</strong>
                            &mdash; {{ $exp->credentialType?->display_name ?? '—' }}
                            &mdash; Expires {{ \Carbon\Carbon::parse($exp->expiration_date)->format('M j, Y') }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('credentials.index') }}">
            <div style="background:white;border-radius:0.75rem;padding:1.25rem 1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1.5rem;">
                <div style="font-weight:600;color:#333;margin-bottom:1rem;">Search & Filter</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1rem;">
                    <div>
                        <label style="font-size:0.82rem;font-weight:600;color:#333;display:block;margin-bottom:0.3rem;">Volunteer Name / Email</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search volunteer…">
                    </div>
                    <div>
                        <label style="font-size:0.82rem;font-weight:600;color:#333;display:block;margin-bottom:0.3rem;">Credential Type</label>
                        <select name="credential_type_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach($credentialTypes as $ct)
                                <option value="{{ $ct->credential_type_id }}" {{ request('credential_type_id') == $ct->credential_type_id ? 'selected' : '' }}>
                                    {{ $ct->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.82rem;font-weight:600;color:#333;display:block;margin-bottom:0.3rem;">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="denied"   {{ request('status') === 'denied'   ? 'selected' : '' }}>Denied</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:flex-end;">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;font-weight:600;color:#333;cursor:pointer;">
                            <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }}>
                            Expiring in 30 days
                        </label>
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <button type="submit" style="padding:0.65rem 1.5rem;background:#0099cc;color:white;border:none;border-radius:0.5rem;font-weight:600;cursor:pointer;">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('credentials.index') }}" style="padding:0.65rem 1.5rem;background:#e0e0e0;color:#333;border-radius:0.5rem;font-weight:600;text-decoration:none;display:inline-block;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
            </form>

            {{-- Credentials Table — grouped by volunteer --}}
            <div class="credentials-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Credential Type</th>
                            <th>Approval Date</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grouped = $credentials->getCollection()->groupBy('volunteer_id'); @endphp
                        @if($grouped->isEmpty())
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3rem;color:#999;">
                                <i class="fas fa-certificate" style="font-size:2rem;display:block;margin-bottom:0.75rem;color:#ddd;"></i>
                                No credentials match your filters.
                            </td>
                        </tr>
                        @else
                        @foreach($grouped as $volunteerId => $volCreds)
                        {{-- Volunteer group header --}}
                        <tr class="volunteer-group-header">
                            <td colspan="6" style="background:#f0f4f8;padding:0.6rem 1rem;border-bottom:2px solid #d0dce8;">
                                <strong style="color:#003366;font-size:0.95rem;">
                                    <i class="fas fa-user" style="margin-right:0.4rem;color:#0099cc;"></i>
                                    {{ $volCreds->first()->volunteer->first_name }} {{ $volCreds->first()->volunteer->last_name }}
                                </strong>
                                @if($volCreds->first()->volunteer->phone)
                                    <span style="color:#666;font-size:0.82rem;margin-left:0.75rem;">
                                        <i class="fas fa-phone" style="font-size:0.75rem;"></i>
                                        {{ $volCreds->first()->volunteer->phone }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        {{-- That volunteer's credentials --}}
                        @foreach($volCreds as $cred)
                        @php
                            $isExpiringSoon = $cred->status === 'approved'
                                && $cred->expiration_date
                                && \Carbon\Carbon::parse($cred->expiration_date)->diffInDays(now(), false) >= -30
                                && \Carbon\Carbon::parse($cred->expiration_date)->isFuture();
                            $isExpired = $cred->expiration_date
                                && \Carbon\Carbon::parse($cred->expiration_date)->isPast();
                        @endphp
                        <tr class="{{ $isExpiringSoon ? 'expiration-soon' : '' }}">
                            <td>{{ $cred->facility?->facility_name ?? '—' }}</td>
                            <td>{{ $cred->credentialType?->display_name ?? '—' }}</td>
                            <td>{{ $cred->approval_date ? \Carbon\Carbon::parse($cred->approval_date)->format('M d, Y') : '—' }}</td>
                            <td>
                                @if($cred->expiration_date)
                                    {{ \Carbon\Carbon::parse($cred->expiration_date)->format('M d, Y') }}
                                    @if($isExpiringSoon)
                                        <span style="font-size:0.75rem;color:#ff6b35;font-weight:600;"> (soon)</span>
                                    @elseif($isExpired)
                                        <span style="font-size:0.75rem;color:#dc3545;font-weight:600;"> (expired)</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($cred->status === 'approved' && $isExpiringSoon)
                                    <span class="status-badge status-approved" style="background:#fff3cd;color:#856404;">
                                        <i class="fas fa-exclamation"></i> Expires Soon
                                    </span>
                                @elseif($cred->status === 'approved' && $isExpired)
                                    <span class="status-badge status-expired"><i class="fas fa-times"></i> Expired</span>
                                @elseif($cred->status === 'approved')
                                    <span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span>
                                @elseif($cred->status === 'pending')
                                    <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                                @else
                                    <span class="status-badge status-expired"><i class="fas fa-times"></i> {{ ucfirst($cred->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($cred->status === 'pending')
                                        <form method="POST" action="{{ route('credentials.approve', $cred->credential_id) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-small btn-approve">Approve</button>
                                        </form>
                                        <button type="button" class="btn-small btn-deny"
                                                onclick="toggleDenyForm('{{ $cred->credential_id }}')">Deny</button>
                                    @endif
                                    @if($cred->status === 'approved' || $isExpired)
                                        <button type="button" class="btn-small btn-renew"
                                                onclick="toggleRenewForm('{{ $cred->credential_id }}')">Renew</button>
                                    @endif
                                    <button type="button" class="btn-small btn-edit-cred"
                                            onclick="toggleEditForm('{{ $cred->credential_id }}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('credentials.destroy', $cred->credential_id) }}"
                                          onsubmit="return confirm('Remove this credential?')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-small btn-delete-cred">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Deny inline form --}}
                                <div id="deny-{{ $cred->credential_id }}" style="display:none;margin-top:0.5rem;">
                                    <form method="POST" action="{{ route('credentials.deny', $cred->credential_id) }}">
                                        @csrf @method('PATCH')
                                        <input type="text" name="denial_reason" class="form-control"
                                               placeholder="Reason for denial…"
                                               style="font-size:0.8rem;padding:0.35rem 0.6rem;margin-bottom:0.4rem;" required>
                                        <button type="submit" class="btn-small btn-deny">Confirm Deny</button>
                                        <button type="button" class="btn-small" style="background:#e0e0e0;color:#333;"
                                                onclick="toggleDenyForm('{{ $cred->credential_id }}')">Cancel</button>
                                    </form>
                                </div>

                                {{-- Renew inline form --}}
                                <div id="renew-{{ $cred->credential_id }}" style="display:none;margin-top:0.5rem;">
                                    <form method="POST" action="{{ route('credentials.renew', $cred->credential_id) }}">
                                        @csrf
                                        <label style="font-size:0.78rem;font-weight:600;color:#333;">New expiration date</label>
                                        <input type="date" name="expiration_date" class="form-control"
                                               style="font-size:0.8rem;padding:0.35rem 0.6rem;margin-bottom:0.4rem;"
                                               min="{{ now()->addDay()->format('Y-m-d') }}" required>
                                        <button type="submit" class="btn-small btn-renew">Save Renewal</button>
                                        <button type="button" class="btn-small" style="background:#e0e0e0;color:#333;"
                                                onclick="toggleRenewForm('{{ $cred->credential_id }}')">Cancel</button>
                                    </form>
                                </div>

                                {{-- Edit inline form --}}
                                <div id="edit-{{ $cred->credential_id }}" style="display:none;margin-top:0.75rem;background:#f8f9fa;border-radius:0.5rem;padding:0.75rem;">
                                    <form method="POST" action="{{ route('credentials.update', $cred->credential_id) }}">
                                        @csrf @method('PATCH')
                                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:0.5rem;">
                                            <div>
                                                <label style="font-size:0.78rem;font-weight:600;color:#333;display:block;margin-bottom:0.2rem;">Status</label>
                                                <select name="status" class="form-select" style="font-size:0.8rem;padding:0.35rem 0.5rem;">
                                                    <option value="pending"  {{ $cred->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ $cred->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="denied"   {{ $cred->status === 'denied'   ? 'selected' : '' }}>Denied</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label style="font-size:0.78rem;font-weight:600;color:#333;display:block;margin-bottom:0.2rem;">Approval Date</label>
                                                <input type="date" name="approval_date" class="form-control"
                                                       style="font-size:0.8rem;padding:0.35rem 0.5rem;"
                                                       value="{{ $cred->approval_date ? \Carbon\Carbon::parse($cred->approval_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            <div>
                                                <label style="font-size:0.78rem;font-weight:600;color:#333;display:block;margin-bottom:0.2rem;">Expiration Date</label>
                                                <input type="date" name="expiration_date" class="form-control"
                                                       style="font-size:0.8rem;padding:0.35rem 0.5rem;"
                                                       value="{{ $cred->expiration_date ? \Carbon\Carbon::parse($cred->expiration_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            <div>
                                                <label style="font-size:0.78rem;font-weight:600;color:#333;display:block;margin-bottom:0.2rem;">Notes</label>
                                                <input type="text" name="notes" class="form-control"
                                                       style="font-size:0.8rem;padding:0.35rem 0.5rem;"
                                                       value="{{ $cred->notes }}" placeholder="Optional notes…">
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:0.5rem;">
                                            <button type="submit" class="btn-small btn-approve">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                            <button type="button" class="btn-small" style="background:#e0e0e0;color:#333;"
                                                    onclick="toggleEditForm('{{ $cred->credential_id }}')">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($credentials->hasPages())
            <div style="display:flex;justify-content:center;margin-top:1.5rem;">
                {{ $credentials->appends(request()->query())->links() }}
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Add Credential Modal --}}
<div class="cred-modal-overlay" id="addCredentialModal">
    <div class="cred-modal-dialog">
        <div class="cred-modal-header">
            <span><i class="fas fa-plus-circle"></i> Add Credential</span>
            <button class="cred-modal-close" onclick="closeAddModal()">×</button>
        </div>
        <form method="POST" action="{{ route('credentials.store') }}" id="addCredentialForm">
            @csrf
            <div class="cred-modal-body">
                <div class="cred-form-grid">
                    <div class="cred-form-group" style="grid-column: 1 / -1;">
                        <label>Volunteer <span style="color:#dc3545">*</span></label>
                        <select name="volunteer_id" class="form-select" required>
                            <option value="">Select volunteer…</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->volunteer_id }}" {{ old('volunteer_id') == $v->volunteer_id ? 'selected' : '' }}>
                                    {{ $v->last_name }}, {{ $v->first_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cred-form-group">
                        <label>Credential Type <span style="color:#dc3545">*</span></label>
                        <select name="credential_type_id" class="form-select" required>
                            <option value="">Select type…</option>
                            @foreach($credentialTypes as $ct)
                                <option value="{{ $ct->credential_type_id }}" {{ old('credential_type_id') == $ct->credential_type_id ? 'selected' : '' }}>
                                    {{ $ct->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cred-form-group">
                        <label>Facility <span style="color:#dc3545">*</span></label>
                        <select name="facility_id" class="form-select" required>
                            <option value="">Select facility…</option>
                            @foreach($facilities as $f)
                                <option value="{{ $f->facility_id }}" {{ old('facility_id') == $f->facility_id ? 'selected' : '' }}>
                                    {{ $f->facility_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cred-form-group">
                        <label>Status <span style="color:#dc3545">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending"  {{ old('status', 'pending') === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="denied"   {{ old('status') === 'denied'   ? 'selected' : '' }}>Denied</option>
                        </select>
                    </div>
                    <div class="cred-form-group">
                        <label>Approval Date</label>
                        <input type="date" name="approval_date" class="form-control" value="{{ old('approval_date') }}">
                    </div>
                    <div class="cred-form-group">
                        <label>Expiration Date</label>
                        <input type="date" name="expiration_date" class="form-control" value="{{ old('expiration_date') }}">
                    </div>
                </div>
                <div class="cred-form-group">
                    <label>Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Optional notes…" value="{{ old('notes') }}">
                </div>
                @if($errors->any())
                    <div style="background:#f8d7da;border:1px solid #f5c6cb;border-radius:0.5rem;padding:0.75rem 1rem;margin-top:1rem;font-size:0.85rem;color:#721c24;">
                        <ul style="margin:0;padding-left:1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="cred-modal-footer">
                <button type="button" class="btn-modal-secondary" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-modal-primary">
                    <i class="fas fa-save"></i> Save Credential
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function toggleDenyForm(id) {
        const el = document.getElementById('deny-' + id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
    function toggleRenewForm(id) {
        const el = document.getElementById('renew-' + id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
    function toggleEditForm(id) {
        const el = document.getElementById('edit-' + id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    const addModal = document.getElementById('addCredentialModal');

    function openAddModal() {
        addModal.classList.add('show');
    }
    function closeAddModal() {
        addModal.classList.remove('show');
    }

    addModal.addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });

    @if($errors->any() && old('volunteer_id') !== null)
        document.addEventListener('DOMContentLoaded', openAddModal);
    @endif
</script>
@endsection
