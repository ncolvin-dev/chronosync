<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Facility Model
 *
 * Represents H&I meeting facilities where volunteers lead meetings.
 *
 * @property string $facility_id
 * @property string $facility_name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $main_phone
 * @property string $contact_email
 * @property int $clean_time_requirement
 * @property array $credentialing_types
 * @property bool $gender_restriction
 * @property bool $probation_allowed
 * @property string $status
 * @property string|null $contact1_name
 * @property string|null $contact1_phone
 * @property string|null $contact1_email
 * @property string|null $contact2_name
 * @property string|null $contact2_phone
 * @property string|null $contact2_email
 * @property string $timezone
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Facility extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'facility_id';

    protected $fillable = [
        'facility_name',
        'address',
        'city',
        'state',
        'zip',
        'main_phone',
        'contact_email',
        'clean_time_requirement',
        'credentialing_types',
        'gender_restriction',
        'probation_allowed',
        'status',
        'contact1_name',
        'contact1_phone',
        'contact1_email',
        'contact2_name',
        'contact2_phone',
        'contact2_email',
        'timezone',
    ];

    protected $casts = [
        'credentialing_types' => 'array',
        'gender_restriction' => 'boolean',
        'probation_allowed' => 'boolean',
    ];

    /**
     * Get full address string.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip}";
    }

    /**
     * Check if facility is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if facility allows probation.
     *
     * @return bool
     */
    public function allowsProbation(): bool
    {
        return $this->probation_allowed;
    }

    /**
     * Check if facility has gender restrictions.
     *
     * @return bool
     */
    public function hasGenderRestrictions(): bool
    {
        return $this->gender_restriction;
    }

    /**
     * Scope: Get only active facilities.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get only inactive facilities.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope: Get facilities requiring minimum clean time.
     *
     * @param Builder $query
     * @param int $years
     * @return Builder
     */
    public function scopeCleanTimeRequirement(Builder $query, int $years): Builder
    {
        return $query->where('clean_time_requirement', '<=', $years);
    }

    /**
     * Scope: Get facilities by city.
     *
     * @param Builder $query
     * @param string $city
     * @return Builder
     */
    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    /**
     * Scope: Get facilities by state.
     *
     * @param Builder $query
     * @param string $state
     * @return Builder
     */
    public function scopeByState(Builder $query, string $state): Builder
    {
        return $query->where('state', $state);
    }

    /**
     * Relationship: Meetings at this facility.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'facility_id', 'facility_id');
    }

    /**
     * Relationship: Volunteer credentials for this facility.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function volunteerCredentials()
    {
        return $this->hasMany(VolunteerCredential::class, 'facility_id', 'facility_id');
    }

    /**
     * Relationship: Approved volunteer credentials for this facility.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedCredentials()
    {
        return $this->volunteerCredentials()->where('status', 'approved');
    }

    /**
     * Get active meetings for this facility.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeScheduledMeetings()
    {
        return $this->meetings()
            ->where('status', 'scheduled')
            ->where('meeting_date', '>=', now()->date());
    }
}
