<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AvailabilityController;
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
    Route::get('/', function () {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];
        if (in_array('admin', $roles) || in_array('coordinator', $roles)) {
            return view('coordinator.dashboard');
        }
        return view('volunteer.dashboard');
    })->name('dashboard');
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

    Route::middleware('role:volunteer,coordinator,admin')->group(function () {
        Route::get('/volunteers/{volunteer}/edit', [VolunteerController::class, 'edit'])
            ->name('volunteers.edit');
        Route::patch('/volunteers/{volunteer}', [VolunteerController::class, 'update'])
            ->name('volunteers.update');
    });

    // Coordinator/Admin only
    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/volunteers', [VolunteerController::class, 'index'])
            ->name('volunteers.index');

        Route::delete('/volunteers/{volunteer}', [VolunteerController::class, 'destroy'])
            ->name('volunteers.destroy');
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

        // Automatic matching
        Route::post('/meetings/{meeting}/assign', [MeetingController::class, 'assign'])
            ->name('meetings.assign');

        // Manual assignment with override
        Route::post('/meetings/{meeting}/manual-assign', [MeetingController::class, 'manualAssign'])
            ->name('meetings.manual-assign');

        // Unassign volunteer
        Route::post('/meetings/{meeting}/unassign', [MeetingController::class, 'unassign'])
            ->name('meetings.unassign');

        // Complete meeting
        Route::post('/meetings/{meeting}/complete', [MeetingController::class, 'complete'])
            ->name('meetings.complete');

        // Cancel meeting
        Route::post('/meetings/{meeting}/cancel', [MeetingController::class, 'cancel'])
            ->name('meetings.cancel');

        // Send reminder SMS
        Route::post('/meetings/{meeting}/send-reminder', [SmsController::class, 'sendReminder'])
            ->name('meetings.send-reminder');
    });

    /*
    |----------------------------------------------------------------------
    | Matching API Routes (Coordinator/Admin)
    |----------------------------------------------------------------------
    */

    Route::middleware('role:coordinator,admin')->group(function () {
        Route::get('/api/meetings/{meeting}/candidates', [MatchingController::class, 'getCandidates'])
            ->name('api.candidates');

        Route::post('/api/meetings/{meeting}/auto-assign', [MatchingController::class, 'autoAssign'])
            ->name('api.auto-assign');

        Route::get('/api/meetings/{meeting}/suggestions', [MatchingController::class, 'getSuggestions'])
            ->name('api.suggestions');
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

// Coordinator route aliases for dashboard
Route::redirect('/coordinator/matching', '/meetings')->name('coordinator.matching');
Route::redirect('/coordinator/facilities', '/facilities')->name('coordinator.facilities');

// Coordinator route aliases
Route::redirect('/coordinator/dashboard', '/')->name('coordinator.dashboard');
Route::redirect('/coordinator/volunteers', '/volunteers')->name('coordinator.volunteers');
Route::redirect('/coordinator/credentials', '/credentials')->name('coordinator.credentials');
Route::redirect('/coordinator/sms-config', '/sms/configure')->name('coordinator.sms-config');

// Volunteer route aliases
Route::redirect('/volunteer/dashboard', '/')->name('volunteer.dashboard');
Route::get('/volunteer/profile', function() {
    $volunteer = \App\Models\Volunteer::where('email', auth()->user()->email)->first();
    if (!$volunteer) {
        return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
    }
    return redirect("/volunteers/{$volunteer->volunteer_id}");
})->middleware(['auth', 'session_timeout'])->name('volunteer.profile');

Route::post('/volunteer/profile/update', function(\Illuminate\Http\Request $request) {
    $volunteer = \App\Models\Volunteer::where('email', auth()->user()->email)->first();
    if (!$volunteer) {
        return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
    }
    $validated = $request->validate([
        'first_name'  => 'sometimes|string|max:255',
        'last_name'   => 'sometimes|string|max:255',
        'phone'       => 'sometimes|string|max:20',
        'dob'         => 'sometimes|date',
        'date_of_birth' => 'sometimes|date',
        'gender'      => 'sometimes|string|max:50',
        'clean_date'  => 'sometimes|date',
        'neighborhood'=> 'sometimes|nullable|string|max:255',
        'bus_line'    => 'sometimes|nullable|string|max:255',
    ]);
    // Map date_of_birth → dob if submitted that way
    if (isset($validated['date_of_birth']) && !isset($validated['dob'])) {
        $validated['dob'] = $validated['date_of_birth'];
        unset($validated['date_of_birth']);
    }
    $volunteer->fill($validated)->save();
    return redirect("/volunteers/{$volunteer->volunteer_id}")->with('success', 'Profile updated successfully.');
})->middleware(['auth', 'session_timeout'])->name('volunteer.profile.update');

Route::get('/volunteer/availability', function() {
    $volunteer = \App\Models\Volunteer::where('email', auth()->user()->email)->first();
    if (!$volunteer) {
        return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
    }
    return redirect("/volunteers/{$volunteer->volunteer_id}/availability");
})->middleware(['auth', 'session_timeout'])->name('volunteer.availability');

Route::get('/volunteer/assignments', function() {
    $volunteer = \App\Models\Volunteer::where('email', auth()->user()->email)->first();
    if (!$volunteer) {
        return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
    }
    return redirect("/volunteers/{$volunteer->volunteer_id}/meetings");
})->middleware(['auth', 'session_timeout'])->name('volunteer.assignments');

// Profile and reports aliases
Route::redirect('/profile/edit', '/')->name('profile.edit');
Route::redirect('/reports/coverage', '/reports/coverage-summary')->name('reports.coverage');
