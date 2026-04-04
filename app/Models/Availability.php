<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Availability Model
 *
 * Represents a volunteer's available time slots for meetings.
 *
 * @property string $availability_id
 * @property string $volunteer_id
 * @property int $week_of_month
 * @property int $day_of_week
 * @property int $hour_start
 * @property int $hour_end
 * @property bool $is_available
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Availability extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'availability_id';

    protected $table = 'availability';

    protected $fillable = [
        'volunteer_id',
        'week_of_month',
        'day_of_week',
        'hour_start',
        'hour_end',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Get day name from day_of_week number.
     *
     * @return string
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
        return $days[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Get week label.
     *
     * @return string
     */
    public function getWeekLabelAttribute(): string
    {
        $weeks = [
            1 => '1st',
            2 => '2nd',
            3 => '3rd',
            4 => '4th',
            5 => '5th',
        ];
        return $weeks[$this->week_of_month] ?? 'Unknown';
    }

    /**
     * Get hour range as string.
     *
     * @return string
     */
    public function getHourRangeAttribute(): string
    {
        return "{$this->hour_start}:00 - {$this->hour_end}:00";
    }

    /**
     * Check if availability slot overlaps with a given hour range.
     *
     * @param int $startHour
     * @param int $endHour
     * @return bool
     */
    public function overlaps(int $startHour, int $endHour): bool
    {
        return !($this->hour_end <= $startHour || $this->hour_start >= $endHour);
    }

    /**
     * Check if slot covers a specific hour.
     *
     * @param int $hour
     * @return bool
     */
    public function coversHour(int $hour): bool
    {
        return $hour >= $this->hour_start && $hour < $this->hour_end;
    }

    /**
     * Scope: Get available slots.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Get unavailable slots.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnavailable(Builder $query): Builder
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope: Get slots for a specific volunteer.
     *
     * @param Builder $query
     * @param string $volunteerId
     * @return Builder
     */
    public function scopeForVolunteer(Builder $query, string $volunteerId): Builder
    {
        return $query->where('volunteer_id', $volunteerId);
    }

    /**
     * Scope: Get slots for a specific week of month.
     *
     * @param Builder $query
     * @param int $weekOfMonth
     * @return Builder
     */
    public function scopeForWeek(Builder $query, int $weekOfMonth): Builder
    {
        return $query->where('week_of_month', $weekOfMonth);
    }

    /**
     * Scope: Get slots for a specific day.
     *
     * @param Builder $query
     * @param int $dayOfWeek
     * @return Builder
     */
    public function scopeForDay(Builder $query, int $dayOfWeek): Builder
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope: Get slots for a specific day and week.
     *
     * @param Builder $query
     * @param int $weekOfMonth
     * @param int $dayOfWeek
     * @return Builder
     */
    public function scopeForWeekDay(Builder $query, int $weekOfMonth, int $dayOfWeek): Builder
    {
        return $query->where('week_of_month', $weekOfMonth)
            ->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope: Get slots with specific hour range.
     *
     * @param Builder $query
     * @param int $startHour
     * @param int $endHour
     * @return Builder
     */
    public function scopeForHours(Builder $query, int $startHour, int $endHour): Builder
    {
        return $query->where('hour_start', '<=', $startHour)
            ->where('hour_end', '>=', $endHour);
    }

    /**
     * Relationship: Volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id', 'volunteer_id');
    }
}
