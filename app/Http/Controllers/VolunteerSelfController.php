<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

/**
 * VolunteerSelfController
 *
 * Handles all self-service actions that a logged-in volunteer performs on
 * their own record. Every method here is scoped to the currently authenticated
 * user — no volunteer ID is accepted from the URL, preventing one volunteer
 * from acting on another's data.
 *
 * The User and Volunteer models are separate tables joined by email address.
 * `resolveVolunteer()` encapsulates that lookup so every action stays DRY.
 *
 * Route aliases (e.g. /volunteer/profile, /volunteer/assignments) live here
 * rather than in VolunteerController, which is reserved for coordinator/admin
 * operations on arbitrary volunteer records.
 */
class VolunteerSelfController extends Controller
{
    /**
     * Look up the Volunteer record that belongs to the authenticated user.
     *
     * Returns null if no matching volunteer exists (e.g. a user account was
     * created manually without going through volunteer registration).
     */
    private function resolveVolunteer(): ?Volunteer
    {
        return Volunteer::where('email', auth()->user()->email)->first();
    }

    /**
     * Render the role-appropriate dashboard.
     *
     * Coordinators and admins are sent to the coordinator dashboard — they
     * share the `/` root route because login always redirects there.
     * Volunteers see their own dashboard with upcoming assignments, recent
     * history, and an availability summary.
     *
     * All data is fetched here so the Blade view stays logic-free. Collections
     * default to empty so the view renders safely even if no volunteer record
     * exists yet.
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Coordinators and admins land on their own dashboard at the same URL.
        if ($user->hasAnyRole(['coordinator', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        $volunteer           = $this->resolveVolunteer();
        $upcomingAssignments = collect();
        $pastAssignments     = collect();
        $availabilityByDay   = collect();
        $totalAvailableSlots = 0;

        if ($volunteer) {
            // Upcoming: active statuses only, next 5 in chronological order.
            $upcomingAssignments = $volunteer->assignments()
                ->with('meeting.facility')
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->where('assignment_date', '>=', now()->toDateString())
                ->orderBy('assignment_date')
                ->take(5)
                ->get();

            // History: most recent 5 past assignments, newest first.
            $pastAssignments = $volunteer->assignments()
                ->with('meeting.facility')
                ->where('assignment_date', '<', now()->toDateString())
                ->orderBy('assignment_date', 'desc')
                ->take(5)
                ->get();

            // Availability summary: group individual hour slots by day so the
            // dashboard card can display "Mon 9 AM – 5 PM (8h)" style rows.
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

    /**
     * Redirect the volunteer to their full profile page.
     *
     * The canonical volunteer profile URL includes the volunteer_id
     * (e.g. /volunteers/01abc...). This route acts as a stable alias at
     * /volunteer/profile so the navbar link never needs to know the ID.
     */
    public function profileRedirect()
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('profile.edit');
        }

        return redirect("/volunteers/{$volunteer->volunteer_id}");
    }

    public function coordinatorProfile()
    {
        $user      = auth()->user();
        $volunteer = $this->resolveVolunteer();

        // Only show the full volunteer profile (with clean date, credentials, etc.)
        // if the user actually has the volunteer role — i.e. was promoted from volunteer.
        if ($volunteer && $user->hasRole('volunteer')) {
            $volunteer->load('credentials.credentialType');
            $readOnly = false;
            return view('volunteer.profile', compact('volunteer', 'readOnly'));
        }

        // Pure coordinators use the stripped-down coordinator profile view,
        // even if a Volunteer contact record exists (created when they saved name/phone).
        $contact = $volunteer;
        return view('coordinator.profile', compact('user', 'contact'));
    }

    public function coordinatorProfileUpdate(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|max:30',
        ]);

        $user = auth()->user();

        Volunteer::updateOrCreate(
            ['email' => $user->email],
            array_merge($validated, ['email' => $user->email])
        );

        return redirect()->route('coordinator.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Save changes submitted from the volunteer's profile edit form.
     *
     * The form uses several field names that differ from the Volunteer model's
     * column names (for clarity in the UI). This method remaps them before
     * calling fill():
     *
     *   date_of_birth          → dob
     *   has_treatment_facility → treatment_facility (boolean)
     *   treatment_facility_name→ facility_name
     *   on_probation           → probation_status ('active_probation' | 'not_probation')
     *
     * All fields use `sometimes` so partial updates work — only fields that are
     * actually present in the request get validated and saved. Null values are
     * filtered out to avoid overwriting existing data with empty submissions.
     */
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
            'date_of_birth'           => 'sometimes|nullable|date',  // alternate key from some form sections
            'gender'                  => 'sometimes|string|max:50',
            'clean_date'              => 'sometimes|nullable|date',
            'neighborhood'            => 'sometimes|nullable|string|max:255',
            'bus_line'                => 'sometimes|nullable|string|max:255',
            'has_treatment_facility'  => 'sometimes|boolean',
            'treatment_facility_name' => 'sometimes|nullable|string|max:255',
            'on_probation'            => 'sometimes|boolean',
        ]);

        // Normalize the date_of_birth alias to the model's dob column.
        if (isset($validated['date_of_birth']) && !isset($validated['dob'])) {
            $validated['dob'] = $validated['date_of_birth'];
            unset($validated['date_of_birth']);
        }

        // Remap UI field names to Volunteer model column names.
        if (array_key_exists('has_treatment_facility', $validated)) {
            $validated['treatment_facility'] = $validated['has_treatment_facility'];
            unset($validated['has_treatment_facility']);
        }

        if (array_key_exists('treatment_facility_name', $validated)) {
            $validated['facility_name'] = $validated['treatment_facility_name'];
            unset($validated['treatment_facility_name']);
        }

        // Convert the boolean checkbox to the string enum the DB column expects.
        if (array_key_exists('on_probation', $validated)) {
            $validated['probation_status'] = $validated['on_probation'] ? 'active_probation' : 'not_probation';
            unset($validated['on_probation']);
        }

        // Skip null values so fields left blank in a partial form don't clear existing data.
        $volunteer->fill(array_filter($validated, fn($v) => $v !== null))->save();

        return redirect("/volunteers/{$volunteer->volunteer_id}")->with('success', 'Profile updated successfully.');
    }

    /**
     * Redirect the volunteer to their availability grid.
     *
     * Same alias pattern as profileRedirect — keeps the navbar URL stable
     * regardless of the volunteer's ULID.
     */
    public function availabilityRedirect()
    {
        $volunteer = $this->resolveVolunteer();

        if (!$volunteer) {
            return redirect()->route('dashboard')->with('error', 'No volunteer record linked to your account.');
        }

        return redirect("/volunteers/{$volunteer->volunteer_id}/availability");
    }

    /**
     * Render the volunteer's full assignment list.
     *
     * Loads all assignments (all statuses, all dates) sorted newest-first so
     * the most recent activity appears at the top. The view handles client-side
     * tab filtering (Upcoming / Past / All) from this single dataset.
     */
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
