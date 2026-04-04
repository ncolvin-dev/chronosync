<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        // Look up facilities by facility_name (not name)
        $metroHospital   = Facility::where('facility_name', 'Metro Hospital')->first();
        $countyJail      = Facility::where('facility_name', 'County Jail')->first();
        $treatmentCenter = Facility::where('facility_name', 'Springfield Treatment Center')->first();
        $communityCenter = Facility::where('facility_name', 'Community Center')->first();
        $youthDetention  = Facility::where('facility_name', 'Youth Detention Center')->first();

        $this->createMetroHospitalMeetings($metroHospital);
        $this->createCountyJailMeetings($countyJail);
        $this->createTreatmentCenterMeetings($treatmentCenter);
        $this->createCommunityCenterMeetings($communityCenter);
        $this->createYouthDetentionMeetings($youthDetention);
    }

    private function createMetroHospitalMeetings(?Facility $facility): void
    {
        if (!$facility) return;

        foreach ([3, 4, 5] as $month) {
            $mondays = $this->getDaysOfWeek(2026, $month, Carbon::MONDAY);
            foreach (array_filter([$mondays[0] ?? null, $mondays[2] ?? null, $mondays[4] ?? null]) as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(14, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'Metro Hospital regular meeting',
                ]);
            }
        }
    }

    private function createCountyJailMeetings(?Facility $facility): void
    {
        if (!$facility) return;

        for ($month = 3; $month <= 5; $month++) {
            foreach ($this->getDaysOfWeek(2026, $month, Carbon::WEDNESDAY) as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(19, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'County Jail weekly meeting',
                ]);
            }
        }
    }

    private function createTreatmentCenterMeetings(?Facility $facility): void
    {
        if (!$facility) return;

        for ($month = 3; $month <= 5; $month++) {
            foreach ($this->getDaysOfWeek(2026, $month, Carbon::THURSDAY) as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(18, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'Treatment Center meeting',
                ]);
            }
        }
    }

    private function createCommunityCenterMeetings(?Facility $facility): void
    {
        if (!$facility) return;

        for ($month = 3; $month <= 5; $month++) {
            foreach ($this->getDaysOfWeek(2026, $month, Carbon::TUESDAY) as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(9, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'Community Center Tuesday meeting',
                ]);
            }
            foreach ($this->getDaysOfWeek(2026, $month, Carbon::FRIDAY) as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(17, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'Community Center Friday meeting',
                ]);
            }
        }
    }

    private function createYouthDetentionMeetings(?Facility $facility): void
    {
        if (!$facility) return;

        for ($month = 3; $month <= 5; $month++) {
            $days = array_merge(
                $this->getDaysOfWeek(2026, $month, Carbon::MONDAY),
                $this->getDaysOfWeek(2026, $month, Carbon::WEDNESDAY),
                $this->getDaysOfWeek(2026, $month, Carbon::FRIDAY)
            );
            foreach ($days as $day) {
                Meeting::create([
                    'facility_id'      => $facility->facility_id,
                    'scheduled_time'   => $day->copy()->setTime(15, 0),
                    'duration_minutes' => 60,
                    'status'           => $this->randomStatus(),
                    'notes'            => 'Youth Detention Center meeting',
                ]);
            }
        }
    }

    private function getDaysOfWeek(int $year, int $month, int $dayOfWeek): array
    {
        $days = [];
        $date = Carbon::create($year, $month, 1);
        while ($date->month === $month) {
            if ($date->dayOfWeek === $dayOfWeek) {
                $days[] = $date->copy();
            }
            $date->addDay();
        }
        return $days;
    }

    private function randomStatus(): string
    {
        return ['scheduled', 'assigned', 'completed'][array_rand(['scheduled', 'assigned', 'completed'])];
    }
}
