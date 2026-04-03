<?php

declare(straight_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Meeting Model
 *
 * Represents scheduled H&I meetings at facilities.
 *
 * @property string $meeting_id
 * @property string $facility_id
 * @property \Carbon\Carbon $meeting_date
 * @property string $meeting_time
 * @property string $format
 * @property string $status
 * @property string|null $assigned_volunteer_id
 * @property string $confirmed_status
 * @property string|null $notes
 * @property \Carbon\Carbon|null $completed_at
 * @property int $week_of_month
 * @property int $day_of_week
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Meeting extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'meeting_id';

    protected $fillable = [
        'facility_id',
        'meeting_date',
        'meeting_time',
        'format',
        'status',
        'assigned_volunteer_id',
        'confirmed_status',
        'notes',
        'completed_at',
        'week_of_month',
        'day_of_week',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the datetime combination of meeting date and time.
     *
     * @return Carbon
     */
    public function getMeetingDateTimeAttribute(): Carbon
    {
        return Carbon::parse("{$this->meeting_date} {$this->meeting_time}");
    }

    /**
     * Check if meeting is scheduled.
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if meeting is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if meeting is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if meeting has a confirmed volunteer.
     *
     * @return bool
     */
    public function hasConfirmedVolunteer(): bool
    {
        return $this->confirmed_status === 'confirmed' && $this->assigned_volunteer_id !== null;
    }

    /**
     * Check if meeting is virtual.
     *
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->format === 'virtual';
    }

    /**
     * Check if meeting is in-person.
     *
     * @return bool
     */
    public function isInPerson(): bool
    {
        return $this->format === 'in_person';
    }

    /**
     * Check if meeting is upcoming (not completed or cancelled).
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->isScheduled() && $this->meeting_date >= now()->date();
    }

    /**
     * Scope: Get scheduled meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope: Get completed meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get cancelled meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope: Get upcoming meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->scheduled()
            ->where('meeting_date', '>=', now()->date());
    }

    /**
     * Scope: Get meetings on a specific date.
     *
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeOnDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('meeting_date', $date->toDateString());
    }

    /**
     * Scope: Get meetings in a date range.
     *
     * @param Builder $query
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Builder
     */
    public function scopeDateBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('meeting_date', [
            $startDate->toDateString(),
            $endDate->toDateString(),
        ]);
    }

    /**
     * Scope: Get meetings with confirmed volunteers.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('confirmed_status', 'confirmed');
    }

    /**
     * Scope: Get unassigned meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_volunteer_id');
    }

    /**
     * Scope: Get virtual meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeVirtual(Builder $query): Builder
    {
        return $query->where('format', 'virtual');
    }

    /**
     * Scope: Get in-person meetings.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInPerson(Builder $query): Builder
    {
        return $query->where('format', 'in_person');
    }

    /**
     * Relationship: Facility hosting this meeting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Relationship: Assigned volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedVolunteer()
    {
        return $this->belongsTo(Volunteer::class, 'assigned_volunteer_id', 'volunteer_id');
    }

    /**
     * Relationship: SMS logs for this meeting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'meeting_id', 'meeting_id');
    }
}
