<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AuditLog Model
 *
 * Records all auditable actions taken in the system for compliance and tracking.
 *
 * @property string $audit_id
 * @property string $actor_user_id
 * @property string $action_type
 * @property string $table_name
 * @property string $record_id
 * @property array|null $change_details
 * @property \Carbon\Carbon $timestamp
 */
class AuditLog extends Model
{
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $primaryKey = 'audit_log_id';

    protected $fillable = [
        'actor_user_id',
        'action',
        'entity_type',
        'entity_id',
        'change_details',
        'reason',
        'ip_address',
    ];

    protected $casts = [
        'change_details' => 'array',
    ];

    /**
     * Get action type display name.
     *
     * @return string
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'assign_volunteer' => 'Assigned Volunteer',
            'unassign_volunteer' => 'Unassigned Volunteer',
            'override_buffer' => 'Overrode Buffer Time',
            'update_credential' => 'Updated Credential',
            'approve_credential' => 'Approved Credential',
            'deny_credential' => 'Denied Credential',
            'send_sms' => 'Sent SMS',
            'confirm_meeting' => 'Confirmed Meeting',
            'cancel_meeting' => 'Cancelled Meeting',
            'complete_meeting' => 'Completed Meeting',
            'update_volunteer' => 'Updated Volunteer',
            'create_volunteer' => 'Created Volunteer',
            'delete_volunteer' => 'Deleted Volunteer',
            'update_facility' => 'Updated Facility',
            'create_facility' => 'Created Facility',
            'delete_facility' => 'Deleted Facility',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Scope: Get logs for a specific user.
     *
     * @param Builder $query
     * @param string $userId
     * @return Builder
     */
    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('actor_user_id', $userId);
    }

    /**
     * Scope: Get logs for a specific action type.
     *
     * @param Builder $query
     * @param string $actionType
     * @return Builder
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs for a specific entity type.
     *
     * @param Builder $query
     * @param string $entityType
     * @return Builder
     */
    public function scopeForEntity(Builder $query, string $entityType): Builder
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope: Get logs for a specific entity.
     *
     * @param Builder $query
     * @param string $entityId
     * @return Builder
     */
    public function scopeForEntityId(Builder $query, string $entityId): Builder
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope: Get logs for a specific entity type and ID combination.
     *
     * @param Builder $query
     * @param string $entityType
     * @param string $entityId
     * @return Builder
     */
    public function scopeForEntityRecord(Builder $query, string $entityType, string $entityId): Builder
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    /**
     * Scope: Get logs in a date range.
     *
     * @param Builder $query
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return Builder
     */
    public function scopeCreatedBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Get recent logs (last N days).
     *
     * @param Builder $query
     * @param int $days
     * @return Builder
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Get logs ordered by created_at descending (most recent first).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Relationship: Actor user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id', 'user_id');
    }
}
