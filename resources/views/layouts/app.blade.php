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

    <style>
        /*
         * Pagination — global styles applied to every page.
         *
         * Laravel uses a custom pagination template at
         * resources/views/vendor/pagination/default.blade.php which renders:
         *   <ul class="pagination">
         *     <li class="disabled">   ← prev/next when unavailable
         *     <li>                    ← normal page link
         *     <li class="active">     ← current page
         *
         * These styles override Bootstrap's default .pagination so the look
         * is consistent with ChronoSync's navy/teal brand colours across
         * volunteers, facilities, credentials, and matching pages.
         */

        /* Row of page buttons — centered with a dividing line above */
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

        /* Default button appearance — white pill with navy text */
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

        /* Hover — teal fill */
        .pagination li a:hover {
            background-color: #0099cc;
            border-color: #0099cc;
            color: white;
        }

        /* Current page — teal fill, no pointer needed */
        .pagination li.active span {
            background-color: #0099cc;
            border-color: #0099cc;
            color: white;
        }

        /* Disabled prev/next (on first or last page) — faded, no click */
        .pagination li.disabled span {
            opacity: 0.45;
            cursor: not-allowed;
            color: #999;
        }

        /* ── Dark mode overrides ── */

        /* Default button in dark mode — dark surface, muted text */
        html.dark .pagination li a,
        html.dark .pagination li span {
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        /* Hover in dark mode — same teal as light mode */
        html.dark .pagination li a:hover {
            background-color: #0099cc;
            border-color: #0099cc;
            color: white;
        }

        /* Active page in dark mode — same teal as light mode */
        html.dark .pagination li.active span {
            background-color: #0099cc;
            border-color: #0099cc;
            color: white;
        }

        /* Disabled in dark mode — faded slate text */
        html.dark .pagination li.disabled span {
            opacity: 0.45;
            color: #475569;
        }
    </style>

    @yield('extra-styles')
</head>
<body>
    <!-- Navigation -->
    <!-- Drawer overlay -->
    <div id="drawerOverlay" onclick="closeDrawer()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1040;"></div>

    <!-- Slide-out nav drawer -->
    <div id="navDrawer" style="position:fixed;top:0;left:0;height:100%;width:260px;background:#003366;z-index:1050;transform:translateX(-100%);transition:transform 0.25s ease;display:flex;flex-direction:column;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.12);">
            <span style="color:white;font-weight:700;font-size:1.05rem;"><i class="fas fa-clock-list"></i> ChronoSync</span>
            <button onclick="closeDrawer()" style="background:none;border:none;color:rgba(255,255,255,0.7);font-size:1.3rem;cursor:pointer;line-height:1;">×</button>
        </div>
        <nav style="flex:1;padding:0.5rem 0;">
            @auth
                @if(auth()->user()->hasRole('volunteer') && !auth()->user()->hasAnyRole(['coordinator', 'admin']))
                    <a class="drawer-link {{ request()->routeIs('volunteer.dashboard') ? 'drawer-active' : '' }}" href="{{ route('volunteer.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                    <a class="drawer-link {{ request()->routeIs('volunteer.availability') ? 'drawer-active' : '' }}" href="{{ route('volunteer.availability') }}"><i class="fas fa-calendar"></i> Availability</a>
                    <a class="drawer-link {{ request()->routeIs('volunteer.assignments') ? 'drawer-active' : '' }}" href="{{ route('volunteer.assignments') }}"><i class="fas fa-tasks"></i> Assignments</a>
                    <a class="drawer-link {{ request()->routeIs('volunteer.profile') ? 'drawer-active' : '' }}" href="{{ route('volunteer.profile') }}"><i class="fas fa-user"></i> Profile</a>
                    <a class="drawer-link {{ request()->routeIs('profile.edit') ? 'drawer-active' : '' }}" href="{{ route('profile.edit') }}"><i class="fas fa-cog"></i> Settings</a>
                @elseif(auth()->user()->hasAnyRole(['coordinator', 'admin']))
                    <a class="drawer-link {{ request()->routeIs('coordinator.dashboard') ? 'drawer-active' : '' }}" href="{{ route('coordinator.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.facilities') ? 'drawer-active' : '' }}" href="{{ route('coordinator.facilities') }}"><i class="fas fa-building"></i> Facilities</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.volunteers') ? 'drawer-active' : '' }}" href="{{ route('coordinator.volunteers') }}"><i class="fas fa-users"></i> Volunteers</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.coordinators') ? 'drawer-active' : '' }}" href="{{ route('coordinator.coordinators') }}"><i class="fas fa-user-tie"></i> Coordinators</a>
                    <a class="drawer-link {{ request()->routeIs('meetings.index') ? 'drawer-active' : '' }}" href="{{ route('meetings.index') }}"><i class="fas fa-calendar-alt"></i> Meetings</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.matching') ? 'drawer-active' : '' }}" href="{{ route('coordinator.matching') }}"><i class="fas fa-link"></i> Matching</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.credentials') ? 'drawer-active' : '' }}" href="{{ route('coordinator.credentials') }}"><i class="fas fa-certificate"></i> Credentials</a>
                    <div style="padding:0.35rem 1.25rem;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.4);margin-top:0.5rem;">Reports</div>
                    <a class="drawer-link {{ request()->routeIs('reports.coverage') ? 'drawer-active' : '' }}" href="{{ route('reports.coverage') }}"><i class="fas fa-file-alt"></i> Coverage Report</a>
                    <a class="drawer-link {{ request()->routeIs('coordinator.sms-config') ? 'drawer-active' : '' }}" href="{{ route('coordinator.sms-config') }}"><i class="fas fa-sms"></i> SMS Configuration</a>
                    <div style="height:1px;background:rgba(255,255,255,0.1);margin:0.5rem 1.25rem;"></div>
                    <a class="drawer-link {{ request()->routeIs('coordinator.profile') ? 'drawer-active' : '' }}" href="{{ route('coordinator.profile') }}"><i class="fas fa-user"></i> Profile</a>
                @endif
            @endauth
        </nav>
        @auth
        <div style="padding:0.75rem 1.25rem;border-top:1px solid rgba(255,255,255,0.12);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.75);font-size:0.9rem;cursor:pointer;padding:0;width:100%;text-align:left;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <nav class="navbar navbar-dark fixed-top" style="padding:0.6rem 1rem;">
        <div class="container-fluid" style="gap:1rem;">
            @unless(request()->routeIs('login', 'register'))
            <button onclick="openDrawer()" style="background:none;border:none;color:white;font-size:1.35rem;cursor:pointer;padding:0.25rem;line-height:1;" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
            @endunless
            <a class="navbar-brand mb-0" href="{{ route('home') }}" style="margin:0;">
                <i class="fas fa-clock-list"></i> ChronoSync
            </a>
            @auth
                @php
                    $volunteer   = \App\Models\Volunteer::where('email', auth()->user()->email)->first();
                    $displayName = $volunteer?->first_name ?? auth()->user()->email;
                @endphp
                <span style="color:rgba(255,255,255,0.85);font-size:0.875rem;margin-left:auto;white-space:nowrap;">
                    Welcome, {{ $displayName }}
                </span>
            @endauth
        </div>
    </nav>

    <style>
        .drawer-link {
            display: block;
            padding: 0.7rem 1.25rem;
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            font-size: 0.92rem;
            transition: background 0.15s, color 0.15s;
        }
        .drawer-link i { width: 1.25rem; }
        .drawer-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .drawer-active { background: rgba(0,153,204,0.25); color: white; border-left: 3px solid #0099cc; }
        html.dark #navDrawer { background: #0f172a; }
    </style>

    <script>
        function openDrawer() {
            document.getElementById('navDrawer').style.transform = 'translateX(0)';
            document.getElementById('drawerOverlay').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            document.getElementById('navDrawer').style.transform = 'translateX(-100%)';
            document.getElementById('drawerOverlay').style.display = 'none';
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
    </script>

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

    <!-- Auto-dismiss flash messages (success/warning after 3 s, errors after 6 s) -->
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
            document.querySelectorAll('.flash-messages .alert-danger').forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'opacity 0.6s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () {
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    }, 600);
                }, 6000);
            });
        });
    </script>
</body>
</html>
