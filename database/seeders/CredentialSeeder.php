<?php

namespace Database\Seeders;

use App\Models\CredentialType;
use App\Models\Facility;
use App\Models\Volunteer;
use App\Models\VolunteerCredential;
use Illuminate\Database\Seeder;

class CredentialSeeder extends Seeder
{
    public function run(): void
    {
        // ── Credential types ─────────────────────────────────────────────

        $backgroundCheck = CredentialType::create([
            'name'            => 'background_check',
            'description'     => 'Criminal background check required by facility',
            'expiration_days' => 1095, // 3 years
        ]);

        $tbTest = CredentialType::create([
            'name'            => 'tb_test',
            'description'     => 'Tuberculosis test required by facility',
            'expiration_days' => 365, // 1 year
        ]);

        $referenceCheck = CredentialType::create([
            'name'            => 'reference_check',
            'description'     => 'Character reference check required by facility',
            'expiration_days' => 730, // 2 years
        ]);

        CredentialType::create([
            'name'            => 'orientation',
            'description'     => 'Facility orientation training',
            'expiration_days' => null, // Permanent
        ]);

        // ── Look up facilities ───────────────────────────────────────────

        $cat         = Facility::where('facility_name', 'Center for Addiction Treatment')->first();
        $glenwood    = Facility::where('facility_name', 'Glenwood Behavioral Health Hospital')->first();
        $riverCity   = Facility::where('facility_name', 'River City Correctional Center')->first();

        // ── Look up volunteers ───────────────────────────────────────────
        // These emails all exist in the current VolunteerSeeder.

        $sarah    = Volunteer::where('email', 'sarah@example.com')->first();
        $robert   = Volunteer::where('email', 'robert@example.com')->first();
        $carlos   = Volunteer::where('email', 'carlos@example.com')->first();
        $patricia = Volunteer::where('email', 'patricia@example.com')->first();

        // ── Sarah — approved at Center for Addiction Treatment ────────────

        if ($sarah && $cat) {
            VolunteerCredential::create([
                'volunteer_id'       => $sarah->volunteer_id,
                'facility_id'        => $cat->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subYears(2)->toDateString(),
                'expiration_date'    => now()->addYear()->toDateString(),
            ]);
            VolunteerCredential::create([
                'volunteer_id'       => $sarah->volunteer_id,
                'facility_id'        => $cat->facility_id,
                'credential_type_id' => $tbTest->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subMonths(6)->toDateString(),
                'expiration_date'    => now()->addMonths(6)->toDateString(),
            ]);
        }

        // ── Robert — pending background check at Glenwood ─────────────────

        if ($robert && $glenwood) {
            VolunteerCredential::create([
                'volunteer_id'       => $robert->volunteer_id,
                'facility_id'        => $glenwood->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'pending',
                'approval_date'      => null,
                'expiration_date'    => null,
            ]);
        }

        // ── Carlos — approved at CAT and Glenwood, plus River City ────────

        if ($carlos) {
            if ($cat) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $cat->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $cat->facility_id,
                    'credential_type_id' => $tbTest->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subMonths(6)->toDateString(),
                    'expiration_date'    => now()->addMonths(6)->toDateString(),
                ]);
            }
            if ($glenwood) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $glenwood->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
            }
            if ($riverCity) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $riverCity->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $riverCity->facility_id,
                    'credential_type_id' => $referenceCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subMonths(12)->toDateString(),
                    'expiration_date'    => now()->addMonths(12)->toDateString(),
                ]);
            }
        }

        // ── Patricia — expired background check at River City ─────────────

        if ($patricia && $riverCity) {
            VolunteerCredential::create([
                'volunteer_id'       => $patricia->volunteer_id,
                'facility_id'        => $riverCity->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subYears(3)->subMonths(3)->toDateString(),
                'expiration_date'    => now()->subDays(90)->toDateString(), // Expired — good test case
            ]);
        }
    }
}
