<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = User::where(function ($q) {
            $q->whereJsonContains('roles', 'coordinator')
              ->orWhereJsonContains('roles', 'admin');
        });

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('email', 'like', $search);
        }

        if ($request->filled('role')) {
            $query->whereJsonContains('roles', $request->role);
        }

        $coordinators = $query->orderBy('email')->paginate(15);

        // Attach volunteer contact info (name, phone) keyed by email
        $coordinatorEmails = $coordinators->pluck('email')->toArray();
        $volunteerContacts = Volunteer::whereIn('email', $coordinatorEmails)
            ->get(['email', 'first_name', 'last_name', 'phone'])
            ->keyBy('email');

        // Volunteers not already holding a coordinator/admin role
        $existingCoordEmails = User::where(function ($q) {
            $q->whereJsonContains('roles', 'coordinator')
              ->orWhereJsonContains('roles', 'admin');
        })->pluck('email')->toArray();

        $promotableVolunteers = Volunteer::whereNotIn('email', $existingCoordEmails)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('coordinator.coordinators', compact('coordinators', 'promotableVolunteers', 'volunteerContacts'));
    }

    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $actorIsAdmin = auth()->user()->hasRole('admin');
        $roleRule     = $actorIsAdmin ? 'required|in:coordinator,admin' : 'required|in:coordinator';

        $validated = $request->validate([
            'volunteer_id' => 'required|exists:volunteers,volunteer_id',
            'role'         => $roleRule,
        ]);

        $volunteer = Volunteer::findOrFail($validated['volunteer_id']);
        $user      = User::where('email', $volunteer->email)->first();

        if (!$user) {
            return back()->with('error', "No login account found for {$volunteer->first_name} {$volunteer->last_name}.");
        }

        $currentRoles = is_array($user->roles) ? $user->roles : (json_decode($user->roles, true) ?? []);

        if (in_array('coordinator', $currentRoles) || in_array('admin', $currentRoles)) {
            return back()->with('error', "{$volunteer->first_name} {$volunteer->last_name} is already a coordinator or admin.");
        }

        $user->roles = array_values(array_unique(array_merge($currentRoles, [$validated['role']])));
        $user->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'promote_to_coordinator',
            'entity_type'    => 'users',
            'entity_id'      => $user->user_id,
            'change_details' => [
                'volunteer'  => $volunteer->first_name . ' ' . $volunteer->last_name,
                'email'      => $user->email,
                'role_added' => $validated['role'],
                'all_roles'  => $user->roles,
            ],
        ]);

        return redirect()->route('coordinators.index')
            ->with('success', "{$volunteer->first_name} {$volunteer->last_name} has been promoted to {$validated['role']}.");
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeCoordinatorOrAdmin();

        $actorIsAdmin = auth()->user()->hasRole('admin');

        if (!$actorIsAdmin && $user->hasRole('admin')) {
            abort(403, 'Coordinators cannot edit admin accounts.');
        }

        $roleRule = $actorIsAdmin ? 'required|in:coordinator,admin' : 'required|in:coordinator';

        $validated = $request->validate([
            'role' => $roleRule,
        ]);

        $currentRoles = is_array($user->roles) ? $user->roles : (json_decode($user->roles, true) ?? []);
        $otherRoles   = array_values(array_diff($currentRoles, ['coordinator', 'admin']));
        $newRoles     = array_values(array_unique(array_merge($otherRoles, [$validated['role']])));

        if ($currentRoles !== $newRoles) {
            $user->roles = $newRoles;
            $user->save();

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'update_coordinator_role',
                'entity_type'    => 'users',
                'entity_id'      => $user->user_id,
                'change_details' => ['old_roles' => $currentRoles, 'new_roles' => $newRoles],
            ]);
        }

        return redirect()->route('coordinators.index')
            ->with('success', "Role updated for '{$user->email}'.");
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if (auth()->id() === $user->user_id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'delete_coordinator',
            'entity_type'    => 'users',
            'entity_id'      => $user->user_id,
            'change_details' => ['deleted' => true, 'email' => $user->email],
        ]);

        $user->delete();

        return redirect()->route('coordinators.index')
            ->with('success', 'Coordinator deleted successfully.');
    }
}
