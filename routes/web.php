<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\VolunteerSelfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Volunteer registration
    Route::get('/register', [VolunteerController::class, 'create'])->name('register');
    Route::post('/volunteers', [VolunteerController::class, 'store'])->name('volunteers.store');
});

/*
|--------------------------------------------------------------------------
| SMS Webhook (Unauthenticated)
|--------------------------------------------------------------------------
*/

Route::post('/sms/webhook/response', [SmsController::class, 'handleResponse'])->name('sms.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'session_timeout'])->group(function () {
    // Dashboard
    Route::get('/', [VolunteerSelfController::class, 'dashboard'])->name('dashboard');
    Route::redirect('/home', '/')->name('home');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |----------------------------------------------------------------------
    | Volunteer Routes
    |----------------------------------------------------------------------
    */

    Route::get('/volunteers/{volunteer}', [VolunteerController::class, 'show'])
        ->name('volunteers.show');

    // Confirm / decline / cancel an assignment (volunteers can act on their own)
    Route::post('/meeting-assignments/{meetingAssignment}/confirm', [MeetingController::class, 'confirmAssignment'])
        ->name('meeting-assignments.confirm');
    Route::post('/meeting-assignments/{meetingAssignment}/decline', [MeetingController::class, 'declineAssignment'])
        ->name('meeting-assignments.decline');
    Route::post('/meeting-assignments/{meetingAssignment}/cancel', [MeetingController::class, 'cancelAssignment'])
        ->name('meeting-assignments.cancel');
    Route::post('/meeting-assignments/{meetingAssignment}/reinstate', [MeetingController::class, 'reinstateAssignment'])
        ->name('meeting-assignments.reinstate');

    Route::middleware('role:volunteer,coordinator,admin')->group(function () {
        Route::get('/volunteers/{volunteer}/edit', [VolunteerController::class, 'edit'])
            ->name('volunteers.edit');
        Route::patch('/volunteers/{volunteer}', [VolunteerController::class, 'update'])
            ->name('volunteers.update');
    });

    // Coordinator/Admin only
    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/coordinators', [CoordinatorController::class, 'index'])
            ->name('coordinators.index');
        Route::post('/coordinators', [CoordinatorController::class, 'store'])
            ->name('coordinators.store');
        Route::patch('/coordinators/{user}', [CoordinatorController::class, 'update'])
            ->name('coordinators.update');

        Route::get('/volunteers', [VolunteerController::class, 'index'])
            ->name('volunteers.index');

        Route::post('/coordinator/volunteers', [VolunteerController::class, 'store'])
            ->name('coordinator.volunteers.store');

        Route::delete('/volunteers/{volunteer}', [VolunteerController::class, 'destroy'])
            ->name('volunteers.destroy');

        Route::patch('/coordinator/volunteers/{volunteer}', [VolunteerController::class, 'coordinatorUpdate'])
            ->name('coordinator.volunteers.update');

        Route::post('/coordinator/volunteers/{volunteer}/credentials', [VolunteerController::class, 'storeCredential'])
            ->name('coordinator.volunteers.credentials.store');

        Route::patch('/coordinator/volunteers/{volunteer}/credentials/{credential}', [VolunteerController::class, 'updateCredential'])
            ->name('coordinator.volunteers.credentials.update');

        Route::delete('/coordinator/volunteers/{volunteer}/credentials/{credential}', [VolunteerController::class, 'destroyCredential'])
            ->name('coordinator.volunteers.credentials.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Availability Routes
    |----------------------------------------------------------------------
    */

    Route::get('/volunteers/{volunteer}/availability', [AvailabilityController::class, 'show'])
        ->name('availability.show');

    Route::post('/volunteers/{volunteer}/availability', [AvailabilityController::class, 'update'])
        ->name('availability.update');

    Route::get('/api/volunteers/{volunteer}/availability-matching', [AvailabilityController::class, 'getForMatching'])
        ->name('availability.for-matching');

    // Admin bulk availability update
    Route::middleware('role:admin')->group(function () {
        Route::post('/volunteers/availability/bulk', [AvailabilityController::class, 'bulkUpdate'])
            ->name('availability.bulk-update');
    });

    /*
    |----------------------------------------------------------------------
    | Facility Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    */

    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/facilities', [FacilityController::class, 'index'])
            ->name('facilities.index');

        Route::get('/facilities/create', [FacilityController::class, 'create'])
            ->name('facilities.create');

        Route::post('/facilities', [FacilityController::class, 'store'])
            ->name('facilities.store');

        Route::get('/facilities/{facility}', [FacilityController::class, 'show'])
            ->name('facilities.show');

        Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])
            ->name('facilities.edit');

        Route::patch('/facilities/{facility}', [FacilityController::class, 'update'])
            ->name('facilities.update');

        Route::patch('/facilities/{facility}/toggle-status', [FacilityController::class, 'toggleStatus'])
            ->name('facilities.toggle-status');

        Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])
            ->name('facilities.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Meeting Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    */

    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/meetings', [MeetingController::class, 'index'])
            ->name('meetings.index');

        Route::post('/meetings', [MeetingController::class, 'store'])
            ->name('meetings.store');

        Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])
            ->name('meetings.show');

        // Deactivate / reactivate / delete recurring meeting slots
        Route::post('/meetings/{meeting}/deactivate', [MeetingController::class, 'deactivate'])
            ->name('meetings.deactivate');
        Route::post('/meetings/{meeting}/activate', [MeetingController::class, 'activate'])
            ->name('meetings.activate');
        Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])
            ->name('meetings.destroy');

        // Assign a volunteer to a specific occurrence
        Route::post('/meetings/{meeting}/assign', [MeetingController::class, 'assign'])
            ->name('meetings.assign');

        // Send reminder SMS
        Route::post('/meetings/{meeting}/send-reminder', [SmsController::class, 'sendReminder'])
            ->name('meetings.send-reminder');

    });

    /*
    |----------------------------------------------------------------------
    | Matching API Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    |
    | Read endpoints allow 60 requests per minute per authenticated user —
    | enough for interactive use while protecting against runaway clients.
    | The auto-assign write endpoint is capped at 10 per minute because it
    | mutates data, triggers downstream jobs, and hits the database harder.
    |
    */

    // Read endpoints — candidates and suggestions lists
    Route::middleware(['role:coordinator,admin', 'throttle:60,1'])->group(function () {
        Route::get('/api/meetings/{meeting}/candidates', [MatchingController::class, 'getCandidates'])
            ->name('api.candidates');

        Route::get('/api/meetings/{meeting}/suggestions', [MatchingController::class, 'getSuggestions'])
            ->name('api.suggestions');
    });

    // Write endpoint — auto-assign gets a tighter rate limit
    Route::middleware(['role:coordinator,admin', 'throttle:10,1'])->group(function () {
        Route::post('/api/meetings/{meeting}/auto-assign', [MatchingController::class, 'autoAssign'])
            ->name('api.auto-assign');
    });

    /*
    |----------------------------------------------------------------------
    | Credential Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    */

    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/credentials', [CredentialController::class, 'index'])
            ->name('credentials.index');

        Route::post('/credentials', [CredentialController::class, 'store'])
            ->name('credentials.store');

        Route::patch('/credentials/{credential}/approve', [CredentialController::class, 'approve'])
            ->name('credentials.approve');

        Route::patch('/credentials/{credential}/deny', [CredentialController::class, 'deny'])
            ->name('credentials.deny');

        Route::post('/credentials/{credential}/renew', [CredentialController::class, 'renew'])
            ->name('credentials.renew');

        Route::patch('/credentials/{credential}', [CredentialController::class, 'update'])
            ->name('credentials.update');

        Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy'])
            ->name('credentials.destroy');

        Route::get('/credentials/expiring', [CredentialController::class, 'getExpiringCredentials'])
            ->name('credentials.expiring');
    });

    /*
    |----------------------------------------------------------------------
    | SMS Routes
    |----------------------------------------------------------------------
    */

    // Coordinator/Admin
    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/sms/log', [SmsController::class, 'getLog'])
            ->name('sms.log');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/sms/configure', [SmsController::class, 'configure'])
            ->name('sms.configure');

        Route::post('/sms/configure', [SmsController::class, 'configure'])
            ->name('sms.configure.save');

        Route::post('/sms/retry-failed', [SmsController::class, 'retryFailed'])
            ->name('sms.retry-failed');
    });

    /*
    |----------------------------------------------------------------------
    | Report Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    */

    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/reports/coverage-summary', [ReportController::class, 'coverageSummary'])
            ->name('reports.coverage-summary');

        Route::get('/reports/facility-schedule', [ReportController::class, 'facilitySchedule'])
            ->name('reports.facility-schedule');

        Route::get('/reports/credential-expiration', [ReportController::class, 'credentialExpiration'])
            ->name('reports.credential-expiration');

        Route::get('/reports/unfilled-meetings', [ReportController::class, 'unfilledMeetings'])
            ->name('reports.unfilled-meetings');

        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
            ->name('reports.export-csv');

        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.export-pdf');
    });

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {
        Route::delete('/coordinators/{user}', [CoordinatorController::class, 'destroy'])
            ->name('coordinators.destroy');

        // System settings, user management, audit logs, etc.
        Route::get('/admin/dashboard', function () {
            return view('coordinator.dashboard');
        })->name('admin.dashboard');

        Route::get('/admin/audit-logs', function () {
            return view('admin.audit-logs');
        })->name('admin.audit-logs');
    });
});

