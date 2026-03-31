<?php

namespace Database\Seeders;

use App\Models\Credential;
use App\Models\CredentialType;
use App\Models\Facility;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

class CredentialSeeder extends Seeder
{
    /**
     * Seed the credential types and volunteer credentials.
     */
    public function run(): void
    {
        // Create credential types
        $backgroundCheck = CredentialType::create([
            'name' => 'background_check',
            'display_name' => 'Background Check',
            'validity_years' => 3,
        ]);

        $tbTest = CredentialType::create([
            'name' => 'TB_test',
            'display_name' => 'TB Test',
            'validity_years' => 1,
        ]);

        $referenceCheck = CredentialType::create([
            'name' => 'reference_check',
            'display_name' => 'Reference Check',
            'validity_years' => 2,
        ]);

        $orientation = CredentialType::create([
            'name' => 'orientation',
            'display_name' => 'Orientation',
            'validity_years' => null, // Permanent
        ]);

        // Get facilities for credential assignment
        $metroHospital = Facility::where('name', 'Metro Hospital')->first();
        $countyJail = Facility::where('name', 'County Jail')->first();
        $youthDetention = Facility::where('name', 'Youth Detention Center')->first();

        // Sarah Johnson - has approved credentials at Metro Hospital
        $sarahVolunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'sarah@example.com');
        })->first();

        if ($sarahVolunteer && $metroHospital) {
            Credential::create([
                'volunteer_id' => $sarahVolunteer->id,
                'facility_id' => $metroHospital->id,
                'credential_type_id' => $backgroundCheck->id,
                'status' => 'approved',
                'issued_at' => now()->subYears(2),
                'expires_at' => now()->addYears(1),
            ]);

            Credential::create([
                'volunteer_id' => $sarahVolunteer->id,
                'facility_id' => $metroHospital->id,
                'credential_type_id' => $tbTest->id,
                'status' => 'approved',
                'issued_at' => now()->subMonths(6),
                'expires_at' => now()->addMonths(6),
            ]);
        }

        // Robert Davis - has pending credentials
        $robertVolunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'robert@example.com');
        })->first();

        if ($robertVolunteer && $metroHospital) {
            Credential::create([
                'volunteer_id' => $robertVolunteer->id,
                'facility_id' => $metroHospital->id,
                'credential_type_id' => $backgroundCheck->id,
                'status' => 'pending',
                'issued_at' => now()->subDays(7),
                'expires_at' => null,
            ]);
        }

        // Carlos Garcia - has all credentials approved
        $carlosVolunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'carlos@example.com');
        })->first();

        if ($carlosVolunteer) {
            // Approve for Metro Hospital
            if ($metroHospital) {
                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $metroHospital->id,
                    'credential_type_id' => $backgroundCheck->id,
                    'status' => 'approved',
                    'issued_at' => now()->subYears(2),
                    'expires_at' => now()->addYears(1),
                ]);

                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $metroHospital->id,
                    'credential_type_id' => $tbTest->id,
                    'status' => 'approved',
                    'issued_at' => now()->subMonths(6),
                    'expires_at' => now()->addMonths(6),
                ]);
            }

            // Approve for County Jail
            if ($countyJail) {
                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $countyJail->id,
                    'credential_type_id' => $backgroundCheck->id,
                    'status' => 'approved',
                    'issued_at' => now()->subYears(2),
                    'expires_at' => now()->addYears(1),
                ]);
            }

            // Approve for Youth Detention
            if ($youthDetention) {
                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $youthDetention->id,
                    'credential_type_id' => $backgroundCheck->id,
                    'status' => 'approved',
                    'issued_at' => now()->subYears(2),
                    'expires_at' => now()->addYears(1),
                ]);

                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $youthDetention->id,
                    'credential_type_id' => $tbTest->id,
                    'status' => 'approved',
                    'issued_at' => now()->subMonths(6),
                    'expires_at' => now()->addMonths(6),
                ]);

                Credential::create([
                    'volunteer_id' => $carlosVolunteer->id,
                    'facility_id' => $youthDetention->id,
                    'credential_type_id' => $referenceCheck->id,
                    'status' => 'approved',
                    'issued_at' => now()->subMonths(12),
                    'expires_at' => now()->addMonths(12),
                ]);
            }
        }

        // Patricia Brown - expired credential at County Jail
        $patriciaVolunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'patricia@example.com');
        })->first();

        if ($patriciaVolunteer && $countyJail) {
            Credential::create([
                'volunteer_id' => $patriciaVolunteer->id,
                'facility_id' => $countyJail->id,
                'credential_type_id' => $backgroundCheck->id,
                'status' => 'approved',
                'issued_at' => now()->subYears(3)->subMonths(3),
                'expires_at' => now()->subDays(90), // Expired on 2025-12-31
            ]);
        }
    }
}
