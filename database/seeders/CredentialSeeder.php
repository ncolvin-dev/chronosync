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
        // Create credential types (matching credential_types table schema)
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

        // Look up facilities by facility_name
        $metroHospital  = Facility::where('facility_name', 'Metro Hospital')->first();
        $countyJail     = Facility::where('facility_name', 'County Jail')->first();
        $youthDetention = Facility::where('facility_name', 'Youth Detention Center')->first();

        // Sarah Johnson - approved credentials at Metro Hospital
        $sarah = Volunteer::where('email', 'sarah@example.com')->first();
        if ($sarah && $metroHospital) {
            VolunteerCredential::create([
                'volunteer_id'       => $sarah->volunteer_id,
                'facility_id'        => $metroHospital->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subYears(2)->toDateString(),
                'expiration_date'    => now()->addYear()->toDateString(),
            ]);
            VolunteerCredential::create([
                'volunteer_id'       => $sarah->volunteer_id,
                'facility_id'        => $metroHospital->facility_id,
                'credential_type_id' => $tbTest->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subMonths(6)->toDateString(),
                'expiration_date'    => now()->addMonths(6)->toDateString(),
            ]);
        }

        // Robert Davis - pending credential at Metro Hospital
        $robert = Volunteer::where('email', 'robert@example.com')->first();
        if ($robert && $metroHospital) {
            VolunteerCredential::create([
                'volunteer_id'       => $robert->volunteer_id,
                'facility_id'        => $metroHospital->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'pending',
                'approval_date'      => null,
                'expiration_date'    => null,
            ]);
        }

        // Carlos Garcia - approved at multiple facilities
        $carlos = Volunteer::where('email', 'carlos@example.com')->first();
        if ($carlos) {
            if ($metroHospital) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $metroHospital->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $metroHospital->facility_id,
                    'credential_type_id' => $tbTest->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subMonths(6)->toDateString(),
                    'expiration_date'    => now()->addMonths(6)->toDateString(),
                ]);
            }
            if ($countyJail) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $countyJail->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
            }
            if ($youthDetention) {
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $youthDetention->facility_id,
                    'credential_type_id' => $backgroundCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subYears(2)->toDateString(),
                    'expiration_date'    => now()->addYear()->toDateString(),
                ]);
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $youthDetention->facility_id,
                    'credential_type_id' => $tbTest->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subMonths(6)->toDateString(),
                    'expiration_date'    => now()->addMonths(6)->toDateString(),
                ]);
                VolunteerCredential::create([
                    'volunteer_id'       => $carlos->volunteer_id,
                    'facility_id'        => $youthDetention->facility_id,
                    'credential_type_id' => $referenceCheck->credential_type_id,
                    'status'             => 'approved',
                    'approval_date'      => now()->subMonths(12)->toDateString(),
                    'expiration_date'    => now()->addMonths(12)->toDateString(),
                ]);
            }
        }

        // Patricia Brown - expired credential at County Jail
        $patricia = Volunteer::where('email', 'patricia@example.com')->first();
        if ($patricia && $countyJail) {
            VolunteerCredential::create([
                'volunteer_id'       => $patricia->volunteer_id,
                'facility_id'        => $countyJail->facility_id,
                'credential_type_id' => $backgroundCheck->credential_type_id,
                'status'             => 'approved',
                'approval_date'      => now()->subYears(3)->subMonths(3)->toDateString(),
                'expiration_date'    => now()->subDays(90)->toDateString(), // Expired
            ]);
        }
    }
}
