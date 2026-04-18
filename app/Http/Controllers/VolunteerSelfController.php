<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerSelfController extends Controller
{
    private function resolveVolunteer(): ?Volunteer
    {
        return Volunteer::where('email', auth()->user()->email)->first();
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['coordinator', 'admin'])) {
            return view('coordinator.dashboard');
        }

        $volunteer           = $this->resolveVolunteer();
        $upcomingAssignments = collect();
        $pastAssignments     = collect();
        $availabilityByDay   = collect();
        $totalAvailableSlots = 0;

        if ($volunteer) {
            $upcomingAssignments = $volunteer->assignments()
                ->with('meeting.facility')
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->where('assignment_date', '>=', now()->toDateString())
                ->orderBy('assignment_date')
                ->take(5)
                ->get();

            $pastAssignments = $volunteer->assignments()
                ->with('meeting.facility')
                ->where('assignment_date', '<', now()->toDateString())
                ->orderBy('assignment_date', 'desc')
                ->take(5)
                ->get();

            $availability        = $volunteer->availability()->where('is_available', true)->get();
            $totalAvailableSlots = $availability->count();
            $availabilityByDay   = $availability
                ->groupBy('day_of_week')
                ->map(fn($slots) => $slots->pluck('hour_start')->sort()->values())
                ->sortKeys();
        }

        return view('volunteer.dashboard', compact(
            'volunteer', 'upcomingAssignments', 'pastAssignments',
            'availabilityByDay', 'totalAvailableSlots'
        ));
    }

    public function profileRedirect()
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
        }

        return redirect("/volunteers/{$volunteer->volunteer_id}");
    }

    public function updateProfile(Request $request)
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
        }

        $validated = $request->validate([
            'first_name'              => 'sometimes|string|max:255',
            'last_name'               => 'sometimes|string|max:255',
            'phone'                   => 'sometimes|string|max:20',
            'dob'                     => 'sometimes|nullable|date',
            'date_of_birth'           => 'sometimes|nullable|date',
            'gender'                  => 'sometimes|string|max:50',
            'clean_date'              => 'sometimes|nullable|date',
            'neighborhood'            => 'sometimes|nullable|string|max:255',
            'bus_line'                => 'sometimes|nullable|string|max:255',
            'has_treatment_facility'  => 'sometimes|boolean',
            'treatment_facility_name' => 'sometimes|nullable|string|max:255',
            'on_probation'            => 'sometimes|boolean',
        ]);

        if (isset($validated['date_of_birth']) && !isset($validated['dob'])) {
            $validated['dob'] = $validated['date_of_birth'];
            unset($validated['date_of_birth']);
        }

        if (array_key_exists('has_treatment_facility', $validated)) {
            $validated['treatment_facility'] = $validated['has_treatment_facility'];
            unset($validated['has_treatment_facility']);
        }

        if (array_key_exists('treatment_facility_name', $validated)) {
            $validated['facility_name'] = $validated['treatment_facility_name'];
            unset($validated['treatment_facility_name']);
        }

        if (array_key_exists('on_probation', $validated)) {
            $validated['probation_status'] = $validated['on_probation'] ? 'active_probation' : 'not_probation';
            unset($validated['on_probation']);
        }

        $volunteer->fill(array_filter($validated, fn($v) => $v !== null))->save();

        return redirect("/volunteers/{$volunteer->volunteer_id}")->with('success', 'Profile updated successfully.');
    }

    public function availabilityRedirect()
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
        }

        return redirect("/volunteers/{$volunteer->volunteer_id}/availability");
    }

    public function assignments()
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
        }

        $assignments = $volunteer->assignments()
            ->with('meeting.facility')
            ->orderBy('assignment_date', 'desc')
            ->get();

        return view('volunteer.assignments', compact('volunteer', 'assignments'));
    }
}