/*
|--------------------------------------------------------------------------
| Error Routes
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

/*
|--------------------------------------------------------------------------
| Coordinator Route Aliases
|--------------------------------------------------------------------------
|
| Stable /coordinator/* URLs used throughout the coordinator UI. Each one
| redirects to the canonical resource URL so the nav links never need to
| know the underlying path structure.
|
*/
Route::redirect('/coordinator/dashboard', '/')->name('coordinator.dashboard');
Route::redirect('/coordinator/matching', '/meetings')->name('coordinator.matching');
Route::redirect('/coordinator/facilities', '/facilities')->name('coordinator.facilities');
Route::redirect('/coordinator/volunteers', '/volunteers')->name('coordinator.volunteers');
Route::redirect('/coordinator/coordinators', '/coordinators')->name('coordinator.coordinators');
Route::redirect('/coordinator/credentials', '/credentials')->name('coordinator.credentials');
Route::redirect('/coordinator/sms-config', '/sms/configure')->name('coordinator.sms-config');

/*
|--------------------------------------------------------------------------
| Volunteer Self-Service Routes
|--------------------------------------------------------------------------
|
| Stable /volunteer/* URLs for the volunteer-facing navbar. These are alias
| routes — each one resolves the volunteer's ULID from the authenticated
| user's email and redirects or renders accordingly. Keeping them separate
| from the coordinator VolunteerController routes ensures volunteers can
| never accidentally access another user's data via URL manipulation.
|
*/
Route::redirect('/volunteer/dashboard', '/')->name('volunteer.dashboard');

