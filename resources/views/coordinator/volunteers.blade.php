@extends('layouts.app')

@section('title', 'Volunteers - ChronoSync')

@section('extra-styles')
<style>
    .volunteers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .volunteers-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .search-and-filter {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .filter-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }

    .filter-controls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 0;
    }

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

    .btn-filter:hover {
        background-color: #003366;
    }

    .btn-reset {
        padding: 0.75rem 1.5rem;
        background-color: #e0e0e0;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #d0d0d0;
    }

    /* Volunteers List */
    .volunteers-list-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .volunteer-row {
        display: flex;
        gap: 2rem;
        padding: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        align-items: flex-start;
        transition: background-color 0.15s;
    }

    .volunteer-row:last-child {
        border-bottom: none;
    }

    .volunteer-row:hover {
        background-color: #f8f9fa;
    }

    .volunteer-identity {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 200px;
        flex: 1.2;
    }

    .volunteer-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: #0099cc;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .volunteer-name {
        font-weight: 600;
        color: #003366;
        font-size: 0.95rem;
    }

    .volunteer-email {
        font-size: 0.8rem;
        color: #999;
        display: block;
        margin-top: 0.1rem;
    }

    .volunteer-details {
        display: flex;
        gap: 2rem;
        flex: 2;
        flex-wrap: wrap;
    }

    .vol-info-item {
        min-width: 90px;
    }

    .vol-info-label {
        font-size: 0.7rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .vol-info-value {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .volunteer-credentials {
        flex: 1.5;
        min-width: 140px;
    }

    .credentials-label {
        font-size: 0.7rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .credentials-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .credential-badge {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 0.3rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .credential-badge.pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .credential-badge.expired {
        background-color: #f8d7da;
        color: #721c24;
    }

    .volunteer-status-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.75rem;
        min-width: 130px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
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

    .btn-view {
        background-color: #0099cc;
        color: white;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-view:hover {
        background-color: #003366;
        color: white;
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

    /* ── Dark Mode ── */
    html.dark .volunteers-title {
        color: #e2e8f0 !important;
    }

    html.dark .search-and-filter {
        background-color: #1a2235 !important;
        border-color: #2a3a50 !important;
    }

    html.dark .filter-title {
        color: #e2e8f0 !important;
    }

    html.dark .volunteers-list-container {
        background-color: #1a2235 !important;
    }

    html.dark .volunteer-row {
        border-color: #2a3a50 !important;
    }

    html.dark .volunteer-row:hover {
        background-color: #1e2a3a !important;
    }

    html.dark .volunteer-name {
        color: #93c5fd !important;
    }

    html.dark .volunteer-email {
        color: #64748b !important;
    }

    html.dark .vol-info-label {
        color: #64748b !important;
    }

    html.dark .vol-info-value {
        color: #e2e8f0 !important;
    }

    html.dark .credentials-label {
        color: #64748b !important;
    }

    html.dark .status-active {
        background-color: #14532d;
        color: #bbf7d0;
    }

    html.dark .status-inactive {
        background-color: #1e2a3a;
        color: #94a3b8;
    }

    html.dark .status-pending {
        background-color: #422006;
        color: #fde68a;
    }

    html.dark .credential-badge {
        background-color: #0c4a6e;
        color: #bae6fd;
    }

    html.dark .credential-badge.pending {
        background-color: #422006;
        color: #fde68a;
    }

    html.dark .credential-badge.expired {
        background-color: #450a0a;
        color: #fecaca;
    }

    html.dark .btn-edit {
        background-color: #1e2a3a !important;
        color: #93c5fd !important;
    }

    html.dark .btn-edit:hover {
        background-color: #003366 !important;
        color: white !important;
    }

    html.dark .btn-reset {
        background-color: #2a3a50 !important;
        color: #e2e8f0 !important;
    }

    html.dark .btn-reset:hover {
        background-color: #3a4a60 !important;
    }


    html.dark .empty-state {
        background-color: #1a2235 !important;
    }

    html.dark .empty-state-title {
        color: #94a3b8 !important;
    }

    html.dark .empty-state-text {
        color: #64748b !important;
    }

    @media (max-width: 1024px) {
        .volunteers-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .volunteer-row {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .volunteer-details {
            gap: 1rem;
        }

        .volunteer-status-actions {
            align-items: flex-start;
            min-width: unset;
        }

        .filter-controls {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .volunteers-title {
            font-size: 1.25rem;
        }

        .volunteer-identity {
            min-width: unset;
            flex: unset;
            width: 100%;
        }

        .volunteer-email {
            display: none;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-filter,
        .btn-reset {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="volunteers-header">
                <h1 class="volunteers-title">
                    <i class="fas fa-users"></i> Volunteer Management
                </h1>
            </div>

            <!-- Search and Filters -->
            <form method="GET" action="{{ route('volunteers.index') }}" id="filter-form">
            <div class="search-and-filter">
                <div class="filter-title">Search & Filter</div>

                <div class="filter-controls">
                    <div class="form-group">
                        <label for="search" class="form-label">Search by Name or Email</label>
                        <input
                            type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Enter name or email..."
                        >
                    </div>

                    <div class="form-group">
                        <label for="clean_time" class="form-label">Clean Time</label>
                        <select class="form-select" id="clean_time" name="clean_time">
                            <option value="">All</option>
                            <option value="0-1" {{ request('clean_time') === '0-1' ? 'selected' : '' }}>0–1 years</option>
                            <option value="1-2" {{ request('clean_time') === '1-2' ? 'selected' : '' }}>1–2 years</option>
                            <option value="2-5" {{ request('clean_time') === '2-5' ? 'selected' : '' }}>2–5 years</option>
                            <option value="5+"  {{ request('clean_time') === '5+'  ? 'selected' : '' }}>5+ years</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All</option>
                            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>On Probation</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('volunteers.index') }}" class="btn-reset" style="display:inline-block; text-decoration:none;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
            </form>

            <!-- Volunteers List -->
            <div class="volunteers-list-container">

                @forelse($volunteers as $volunteer)
                @php
                    $initials   = strtoupper(substr($volunteer->first_name, 0, 1) . substr($volunteer->last_name, 0, 1));
                    $cleanYears  = (int) $volunteer->clean_date->diffInYears(now());
                    $cleanMonths = (int) $volunteer->clean_date->copy()->addYears($cleanYears)->diffInMonths(now());
                    $cleanLabel  = $cleanYears . ' yrs ' . $cleanMonths . ' mo';
                    $onProbation = $volunteer->probation_status === 'active_probation';
                @endphp
                <div class="volunteer-row">
                    <div class="volunteer-identity">
                        <div class="volunteer-avatar">{{ $initials }}</div>
                        <div>
                            <div class="volunteer-name">{{ $volunteer->first_name }} {{ $volunteer->last_name }}</div>
                            <span class="volunteer-email">{{ $volunteer->email }}</span>
                        </div>
                    </div>
                    <div class="volunteer-details">
                        <div class="vol-info-item">
                            <div class="vol-info-label">Clean Time</div>
                            <div class="vol-info-value">{{ $cleanLabel }}</div>
                        </div>
                        <div class="vol-info-item">
                            <div class="vol-info-label">Gender</div>
                            <div class="vol-info-value">{{ ucfirst($volunteer->gender) }}</div>
                        </div>
                        <div class="vol-info-item">
                            <div class="vol-info-label">Phone</div>
                            <div class="vol-info-value">{{ $volunteer->phone }}</div>
                        </div>
                    </div>
                    <div class="volunteer-credentials">
                        <div class="credentials-label">Credentials</div>
                        <div class="credentials-list">
                            @forelse($volunteer->credentials as $cred)
                                @php
                                    $typeName = $cred->credentialType?->name ?? 'Unknown';
                                    $badgeClass = match($cred->status) {
                                        'pending' => 'pending',
                                        'denied'  => 'expired',
                                        default   => '',
                                    };
                                    $label = $typeName . ($cred->status !== 'approved' ? ' (' . ucfirst($cred->status) . ')' : '');
                                @endphp
                                <span class="credential-badge {{ $badgeClass }}">{{ $label }}</span>
                            @empty
                                <span style="font-size: 0.8rem; color: #999;">None</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="volunteer-status-actions">
                        @if($onProbation)
                            <span class="status-badge status-pending"><i class="fas fa-gavel"></i> Probation</span>
                        @else
                            <span class="status-badge status-active"><i class="fas fa-check"></i> Active</span>
                        @endif
                        <div class="action-buttons">
                            <a href="{{ route('volunteers.show', $volunteer->volunteer_id) }}" class="btn-small btn-view">View</a>
                            <a href="{{ route('volunteers.edit', $volunteer->volunteer_id) }}" class="btn-small btn-edit">Edit</a>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-users"></i></div>
                        <div class="empty-state-title">No volunteers found</div>
                        <div class="empty-state-text">Try adjusting your filters.</div>
                    </div>
                @endforelse

            </div>

            <!-- Pagination -->
            @if($volunteers->hasPages())
            <div class="volunteers-list-container" style="box-shadow: none; padding: 0;">
                <div class="pagination">
                    {{ $volunteers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
