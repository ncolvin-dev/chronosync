<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * VolunteerCredential Model
 *
 * Represents the credentialing status of a volunteer for a specific facility.
 *
 * @property string $credential_id
 * @property string $volunteer_id
 * @property string $facility_id
 * @property string $credential_type
 * @property string $status
 * @property \Carbon\Carbon|null $approval_date
 * @property \Carbon\Carbon|null $expiration_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class VolunteerCredential extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'credential_id';

    protected $fillable = [
        'volunteer_id',
        'facility_id',
        'credential_type_id',
        'status',
        'approval_date',
        'expiration_date',
        'notes',
    ];

    protected $casts = [
        'approval_date' => 'datetime',
        'expiration_date' => 'date',
    ];

    /**
     * Check if credential is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if credential is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if credential is denied.
     *
     * @return bool
     */
    public function isDenied(): bool
    {
        return $this->status === 'denied';
    }

    /**
     * Check if credential has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expiration_date === null) {
            return false;
        }
        return now()->date() > $this->expiration_date;
    }

    /**
     * Check if credential is expiring soon (within 30 days).
     *
     * @return bool
     */
    public function isExpiringSoon(): bool
    {
        if ($this->expiration_date === null || !$this->isApproved()) {
            return false;
        }
        $daysUntilExpiration = now()->diffInDays($this->expiration_date, false);
        return $daysUntilExpiration >= 0 && $daysUntilExpiration <= 30;
    }

    /**
     * Get days until expiration.
     *
     * @return int|null
     */
    public function daysUntilExpiration(): ?int
    {
        if ($this->expiration_date === null) {
            return null;
        }
        return now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Check if credential is valid (approved and not expired).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isApproved() && !$this->isExpired();
    }

    /**
     * Scope: Get pending credentials.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get approved credentials.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get denied credentials.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDenied(Builder $query): Builder
    {
        return $query->where('status', 'denied');
    }

    /**
     * Scope: Get expired credentials.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiration_date', '<', now()->toDateString());
    }

    /**
     * Scope: Get valid (approved and not expired) credentials.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->approved()
            ->where(function (Builder $q) {
                $q->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now()->toDateString());
            });
    }

    /**
     * Scope: Get credentials expiring soon (within 30 days).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpiringSoon(Builder $query): Builder
    {
        return $query->approved()
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [
                now()->toDateString(),
                now()->addDays(30)->toDateString(),
            ]);
    }

    /**
     * Scope: Get credentials for a specific volunteer.
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
     * Scope: Get credentials for a specific facility.
     *
     * @param Builder $query
     * @param string $facilityId
     * @return Builder
     */
    public function scopeForFacility(Builder $query, string $facilityId): Builder
    {
        return $query->where('facility_id', $facilityId);
    }

    /**
     * Relationship: Credential type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function credentialType()
    {
        return $this->belongsTo(CredentialType::class, 'credential_type_id', 'credential_type_id');
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

    /**
     * Relationship: Facility.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    /**
     * Relationship: CredentialType (alias for credentialType, matches HasUlids convention).
     */
    public function type()
    {
        return $this->credentialType();
    }
}
