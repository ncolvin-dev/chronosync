@extends('layouts.app')

@section('title', 'Login - ChronoSync')

@section('extra-styles')
<style>
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 56px);
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        padding: 2rem;
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-logo {
        font-size: 2.5rem;
        color: #003366;
        margin-bottom: 0.5rem;
    }

    .login-title {
        color: #003366;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .login-subtitle {
        color: #666;
        font-size: 0.875rem;
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

    .form-control {
        height: 2.75rem;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
    }

    .form-check {
        margin-bottom: 1rem;
    }

    .form-check-label {
        color: #666;
        font-size: 0.875rem;
    }

    .login-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .forgot-password {
        color: #0099cc;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        height: 2.75rem;
        background-color: #0099cc;
        border-color: #0099cc;
        color: white;
        font-weight: 600;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .btn-login:hover {
        background-color: #003366;
        border-color: #003366;
        color: white;
    }

    .signup-section {
        text-align: center;
        border-top: 1px solid #e0e0e0;
        padding-top: 1.5rem;
    }

    .signup-text {
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .signup-link {
        color: #0099cc;
        text-decoration: none;
        font-weight: 600;
    }

    .signup-link:hover {
        text-decoration: underline;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    @media (max-width: 576px) {
        .login-card {
            margin: 1rem;
            padding: 1.5rem;
        }

        .login-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-clock-list"></i>
            </div>
            <h1 class="login-title">ChronoSync</h1>
            <p class="login-subtitle">Volunteer Meeting Coordination</p>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="remember"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>

            <div class="login-actions">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Login failed. Please check your credentials.
                </div>
            @endif

            <div style="text-align: center; margin-bottom: 1rem;">
                <a href="{{ route('password.request') }}" class="forgot-password">
                    Forgot your password?
                </a>
            </div>
        </form>

        <div class="signup-section">
            <p class="signup-text">Don't have an account?</p>
            <a href="{{ route('register') }}" class="signup-link">
                Create one now <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Client-side validation
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        // Simple email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }

        if (password.length < 1) {
            e.preventDefault();
            alert('Please enter your password.');
            return false;
        }
    });
</script>
@endsection