Route::get('/volunteer/profile', [VolunteerSelfController::class, 'profileRedirect'])
    ->middleware(['auth', 'session_timeout'])->name('volunteer.profile');

Route::put('/volunteer/profile/update', [VolunteerSelfController::class, 'updateProfile'])
    ->middleware(['auth', 'session_timeout'])->name('volunteer.profile.update');

Route::get('/volunteer/availability', [VolunteerSelfController::class, 'availabilityRedirect'])
    ->middleware(['auth', 'session_timeout'])->name('volunteer.availability');

Route::get('/volunteer/assignments', [VolunteerSelfController::class, 'assignments'])
    ->middleware(['auth', 'session_timeout'])->name('volunteer.assignments');

/*
|--------------------------------------------------------------------------
| Account Settings
|--------------------------------------------------------------------------
|
| Email and password changes for the authenticated User record. These are
| separate from volunteer profile edits — settings affect login credentials,
| while profile edits affect scheduling data.
|
*/
Route::get('/settings', [SettingsController::class, 'index'])
    ->middleware(['auth', 'session_timeout'])->name('profile.edit');
Route::post('/settings/email', [SettingsController::class, 'updateEmail'])
    ->middleware(['auth', 'session_timeout'])->name('settings.email');
Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
    ->middleware(['auth', 'session_timeout'])->name('settings.password');

Route::redirect('/reports/coverage', '/reports/coverage-summary')->name('reports.coverage');
