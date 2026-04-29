<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use Illuminate\Database\Seeder;

/**
 * MeetingSeeder — recurring weekly H&I meetings for each facility.
 *
 * Uses the recurring-pattern fields:
 *   day_of_week    : 0=Sun 1=Mon 2=Tue 3=Wed 4=Thu 5=Fri 6=Sat
 *   week_of_month  : 1-4 = specific week, 5 = last occurrence
 *   meeting_time   : time string (HH:MM:SS)
 *   scheduled_time : null  ← must be null for recurring meetings
 */
class MeetingSeeder extends Seeder
{
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

        $meetings = [
            // Metro Hospital — every Monday at 2pm
            ['facility' => $metroHospital,   'dow' => self::MON, 'wom' => null, 'time' => '14:00:00', 'volunteers' => 2, 'status' => 'active',   'notes' => 'Metro Hospital Monday meeting'],
            // County Jail — every Wednesday at 7pm
            ['facility' => $countyJail,      'dow' => self::WED, 'wom' => null, 'time' => '19:00:00', 'volunteers' => 2, 'status' => 'active',   'notes' => 'County Jail Wednesday meeting'],
            // County Jail — every Friday at 7pm (inactive)
            ['facility' => $countyJail,      'dow' => self::FRI, 'wom' => null, 'time' => '19:00:00', 'volunteers' => 1, 'status' => 'inactive', 'notes' => 'County Jail Friday meeting — currently suspended'],
            // Springfield Treatment Center — every Thursday at 6pm
            ['facility' => $treatmentCenter, 'dow' => self::THU, 'wom' => null, 'time' => '18:00:00', 'volunteers' => 2, 'status' => 'active',   'notes' => 'Treatment Center Thursday meeting'],
            // Community Center — every Tuesday at 9am
            ['facility' => $communityCenter, 'dow' => self::TUE, 'wom' => null, 'time' => '09:00:00', 'volunteers' => 3, 'status' => 'active',   'notes' => 'Community Center Tuesday morning meeting'],
            // Community Center — every Friday at 5pm
            ['facility' => $communityCenter, 'dow' => self::FRI, 'wom' => null, 'time' => '17:00:00', 'volunteers' => 2, 'status' => 'active',   'notes' => 'Community Center Friday afternoon meeting'],
            // Youth Detention Center — every Monday at 3pm
            ['facility' => $youthDetention,  'dow' => self::MON, 'wom' => null, 'time' => '15:00:00', 'volunteers' => 2, 'status' => 'active',   'notes' => 'Youth Detention Monday meeting'],
            // Youth Detention Center — every Wednesday at 3pm (inactive)
            ['facility' => $youthDetention,  'dow' => self::WED, 'wom' => null, 'time' => '15:00:00', 'volunteers' => 1, 'status' => 'inactive', 'notes' => 'Youth Detention Wednesday meeting — under review'],
        ];

        foreach ($meetings as $m) {
            if (!$m['facility']) continue;
            Meeting::create([
                'facility_id'       => $m['facility']->facility_id,
                'scheduled_time'    => null,          // null = recurring pattern
                'day_of_week'       => $m['dow'],
                'week_of_month'     => $m['wom'],
                'meeting_time'      => $m['time'],
                'duration_minutes'  => 60,
                'format'            => 'in_person',
                'volunteers_needed' => $m['volunteers'],
                'status'            => $m['status'],
                'notes'             => $m['notes'],
            ]);
        }
    }
}
