@extends('layouts.app')

@section('title', 'Forgot Password - ChronoSync')

@section('extra-styles')
<style>
    .forgot-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 56px);
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
    }

    .forgot-card {
        width: 100%;
        max-width: 450px;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        padding: 2.5rem;
    }

    .forgot-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .forgot-icon {
        font-size: 2.5rem;
        color: #0099cc;
        margin-bottom: 1rem;
    }

    .forgot-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .forgot-subtitle {
        color: #666;
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-control {
        height: 2.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 0.75rem;
    }

    .form-control:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .btn-submit {
        width: 100%;
        height: 2.75rem;
        background-color: #0099cc;
        border-color: #0099cc;
        color: white;
        font-weight: 600;
        border-radius: 0.5rem;
        margin-top: 1.5rem;
    }

    .btn-submit:hover {
        background-color: #003366;
        border-color: #003366;
        color: white;
    }

    .back-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
    }

    .back-link a {
        color: #0099cc;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .back-link a:hover {
        text-decoration: underline;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 576px) {
        .forgot-card {
            margin: 1rem;
            padding: 1.5rem;
        }

        .forgot-title {
            font-size: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="forgot-container">
    <div class="forgot-card">
        <div class="forgot-header">
            <div class="forgot-icon">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="forgot-title">Forgot Password?</h1>
            <p class="forgot-subtitle">
                No worries! Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        @if(session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>
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
                    autofocus
                    placeholder="your@email.com"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="fas fa-envelope"></i> Send Reset Link
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
