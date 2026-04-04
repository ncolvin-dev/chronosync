<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MeetingAssignment Model
 *
 * Represents a volunteer being assigned to a specific occurrence
 * (calendar date) of a recurring meeting slot. Up to 5 volunteers
 * can be assigned to a single occurrence.
 *
 * @property string $meeting_assignment_id
 * @property string $meeting_id
 * @property string $volunteer_id
 * @property \Carbon\Carbon $assignment_date
 * @property string $status              pending_confirmation | confirmed | declined | cancelled
 * @property string $assignment_type     auto | manual
 * @property string|null $override_reason
 * @property \Carbon\Carbon|null $confirmed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MeetingAssignment extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'meeting_assignment_id';

    protected $fillable = [
        'meeting_id',
        'volunteer_id',
        'assignment_date',
        'status',
        'assignment_type',
        'override_reason',
        'confirmed_at',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'confirmed_at'    => 'datetime',
    ];

    /**
     * Whether this assignment is awaiting the volunteer's response.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending_confirmation';
    }

    /**
     * Whether the volunteer has confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Whether the volunteer declined.
     */
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    /**
     * Whether this assignment has been cancelled by a coordinator.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Scope: Only active assignments (pending or confirmed).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending_confirmation', 'confirmed']);
    }

    /**
     * Scope: Confirmed assignments only.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Assignments for a specific date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('assignment_date', $date);
    }

    /**
     * Relationship: The recurring meeting slot this covers.
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id', 'meeting_id');
    }

    /**
     * Relationship: The assigned volunteer.
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id', 'volunteer_id');
    }
}
