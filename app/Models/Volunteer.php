<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MeetingAssignment;

/**
 * Volunteer Model
 *
 * Represents H&I volunteers who can be assigned to meetings at facilities.
 *
 * @property string $volunteer_id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property \Carbon\Carbon $dob
 * @property string $phone
 * @property \Carbon\Carbon $clean_date
 * @property string $probation_status
 * @property string|null $treatment_facility
 * @property string|null $facility_name
 * @property \Carbon\Carbon|null $discharge_date
 * @property string $gender
 * @property string|null $neighborhood
 * @property string|null $bus_line
 * @property bool $is_sms_deliverable
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Volunteer extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'volunteer_id';

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'dob',
        'phone',
        'clean_date',
        'probation_status',
        'treatment_facility',
        'facility_name',
        'discharge_date',
        'gender',
        'neighborhood',
        'bus_line',
        'is_sms_deliverable',
    ];

    protected $casts = [
        'dob' => 'date',
        'clean_date' => 'date',
        'discharge_date' => 'date',
        'is_sms_deliverable' => 'boolean',
    ];

    /**
     * Get the full name of the volunteer.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if volunteer meets clean time requirement for a facility.
     *
     * @param Facility $facility
     * @return bool
     */
    public function meetsCleanTimeRequirement(Facility $facility): bool
    {
        $yearsClean = $this->clean_date->diffInYears(now());
        return $yearsClean >= $facility->clean_time_requirement;
    }

    /**
     * Check if volunteer is on probation.
     *
     * @return bool
     */
    public function isOnProbation(): bool
    {
        return $this->probation_status === 'active_probation';
    }

    /**
     * Calculate how long this volunteer has been clean.
     *
     * Returns an array with integer `years` and `months` components so views
     * can format the value however they need (e.g. "2y 4m").
     *
     * Carbon 3 changed diffInYears() and diffInMonths() to return floats, so
     * both values are explicitly cast to int to prevent decimal display. The
     * months component is calculated from the anniversary of the last full year
     * (clean_date + years) rather than from clean_date directly, giving the
     * correct remainder months (e.g. 2 years and 4 months, not 28 months).
     *
     * Returns [0, 0] if clean_date is not set.
     *
     * @return array{years: int, months: int}
     */
    public function cleanTime(): array
    {
        if (!$this->clean_date) {
            return ['years' => 0, 'months' => 0];
        }

        $years  = (int) $this->clean_date->diffInYears(now());
        $months = (int) $this->clean_date->copy()->addYears($years)->diffInMonths(now());

        return ['years' => $years, 'months' => $months];
    }

    /**
     * Check if volunteer is SMS deliverable.
     *
     * @return bool
     */
    public function canReceiveSms(): bool
    {
        return $this->is_sms_deliverable;
    }

    /**
     * Get the age of the volunteer.
     *
     * @return int
     */
    public function getAgeAttribute(): int
    {
        return $this->dob->diffInYears(now());
    }

    /**
     * Get years of sobriety.
     *
     * @return float
     */
    public function getYearsCleanAttribute(): float
    {
        return round($this->clean_date->diffInDays(now()) / 365.25, 2);
    }

    /**
     * Scope: Get only SMS-deliverable volunteers.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSmsDeliverable(Builder $query): Builder
    {
        return $query->where('is_sms_deliverable', true);
    }

    /**
     * Scope: Get only volunteers currently on probation.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnProbation(Builder $query): Builder
    {
        return $query->where('probation_status', 'active_probation');
    }

    /**
     * Scope: Get only volunteers not on probation.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotOnProbation(Builder $query): Builder
    {
        return $query->where('probation_status', '!=', 'active_probation');
    }

    /**
     * Scope: Get volunteers with at least X years clean.
     *
     * @param Builder $query
     * @param int $years
     * @return Builder
     */
    public function scopeMinimumYearsClean(Builder $query, int $years): Builder
    {
        $date = now()->subYears($years);
        return $query->where('clean_date', '<=', $date);
    }

    /**
     * Scope: Get volunteers by gender.
     *
     * @param Builder $query
     * @param string $gender
     * @return Builder
     */
    public function scopeByGender(Builder $query, string $gender): Builder
    {
        return $query->where('gender', $gender);
    }

    /**
     * Relationship: Individual assignment records for this volunteer.
     *
     * Each MeetingAssignment links a volunteer to one specific occurrence of a
     * recurring meeting (identified by meeting_id + assignment_date). This is the
     * join table between volunteers and meetings.
     */
    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MeetingAssignment::class, 'volunteer_id', 'volunteer_id');
    }

    /**
     * Relationship: Recurring meeting slots this volunteer participates in.
     *
     * Uses a many-to-many relationship through the meeting_assignments table.
     * The pivot carries the date, status, and assignment type for each occurrence.
     */
    public function meetings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Meeting::class,
            'meeting_assignments',
            'volunteer_id',
            'meeting_id',
            'volunteer_id',
            'meeting_id'
        )->withPivot(['assignment_date', 'status', 'assignment_type', 'confirmed_at'])
         ->withTimestamps();
    }

    /**
     * Relationship: Credentials for this volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credentials()
    {
        return $this->hasMany(VolunteerCredential::class, 'volunteer_id', 'volunteer_id');
    }

    /**
     * Relationship: Availability slots for this volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availability()
    {
        return $this->hasMany(Availability::class, 'volunteer_id', 'volunteer_id');
    }

    /**
     * Relationship: SMS logs for this volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'volunteer_id', 'volunteer_id');
    }
}
