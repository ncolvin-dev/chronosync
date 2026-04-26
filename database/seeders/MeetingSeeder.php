<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use Illuminate\Database\Seeder;

/**
 * MeetingSeeder — recurring weekly H&I meetings for each facility.
 *
 * Uses the recurring-pattern fields added by
 * 2024_01_04_100000_update_meetings_for_recurring_patterns.php:
 *   day_of_week   : 0=Sun 1=Mon 2=Tue 3=Wed 4=Thu 5=Fri 6=Sat
 *   week_of_month : null = every week, 1-4 = specific week
 *   meeting_time  : time string (HH:MM:SS)
 *   duration_minutes: always 60 unless noted
 *   scheduled_time  : null (pattern-based, not one-off)
 *
 * Gender notes (where a co-ed facility has gender-specific sessions)
 * are recorded in the 'notes' field. The meetings table has no
 * gender column — the facility-level gender_restriction flag governs
 * volunteer eligibility at the matching stage.
 *
 * ⚠️  ASSUMPTION: Glenwood Behavioral Health has no day specified →
 *      defaulted to Wednesday. Brookside Health Center has no day
 *      specified → defaulted to Thursday. Update if incorrect.
 */
class MeetingSeeder extends Seeder
{
    // Day-of-week constants (Carbon convention)
    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    public function run(): void
    {
        $metroHospital   = Facility::where('facility_name', 'Metro Hospital')->first();
        $countyJail      = Facility::where('facility_name', 'County Jail')->first();
        $treatmentCenter = Facility::where('facility_name', 'Springfield Treatment Center')->first();
        $communityCenter = Facility::where('facility_name', 'Community Center')->first();
        $youthDetention  = Facility::where('facility_name', 'Youth Detention Center')->first();

        // Metro Hospital — Monday at 2pm (active)
        Meeting::create([
            'facility_id'      => $metroHospital?->facility_id,
            'scheduled_time'   => Carbon::parse('next monday')->setTime(14, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'active',
            'notes'            => 'Metro Hospital Monday meeting',
        ]);

        // County Jail — Wednesday at 7pm (active)
        Meeting::create([
            'facility_id'      => $countyJail?->facility_id,
            'scheduled_time'   => Carbon::parse('next wednesday')->setTime(19, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'active',
            'notes'            => 'County Jail Wednesday meeting',
        ]);

        // County Jail — Friday at 7pm (inactive — facility temporarily unavailable)
        Meeting::create([
            'facility_id'      => $countyJail?->facility_id,
            'scheduled_time'   => Carbon::parse('next friday')->setTime(19, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 1,
            'status'           => 'inactive',
            'notes'            => 'County Jail Friday meeting — currently suspended',
        ]);

        // Springfield Treatment Center — Thursday at 6pm (active)
        Meeting::create([
            'facility_id'      => $treatmentCenter?->facility_id,
            'scheduled_time'   => Carbon::parse('next thursday')->setTime(18, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'active',
            'notes'            => 'Treatment Center Thursday meeting',
        ]);

        // Community Center — Tuesday at 9am (active)
        Meeting::create([
            'facility_id'      => $communityCenter?->facility_id,
            'scheduled_time'   => Carbon::parse('next tuesday')->setTime(9, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 3,
            'status'           => 'active',
            'notes'            => 'Community Center Tuesday morning meeting',
        ]);

        // Community Center — Friday at 5pm (active)
        Meeting::create([
            'facility_id'      => $communityCenter?->facility_id,
            'scheduled_time'   => Carbon::parse('next friday')->setTime(17, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'active',
            'notes'            => 'Community Center Friday afternoon meeting',
        ]);

        // Youth Detention Center — Monday at 3pm (active)
        Meeting::create([
            'facility_id'      => $youthDetention?->facility_id,
            'scheduled_time'   => Carbon::parse('next monday')->setTime(15, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'active',
            'notes'            => 'Youth Detention Monday meeting',
        ]);

        // Youth Detention Center — Wednesday at 3pm (inactive — under review)
        Meeting::create([
            'facility_id'      => $youthDetention?->facility_id,
            'scheduled_time'   => Carbon::parse('next wednesday')->setTime(15, 0),
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 1,
            'status'           => 'inactive',
            'notes'            => 'Youth Detention Wednesday meeting — under review',
        ]);
    }
}
