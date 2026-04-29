<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Meeting Model — Recurring Meeting Slot
 *
 * Represents a recurring H&I meeting at a facility, defined by which
 * week of the month and day of the week it occurs. Individual occurrences
 * are covered by MeetingAssignment records that link a volunteer to a
 * specific calendar date.
 *
 * @property string $meeting_id
 * @property string $facility_id
 * @property int $day_of_week        0=Sunday through 6=Saturday
 * @property int $week_of_month      1-4 = specific week, 5 = last week
 * @property string $meeting_time    HH:MM:SS
 * @property int $duration_minutes
 * @property string $format          in_person | virtual | hybrid
 * @property int $volunteers_needed  1-5
 * @property string $status          active | inactive
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Meeting extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'meeting_id';

    protected $fillable = [
        'facility_id',
        'scheduled_time',
        'day_of_week',
        'week_of_month',
        'meeting_time',
        'duration_minutes',
        'format',
        'volunteers_needed',
        'status',
        'notes',
        'sns_topic_arn',
    ];

    protected $casts = [
        'scheduled_time'    => 'datetime',
        'day_of_week'       => 'integer',
        'week_of_month'     => 'integer',
        'duration_minutes'  => 'integer',
        'volunteers_needed' => 'integer',
    ];

    /**
     * Human-readable day names.
     */
    public const DAY_NAMES = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Human-readable week labels.
     */
    public const WEEK_LABELS = [
        1 => '1st',
        2 => '2nd',
        3 => '3rd',
        4 => '4th',
        5 => 'Last',
    ];

    public function isOneOff(): bool
    {
        return !is_null($this->scheduled_time);
    }

    public function isRecurring(): bool
    {
        return is_null($this->scheduled_time);
    }

    /**
     * Human-readable label for display and SMS messages.
     * One-off:   "Apr 30, 2025 at 7:00 PM"
     * Recurring: "Every 2nd Tuesday at 7:00 PM"
     */
    public function getScheduleLabelAttribute(): string
    {
        if ($this->isOneOff()) {
            return $this->scheduled_time->format('M j, Y \a\t g:i A');
        }

        $week = self::WEEK_LABELS[$this->week_of_month] ?? $this->week_of_month;
        $day  = self::DAY_NAMES[$this->day_of_week] ?? $this->day_of_week;
        $time = $this->meeting_time
            ? Carbon::parse($this->meeting_time)->format('g:i A')
            : '';

        return "Every {$week} {$day}" . ($time ? " at {$time}" : '');
    }

    /**
     * Compute the next upcoming occurrence date.
     *
     * One-off meetings: returns scheduled_time if it hasn't passed yet.
     * Every-week meetings (week_of_month = null): returns the next calendar
     *   date matching day_of_week. If today is the meeting day and the
     *   meeting time hasn't passed yet, today is returned; otherwise the
     *   same weekday next week.
     * Specific-week meetings (week_of_month 1-5): searches current month
     *   then next month.
     */
    public function nextOccurrence(): ?Carbon
    {
        if ($this->isOneOff()) {
            return $this->scheduled_time?->isFuture() ? $this->scheduled_time : null;
        }

        // Every-week meetings: find the next upcoming weekday from today.
        if (is_null($this->week_of_month)) {
            $today = now()->startOfDay();
            $diff  = ($this->day_of_week - $today->dayOfWeek + 7) % 7;
            $candidate = $today->copy()->addDays($diff);

            // If the candidate is today, check whether the meeting time has already passed.
            if ($diff === 0 && $this->meeting_time) {
                $meetingDateTime = $today->copy()->setTimeFromTimeString($this->meeting_time);
                if ($meetingDateTime->lte(now())) {
                    $candidate->addWeek();
                }
            }

            return $candidate;
        }

        // Specific-week meeting: use the current month's occurrence only when it
        // hasn't passed yet.  If the occurrence date is today and the meeting time
        // has already elapsed, treat it as past and fall through to next month.
        $today     = now()->startOfDay();
        $thisMonth = $this->occurrenceInMonth(now()->year, now()->month);

        if ($thisMonth && $thisMonth->gte($today)) {
            // The occurrence is today — additionally verify the meeting time is still ahead.
            if ($thisMonth->eq($today) && $this->meeting_time) {
                $meetingDateTime = $thisMonth->copy()->setTimeFromTimeString($this->meeting_time);
                if ($meetingDateTime->lte(now())) {
                    return $this->occurrenceInMonth(now()->addMonth()->year, now()->addMonth()->month);
                }
            }
            return $thisMonth;
        }

        // Current month's occurrence is already past; return next month's.
        return $this->occurrenceInMonth(now()->addMonth()->year, now()->addMonth()->month);
    }

    /**
     * Compute the occurrence date for a specific week-of-month pattern in a given year/month.
     * Only meaningful when week_of_month is 1–5; returns null for every-week meetings.
     */
    public function occurrenceInMonth(int $year, int $month): ?Carbon
    {
        // Every-week meetings don't have a single "the" occurrence per month.
        if (is_null($this->week_of_month)) {
            return null;
        }

        $target = $this->day_of_week; // 0-6

        // Find the first day of the month that matches day_of_week
        $first = Carbon::create($year, $month, 1);
        $diff  = ($target - $first->dayOfWeek + 7) % 7;
        $firstOccurrence = $first->copy()->addDays($diff);

        if ($this->week_of_month <= 4) {
            $date = $firstOccurrence->copy()->addWeeks($this->week_of_month - 1);
        } else {
            // "Last" occurrence: start from the next month's first, go back to find last
            $nextFirst = Carbon::create($year, $month, 1)->addMonth();
            $nextDiff  = ($target - $nextFirst->dayOfWeek + 7) % 7;
            // The last occurrence is 7 days before the first occurrence of the next month
            $date = $nextFirst->copy()->addDays($nextDiff)->subWeek();
        }

        // Guard against month overflow (e.g., week 5 doesn't exist for this weekday)
        if ($date->month !== $month) {
            return null;
        }

        return $date;
    }

    /**
     * Scope: Active meeting slots only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by format.
     */
    public function scopeFormat(Builder $query, string $format): Builder
    {
        return $query->where('format', $format);
    }

    /**
     * Relationship: Facility hosting this recurring meeting.
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Relationship: All assignments across all occurrences.
     */
    public function assignments()
    {
        return $this->hasMany(MeetingAssignment::class, 'meeting_id', 'meeting_id');
    }

    /**
     * Relationship: Assignments for a specific calendar date.
     */
    public function assignmentsForDate(string $date)
    {
        return $this->assignments()->where('assignment_date', $date);
    }

    /**
     * Relationship: Active (confirmed + pending) assignments for a date.
     */
    public function activeAssignmentsForDate(string $date)
    {
        return $this->assignmentsForDate($date)
            ->whereIn('status', ['confirmed', 'pending_confirmation']);
    }

    /**
     * Check whether a given occurrence date still needs more volunteers.
     */
    public function needsVolunteersForDate(string $date): bool
    {
        $assigned = $this->activeAssignmentsForDate($date)->count();
        return $assigned < $this->volunteers_needed;
    }

    /**
     * Relationship: SMS logs tied to this meeting.
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'meeting_id', 'meeting_id');
    }
}
