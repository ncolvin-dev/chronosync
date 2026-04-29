<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Volunteer;
use App\Models\VolunteerCredential;

class DashboardController extends Controller
{
    public function index()
    {
        $this->authorizeCoordinatorOrAdmin();

        $today = now()->toDateString();

        // Stat cards
        $activeVolunteers   = Volunteer::count();
        $newVolunteersMonth = Volunteer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $activeFacilities = Facility::where('status', 'active')->count();
        $activeMeetings   = Meeting::where('status', 'active')->count();

        $pendingConfirmations = MeetingAssignment::where('status', 'pending_confirmation')
            ->where('assignment_date', '>=', $today)
            ->count();

        // Meetings with no upcoming confirmed/pending assignment at all
        $meetingsNeedingVolunteers = Meeting::where('status', 'active')
            ->whereDoesntHave('assignments', function ($q) use ($today) {
                $q->whereIn('status', ['confirmed', 'pending_confirmation'])
                  ->where('assignment_date', '>=', $today);
            })
            ->count();

        // This week's schedule (next 7 days)
        $weekEnd         = now()->addDays(6)->toDateString();
        $weekAssignments = MeetingAssignment::with(['volunteer', 'meeting.facility'])
            ->whereBetween('assignment_date', [$today, $weekEnd])
            ->whereIn('status', ['confirmed', 'pending_confirmation'])
            ->orderBy('assignment_date')
            ->get();

        // Alerts
        $expiringCredentials = VolunteerCredential::where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$today, now()->addDays(30)->toDateString()])
            ->count();

        // Recent activity — last 5 audit log entries
        $recentActivity = AuditLog::with('actor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('coordinator.dashboard', compact(
            'activeVolunteers',
            'newVolunteersMonth',
            'activeFacilities',
            'activeMeetings',
            'pendingConfirmations',
            'meetingsNeedingVolunteers',
            'weekAssignments',
            'expiringCredentials',
            'recentActivity',
        ));
    }
}
