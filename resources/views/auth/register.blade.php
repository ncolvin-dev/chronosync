@extends('layouts.app')

@section('title', 'Sign Up - ChronoSync')

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
        max-width: 620px;
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

    .step-item.active:not(:last-child)::after,
    .step-item.completed:not(:last-child)::after {
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
        padding: 0.5rem 0.75rem;
        width: 100%;
        font-size: 0.9rem;
        transition: border-color 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        outline: none;
    }

    .form-text {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.25rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
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

    .password-strength-bar.weak   { width: 33%; background-color: #dc3545; }
    .password-strength-bar.fair   { width: 66%; background-color: #ffc107; }
    .password-strength-bar.strong { width: 100%; background-color: #28a745; }

    .password-strength-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-weight: 600;
    }

    .password-strength-text.weak   { color: #dc3545; }
    .password-strength-text.fair   { color: #ffc107; }
    .password-strength-text.strong { color: #28a745; }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-nav {
        height: 2.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0 1.25rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-primary {
        background-color: #0099cc;
        border: 1px solid #0099cc;
        color: white;
    }

    .btn-primary:hover {
        background-color: #003366;
        border-color: #003366;
    }

    .btn-outline-secondary {
        color: #666;
        border: 1px solid #ddd;
        background: transparent;
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

    .button-group button { flex: 1; }

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
        font-size: 0.875rem;
        color: #666;
    }

    .login-link a {
        color: #0099cc;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover { text-decoration: underline; }

    .step-content { display: none; }

    .step-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .section-heading {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #0099cc;
        border-bottom: 1px solid #e8f4fb;
        padding-bottom: 0.4rem;
        margin-bottom: 1.25rem;
    }

    .form-check {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .form-check-input {
        margin-top: 0.2rem;
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
        accent-color: #0099cc;
    }

    .form-check-label {
        font-size: 0.875rem;
        color: #444;
        line-height: 1.4;
    }

    .conditional-field {
        display: none;
    }

    .conditional-field.show {
        display: block;
        animation: fadeIn 0.2s ease;
    }

    /* ── Dark Mode ── */
    html.dark .register-container {
        background: linear-gradient(135deg, #0a1628 0%, #0c3a5c 100%);
    }

    html.dark .register-card {
        background-color: #1a2235;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    }

    html.dark .register-title { color: #38bdf8; }
    html.dark .register-subtitle { color: #94a3b8; }

    html.dark .section-heading {
        color: #38bdf8;
        border-bottom-color: #1e3a4a;
    }

    html.dark .step-circle {
        background-color: #2a3a50;
        color: #64748b;
    }

    html.dark .step-item.active .step-circle {
        background-color: #0099cc;
        color: white;
    }

    html.dark .step-item.completed .step-circle {
        background-color: #28a745;
        color: white;
    }

    html.dark .step-item::after { background-color: #2a3a50 !important; }

    html.dark .step-item.active::after,
    html.dark .step-item.completed::after { background-color: #0099cc !important; }

    html.dark .step-label { color: #64748b; }
    html.dark .form-label { color: #cbd5e1; }

    html.dark .form-control,
    html.dark .form-select {
        background-color: #141d2e;
        border-color: #2a3a50;
        color: #e2e8f0;
    }

    html.dark .form-control::placeholder { color: #4a5a6a; }

    html.dark .form-control:focus,
    html.dark .form-select:focus {
        background-color: #141d2e;
        border-color: #0099cc;
        color: #e2e8f0;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.2);
    }

    html.dark .form-select option {
        background-color: #1a2235;
        color: #e2e8f0;
    }

    html.dark .form-text { color: #64748b; }
    html.dark .form-check-label { color: #94a3b8; }

    html.dark .form-check-input {
        background-color: #141d2e;
        border-color: #2a3a50;
    }

    html.dark .form-check-input:checked {
        background-color: #0099cc;
        border-color: #0099cc;
    }

    html.dark .password-strength { background-color: #2a3a50; }

    html.dark .btn-outline-secondary {
        color: #94a3b8;
        border-color: #2a3a50;
        background: transparent;
    }

    html.dark .btn-outline-secondary:hover {
        background-color: #2a3a50;
        border-color: #4a5a6a;
        color: #e2e8f0;
    }

    html.dark .login-link {
        border-top-color: #2a3a50;
        color: #94a3b8;
    }

    html.dark .login-link a { color: #38bdf8; }

    html.dark .alert-danger {
        background-color: #2d1a1a;
        border-color: #5c2a2a;
        color: #fca5a5;
    }

    @media (max-width: 768px) {
        .register-card {
            padding: 1.5rem;
            margin: 1rem;
        }

        .register-title { font-size: 1.25rem; }

        .form-row { grid-template-columns: 1fr; gap: 0; }

        .button-group { flex-direction: column-reverse; }

        .step-item:not(:last-child)::after { display: none; }

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
            <h1 class="register-title"><i class="fas fa-clock"></i> ChronoSync</h1>
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
                <div class="step-label">Recovery</div>
            </div>
            <div class="step-item" id="step-3-indicator">
                <div class="step-circle">3</div>
                <div class="step-label">Account</div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-3" style="border-radius:0.5rem; padding:0.75rem 1rem; background:#fff5f5; border:1px solid #f5c6cb; color:#721c24; font-size:0.875rem;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('volunteers.store') }}" id="registerForm" novalidate>
            @csrf

            <!-- STEP 1: Personal Information -->
            <div class="step-content active" id="step-1">
                <div class="section-heading">Personal Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name <span style="color:#dc3545">*</span></label>
                        <input type="text"
                               class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name" name="first_name"
                               value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name <span style="color:#dc3545">*</span></label>
                        <input type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name" name="last_name"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span style="color:#dc3545">*</span></label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ old('email') }}"
                           autocomplete="email" required>
                    @error('email')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number <span style="color:#dc3545">*</span></label>
                    <input type="tel"
                           class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone"
                           value="{{ old('phone') }}"
                           placeholder="(123) 456-7890" required>
                    <div class="form-text">Format: (XXX) XXX-XXXX</div>
                    @error('phone')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dob" class="form-label">Date of Birth <span style="color:#dc3545">*</span></label>
                        <input type="date"
                               class="form-control @error('dob') is-invalid @enderror"
                               id="dob" name="dob"
                               value="{{ old('dob') }}" required>
                        @error('dob')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="gender" class="form-label">Gender <span style="color:#dc3545">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror"
                                id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="male"              {{ old('gender') === 'male'              ? 'selected' : '' }}>Male</option>
                            <option value="female"            {{ old('gender') === 'female'            ? 'selected' : '' }}>Female</option>
                            <option value="non-binary"        {{ old('gender') === 'non-binary'        ? 'selected' : '' }}>Non-Binary</option>
                            <option value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer Not to Say</option>
                            <option value="other"             {{ old('gender') === 'other'             ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- STEP 2: Recovery Information -->
            <div class="step-content" id="step-2">
                <div class="section-heading">Recovery Information</div>

                <div class="form-group">
                    <label class="form-label">Probation Status <span style="color:#dc3545">*</span></label>
                    <div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input"
                                   id="probation_no" name="on_probation" value="0"
                                   {{ old('on_probation', '0') === '0' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="probation_no">Not on probation</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input"
                                   id="probation_yes" name="on_probation" value="1"
                                   {{ old('on_probation') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="probation_yes">Currently on probation</label>
                        </div>
                    </div>
                    @error('on_probation')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="clean_date" class="form-label">Clean Date</label>
                    <input type="date"
                           class="form-control @error('clean_date') is-invalid @enderror"
                           id="clean_date" name="clean_date"
                           value="{{ old('clean_date') }}">
                    <div class="form-text">The date you became clean/sober (optional).</div>
                    @error('clean_date')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Treatment Facility Experience</label>
                    <div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input"
                                   id="treatment_no" name="has_treatment_facility" value="0"
                                   {{ old('has_treatment_facility', '0') === '0' ? 'checked' : '' }}
                                   onchange="toggleTreatment(false)">
                            <label class="form-check-label" for="treatment_no">No treatment facility experience</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input"
                                   id="treatment_yes" name="has_treatment_facility" value="1"
                                   {{ old('has_treatment_facility') === '1' ? 'checked' : '' }}
                                   onchange="toggleTreatment(true)">
                            <label class="form-check-label" for="treatment_yes">Yes, I have treatment facility experience</label>
                        </div>
                    </div>
                </div>

                <div class="conditional-field {{ old('has_treatment_facility') === '1' ? 'show' : '' }}" id="treatment-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="treatment_facility_name" class="form-label">Facility Name</label>
                            <input type="text"
                                   class="form-control @error('treatment_facility_name') is-invalid @enderror"
                                   id="treatment_facility_name" name="treatment_facility_name"
                                   value="{{ old('treatment_facility_name') }}">
                            @error('treatment_facility_name')<div class="error-message">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="discharge_date" class="form-label">Discharge Date</label>
                            <input type="date"
                                   class="form-control @error('discharge_date') is-invalid @enderror"
                                   id="discharge_date" name="discharge_date"
                                   value="{{ old('discharge_date') }}">
                            @error('discharge_date')<div class="error-message">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="neighborhood" class="form-label">Neighborhood</label>
                        <input type="text"
                               class="form-control @error('neighborhood') is-invalid @enderror"
                               id="neighborhood" name="neighborhood"
                               value="{{ old('neighborhood') }}"
                               placeholder="e.g. Downtown, Eastside">
                        @error('neighborhood')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="bus_line" class="form-label">Primary Bus Line</label>
                        <input type="text"
                               class="form-control @error('bus_line') is-invalid @enderror"
                               id="bus_line" name="bus_line"
                               value="{{ old('bus_line') }}"
                               placeholder="e.g. Line 5">
                        @error('bus_line')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- STEP 3: Account Setup -->
            <div class="step-content" id="step-3">
                <div class="section-heading">Set Your Password</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span style="color:#dc3545">*</span></label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password"
                               autocomplete="new-password" required>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                        <div class="form-text">Min 8 characters with uppercase, lowercase, and a number.</div>
                        @error('password')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password <span style="color:#dc3545">*</span></label>
                        <input type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               id="password_confirmation" name="password_confirmation"
                               autocomplete="new-password" required>
                        @error('password_confirmation')<div class="error-message">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="button-group">
                <button type="button" class="btn btn-outline-secondary btn-nav" id="prevBtn" style="display:none;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-nav" id="nextBtn">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-primary btn-nav" id="submitBtn" style="display:none;">
                    <i class="fas fa-check"></i> Create Account
                </button>
            </div>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Log in here</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    let currentStep = 1;
    const totalSteps = 3;

    function showStep(n) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + n).classList.add('active');

        document.querySelectorAll('.step-item').forEach((el, i) => {
            el.classList.remove('active', 'completed');
            if (i + 1 < n)      el.classList.add('completed');
            else if (i + 1 === n) el.classList.add('active');
        });

        document.getElementById('prevBtn').style.display   = n === 1 ? 'none' : 'block';
        document.getElementById('nextBtn').style.display   = n === totalSteps ? 'none' : 'block';
        document.getElementById('submitBtn').style.display = n === totalSteps ? 'block' : 'none';

        currentStep = n;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    const step1Fields = ['first_name', 'last_name', 'email', 'phone', 'dob', 'gender'];

    function validateStep(n) {
        if (n === 1) {
            return step1Fields.every(id => document.getElementById(id).value.trim() !== '');
        }
        return true;
    }

    document.getElementById('nextBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(currentStep)) {
            showStep(currentStep + 1);
        } else {
            step1Fields.forEach(id => {
                const el = document.getElementById(id);
                if (!el.value.trim()) el.style.borderColor = '#dc3545';
            });
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function(e) {
        e.preventDefault();
        showStep(currentStep - 1);
    });

    document.querySelectorAll('.form-control, .form-select').forEach(el => {
        el.addEventListener('input', () => { el.style.borderColor = ''; });
    });

    // Phone auto-format
    document.getElementById('phone').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 10);
        if (v.length > 6)      v = '(' + v.slice(0,3) + ') ' + v.slice(3,6) + '-' + v.slice(6);
        else if (v.length > 3) v = '(' + v.slice(0,3) + ') ' + v.slice(3);
        else if (v.length > 0) v = '(' + v;
        e.target.value = v;
    });

    // Treatment facility conditional
    function toggleTreatment(show) {
        document.getElementById('treatment-fields').classList.toggle('show', show);
    }

    // Password strength
    const strengthBar  = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    document.getElementById('password').addEventListener('input', function() {
        const pw = this.value;
        let score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^a-zA-Z0-9]/.test(pw)) score++;
        const s = Math.min(Math.ceil(score / 2), 3);

        strengthBar.className = 'password-strength-bar';
        strengthText.className = 'password-strength-text';
        if (!pw) { strengthBar.style.width = '0%'; strengthText.textContent = ''; return; }
        const cls = ['', 'weak', 'fair', 'strong'][s];
        strengthBar.classList.add(cls);
        strengthText.classList.add(cls);
        strengthText.textContent = ['', 'Weak', 'Fair', 'Strong'][s] + ' password';
    });

    // Submit validation
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const pw  = document.getElementById('password').value;
        const pwc = document.getElementById('password_confirmation').value;
        if (pw !== pwc) {
            e.preventDefault();
            document.getElementById('password_confirmation').style.borderColor = '#dc3545';
            alert('Passwords do not match.');
        }
    });

    // On validation error return, jump to step with errors
    @if($errors->hasAny(['password', 'password_confirmation']))
        showStep(3);
    @elseif($errors->hasAny(['on_probation', 'clean_date', 'has_treatment_facility', 'treatment_facility_name', 'discharge_date', 'neighborhood', 'bus_line']))
        showStep(2);
    @elseif($errors->any())
        showStep(1);
    @else
        showStep(1);
    @endif
</script>
@endsection
