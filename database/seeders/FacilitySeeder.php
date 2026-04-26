<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

/**
 * FacilitySeeder — Cincinnati-area H&I meeting facilities.
 *
 * Contact names and email addresses are fictional / for testing only.
 * Addresses are plausible Cincinnati-area locations.
 *
 * ⚠️  ASSUMPTION: Glenwood Behavioral Health has no day of week specified —
 *      defaulted to Wednesday. Brookside Health Center also has no day
 *      specified — defaulted to Thursday. Update MeetingSeeder if incorrect.
 *
 * gender_restriction = true means the facility serves one gender only.
 *   River City Correctional Center → men only
 *   Esther Marie Hatton Center     → women only
 *   All others                     → false (co-ed facility)
 *
 * Individual meeting-level gender notes (men-only sessions at co-ed
 * facilities, e.g. CAT) are recorded in MeetingSeeder notes fields.
 *
 * State codes use the values from config/states.php.
 * clean_time_requirement is in years.
 */
class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [

            // ── Treatment Centers ─────────────────────────────────────────

            [
                'facility_name'          => 'Center for Addiction Treatment',
                'address'                => '1151 E Galbraith Rd',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45215',
                'main_phone'             => '513-924-7900',
                'contact_email'          => 'walsh.p@catcincinnati.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check', 'tb_test'],
                'gender_restriction'     => false,   // has both co-ed and men-only meetings
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Patricia Walsh',
                'contact1_phone'         => '513-924-7910',
                'contact1_email'         => 'walsh.p@catcincinnati.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Glenwood Behavioral Health Hospital',
                'address'                => '2700 Chancellor Dr',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45211',
                'main_phone'             => '513-351-7700',
                'contact_email'          => 'harmon.d@glenwoodbh.org',
                // ⚠️ Day of week not specified by user — defaulted to Wednesday in MeetingSeeder
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check', 'tb_test'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Dennis Harmon',
                'contact1_phone'         => '513-351-7710',
                'contact1_email'         => 'harmon.d@glenwoodbh.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Lumiere Healing Center',
                'address'                => '4291 Fosse Way',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45209',
                'main_phone'             => '513-871-4200',
                'contact_email'          => 'sandra.voss@lumierehealing.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Sandra Voss',
                'contact1_phone'         => '513-871-4210',
                'contact1_email'         => 'sandra.voss@lumierehealing.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Talbert House ADAPT',
                'address'                => '3803 Reading Rd',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45229',
                'main_phone'             => '513-872-0100',
                'contact_email'          => 'kevin.rhodes@talberthouse.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Kevin Rhodes',
                'contact1_phone'         => '513-872-0110',
                'contact1_email'         => 'kevin.rhodes@talberthouse.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Brookside Health Center',
                'address'                => '1101 Brookside Dr',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45238',
                'main_phone'             => '513-245-7800',
                'contact_email'          => 'donna.fitz@brooksidehealth.org',
                // ⚠️ Day of week not specified by user — defaulted to Thursday in MeetingSeeder
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Donna Fitzgerald',
                'contact1_phone'         => '513-245-7810',
                'contact1_email'         => 'donna.fitz@brooksidehealth.org',
                'timezone'               => 'America/New_York',
            ],

            // ── Recovery Residences / Community ───────────────────────────

            [
                'facility_name'          => 'Joseph House',
                'address'                => '3636 Woodford Rd',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45213',
                'main_phone'             => '513-396-2200',
                'contact_email'          => 'mobrien@josephhousecincinnati.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Michael O\'Brien',
                'contact1_phone'         => '513-396-2210',
                'contact1_email'         => 'mobrien@josephhousecincinnati.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Barron Center',
                'address'                => '835 Ezzard Charles Dr',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45214',
                'main_phone'             => '513-241-1900',
                'contact_email'          => 'a.stanton@barroncenter.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Andrea Stanton',
                'contact1_phone'         => '513-241-1910',
                'contact1_email'         => 'a.stanton@barroncenter.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Adams Recovery Center',
                'address'                => '9 W Loveland Ave',
                'city'                   => 'Loveland',
                'state'                  => 'OH',
                'zip'                    => '45140',
                'main_phone'             => '513-683-5500',
                'contact_email'          => 'tbeckley@adamsrecovery.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Thomas Beckley',
                'contact1_phone'         => '513-683-5510',
                'contact1_email'         => 'tbeckley@adamsrecovery.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Talbert House Pathways',
                'address'                => '4615 Duck Creek Rd',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45227',
                'main_phone'             => '513-351-6600',
                'contact_email'          => 'l.engle@talberthouse.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Laura Engle',
                'contact1_phone'         => '513-351-6610',
                'contact1_email'         => 'l.engle@talberthouse.org',
                'timezone'               => 'America/New_York',
            ],

            // ── Correctional ─────────────────────────────────────────────

            [
                'facility_name'          => 'River City Correctional Center',
                'address'                => '301 W Court St',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45202',
                'main_phone'             => '513-946-7500',
                'contact_email'          => 'j.wyatt@rivercitycorrectional.org',
                'clean_time_requirement' => 2,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => true,   // men only
                'probation_allowed'      => false,
                'status'                 => 'active',
                'contact1_name'          => 'Captain James Wyatt',
                'contact1_phone'         => '513-946-7510',
                'contact1_email'         => 'j.wyatt@rivercitycorrectional.org',
                'timezone'               => 'America/New_York',
            ],

            // ── Women-Specific ────────────────────────────────────────────

            [
                'facility_name'          => 'Esther Marie Hatton Center for Women',
                'address'                => '514 Oak St',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45219',
                'main_phone'             => '513-651-2300',
                'contact_email'          => 'r.caldwell@hattonwomen.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => true,   // women only
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Renee Caldwell',
                'contact1_phone'         => '513-651-2310',
                'contact1_email'         => 'r.caldwell@hattonwomen.org',
                'timezone'               => 'America/New_York',
            ],

            // ── Community / Outpatient ─────────────────────────────────────

            [
                'facility_name'          => 'Crossroads Center',
                'address'                => '4760 Red Bank Rd',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45227',
                'main_phone'             => '513-631-5000',
                'contact_email'          => 'b.holman@crossroadscenter.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Brian Holman',
                'contact1_phone'         => '513-631-5010',
                'contact1_email'         => 'b.holman@crossroadscenter.org',
                'timezone'               => 'America/New_York',
            ],
            [
                'facility_name'          => 'Talbert House Spring Grove Center',
                'address'                => '4615 Spring Grove Ave',
                'city'                   => 'Cincinnati',
                'state'                  => 'OH',
                'zip'                    => '45232',
                'main_phone'             => '513-921-3400',
                'contact_email'          => 'n.pierce@talberthouse.org',
                'clean_time_requirement' => 1,
                'credentialing_types'    => ['background_check'],
                'gender_restriction'     => false,
                'probation_allowed'      => true,
                'status'                 => 'active',
                'contact1_name'          => 'Natalie Pierce',
                'contact1_phone'         => '513-921-3410',
                'contact1_email'         => 'n.pierce@talberthouse.org',
                'timezone'               => 'America/New_York',
            ],

        ];

        foreach ($facilities as $data) {
            Facility::create($data);
        }
    }
}
