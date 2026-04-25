<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
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

        return view('coordinator.coordinators', compact('coordinators'));
    }

    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $actorIsAdmin = auth()->user()->hasRole('admin');
        $roleRule     = $actorIsAdmin ? 'required|in:coordinator,admin' : 'required|in:coordinator';

        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => $roleRule,
        ]);

        $user = User::create([
            'email'         => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'roles'         => [$validated['role']],
        ]);

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'create_coordinator',
            'entity_type'    => 'users',
            'entity_id'      => $user->user_id,
            'change_details' => ['email' => $user->email, 'role' => $validated['role']],
        ]);

        return redirect()->route('coordinators.index')
            ->with('success', "Coordinator '{$user->email}' added successfully.");
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeCoordinatorOrAdmin();

        $actorIsAdmin = auth()->user()->hasRole('admin');

        // Coordinators cannot edit admin users
        if (!$actorIsAdmin && $user->hasRole('admin')) {
            abort(403, 'Coordinators cannot edit admin accounts.');
        }

        $roleRule = $actorIsAdmin ? 'required|in:coordinator,admin' : 'required|in:coordinator';

        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'role'     => $roleRule,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $changes = [];

        if ($user->email !== $validated['email']) {
            $changes['email'] = ['old' => $user->email, 'new' => $validated['email']];
            $user->email = $validated['email'];
        }

        $currentRoles = $user->roles ?? [];
        $otherRoles   = array_values(array_diff($currentRoles, ['coordinator', 'admin']));
        $newRoles     = array_values(array_unique(array_merge($otherRoles, [$validated['role']])));

        if ($currentRoles !== $newRoles) {
            $changes['roles'] = ['old' => $currentRoles, 'new' => $newRoles];
            $user->roles = $newRoles;
        }

        if (!empty($validated['password'])) {
            $changes['password'] = ['old' => '***', 'new' => '***'];
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        if (!empty($changes)) {
            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'update_coordinator',
                'entity_type'    => 'users',
                'entity_id'      => $user->user_id,
                'change_details' => $changes,
            ]);
        }

        return redirect()->route('coordinators.index')
            ->with('success', "Coordinator '{$user->email}' updated successfully.");
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
