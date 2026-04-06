<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'facility_name'          => 'Metro Hospital',
                'address'                => '123 Medical Drive',
                'city'                   => 'Springfield',
                'state'                  => 'IL',
                'zip'                    => '62701',
                'main_phone'             => '555-100-0001',
                'contact_email'          => 'contact@metrohospital.org',
                'clean_time_requirement' => 2,
                'credentialing_types'    => ['background_check', 'tb_test'],
                'gender_restriction'     => false,
                'probation_allowed'      => false,
                'status'                 => 'active',
                'timezone'               => 'America/Chicago',
            ],
            [
                'facility_name'          => 'County Jail',
                'address'                => '456 Justice Ave',
                'city'                   => 'Springfield',
                'state'                  => 'IL',
                'zip'                    => '62702',
                'main_phone'             => '555-100-0002',
                'contact_email'          => 'contact@countyjail.gov',
                'clean_time_requirement' => 3,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => true,
                'probation_allowed'      => false,
                'status'                 => 'active',
                'timezone'               => 'America/Chicago',
            ],
            [
                'facility_name'          => 'Springfield Treatment Center',
                'address'                => '789 Recovery Blvd',
                'city'                   => 'Springfield',
                'state'                  => 'IL',
                'zip'                    => '62703',
                'main_phone'             => '555-100-0003',
                'contact_email'          => 'contact@springfieldtc.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => [],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'timezone'               => 'America/Chicago',
            ],
            [
                'facility_name'          => 'Community Center',
                'address'                => '321 Main St',
                'city'                   => 'Springfield',
                'state'                  => 'IL',
                'zip'                    => '62704',
                'main_phone'             => '555-100-0004',
                'contact_email'          => 'contact@communitycenter.org',
                'clean_time_requirement' => 0,
                'credentialing_types'    => [],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'timezone'               => 'America/Chicago',
            ],
            [
                'facility_name'          => 'Youth Detention Center',
                'address'                => '654 Juvenile Rd',
                'city'                   => 'Springfield',
                'state'                  => 'IL',
                'zip'                    => '62705',
                'main_phone'             => '555-100-0005',
                'contact_email'          => 'contact@youthdetention.gov',
                'clean_time_requirement' => 5,
                'credentialing_types'    => ['background_check', 'tb_test', 'reference_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => false,
                'status'                 => 'active',
                'timezone'               => 'America/Chicago',
            ],
        ];

        foreach ($facilities as $data) {
            Facility::create($data);
        }
    }
}
