<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialType extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'credential_type_id';

    protected $fillable = [
        'name',
        'description',
        'expiration_days',
    ];

    protected $casts = [
        'expiration_days' => 'integer',
    ];

    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->name));
    }

    /**
     * Relationship: Volunteer credentials of this type.
     */
    public function volunteerCredentials()
    {
        return $this->hasMany(VolunteerCredential::class, 'credential_type_id', 'credential_type_id');
    }
}
