<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ChronoSync') - Volunteer Meeting Coordination</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        :root {
            --navy: #003366;
            --light-navy: #004080;
            --sky-blue: #0099cc;
            --success-green: #28a745;
            --pending-yellow: #ffc107;
            --unfilled-red: #dc3545;
            --light-gray: #f8f9fa;
        }

        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: var(--light-gray);
            padding-top: 56px;
        }

        .navbar {
            background-color: var(--navy);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: color 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }

        .btn-primary {
            background-color: var(--sky-blue);
            border-color: var(--sky-blue);
        }

        .btn-primary:hover {
            background-color: var(--light-navy);
            border-color: var(--light-navy);
        }

        .badge-confirmed {
            background-color: var(--success-green);
        }

        .badge-pending {
            background-color: var(--pending-yellow);
            color: #333;
        }

        .badge-unfilled {
            background-color: var(--unfilled-red);
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--light-gray);
            border-bottom: 1px solid #e0e0e0;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--sky-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--sky-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        }

        .flash-messages {
            position: fixed;
            top: 56px;
            right: 0;
            z-index: 1050;
            width: 100%;
            max-width: 500px;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 56px;
            }

            .flash-messages {
                max-width: 100%;
            }
        }

        .main-content {
            min-height: calc(100vh - 56px);
            padding: 1.5rem 0;
        }

        .session-warning {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1060;
            min-width: 300px;
        }

        /* ── Dark Mode ── */
        html.dark {
            color-scheme: dark;
        }

        html.dark body {
            background-color: #0f1117;
            color: #e2e8f0;
        }

        html.dark .navbar {
            background-color: #0a0f1a !important;
            border-bottom: 1px solid #1e2a3a;
        }

        html.dark .main-content {
            background-color: #0f1117;
        }

        html.dark .card,
        html.dark .filter-section,
        html.dark .meetings-container,
        html.dark .section-card,
        html.dark .stat-card,
        html.dark .modal-content {
            background-color: #1a2235 !important;
            border-color: #2a3a50 !important;
            color: #e2e8f0;
        }

        html.dark .card-header {
            background-color: #1e2a3a !important;
            border-color: #2a3a50 !important;
            color: #e2e8f0;
        }

        html.dark .form-control,
        html.dark .form-select {
            background-color: #0f1117;
            border-color: #2a3a50;
            color: #e2e8f0;
        }

        html.dark .form-control:focus,
        html.dark .form-select:focus {
            background-color: #0f1117;
            color: #e2e8f0;
            border-color: var(--sky-blue);
        }

        html.dark .form-label,
        html.dark .filter-title,
        html.dark .matching-title,
        html.dark .section-title,
        html.dark .stat-card-value,
        html.dark h1, html.dark h2, html.dark h3,
        html.dark h4, html.dark h5, html.dark h6 {
            color: #e2e8f0 !important;
        }

        html.dark .meetings-table th {
            background-color: #1e2a3a !important;
            color: #93c5fd !important;
            border-color: #2a3a50 !important;
        }

        html.dark .meetings-table td,
        html.dark .meeting-row {
            border-color: #2a3a50 !important;
            color: #e2e8f0;
        }

        html.dark .meetings-table tr:hover,
        html.dark .meeting-row:hover {
            background-color: #1e2a3a !important;
        }

        html.dark .volunteer-chip {
            background-color: #1e2a3a !important;
            color: #e2e8f0 !important;
        }

        html.dark .meeting-info-label,
        html.dark .stat-card-label,
        html.dark p, html.dark small, html.dark span {
            color: #94a3b8;
        }

        html.dark .meeting-info-value,
        html.dark .meeting-info-value *,
        html.dark .volunteers-title {
            color: #e2e8f0 !important;
        }

        html.dark .alert-success {
            background-color: #14532d;
            border-color: #166534;
            color: #bbf7d0;
        }

        html.dark .alert-danger {
            background-color: #450a0a;
            border-color: #7f1d1d;
            color: #fecaca;
        }

        html.dark .alert-warning {
            background-color: #431407;
            border-color: #7c2d12;
            color: #fed7aa;
        }

        html.dark .dropdown-menu {
            background-color: #1a2235;
            border-color: #2a3a50;
        }

        html.dark .dropdown-item {
            color: #e2e8f0;
        }

        html.dark .dropdown-item:hover {
            background-color: #1e2a3a;
            color: #fff;
        }

        html.dark .modal-header {
            border-color: #2a3a50;
        }

        html.dark .modal-footer {
            border-color: #2a3a50;
            background-color: #1a2235;
        }

        html.dark .table {
            color: #e2e8f0;
        }

        html.dark .table td, html.dark .table th {
            border-color: #2a3a50;
        }

        html.dark hr, html.dark .dropdown-divider {
            border-color: #2a3a50;
        }

        html.dark .text-muted {
            color: #64748b !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #475569;
        }
    </style>

    @yield('extra-styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-clock-list"></i> ChronoSync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->hasRole('volunteer') && !auth()->user()->hasAnyRole(['coordinator', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('volunteer.dashboard') ? 'active' : '' }}" href="{{ route('volunteer.dashboard') }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('volunteer.availability') ? 'active' : '' }}" href="{{ route('volunteer.availability') }}">
                                    <i class="fas fa-calendar"></i> Availability
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('volunteer.assignments') ? 'active' : '' }}" href="{{ route('volunteer.assignments') }}">
                                    <i class="fas fa-tasks"></i> Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('volunteer.profile') ? 'active' : '' }}" href="{{ route('volunteer.profile') }}">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                            </li>
                        @elseif(auth()->user()->hasAnyRole(['coordinator', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.dashboard') ? 'active' : '' }}" href="{{ route('coordinator.dashboard') }}">
                                    <i class="fas fa-chart-line"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.facilities') ? 'active' : '' }}" href="{{ route('coordinator.facilities') }}">
                                    <i class="fas fa-building"></i> Facilities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.volunteers') ? 'active' : '' }}" href="{{ route('coordinator.volunteers') }}">
                                    <i class="fas fa-users"></i> Volunteers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.coordinators') ? 'active' : '' }}" href="{{ route('coordinator.coordinators') }}">
                                    <i class="fas fa-user-tie"></i> Coordinators
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('meetings.index') ? 'active' : '' }}" href="{{ route('meetings.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Meetings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.matching') ? 'active' : '' }}" href="{{ route('coordinator.matching') }}">
                                    <i class="fas fa-link"></i> Matching
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('coordinator.credentials') ? 'active' : '' }}" href="{{ route('coordinator.credentials') }}">
                                    <i class="fas fa-certificate"></i> Credentials
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-file-chart-line"></i> Reports
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                                    <li><a class="dropdown-item" href="{{ route('reports.coverage') }}">Coverage Report</a></li>
                                    <li><a class="dropdown-item" href="{{ route('coordinator.sms-config') }}">SMS Configuration</a></li>
                                </ul>
                            </li>
                        @endif

                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if($errors->any())
        <div class="flash-messages">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="flash-messages">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-messages">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="flash-messages">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <!-- Session Warning Modal -->
    <div class="modal fade" id="sessionWarningModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-clock"></i> Session Timeout Warning</h5>
                </div>
                <div class="modal-body">
                    <p>Your session will expire in <strong id="countdown">5</strong> minutes due to inactivity.</p>
                    <p>Click "Stay Logged In" to extend your session or you will be logged out automatically.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="logoutBtn">Logout Now</button>
                    <button type="button" class="btn btn-primary" id="stayLoggedInBtn">Stay Logged In</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dark mode: apply before paint to avoid flash -->
    <script>
        (function () {
            if (localStorage.getItem('darkMode') === 'true') {
                document.getElementById('htmlRoot').classList.add('dark');
            }
        })();
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Session Timeout Script -->
    <script>
        @auth
        (function() {
            const TIMEOUT_WARNING = 55 * 60 * 1000; // 55 minutes
            const TIMEOUT_LOGOUT = 60 * 60 * 1000;  // 60 minutes
            const COUNTDOWN_DURATION = 5 * 60 * 1000; // 5 minutes warning duration

            let timeoutWarningTimer;
            let timeoutLogoutTimer;
            let countdownInterval;
            let warningShown = false;

            function resetTimers() {
                clearTimeout(timeoutWarningTimer);
                clearTimeout(timeoutLogoutTimer);
                clearInterval(countdownInterval);
                warningShown = false;
                const modal = bootstrap.Modal.getInstance(document.getElementById('sessionWarningModal'));
                if (modal) {
                    modal.hide();
                }

                timeoutWarningTimer = setTimeout(() => {
                    showWarning();
                }, TIMEOUT_WARNING);

                timeoutLogoutTimer = setTimeout(() => {
                    logout();
                }, TIMEOUT_LOGOUT);
            }

            function showWarning() {
                if (!warningShown) {
                    warningShown = true;
                    const modal = new bootstrap.Modal(document.getElementById('sessionWarningModal'));
                    modal.show();

                    let remaining = COUNTDOWN_DURATION / 1000;
                    document.getElementById('countdown').textContent = Math.ceil(remaining / 60);

                    countdownInterval = setInterval(() => {
                        remaining -= 1;
                        document.getElementById('countdown').textContent = Math.ceil(remaining / 60);
                        if (remaining <= 0) {
                            clearInterval(countdownInterval);
                        }
                    }, 1000);
                }
            }

            function logout() {
                window.location.href = '{{ route("logout") }}';
            }

            // Attach event listeners
            document.getElementById('stayLoggedInBtn').addEventListener('click', resetTimers);
            document.getElementById('logoutBtn').addEventListener('click', logout);

            // Track user activity
            ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetTimers, true);
            });

            // Initial setup
            resetTimers();
        })();
        @endauth
    </script>

    @yield('extra-scripts')

    <!-- Auto-dismiss success/warning flash messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.flash-messages .alert-success, .flash-messages .alert-warning').forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'opacity 0.6s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () {
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    }, 600);
                }, 3000);
            });
        });
    </script>
</body>
</html>
