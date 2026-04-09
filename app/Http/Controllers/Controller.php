<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Check that the authenticated user holds the coordinator or admin role.
     *
     * Roles are stored as a JSON array on the User model, so we decode if needed.
     * Defined once here so every subcontroller inherits it without repeating the logic.
     * Uses an early-return guard clause to keep the flow flat and readable.
     */
    protected function authorizeCoordinatorOrAdmin(): void
    {
        $user  = auth()->user();
        $roles = is_array($user->roles)
            ? $user->roles
            : json_decode($user->roles, true) ?? [];

        // Allow access for coordinators and admins — return immediately so the
        // calling method can continue without nesting inside an if block.
        if (in_array('coordinator', $roles) || in_array('admin', $roles)) {
            return;
        }

        abort(403, 'Unauthorized.');
    }
}
