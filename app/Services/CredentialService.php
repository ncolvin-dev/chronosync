<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerCredential;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * CredentialService
 *
 * Manages volunteer credentialing with:
 * - Status workflow (pending/approved/denied)
 * - Expiration tracking and alerts
 * - Audit logging of all credential changes
 */
class CredentialService
{
    /**
     * Create a new credential for a volunteer at a facility.
     *
     * @param Volunteer $volunteer
     * @param Facility $facility
     * @param string $credentialType
     * @param User|null $auditUser
     * @return VolunteerCredential
     */
    public function createCredential(Volunteer $volunteer, Facility $facility, string $credentialType, ?User $auditUser = null): VolunteerCredential
    {
        $credential = VolunteerCredential::create([
            'volunteer_id' => $volunteer->volunteer_id,
            'facility_id' => $facility->facility_id,
            'credential_type' => $credentialType,
            'status' => 'pending',
        ]);

        if ($auditUser !== null) {
            $this->auditLog(
                user: $auditUser,
                action: 'create_credential',
                credential: $credential,
                details: [
                    'credential_type' => $credentialType,
                    'status' => 'pending',
                ],
            );
        }

        Log::info("Credential created", [
            'credential_id' => $credential->credential_id,
            'volunteer_id' => $volunteer->volunteer_id,
            'facility_id' => $facility->facility_id,
            'credential_type' => $credentialType,
        ]);

        return $credential;
    }

    /**
     * Approve a credential.
     *
     * @param VolunteerCredential $credential
     * @param User|null $auditUser
     * @return VolunteerCredential
     */
    public function approveCredential(VolunteerCredential $credential, ?User $auditUser = null): VolunteerCredential
    {
        $oldStatus = $credential->status;

        $credential->status = 'approved';
        $credential->approval_date = now();

        // Calculate expiration date if credential type has expiration years
        if ($credential->facility && !empty($credential->facility->credentialing_types)) {
            // In a real implementation, look up expiration_years from credential_types table
            // For now, assume 2-year renewal
            $credential->expiration_date = now()->addYears(2);
        }

        $credential->save();

        if ($auditUser !== null) {
            $this->auditLog(
                user: $auditUser,
                action: 'approve_credential',
                credential: $credential,
                details: [
                    'previous_status' => $oldStatus,
                    'new_status' => 'approved',
                    'approval_date' => $credential->approval_date,
                    'expiration_date' => $credential->expiration_date,
                ],
            );
        }

        Log::info("Credential approved", [
            'credential_id' => $credential->credential_id,
            'volunteer_id' => $credential->volunteer_id,
            'expiration_date' => $credential->expiration_date,
        ]);

        return $credential;
    }

    /**
     * Deny a credential.
     *
     * @param VolunteerCredential $credential
     * @param User|null $auditUser
     * @return VolunteerCredential
     */
    public function denyCredential(VolunteerCredential $credential, ?User $auditUser = null): VolunteerCredential
    {
        $oldStatus = $credential->status;

        $credential->status = 'denied';
        $credential->save();

        if ($auditUser !== null) {
            $this->auditLog(
                user: $auditUser,
                action: 'deny_credential',
                credential: $credential,
                details: [
                    'previous_status' => $oldStatus,
                    'new_status' => 'denied',
                ],
            );
        }

        Log::info("Credential denied", [
            'credential_id' => $credential->credential_id,
            'volunteer_id' => $credential->volunteer_id,
        ]);

        return $credential;
    }

    /**
     * Renew a credential (reset expiration date).
     *
     * @param VolunteerCredential $credential
     * @param User|null $auditUser
     * @return VolunteerCredential
     */
    public function renewCredential(VolunteerCredential $credential, ?User $auditUser = null): VolunteerCredential
    {
        if (!$credential->isExpired()) {
            Log::warning("Credential not expired, cannot renew", [
                'credential_id' => $credential->credential_id,
            ]);
            return $credential;
        }

        $oldExpirationDate = $credential->expiration_date;

        // Renew for another 2 years from expiration date (or from now if more than 1 year overdue)
        if ($credential->expiration_date->addYears(1) < now()) {
            $credential->expiration_date = now()->addYears(2);
        } else {
            $credential->expiration_date = $credential->expiration_date->addYears(2);
        }

        $credential->status = 'approved';
        $credential->save();

        if ($auditUser !== null) {
            $this->auditLog(
                user: $auditUser,
                action: 'renew_credential',
                credential: $credential,
                details: [
                    'previous_expiration' => $oldExpirationDate,
                    'new_expiration' => $credential->expiration_date,
                ],
            );
        }

        Log::info("Credential renewed", [
            'credential_id' => $credential->credential_id,
            'new_expiration' => $credential->expiration_date,
        ]);

        return $credential;
    }

    /**
     * Get all credentials expiring soon (within 30 days).
     *
     * @return Collection
     */
    public function getExpiringCredentials(): Collection
    {
        return VolunteerCredential::expiringSoon()->get();
    }

