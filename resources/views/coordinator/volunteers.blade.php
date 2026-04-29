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

    /* ── Volunteer Modal ── */
    .vol-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1080;
        overflow-y: auto;
        padding: 5rem 1rem 2rem;
    }

    .vol-modal-overlay.show { display: block; }

    .vol-modal-dialog {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 620px;
        margin: 0 auto;
    }

    .vol-modal-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .vol-modal-title { font-size: 1.1rem; font-weight: 700; }

    .vol-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }

    .vol-modal-body { padding: 1.5rem; }

    .vol-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .vol-section-heading {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #0099cc;
        border-bottom: 1px solid #e8f4fb;
        padding-bottom: 0.4rem;
        margin: 1.25rem 0 1rem;
    }

    .vol-section-heading:first-child { margin-top: 0; }

    .vol-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .vol-form-group { margin-bottom: 1.1rem; }

    .vol-form-label {
        font-weight: 600;
        color: #333;
        font-size: 0.875rem;
        display: block;
        margin-bottom: 0.35rem;
    }

    .vol-form-control,
    .vol-form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        height: 2.6rem;
        transition: border-color 0.2s;
    }

    .vol-form-control:focus,
    .vol-form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0,153,204,0.2);
        outline: none;
    }

    .vol-form-text {
        font-size: 0.72rem;
        color: #999;
        margin-top: 0.2rem;
    }

    .vol-error-msg {
        font-size: 0.75rem;
        color: #dc3545;
        margin-top: 0.2rem;
        display: none;
    }

    .vol-form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
        font-size: 0.875rem;
        color: #444;
    }

    .vol-form-check input { accent-color: #0099cc; }

    .vol-conditional { display: none; }
    .vol-conditional.show { display: block; }

    .vol-pw-strength {
        height: 5px;
        background: #e0e0e0;
        border-radius: 3px;
        margin-top: 0.4rem;
        overflow: hidden;
    }

    .vol-pw-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: all 0.3s;
    }

    .vol-pw-bar.weak   { width: 33%; background: #dc3545; }
    .vol-pw-bar.fair   { width: 66%; background: #ffc107; }
    .vol-pw-bar.strong { width: 100%; background: #28a745; }

    .vol-pw-text { font-size: 0.72rem; font-weight: 600; margin-top: 0.2rem; }
    .vol-pw-text.weak   { color: #dc3545; }
    .vol-pw-text.fair   { color: #ffc107; }
    .vol-pw-text.strong { color: #28a745; }

    .btn-vol-primary {
        background: #0099cc;
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-vol-primary:hover { background: #003366; }

    .btn-vol-secondary {
        background: #e0e0e0;
        color: #333;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-vol-secondary:hover { background: #ccc; }

    /* Dark mode */
    html.dark .vol-modal-dialog { background: #1a2235; }
    html.dark .vol-modal-footer { border-top-color: #2a3a50; }
    html.dark .vol-section-heading { color: #38bdf8; border-bottom-color: #1e3a4a; }
    html.dark .vol-form-label { color: #cbd5e1; }
    html.dark .vol-form-text  { color: #64748b; }
    html.dark .vol-form-control,
    html.dark .vol-form-select {
        background: #141d2e;
        border-color: #2a3a50;
        color: #e2e8f0;
    }
    html.dark .vol-form-control::placeholder { color: #4a5a6a; }
    html.dark .vol-form-control:focus,
    html.dark .vol-form-select:focus {
        background: #141d2e;
        border-color: #0099cc;
        color: #e2e8f0;
    }
    html.dark .vol-form-select option { background: #1a2235; color: #e2e8f0; }
    html.dark .vol-form-check { color: #94a3b8; }
    html.dark .vol-pw-strength { background: #2a3a50; }
    html.dark .btn-vol-secondary { background: #2a3a50; color: #cbd5e1; }
    html.dark .btn-vol-secondary:hover { background: #374f6b; }

    @media (max-width: 576px) {
        .vol-form-row { grid-template-columns: 1fr; gap: 0; }
    }

    /* Sort select styling */
    .sort-controls { display: flex; gap: 0.75rem; align-items: flex-end; }
    .sort-controls .form-select { height: 2.6rem; padding: 0.5rem 0.75rem; }

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
                <button class="btn-add" onclick="openAddVolunteerModal()">
                    <i class="fas fa-plus"></i> Add Volunteer
                </button>
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

                    <div class="form-group">
                        <label class="form-label">Sort By</label>
                        <div class="sort-controls">
                            <select class="form-select" name="sort" style="flex:1;">
                                <option value="last_name"       {{ ($sort ?? 'last_name') === 'last_name'       ? 'selected' : '' }}>Last Name</option>
                                <option value="first_name"      {{ ($sort ?? '') === 'first_name'      ? 'selected' : '' }}>First Name</option>
                                <option value="clean_date"      {{ ($sort ?? '') === 'clean_date'      ? 'selected' : '' }}>Clean Date</option>
                                <option value="probation_status"{{ ($sort ?? '') === 'probation_status'? 'selected' : '' }}>Status</option>
                            </select>
                            <select class="form-select" name="dir" style="width:90px;">
                                <option value="asc"  {{ ($dir ?? 'asc') === 'asc'  ? 'selected' : '' }}>A–Z ▲</option>
                                <option value="desc" {{ ($dir ?? '') === 'desc' ? 'selected' : '' }}>Z–A ▼</option>
                            </select>
                        </div>
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
                    $cleanYears = (int) $volunteer->clean_date->diffInYears(now());
                    $cleanMonths = (int) $volunteer->clean_date->copy()->addYears($cleanYears)->diffInMonths(now());
                    $cleanLabel = $cleanYears . ' yr' . ($cleanYears != 1 ? 's' : '')
                                . ' ' . $cleanMonths . ' mo';
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
                                    $typeName = $cred->credentialType?->display_name ?? 'Unknown';
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
                    {{ $volunteers->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Volunteer Modal -->
<div class="vol-modal-overlay" id="volunteerModal">
    <div class="vol-modal-dialog">
        <div class="vol-modal-header">
            <span class="vol-modal-title"><i class="fas fa-user-plus"></i> Add New Volunteer</span>
            <button class="vol-modal-close" onclick="closeVolunteerModal()">×</button>
        </div>

        <form method="POST" action="{{ route('coordinator.volunteers.store') }}" id="volunteerForm" novalidate>
            @csrf
            <div class="vol-modal-body">

                <div class="vol-section-heading">Personal Information</div>

                <div class="vol-form-row">
                    <div class="vol-form-group">
                        <label class="vol-form-label">First Name <span style="color:#dc3545">*</span></label>
                        <input type="text" class="vol-form-control" name="first_name" id="v_first_name" required>
                        <div class="vol-error-msg" id="err-v_first_name"></div>
                    </div>
                    <div class="vol-form-group">
                        <label class="vol-form-label">Last Name <span style="color:#dc3545">*</span></label>
                        <input type="text" class="vol-form-control" name="last_name" id="v_last_name" required>
                        <div class="vol-error-msg" id="err-v_last_name"></div>
                    </div>
                </div>

                <div class="vol-form-group">
                    <label class="vol-form-label">Email Address <span style="color:#dc3545">*</span></label>
                    <input type="email" class="vol-form-control" name="email" id="v_email" autocomplete="off" required>
                    <div class="vol-error-msg" id="err-v_email"></div>
                </div>

                <div class="vol-form-row">
                    <div class="vol-form-group">
                        <label class="vol-form-label">Phone <span style="color:#dc3545">*</span></label>
                        <input type="tel" class="vol-form-control" name="phone" id="v_phone"
                               placeholder="(123) 456-7890" required>
                        <div class="vol-error-msg" id="err-v_phone"></div>
                    </div>
                    <div class="vol-form-group">
                        <label class="vol-form-label">Date of Birth <span style="color:#dc3545">*</span></label>
                        <input type="date" class="vol-form-control" name="dob" id="v_dob" required>
                        <div class="vol-error-msg" id="err-v_dob"></div>
                    </div>
                </div>

                <div class="vol-form-group">
                    <label class="vol-form-label">Gender <span style="color:#dc3545">*</span></label>
                    <select class="vol-form-select" name="gender" id="v_gender" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="non-binary">Non-Binary</option>
                        <option value="prefer_not_to_say">Prefer Not to Say</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="vol-error-msg" id="err-v_gender"></div>
                </div>

                <div class="vol-section-heading">Recovery Information</div>

                <div class="vol-form-group">
                    <label class="vol-form-label">Probation Status <span style="color:#dc3545">*</span></label>
                    <div class="vol-form-check">
                        <input type="radio" name="on_probation" id="v_prob_no" value="0" checked>
                        <label for="v_prob_no">Not on probation</label>
                    </div>
                    <div class="vol-form-check">
                        <input type="radio" name="on_probation" id="v_prob_yes" value="1">
                        <label for="v_prob_yes">Currently on probation</label>
                    </div>
                    <div class="vol-error-msg" id="err-v_on_probation"></div>
                </div>

                <div class="vol-form-row">
                    <div class="vol-form-group">
                        <label class="vol-form-label">Clean Date</label>
                        <input type="date" class="vol-form-control" name="clean_date" id="v_clean_date">
                        <div class="vol-form-text">Optional — date became sober.</div>
                    </div>
                    <div class="vol-form-group">
                        <label class="vol-form-label">Neighborhood</label>
                        <input type="text" class="vol-form-control" name="neighborhood" id="v_neighborhood"
                               placeholder="e.g. Downtown">
                    </div>
                </div>

                <div class="vol-form-group">
                    <div class="vol-form-check">
                        <input type="checkbox" name="has_treatment_facility" id="v_has_treatment" value="1"
                               onchange="toggleTreatment(this.checked)">
                        <label for="v_has_treatment">Has treatment facility experience</label>
                    </div>
                </div>

                <div class="vol-conditional" id="v_treatment_fields">
                    <div class="vol-form-row">
                        <div class="vol-form-group">
                            <label class="vol-form-label">Facility Name</label>
                            <input type="text" class="vol-form-control" name="treatment_facility_name"
                                   id="v_treatment_name">
                        </div>
                        <div class="vol-form-group">
                            <label class="vol-form-label">Discharge Date</label>
                            <input type="date" class="vol-form-control" name="discharge_date" id="v_discharge_date">
                        </div>
                    </div>
                </div>

                <div class="vol-form-group">
                    <label class="vol-form-label">Primary Bus Line</label>
                    <input type="text" class="vol-form-control" name="bus_line" id="v_bus_line"
                           placeholder="e.g. Line 5">
                </div>

                <div class="vol-section-heading">Account Password</div>

                <div class="vol-form-row">
                    <div class="vol-form-group">
                        <label class="vol-form-label">Password <span style="color:#dc3545">*</span></label>
                        <input type="password" class="vol-form-control" name="password" id="v_password"
                               autocomplete="new-password" required>
                        <div class="vol-pw-strength"><div class="vol-pw-bar" id="v_pw_bar"></div></div>
                        <div class="vol-pw-text" id="v_pw_text"></div>
                        <div class="vol-form-text">Min 8 chars, uppercase, lowercase, number.</div>
                        <div class="vol-error-msg" id="err-v_password"></div>
                    </div>
                    <div class="vol-form-group">
                        <label class="vol-form-label">Confirm Password <span style="color:#dc3545">*</span></label>
                        <input type="password" class="vol-form-control" name="password_confirmation"
                               id="v_password_confirmation" autocomplete="new-password" required>
                        <div class="vol-error-msg" id="err-v_password_confirmation"></div>
                    </div>
                </div>

            </div>

            <div class="vol-modal-footer">
                <button type="button" class="btn-vol-secondary" onclick="closeVolunteerModal()">Cancel</button>
                <button type="submit" class="btn-vol-primary">
                    <i class="fas fa-user-plus"></i> Add Volunteer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function openAddVolunteerModal() {
        document.getElementById('volunteerForm').reset();
        document.getElementById('v_treatment_fields').classList.remove('show');
        clearVolErrors();
        document.getElementById('v_pw_bar').className = 'vol-pw-bar';
        document.getElementById('v_pw_text').textContent = '';
        document.getElementById('volunteerModal').classList.add('show');
    }

    function closeVolunteerModal() {
        document.getElementById('volunteerModal').classList.remove('show');
        clearVolErrors();
    }

    function toggleTreatment(show) {
        document.getElementById('v_treatment_fields').classList.toggle('show', show);
    }

    // Close on backdrop click
    document.getElementById('volunteerModal').addEventListener('click', function(e) {
        if (e.target === this) closeVolunteerModal();
    });

    // Phone auto-format
    document.getElementById('v_phone').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 10);
        if (v.length > 6)      v = '(' + v.slice(0,3) + ') ' + v.slice(3,6) + '-' + v.slice(6);
        else if (v.length > 3) v = '(' + v.slice(0,3) + ') ' + v.slice(3);
        else if (v.length > 0) v = '(' + v;
        e.target.value = v;
    });

    // Password strength
    document.getElementById('v_password').addEventListener('input', function() {
        const pw = this.value;
        let score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^a-zA-Z0-9]/.test(pw)) score++;
        const s   = Math.min(Math.ceil(score / 2), 3);
        const cls = ['', 'weak', 'fair', 'strong'][s];
        const lbl = ['', 'Weak', 'Fair', 'Strong'][s];
        const bar = document.getElementById('v_pw_bar');
        const txt = document.getElementById('v_pw_text');
        bar.className = 'vol-pw-bar' + (pw ? ' ' + cls : '');
        txt.className = 'vol-pw-text' + (pw ? ' ' + cls : '');
        txt.textContent = pw ? lbl + ' password' : '';
    });

    // ── Validation ──────────────────────────────────────────────────────────
    const VOL_REQUIRED = [
        { id: 'v_first_name', label: 'First Name' },
        { id: 'v_last_name',  label: 'Last Name' },
        { id: 'v_email',      label: 'Email Address' },
        { id: 'v_phone',      label: 'Phone' },
        { id: 'v_dob',        label: 'Date of Birth' },
        { id: 'v_gender',     label: 'Gender' },
        { id: 'v_password',   label: 'Password' },
        { id: 'v_password_confirmation', label: 'Confirm Password' },
    ];

    function setVolError(id, msg) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.style.borderColor = '#dc3545';
        if (err) { err.textContent = msg; err.style.display = 'block'; }
    }

    function clearVolError(id) {
        const el  = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.style.borderColor = '';
        if (err) { err.textContent = ''; err.style.display = 'none'; }
    }

    function clearVolErrors() {
        VOL_REQUIRED.forEach(f => clearVolError(f.id));
    }

    VOL_REQUIRED.forEach(({ id }) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', () => clearVolError(id));
    });

    document.getElementById('volunteerForm').addEventListener('submit', function(e) {
        clearVolErrors();
        let first = null;

        VOL_REQUIRED.forEach(({ id, label }) => {
            const el = document.getElementById(id);
            if (!el || el.value.trim() !== '') return;
            setVolError(id, label + ' is required.');
            if (!first) first = el;
        });

        const pw  = document.getElementById('v_password').value;
        const pwc = document.getElementById('v_password_confirmation').value;
        if (pw && pwc && pw !== pwc) {
            setVolError('v_password_confirmation', 'Passwords do not match.');
            if (!first) first = document.getElementById('v_password_confirmation');
        }

        if (first) {
            e.preventDefault();
            first.focus();
            first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endsection
