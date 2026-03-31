@extends('layouts.app')

@section('title', 'Register - ChronoSync')

@section('extra-styles')
<style>
    .register-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem 0;
    }

    .register-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
        max-width: 600px;
        margin: 2rem auto;
    }

    .register-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .register-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .register-subtitle {
        color: #666;
        font-size: 0.875rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step-item {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        right: -50%;
        height: 2px;
        background-color: #e0e0e0;
    }

    .step-item.active:not(:last-child)::after {
        background-color: #0099cc;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 auto 0.5rem;
        font-size: 0.875rem;
    }

    .step-item.active .step-circle {
        background-color: #0099cc;
        color: white;
    }

    .step-item.completed .step-circle {
        background-color: #28a745;
        color: white;
    }

    .step-label {
        font-size: 0.75rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .form-control,
    .form-select {
        height: 2.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
    }

    .form-text {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.25rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .password-strength {
        height: 6px;
        background-color: #e0e0e0;
        border-radius: 3px;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .password-strength-bar.weak {
        width: 33%;
        background-color: #dc3545;
    }

    .password-strength-bar.fair {
        width: 66%;
        background-color: #ffc107;
    }

    .password-strength-bar.strong {
        width: 100%;
        background-color: #28a745;
    }

    .password-strength-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-weight: 600;
    }

    .password-strength-text.weak {
        color: #dc3545;
    }

    .password-strength-text.fair {
        color: #ffc107;
    }

    .password-strength-text.strong {
        color: #28a745;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-row.single {
        grid-template-columns: 1fr;
    }

    .conditional-field {
        display: none;
        animation: slideDown 0.3s ease;
    }

    .conditional-field.show {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-nav {
        height: 2.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
    }

    .btn-primary {
        background-color: #0099cc;
        border-color: #0099cc;
    }

    .btn-primary:hover {
        background-color: #003366;
        border-color: #003366;
    }

    .btn-outline-secondary {
        color: #666;
        border-color: #ddd;
    }

    .btn-outline-secondary:hover {
        background-color: #f0f0f0;
        border-color: #999;
        color: #333;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    .button-group button {
        flex: 1;
    }

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
    }

    .login-link a {
        color: #0099cc;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .register-card {
            padding: 1.5rem;
            margin: 1rem;
        }

        .register-title {
            font-size: 1.25rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .button-group {
            flex-direction: column-reverse;
        }

        .step-indicator {
            margin-bottom: 1.5rem;
        }

        .step-item:not(:last-child)::after {
            display: none;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1 class="register-title"><i class="fas fa-clock-list"></i> ChronoSync</h1>
            <p class="register-subtitle">Create your volunteer account</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-item active" id="step-1-indicator">
                <div class="step-circle">1</div>
                <div class="step-label">Personal Info</div>
            </div>
            <div class="step-item" id="step-2-indicator">
                <div class="step-circle">2</div>
                <div class="step-label">Details & Settings</div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
            @csrf

            <!-- STEP 1: Personal Information -->
            <div class="step-content active" id="step-1">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name *</label>
                        <input
                            type="text"
                            class="form-control @error('first_name') is-invalid @enderror"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            required
                        >
                        @error('first_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name *</label>
                        <input
                            type="text"
                            class="form-control @error('last_name') is-invalid @enderror"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                        >
                        @error('last_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
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
                    <label for="phone" class="form-label">Phone Number (US) *</label>
                    <input
                        type="tel"
                        class="form-control @error('phone') is-invalid @enderror"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="(123) 456-7890"
                        required
                    >
                    <div class="form-text">Format: (123) 456-7890</div>
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                    <input
                        type="date"
                        class="form-control @error('date_of_birth') is-invalid @enderror"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                        required
                    >
                    @error('date_of_birth')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- STEP 2: Details & Settings -->
            <div class="step-content" id="step-2">
                <div class="form-group">
                    <label for="clean_date" class="form-label">Clean Date *</label>
                    <input
                        type="date"
                        class="form-control @error('clean_date') is-invalid @enderror"
                        id="clean_date"
                        name="clean_date"
                        value="{{ old('clean_date') }}"
                        required
                    >
                    <div class="form-text">When did you become clean/sober?</div>
                    @error('clean_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender" class="form-label">Gender *</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="non-binary" {{ old('gender') === 'non-binary' ? 'selected' : '' }}>Non-Binary</option>
                            <option value="transgender" {{ old('gender') === 'transgender' ? 'selected' : '' }}>Transgender</option>
                            <option value="prefer-not-to-say" {{ old('gender') === 'prefer-not-to-say' ? 'selected' : '' }}>Prefer Not to Say</option>
                        </select>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="neighborhood" class="form-label">Neighborhood *</label>
                        <input
                            type="text"
                            class="form-control @error('neighborhood') is-invalid @enderror"
                            id="neighborhood"
                            name="neighborhood"
                            value="{{ old('neighborhood') }}"
                            placeholder="e.g., Downtown, Eastside"
                            required
                        >
                        @error('neighborhood')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="bus_line" class="form-label">Primary Bus Line (Optional)</label>
                    <input
                        type="text"
                        class="form-control @error('bus_line') is-invalid @enderror"
                        id="bus_line"
                        name="bus_line"
                        value="{{ old('bus_line') }}"
                        placeholder="e.g., Line 5, Line 12"
                    >
                    @error('bus_line')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Treatment Facility Experience *</label>
                    <div>
                        <div class="form-check">
                            <input
                                type="radio"
                                class="form-check-input"
                                id="has_treatment_yes"
                                name="has_treatment_facility"
                                value="1"
                                {{ old('has_treatment_facility') === '1' ? 'checked' : '' }}
                                required
                            >
                            <label class="form-check-label" for="has_treatment_yes">
                                Yes, I have treatment facility experience
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                type="radio"
                                class="form-check-input"
                                id="has_treatment_no"
                                name="has_treatment_facility"
                                value="0"
                                {{ old('has_treatment_facility') === '0' ? 'checked' : '' }}
                                required
                            >
                            <label class="form-check-label" for="has_treatment_no">
                                No, I do not have treatment facility experience
                            </label>
                        </div>
                    </div>
                </div>

                <div class="conditional-field" id="treatment-fields">
                    <div class="form-group">
                        <label for="treatment_facility_name" class="form-label">Facility Name</label>
                        <input
                            type="text"
                            class="form-control @error('treatment_facility_name') is-invalid @enderror"
                            id="treatment_facility_name"
                            name="treatment_facility_name"
                            value="{{ old('treatment_facility_name') }}"
                        >
                        @error('treatment_facility_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="treatment_completion_date" class="form-label">Completion Date</label>
                        <input
                            type="date"
                            class="form-control @error('treatment_completion_date') is-invalid @enderror"
                            id="treatment_completion_date"
                            name="treatment_completion_date"
                            value="{{ old('treatment_completion_date') }}"
                        >
                        @error('treatment_completion_date')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Current Probation Status *</label>
                    <div>
                        <div class="form-check">
                            <input
                                type="radio"
                                class="form-check-input"
                                id="probation_yes"
                                name="on_probation"
                                value="1"
                                {{ old('on_probation') === '1' ? 'checked' : '' }}
                                required
                            >
                            <label class="form-check-label" for="probation_yes">
                                Yes, I am currently on probation
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                type="radio"
                                class="form-check-input"
                                id="probation_no"
                                name="on_probation"
                                value="0"
                                {{ old('on_probation') === '0' ? 'checked' : '' }}
                                required
                            >
                            <label class="form-check-label" for="probation_no">
                                No, I am not on probation
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password *</label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                        >
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                        <input
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                        >
                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="button-group">
                <button type="button" class="btn btn-outline-secondary btn-nav" id="prevBtn" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-nav" id="nextBtn">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-primary btn-nav" id="submitBtn" style="display: none;">
                    <i class="fas fa-check"></i> Create Account
                </button>
            </div>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Login here</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Multi-step form management
    let currentStep = 1;
    const totalSteps = 2;

    function showStep(stepNum) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.remove('active');
        });

        // Show current step
        document.getElementById('step-' + stepNum).classList.add('active');

        // Update indicators
        document.querySelectorAll('.step-item').forEach((el, idx) => {
            el.classList.remove('active', 'completed');
            if (idx + 1 < stepNum) {
                el.classList.add('completed');
            } else if (idx + 1 === stepNum) {
                el.classList.add('active');
            }
        });

        // Show/hide buttons
        document.getElementById('prevBtn').style.display = stepNum === 1 ? 'none' : 'block';
        document.getElementById('nextBtn').style.display = stepNum === totalSteps ? 'none' : 'block';
        document.getElementById('submitBtn').style.display = stepNum === totalSteps ? 'block' : 'none';

        currentStep = stepNum;
    }

    // Treatment facility conditional field
    function toggleTreatmentFields() {
        const hasTreatment = document.getElementById('has_treatment_yes').checked;
        const treatmentFields = document.getElementById('treatment-fields');
        if (hasTreatment) {
            treatmentFields.classList.add('show');
        } else {
            treatmentFields.classList.remove('show');
        }
    }

    document.getElementById('has_treatment_yes').addEventListener('change', toggleTreatmentFields);
    document.getElementById('has_treatment_no').addEventListener('change', toggleTreatmentFields);

    // Initialize treatment fields visibility
    if (document.getElementById('has_treatment_yes').checked) {
        document.getElementById('treatment-fields').classList.add('show');
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    function evaluatePasswordStrength(password) {
        let strength = 0;

        // Length checks
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Character variety checks
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        return Math.min(Math.ceil(strength / 2), 3);
    }

    passwordInput.addEventListener('input', function() {
        const strength = evaluatePasswordStrength(this.value);

        strengthBar.className = 'password-strength-bar';
        strengthText.className = 'password-strength-text';

        if (this.value.length === 0) {
            strengthBar.style.width = '0%';
            strengthText.textContent = '';
        } else if (strength === 1) {
            strengthBar.classList.add('weak');
            strengthText.classList.add('weak');
            strengthText.textContent = 'Weak password';
        } else if (strength === 2) {
            strengthBar.classList.add('fair');
            strengthText.classList.add('fair');
            strengthText.textContent = 'Fair password';
        } else if (strength >= 3) {
            strengthBar.classList.add('strong');
            strengthText.classList.add('strong');
            strengthText.textContent = 'Strong password';
        }
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = '(' + value;
            } else if (value.length <= 6) {
                value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
            } else {
                value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
            }
        }
        e.target.value = value;
    });

    // Button handlers
    document.getElementById('nextBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(currentStep)) {
            showStep(currentStep + 1);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function(e) {
        e.preventDefault();
        showStep(currentStep - 1);
    });

    function validateStep(step) {
        const form = document.getElementById('registerForm');
        if (step === 1) {
            const fields = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth'];
            return fields.every(field => {
                const input = document.getElementById(field);
                return input.value.trim() !== '';
            });
        }
        return true;
    }

    // Form submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return false;
        }
    });

    // Initialize
    showStep(1);
</script>
@endsection
