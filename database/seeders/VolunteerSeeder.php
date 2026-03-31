<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

class VolunteerSeeder extends Seeder
{
    /**
     * Seed the volunteers table.
     */
    public function run(): void
    {
        $volunteers = [
            [
                'user_email' => 'john@example.com',
                'date_of_birth' => '1985-01-15',
                'clean_date' => '2020-06-15',
                'gender' => 'Male',
                'neighborhood' => 'Downtown',
                'phone' => '(555) 123-4567',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'sarah@example.com',
                'date_of_birth' => '1988-05-22',
                'clean_date' => '2018-03-20',
                'gender' => 'Female',
                'neighborhood' => 'Northside',
                'phone' => '(555) 234-5678',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'marcus@example.com',
                'date_of_birth' => '1992-08-14',
                'clean_date' => '2022-11-01',
                'gender' => 'Male',
                'neighborhood' => 'Eastside',
                'phone' => '(555) 345-6789',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => true,
            ],
            [
                'user_email' => 'emily@example.com',
                'date_of_birth' => '1980-09-14',
                'clean_date' => '2015-09-14',
                'gender' => 'Female',
                'neighborhood' => 'Westside',
                'phone' => '(555) 456-7890',
                'treatment_facility' => 'Springfield Treatment Center',
                'treatment_discharge_date' => '2013-05-01',
                'on_probation' => false,
            ],
            [
                'user_email' => 'robert@example.com',
                'date_of_birth' => '1987-03-10',
                'clean_date' => '2019-07-22',
                'gender' => 'Male',
                'neighborhood' => 'Downtown',
                'phone' => '(555) 567-8901',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'lisa@example.com',
                'date_of_birth' => '1995-11-30',
                'clean_date' => '2023-01-10',
                'gender' => 'Female',
                'neighborhood' => 'Northside',
                'phone' => '(555) 678-9012',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'james@example.com',
                'date_of_birth' => '1990-04-30',
                'clean_date' => '2017-04-30',
                'gender' => 'Non-Binary',
                'neighborhood' => 'Eastside',
                'phone' => '(555) 789-0123',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'amanda@example.com',
                'date_of_birth' => '1993-06-18',
                'clean_date' => '2021-08-15',
                'gender' => 'Trans Woman',
                'neighborhood' => 'Westside',
                'phone' => '(555) 890-1234',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'carlos@example.com',
                'date_of_birth' => '1984-12-01',
                'clean_date' => '2016-12-01',
                'gender' => 'Male',
                'neighborhood' => 'Southside',
                'phone' => '(555) 901-2345',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
            [
                'user_email' => 'patricia@example.com',
                'date_of_birth' => '1989-02-28',
                'clean_date' => '2020-02-28',
                'gender' => 'Female',
                'neighborhood' => 'Northside',
                'phone' => '(555) 012-3456',
                'treatment_facility' => null,
                'treatment_discharge_date' => null,
                'on_probation' => false,
            ],
        ];

        foreach ($volunteers as $volunteerData) {
            $user = User::where('email', $volunteerData['user_email'])->first();

            if ($user) {
                Volunteer::create([
                    'user_id' => $user->id,
                    'date_of_birth' => $volunteerData['date_of_birth'],
                    'clean_date' => $volunteerData['clean_date'],
                    'gender' => $volunteerData['gender'],
                    'neighborhood' => $volunteerData['neighborhood'],
                    'phone' => $volunteerData['phone'],
                    'treatment_facility' => $volunteerData['treatment_facility'],
                    'treatment_discharge_date' => $volunteerData['treatment_discharge_date'],
                    'on_probation' => $volunteerData['on_probation'],
                ]);
            }
        }
    }
}
