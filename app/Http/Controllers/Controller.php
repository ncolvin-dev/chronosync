<?php

namespace App\Http\Controllers;

use App\Models\MeetingAssignment;

abstract class Controller
{
    protected function authorizeCoordinatorOrAdmin(): void
    {
        if (!$this->userHasAnyRole(['coordinator', 'admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    protected function authorizeAdmin(): void
    {
        if (!$this->userHasAnyRole(['admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    protected function authorizeAssignmentOwnerOrCoordinator(MeetingAssignment $assignment): void
    {
        $user = auth()->user();

        if ($user->email === $assignment->volunteer?->email) {
            return;
        }

        if (!$this->userHasAnyRole(['coordinator', 'admin'])) {
            abort(403, 'Unauthorized.');
        }
    }

    private function userHasAnyRole(array $roles): bool
    {
        $user       = auth()->user();
        $userRoles  = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        return count(array_intersect($roles, $userRoles)) > 0;
    }
}