    /**
     * Get all expired credentials.
     *
     * @return Collection
     */
    public function getExpiredCredentials(): Collection
    {
        return VolunteerCredential::expired()->get();
    }

    /**
     * Check if volunteer has valid credentials for a facility.
     *
     * @param Volunteer $volunteer
     * @param Facility $facility
     * @return bool
     */
    public function hasValidCredentials(Volunteer $volunteer, Facility $facility): bool
    {
        return VolunteerCredential::query()
            ->where('volunteer_id', $volunteer->volunteer_id)
            ->where('facility_id', $facility->facility_id)
            ->valid()
            ->exists();
    }

    /**
     * Get all valid credentials for a volunteer at a facility.
     *
     * @param Volunteer $volunteer
     * @param Facility $facility
     * @return Collection
     */
    public function getValidCredentials(Volunteer $volunteer, Facility $facility): Collection
    {
        return VolunteerCredential::query()
            ->where('volunteer_id', $volunteer->volunteer_id)
            ->where('facility_id', $facility->facility_id)
            ->valid()
            ->get();
    }

    /**
     * Get all pending credentials for review.
     *
     * @return Collection
     */
    public function getPendingCredentials(): Collection
    {
        return VolunteerCredential::pending()->get();
    }

    /**
     * Get expiration alert details.
     *
     * @param VolunteerCredential $credential
     * @return array
     */
    public function getExpirationAlert(VolunteerCredential $credential): array
    {
        $daysUntilExpiration = $credential->daysUntilExpiration();

        return [
            'credential_id' => $credential->credential_id,
            'volunteer_name' => $credential->volunteer->getFullNameAttribute(),
            'facility_name' => $credential->facility->facility_name,
            'credential_type' => $credential->credential_type,
            'expiration_date' => $credential->expiration_date,
            'days_until_expiration' => $daysUntilExpiration,
            'is_expiring_soon' => $credential->isExpiringSoon(),
            'is_expired' => $credential->isExpired(),
            'urgency' => $this->getUrgencyLevel($daysUntilExpiration),
        ];
    }

    /**
     * Get urgency level for expiring credential.
     *
     * @param int|null $daysUntilExpiration
     * @return string
     */
    private function getUrgencyLevel(?int $daysUntilExpiration): string
    {
        if ($daysUntilExpiration === null) {
            return 'none';
        }

        if ($daysUntilExpiration < 0) {
            return 'critical'; // Expired
        }

        if ($daysUntilExpiration <= 7) {
            return 'urgent'; // 7 days or less
        }

        if ($daysUntilExpiration <= 30) {
            return 'warning'; // 30 days or less
        }

        return 'info'; // More than 30 days
    }

    /**
     * Get credential expiration report.
     *
     * @return array
     */
    public function getExpirationReport(): array
    {
        $expiringCredentials = $this->getExpiringCredentials();
        $expiredCredentials = $this->getExpiredCredentials();

        return [
            'total_expiring_soon' => $expiringCredentials->count(),
            'total_expired' => $expiredCredentials->count(),
            'expiring_soon' => $expiringCredentials->map(fn (VolunteerCredential $c) => $this->getExpirationAlert($c))->toArray(),
            'expired' => $expiredCredentials->map(fn (VolunteerCredential $c) => $this->getExpirationAlert($c))->toArray(),
            'by_urgency' => [
                'critical' => $expiredCredentials->count(),
                'urgent' => $expiringCredentials->filter(fn (VolunteerCredential $c) => $c->daysUntilExpiration() <= 7)->count(),
                'warning' => $expiringCredentials->filter(fn (VolunteerCredential $c) => $c->daysUntilExpiration() > 7)->count(),
            ],
        ];
    }

    /**
     * Get credentials by status.
     *
     * @param string $status
     * @return Collection
     */
    public function getCredentialsByStatus(string $status): Collection
    {
        return VolunteerCredential::where('status', $status)->get();
    }

    /**
     * Get credentials for a volunteer.
     *
     * @param Volunteer $volunteer
     * @return Collection
     */
    public function getVolunteerCredentials(Volunteer $volunteer): Collection
    {
        return VolunteerCredential::where('volunteer_id', $volunteer->volunteer_id)->get();
    }

    /**
     * Get credentials for a facility.
     *
     * @param Facility $facility
     * @return Collection
     */
    public function getFacilityCredentials(Facility $facility): Collection
    {
        return VolunteerCredential::where('facility_id', $facility->facility_id)->get();
    }

    /**
     * Log audit entry.
     *
     * @param User $user
     * @param string $action
     * @param VolunteerCredential $credential
     * @param array $details
     * @return void
     */
    private function auditLog(User $user, string $action, VolunteerCredential $credential, array $details): void
    {
        AuditLog::create([
            'actor_user_id' => $user->user_id,
            'action_type' => $action,
            'table_name' => 'volunteer_credentials',
            'record_id' => $credential->credential_id,
            'change_details' => $details,
        ]);
    }
}
