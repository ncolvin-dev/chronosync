@extends('layouts.app')

@section('title', 'Settings - ChronoSync')

@section('extra-styles')
<style>
    .settings-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }

    html.dark .settings-title {
        color: #93c5fd !important;
    }

    .settings-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    html.dark .settings-card {
        background: #1a2235;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }

    .settings-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    html.dark .settings-card-header {
        border-color: #2a3a50;
    }

    .settings-card-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #e8f4f8;
        color: #0099cc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    html.dark .settings-card-header-icon {
        background-color: #1e3a50;
    }

    .settings-card-title {
        font-weight: 600;
        color: #003366;
        font-size: 1rem;
        margin: 0;
    }

    html.dark .settings-card-title {
        color: #93c5fd !important;
    }

    .settings-card-subtitle {
        font-size: 0.8rem;
        color: #888;
        margin: 0;
    }

    .settings-card-body {
        padding: 1.5rem;
    }

    .settings-form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #333;
        margin-bottom: 0.4rem;
    }

    html.dark .settings-form-label {
        color: #cbd5e1;
    }

    .settings-hint {
        font-size: 0.78rem;
        color: #888;
        margin-top: 0.3rem;
    }

    /* Dark mode toggle */
    .dark-mode-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.25rem 0;
    }

    .dark-mode-label {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .dark-mode-label strong {
        font-size: 0.95rem;
        color: #333;
    }

    html.dark .dark-mode-label strong {
        color: #e2e8f0;
    }

    .dark-mode-label span {
        font-size: 0.8rem;
        color: #888;
    }

    /* Toggle switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        border-radius: 28px;
        transition: 0.3s;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    input:checked + .toggle-slider {
        background-color: #0099cc;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .btn-settings-save {
        padding: 0.6rem 1.5rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: not-allowed;
        opacity: 0.6;
    }

</style>
@endsection

@section('content')
<div class="container" style="max-width: 680px;">

    <div style="margin-bottom: 2rem;">
        <h1 class="settings-title"><i class="fas fa-cog"></i> Settings</h1>
        <p style="color: #666; font-size: 0.9rem; margin: 0;">Manage your account preferences.</p>
    </div>

    <!-- Change Email -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-header-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div>
                <p class="settings-card-title">Change Email</p>
                <p class="settings-card-subtitle">Update the email address for your account.</p>
            </div>
        </div>
        <div class="settings-card-body">
            <div class="mb-3">
                <label class="settings-form-label">Current Email</label>
                <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
            </div>
            <div class="mb-3">
                <label class="settings-form-label">New Email</label>
                <input type="email" class="form-control" placeholder="Enter new email address" disabled>
            </div>
            <div class="mb-4">
                <label class="settings-form-label">Confirm New Email</label>
                <input type="email" class="form-control" placeholder="Confirm new email address" disabled>
            </div>
            <button class="btn-settings-save" disabled>
                <i class="fas fa-save"></i> Save Email
            </button>
        </div>
    </div>

    <!-- Change Password -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-header-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div>
                <p class="settings-card-title">Change Password</p>
                <p class="settings-card-subtitle">Choose a strong password with at least 10 characters.</p>
            </div>
        </div>
        <div class="settings-card-body">
            <div class="mb-3">
                <label class="settings-form-label">Current Password</label>
                <input type="password" class="form-control" placeholder="Enter current password" disabled>
            </div>
            <div class="mb-3">
                <label class="settings-form-label">New Password</label>
                <input type="password" class="form-control" placeholder="Enter new password" disabled>
                <p class="settings-hint">Min. 10 characters with uppercase, lowercase, number, and special character.</p>
            </div>
            <div class="mb-4">
                <label class="settings-form-label">Confirm New Password</label>
                <input type="password" class="form-control" placeholder="Confirm new password" disabled>
            </div>
            <button class="btn-settings-save" disabled>
                <i class="fas fa-save"></i> Save Password
            </button>
        </div>
    </div>

    <!-- Appearance -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-header-icon">
                <i class="fas fa-moon"></i>
            </div>
            <div>
                <p class="settings-card-title">Appearance</p>
                <p class="settings-card-subtitle">Customize how ChronoSync looks for you.</p>
            </div>
        </div>
        <div class="settings-card-body">
            <div class="dark-mode-row">
                <div class="dark-mode-label">
                    <strong><i class="fas fa-moon" style="margin-right: 0.4rem; color: #0099cc;"></i>Dark Mode</strong>
                    <span>Easier on the eyes in low-light environments.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

</div>
@endsection

@section('extra-scripts')
<script>
    const toggle = document.getElementById('darkModeToggle');
    const html = document.getElementById('htmlRoot');

    // Set toggle state from localStorage
    toggle.checked = localStorage.getItem('darkMode') === 'true';

    toggle.addEventListener('change', function () {
        if (this.checked) {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        }
    });
</script>
@endsection
