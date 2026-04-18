<?php

namespace App\Http\Controllers;

use App\Models\MeetingAssignment;

/**
 * Base Controller
 *
 * Provides shared authorization helpers that every subcontroller can call
 * via `$this->authorizeX()`. Centralizing these checks here means role logic
 * lives in exactly one place — add a new role or change the check once and
 * every controller picks it up automatically.
 *
 * Roles are stored as a JSON array on the User model (e.g. ["volunteer"]).
 * The private `userHasAnyRole()` handles the JSON decode so callers never
 * have to think about storage format.
 */
abstract class Controller
{
    /**
     * Abort with 403 unless the authenticated user is a coordinator or admin.
     *
     * Use this on any action that a volunteer should never reach — volunteer
     * list management, facility edits, matching runs, etc.
     */
    protected function authorizeCoordinatorOrAdmin(): void
    {
        if (!$this->userHasAnyRole(['coordinator', 'admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Abort with 403 unless the authenticated user is an admin.
     *
     * Reserved for destructive or system-wide operations such as bulk
     * availability updates, audit log access, and user management.
     */
    protected function authorizeAdmin(): void
    {
        if (!$this->userHasAnyRole(['admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Abort with 403 unless the user owns the assignment or is a coordinator/admin.
     *
     * Volunteers may only act on their own assignments (confirm, decline, cancel,
     * reinstate). Coordinators and admins may act on any assignment. Ownership is
     * determined by matching the authenticated user's email against the volunteer
     * record linked to the assignment — this works because email is the join key
     * between the `users` and `volunteers` tables.
     */
    protected function authorizeAssignmentOwnerOrCoordinator(MeetingAssignment $assignment): void
    {
        $user = auth()->user();

        // Allow the volunteer who owns the assignment to pass through immediately.
        if ($user->email === $assignment->volunteer?->email) {
            return;
        }

        // Otherwise require coordinator or admin role.
        if (!$this->userHasAnyRole(['coordinator', 'admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Check whether the authenticated user holds at least one of the given roles.
     *
     * Roles can be stored as a PHP array (after Eloquent casting) or as a raw
     * JSON string — this method handles both transparently.
     */
    private function userHasAnyRole(array $roles): bool
    {
        $user      = auth()->user();
        $userRoles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        return count(array_intersect($roles, $userRoles)) > 0;
    }
}
