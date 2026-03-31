<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Seed the facilities table.
     */
    public function run(): void
    {
        $facilities = [
            [
                'name' => 'Metro Hospital',
                'address' => '123 Medical Drive',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip_code' => '62701',
                'min_clean_years' => 2,
                'requires_background_check' => true,
                'requires_tb_test' => true,
                'requires_reference_check' => false,
                'gender_restriction' => null,
                'probation_allowed' => false,
            ],
            [
                'name' => 'County Jail',
                'address' => '456 Justice Ave',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip_code' => '62702',
                'min_clean_years' => 3,
                'requires_background_check' => true,
                'requires_tb_test' => false,
                'requires_reference_check' => false,
                'gender_restriction' => 'male',
                'probation_allowed' => false,
            ],
            [
                'name' => 'Springfield Treatment Center',
                'address' => '789 Recovery Blvd',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip_code' => '62703',
                'min_clean_years' => 1,
                'requires_background_check' => false,
                'requires_tb_test' => false,
                'requires_reference_check' => false,
                'gender_restriction' => null,
                'probation_allowed' => true,
            ],
            [
                'name' => 'Community Center',
                'address' => '321 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip_code' => '62704',
                'min_clean_years' => 0,
                'requires_background_check' => false,
                'requires_tb_test' => false,
                'requires_reference_check' => false,
                'gender_restriction' => null,
                'probation_allowed' => true,
            ],
            [
                'name' => 'Youth Detention Center',
                'address' => '654 Juvenile Rd',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip_code' => '62705',
                'min_clean_years' => 5,
                'requires_background_check' => true,
                'requires_tb_test' => true,
                'requires_reference_check' => true,
                'gender_restriction' => null,
                'probation_allowed' => false,
            ],
        ];

        foreach ($facilities as $facilityData) {
            Facility::create($facilityData);
        }
    }
}
