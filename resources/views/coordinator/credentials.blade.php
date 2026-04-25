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

            {{-- Credentials Table --}}
            <div class="credentials-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Volunteer</th>
                            <th>Facility</th>
                            <th>Credential Type</th>
                            <th>Approval Date</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credentials as $cred)
                        @php
                            $isExpiringSoon = $cred->status === 'approved'
                                && $cred->expiration_date
                                && \Carbon\Carbon::parse($cred->expiration_date)->diffInDays(now(), false) >= -30
                                && \Carbon\Carbon::parse($cred->expiration_date)->isFuture();
                            $isExpired = $cred->expiration_date
                                && \Carbon\Carbon::parse($cred->expiration_date)->isPast();
                        @endphp
                        <tr class="{{ $isExpiringSoon ? 'expiration-soon' : '' }}">
                            <td>
                                <strong>{{ $cred->volunteer->first_name }} {{ $cred->volunteer->last_name }}</strong><br>
                                <small style="color:#999;">{{ $cred->volunteer->phone }}</small>
                            </td>
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
                                        {{-- Approve --}}
                                        <form method="POST" action="{{ route('credentials.approve', $cred->credential_id) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-small btn-approve">Approve</button>
                                        </form>
                                        {{-- Deny (opens inline form) --}}
                                        <button type="button" class="btn-small btn-deny"
                                                onclick="toggleDenyForm('{{ $cred->credential_id }}')">Deny</button>
                                    @endif
                                    @if($cred->status === 'approved' || $isExpired)
                                        {{-- Renew (opens inline form) --}}
                                        <button type="button" class="btn-small btn-renew"
                                                onclick="toggleRenewForm('{{ $cred->credential_id }}')">Renew</button>
                                    @endif
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:3rem;color:#999;">
                                <i class="fas fa-certificate" style="font-size:2rem;display:block;margin-bottom:0.75rem;color:#ddd;"></i>
                                No credentials match your filters.
                            </td>
                        </tr>
                        @endforelse
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
</script>
@endsection
