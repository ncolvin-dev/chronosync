<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents system users (admins, coordinators, viewers) managing the volunteer system.
 *
 * @property string $user_id
 * @property string $email
 * @property string $password_hash
 * @property array $roles
 * @property \Carbon\Carbon|null $last_login
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, HasUlids, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'email',
        'password_hash',
        'roles',
        'last_login',
        'email_verified_at',
    ];

    protected $casts = [
        'roles' => 'array',
        'last_login' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the password attribute for authentication.
     * Overrides the default password attribute to use password_hash.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array<string> $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return count(array_intersect($roles, $this->roles ?? [])) > 0;
    }

    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a coordinator.
     *
     * @return bool
     */
    public function isCoordinator(): bool
    {
        return $this->hasRole('coordinator');
    }

    /**
     * Check if user is a viewer.
     *
     * @return bool
     */
    public function isViewer(): bool
    {
        return $this->hasRole('viewer');
    }

    /**
     * Add a role to the user.
     *
     * @param string $role
     * @return void
     */
    public function addRole(string $role): void
    {
        $roles = $this->roles ?? [];
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $this->roles = $roles;
        }
    }

    /**
     * Remove a role from the user.
     *
     * @param string $role
     * @return void
     */
    public function removeRole(string $role): void
    {
        $roles = $this->roles ?? [];
        $this->roles = array_values(array_diff($roles, [$role]));
    }

    /**
     * Update last login timestamp.
     *
     * @return bool
     */
    public function updateLastLogin(): bool
    {
        return $this->update(['last_login' => now()]);
    }

    /**
     * Relationship: AuditLogs where this user is the actor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id', 'user_id');
    }
}
