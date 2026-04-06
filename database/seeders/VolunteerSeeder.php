<?php

namespace Database\Seeders;

use App\Models\Volunteer;
use Illuminate\Database\Seeder;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        $volunteers = [
            [
                'email'              => 'john@example.com',
                'first_name'         => 'John',
                'last_name'          => 'Smith',
                'dob'                => '1985-01-15',
                'phone'              => '555-123-4567',
                'clean_date'         => '2020-06-15',
                'probation_status'   => 'not_probation',
                'gender'             => 'Male',
                'neighborhood'       => 'Downtown',
                'bus_line'           => '12',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'sarah@example.com',
                'first_name'         => 'Sarah',
                'last_name'          => 'Johnson',
                'dob'                => '1990-03-22',
                'phone'              => '555-234-5678',
                'clean_date'         => '2019-11-01',
                'probation_status'   => 'not_probation',
                'gender'             => 'Female',
                'neighborhood'       => 'Westside',
                'bus_line'           => '7',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'marcus@example.com',
                'first_name'         => 'Marcus',
                'last_name'          => 'Williams',
                'dob'                => '1982-07-08',
                'phone'              => '555-345-6789',
                'clean_date'         => '2018-04-20',
                'probation_status'   => 'not_probation',
                'gender'             => 'Male',
                'neighborhood'       => 'Northside',
                'bus_line'           => '3',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'emily@example.com',
                'first_name'         => 'Emily',
                'last_name'          => 'Chen',
                'dob'                => '1993-09-14',
                'phone'              => '555-456-7890',
                'clean_date'         => '2021-02-28',
                'probation_status'   => 'not_probation',
                'gender'             => 'Female',
                'neighborhood'       => 'Eastside',
                'bus_line'           => '15',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'robert@example.com',
                'first_name'         => 'Robert',
                'last_name'          => 'Davis',
                'dob'                => '1978-12-05',
                'phone'              => '555-567-8901',
                'clean_date'         => '2017-08-10',
                'probation_status'   => 'active_probation',
                'gender'             => 'Male',
                'neighborhood'       => 'Southside',
                'bus_line'           => '22',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'lisa@example.com',
                'first_name'         => 'Lisa',
                'last_name'          => 'Martinez',
                'dob'                => '1988-05-30',
                'phone'              => '555-678-9012',
                'clean_date'         => '2022-01-05',
                'probation_status'   => 'not_probation',
                'gender'             => 'Female',
                'neighborhood'       => 'Midtown',
                'bus_line'           => '9',
                'is_sms_deliverable' => false,
            ],
            [
                'email'              => 'james@example.com',
                'first_name'         => 'James',
                'last_name'          => 'Wilson',
                'dob'                => '1975-11-18',
                'phone'              => '555-789-0123',
                'clean_date'         => '2015-03-15',
                'probation_status'   => 'not_probation',
                'gender'             => 'Male',
                'neighborhood'       => 'Uptown',
                'bus_line'           => '1',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'amanda@example.com',
                'first_name'         => 'Amanda',
                'last_name'          => 'Thompson',
                'dob'                => '1995-02-11',
                'phone'              => '555-890-1234',
                'clean_date'         => '2023-06-01',
                'probation_status'   => 'not_probation',
                'gender'             => 'Female',
                'neighborhood'       => 'Riverside',
                'bus_line'           => '18',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'carlos@example.com',
                'first_name'         => 'Carlos',
                'last_name'          => 'Garcia',
                'dob'                => '1980-06-25',
                'phone'              => '555-901-2345',
                'clean_date'         => '2016-10-30',
                'probation_status'   => 'not_probation',
                'gender'             => 'Male',
                'neighborhood'       => 'Downtown',
                'bus_line'           => '5',
                'is_sms_deliverable' => true,
            ],
            [
                'email'              => 'patricia@example.com',
                'first_name'         => 'Patricia',
                'last_name'          => 'Brown',
                'dob'                => '1970-08-03',
                'phone'              => '555-012-3456',
                'clean_date'         => '2014-12-25',
                'probation_status'   => 'not_probation',
                'gender'             => 'Female',
                'neighborhood'       => 'Westside',
                'bus_line'           => '7',
                'is_sms_deliverable' => true,
            ],
        ];

        foreach ($volunteers as $data) {
            Volunteer::create($data);
        }
    }
}
