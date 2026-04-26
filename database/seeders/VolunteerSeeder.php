<?php

namespace Database\Seeders;

use App\Models\Volunteer;
use Illuminate\Database\Seeder;

/**
 * VolunteerSeeder — 30 volunteers across 4 clean-time groups.
 * All dates anchored to April 26, 2026.
 *
 * Schema fields used:
 *   email, first_name, last_name, dob, phone, clean_date,
 *   probation_status, gender, neighborhood, bus_line, is_sms_deliverable
 *
 * Note: The volunteers table does not have street address / city / state / zip
 * fields. The `neighborhood` column is used to store the Cincinnati area /
 * district for reference. See the cheat sheet for full address details.
 *
 * GROUP 1 (5) — Clean date: 3 days to 6 months ago (Oct 26 2025 – Apr 23 2026)
 *               City of Cincinnati addresses. No specific availability required.
 *
 * GROUP 2 (5) — Clean date: 6 months to 2 years ago (Apr 26 2024 – Oct 25 2025)
 *               Cincinnati & Hamilton County. Weekday evenings after 5 pm only.
 *
 * GROUP 3 (10) — Clean date: 2 to 5 years ago (Apr 26 2021 – Apr 25 2024)
 *                Cincinnati & Hamilton County.
 *                Availability: weekdays after 4 pm and/or weekend mornings/afternoons.
 *
 * GROUP 4 (10) — Clean date: 5 to 30 years ago (Apr 26 1996 – Apr 25 2021)
 *                Cincinnati & Hamilton County. Weekends only, 8 am – 10 pm.
 */
