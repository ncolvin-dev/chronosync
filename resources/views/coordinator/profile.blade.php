@extends('layouts.app')

@section('title', 'Profile - ChronoSync')

@section('extra-styles')
{{-- Reuse all styles from the volunteer profile --}}
@include('volunteer.profile-styles')
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ $contact ? strtoupper(substr($contact->first_name, 0, 1) . substr($contact->last_name, 0, 1)) : strtoupper(substr($user->email, 0, 1)) }}
                </div>
                <h1 class="profile-name">{{ $contact ? $contact->first_name . ' ' . $contact->last_name : $user->email }}</h1>
                <p class="profile-email"><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                <div class="status-badges">
                    @foreach(($user->roles ?? []) as $role)
                    <span class="badge badge-info">
                        <i class="fas fa-user-tie"></i> {{ ucfirst($role) }}
                    </span>
                    @endforeach
                </div>
            </div>

            <!-- Personal Information -->
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
                                <div class="info-value">{{ $contact->first_name ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value">{{ $contact->last_name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ $contact->phone ?? '—' }}</div>
                            </div>
                        </div>
                        <button class="edit-button-inline" data-edit-section="personal-info">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <form method="POST" action="{{ route('coordinator.profile.update') }}" class="edit-form" id="personal-info-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="{{ old('first_name', $contact->first_name ?? '') }}" required>
                            @error('first_name')<div style="color:#dc3545;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="{{ old('last_name', $contact->last_name ?? '') }}" required>
                            @error('last_name')<div style="color:#dc3545;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="{{ old('phone', $contact->phone ?? '') }}" required>
                            @error('phone')<div style="color:#dc3545;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="button-group">
                            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                            <button type="button" class="btn-secondary" data-cancel-edit="personal-info"><i class="fas fa-times"></i> Cancel</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Account Details -->
            <div class="collapsible-section">
                <div class="collapsible-header collapsed" data-target="account-info">
                    <div class="collapsible-header-title">
                        <i class="fas fa-shield-halved"></i> Account Details
                    </div>
                    <i class="fas fa-chevron-down collapsible-header-icon"></i>
                </div>
                <div class="collapsible-content" id="account-info">
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Role</div>
                            <div class="info-value">
                                @foreach(($user->roles ?? []) as $role)
                                    <span>{{ ucfirst($role) }}</span>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Member Since</div>
                            <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Last Login</div>
                            <div class="info-value">{{ $user->last_login ? $user->last_login->format('M d, Y g:i A') : 'N/A' }}</div>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="edit-button-inline" style="display:inline-block;text-decoration:none;margin-top:0.5rem;">
                        <i class="fas fa-cog"></i> Change Email / Password
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    document.querySelectorAll('.collapsible-header').forEach(header => {
        header.addEventListener('click', () => {
            const target = document.getElementById(header.dataset.target);
            const isCollapsed = header.classList.contains('collapsed');
            header.classList.toggle('collapsed', !isCollapsed);
            target.classList.toggle('show', isCollapsed);
        });
    });

    document.querySelectorAll('[data-edit-section]').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.dataset.editSection;
            document.getElementById(section + '-display').style.display = 'none';
            document.getElementById(section + '-form').classList.add('show');
        });
    });

    document.querySelectorAll('[data-cancel-edit]').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.dataset.cancelEdit;
            document.getElementById(section + '-display').style.display = '';
            document.getElementById(section + '-form').classList.remove('show');
        });
    });

    @if($errors->any())
        // Re-open the form if there were validation errors
        document.getElementById('personal-info-display').style.display = 'none';
        document.getElementById('personal-info-form').classList.add('show');
        const header = document.querySelector('[data-target="personal-info"]');
        header.classList.remove('collapsed');
        document.getElementById('personal-info').classList.add('show');
    @endif
</script>
@endsection