class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        $volunteers = [

            // ════════════════════════════════════════════════
            // GROUP 1 — 5 volunteers  (3 days – 6 months)
            // Clean dates: Oct 26 2025 → Apr 23 2026
            // City of Cincinnati addresses
            // ════════════════════════════════════════════════

            [
                'email'            => 'tyler.bennett@example.com',
                'first_name'       => 'Tyler',
                'last_name'        => 'Bennett',
                'dob'              => '1998-07-14',
                'phone'            => '513-201-0101',
                'clean_date'       => '2026-04-23', // 3 days ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Downtown Cincinnati (215 W 4th St, 45202)',
                'bus_line'         => '1X',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'kayla.foster@example.com',
                'first_name'       => 'Kayla',
                'last_name'        => 'Foster',
                'dob'              => '2000-03-09',
                'phone'            => '513-201-0202',
                'clean_date'       => '2026-04-05', // ~3 weeks ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Over-the-Rhine (1840 Elm St, 45202)',
                'bus_line'         => '17',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'devon.reyes@example.com',
                'first_name'       => 'Devon',
                'last_name'        => 'Reyes',
                'dob'              => '1997-11-22',
                'phone'            => '513-201-0303',
                'clean_date'       => '2026-03-10', // ~7 weeks ago
                'probation_status' => 'active_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Westwood (6205 Glenway Ave, 45211)',
                'bus_line'         => '19',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'brittney.cole@example.com',
                'first_name'       => 'Brittney',
                'last_name'        => 'Cole',
                'dob'              => '1994-05-30',
                'phone'            => '513-201-0404',
                'clean_date'       => '2026-01-25', // ~3 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Price Hill (3819 Warsaw Ave, 45205)',
                'bus_line'         => '33',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'anthony.price@example.com',
                'first_name'       => 'Anthony',
                'last_name'        => 'Price',
                'dob'              => '1996-09-18',
                'phone'            => '513-201-0505',
                'clean_date'       => '2025-11-18', // ~5 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Evanston (3345 Woodburn Ave, 45208)',
                'bus_line'         => '11',
                'is_sms_deliverable' => false,
            ],

            // ════════════════════════════════════════════════
            // GROUP 2 — 5 volunteers  (6 months – 2 years)
            // Clean dates: Apr 26 2024 → Oct 25 2025
            // Cincinnati & Hamilton County
            // Availability: weekday evenings after 5 pm only
            // ════════════════════════════════════════════════

            [
                'email'            => 'david.anderson@example.com',
                'first_name'       => 'David',
                'last_name'        => 'Anderson',
                'dob'              => '1991-02-14',
                'phone'            => '513-202-0101',
                'clean_date'       => '2025-10-01', // ~7 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Oakley (4521 Marburg Ave, 45209)',
                'bus_line'         => '26',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'jennifer.white@example.com',
                'first_name'       => 'Jennifer',
                'last_name'        => 'White',
                'dob'              => '1988-08-27',
                'phone'            => '513-202-0202',
                'clean_date'       => '2025-07-14', // ~9 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Blue Ash (9245 Plainfield Rd, 45236)',
                'bus_line'         => '38',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'christopher.miller@example.com',
                'first_name'       => 'Christopher',
                'last_name'        => 'Miller',
                'dob'              => '1993-12-05',
                'phone'            => '513-202-0303',
                'clean_date'       => '2025-03-22', // ~13 months ago
                'probation_status' => 'active_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Hyde Park (2745 Observatory Ave, 45208)',
                'bus_line'         => '23',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'michelle.taylor@example.com',
                'first_name'       => 'Michelle',
                'last_name'        => 'Taylor',
                'dob'              => '1985-04-17',
                'phone'            => '513-202-0404',
                'clean_date'       => '2024-12-01', // ~17 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Reading / Roselawn (8120 Reading Rd, 45237)',
                'bus_line'         => '16',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'daniel.thomas@example.com',
                'first_name'       => 'Daniel',
                'last_name'        => 'Thomas',
                'dob'              => '1990-06-23',
                'phone'            => '513-202-0505',
                'clean_date'       => '2024-08-05', // ~20 months ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'College Hill (5432 Hamilton Ave, 45224)',
                'bus_line'         => '27',
                'is_sms_deliverable' => true,
            ],

            // ════════════════════════════════════════════════
            // GROUP 3 — 10 volunteers  (2 – 5 years)
            // Clean dates: Apr 26 2021 → Apr 25 2024
            // Cincinnati & Hamilton County
            // Availability: weekdays after 4 pm and/or
            //               weekend mornings/afternoons (NOT weekend evenings)
            // ════════════════════════════════════════════════

            [
                'email'            => 'jessica.moore@example.com',
                'first_name'       => 'Jessica',
                'last_name'        => 'Moore',
                'dob'              => '1986-10-01',
                'phone'            => '513-203-0101',
                'clean_date'       => '2024-04-10', // ~2 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Columbia Tusculum (3756 Brotherton Rd, 45226)',
                'bus_line'         => '32',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'matthew.jackson@example.com',
                'first_name'       => 'Matthew',
                'last_name'        => 'Jackson',
                'dob'              => '1983-01-29',
                'phone'            => '513-203-0202',
                'clean_date'       => '2023-11-02', // ~2.5 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Kenwood (7845 Kenwood Rd, 45236)',
                'bus_line'         => '39',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'ashley.martin@example.com',
                'first_name'       => 'Ashley',
                'last_name'        => 'Martin',
                'dob'              => '1992-07-11',
                'phone'            => '513-203-0303',
                'clean_date'       => '2023-05-15', // ~3 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Norwood (4411 Montgomery Rd, 45212)',
                'bus_line'         => '24',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'ryan.lee@example.com',
                'first_name'       => 'Ryan',
                'last_name'        => 'Lee',
                'dob'              => '1989-03-16',
                'phone'            => '513-203-0404',
                'clean_date'       => '2022-12-08', // ~3.4 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Anderson Township (6720 Clough Pike, 45244)',
                'bus_line'         => '50',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'lauren.perez@example.com',
                'first_name'       => 'Lauren',
                'last_name'        => 'Perez',
                'dob'              => '1987-12-04',
                'phone'            => '513-203-0505',
                'clean_date'       => '2022-07-04', // ~3.8 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Hyde Park (2350 Dana Ave, 45208)',
                'bus_line'         => '23',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'steven.young@example.com',
                'first_name'       => 'Steven',
                'last_name'        => 'Young',
                'dob'              => '1980-09-07',
                'phone'            => '513-203-0606',
                'clean_date'       => '2022-02-14', // ~4.2 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Springdale / Hamilton County (11234 Springfield Pike, 45246)',
                'bus_line'         => '72',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'stephanie.king@example.com',
                'first_name'       => 'Stephanie',
                'last_name'        => 'King',
                'dob'              => '1984-06-19',
                'phone'            => '513-203-0707',
                'clean_date'       => '2021-10-01', // ~4.6 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Springfield Township (8901 Winton Rd, 45231)',
                'bus_line'         => '41',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'marcus.webb@example.com',
                'first_name'       => 'Marcus',
                'last_name'        => 'Webb',
                'dob'              => '1982-04-25',
                'phone'            => '513-203-0808',
                'clean_date'       => '2021-08-20', // ~4.7 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Anderson Township (3312 Beech Ave, 45255)',
                'bus_line'         => '50',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'tanya.owens@example.com',
                'first_name'       => 'Tanya',
                'last_name'        => 'Owens',
                'dob'              => '1979-08-31',
                'phone'            => '513-203-0909',
                'clean_date'       => '2021-06-11', // ~4.9 years ago
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Delhi Township (5656 Delhi Ave, 45238)',
                'bus_line'         => '49',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'raymond.cruz@example.com',
                'first_name'       => 'Raymond',
                'last_name'        => 'Cruz',
                'dob'              => '1977-02-13',
                'phone'            => '513-203-1010',
                'clean_date'       => '2021-05-01', // ~5.0 years ago (boundary)
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Price Hill / Delhi (4789 Sunset Ave, 45238)',
                'bus_line'         => '33',
                'is_sms_deliverable' => true,
            ],

            // ════════════════════════════════════════════════
            // GROUP 4 — 10 volunteers  (5 – 30 years)
            // Clean dates: Apr 26 1996 → Apr 25 2021
            // Cincinnati & Hamilton County
            // Availability: weekends only, 8 am – 10 pm
            // ════════════════════════════════════════════════

            [
                'email'            => 'john@example.com',
                'first_name'       => 'John',
                'last_name'        => 'Smith',
                'dob'              => '1975-05-10',
                'phone'            => '513-204-0101',
                'clean_date'       => '2020-08-15', // ~5.7 years
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'West End (847 Gest St, 45203)',
                'bus_line'         => '20',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'sarah@example.com',
                'first_name'       => 'Sarah',
                'last_name'        => 'Johnson',
                'dob'              => '1978-11-22',
                'phone'            => '513-204-0202',
                'clean_date'       => '2019-04-22', // ~7 years
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Hyde Park (2156 Grandin Rd, 45208)',
                'bus_line'         => '23',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'marcus@example.com',
                'first_name'       => 'Marcus',
                'last_name'        => 'Williams',
                'dob'              => '1972-08-03',
                'phone'            => '513-204-0303',
                'clean_date'       => '2017-09-10', // ~8.6 years
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Clifton (3398 Clifton Ave, 45220)',
                'bus_line'         => '43',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'emily@example.com',
                'first_name'       => 'Emily',
                'last_name'        => 'Chen',
                'dob'              => '1983-02-14',
                'phone'            => '513-204-0404',
                'clean_date'       => '2021-04-01', // ~5.1 years
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Madeira (6712 Miami Ave, 45243)',
                'bus_line'         => '52',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'robert@example.com',
                'first_name'       => 'Robert',
                'last_name'        => 'Davis',
                'dob'              => '1968-06-30',
                'phone'            => '513-204-0505',
                'clean_date'       => '2016-03-17', // ~10.1 years
                'probation_status' => 'active_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Evanston (1247 Tennessee Ave, 45229)',
                'bus_line'         => '11',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'lisa@example.com',
                'first_name'       => 'Lisa',
                'last_name'        => 'Martinez',
                'dob'              => '1970-09-15',
                'phone'            => '513-204-0606',
                'clean_date'       => '2015-08-08', // ~10.7 years
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Pleasant Ridge (5521 Ridge Ave, 45213)',
                'bus_line'         => '25',
                'is_sms_deliverable' => false,
            ],
            [
                'email'            => 'james@example.com',
                'first_name'       => 'James',
                'last_name'        => 'Wilson',
                'dob'              => '1965-03-28',
                'phone'            => '513-204-0707',
                'clean_date'       => '2013-12-20', // ~12.4 years
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Madeira (9034 Miami Ave, 45243)',
                'bus_line'         => '52',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'amanda@example.com',
                'first_name'       => 'Amanda',
                'last_name'        => 'Thompson',
                'dob'              => '1973-07-04',
                'phone'            => '513-204-0808',
                'clean_date'       => '2010-06-05', // ~15.9 years
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Northside (4156 Spring Grove Ave, 45223)',
                'bus_line'         => '28',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'carlos@example.com',
                'first_name'       => 'Carlos',
                'last_name'        => 'Garcia',
                'dob'              => '1960-12-11',
                'phone'            => '513-204-0909',
                'clean_date'       => '2008-11-11', // ~17.5 years
                'probation_status' => 'not_probation',
                'gender'           => 'Male',
                'neighborhood'     => 'Roselawn (2891 Losantiville Ave, 45237)',
                'bus_line'         => '16',
                'is_sms_deliverable' => true,
            ],
            [
                'email'            => 'patricia@example.com',
                'first_name'       => 'Patricia',
                'last_name'        => 'Brown',
                'dob'              => '1958-04-19',
                'phone'            => '513-204-1010',
                'clean_date'       => '2000-03-15', // ~26.1 years
                'probation_status' => 'not_probation',
                'gender'           => 'Female',
                'neighborhood'     => 'Mason / Hamilton County (11802 Mason-Montgomery Rd, 45249)',
                'bus_line'         => 'none',
                'is_sms_deliverable' => true,
            ],
        ];

        foreach ($volunteers as $data) {
            Volunteer::create($data);
        }
    }
}
