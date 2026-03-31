<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    /**
     * Seed the meetings table.
     */
    public function run(): void
    {
        $metroHospital = Facility::where('name', 'Metro Hospital')->first();
        $countyJail = Facility::where('name', 'County Jail')->first();
        $treatmentCenter = Facility::where('name', 'Springfield Treatment Center')->first();
        $communityCenter = Facility::where('name', 'Community Center')->first();
        $youthDetention = Facility::where('name', 'Youth Detention Center')->first();

        $volunteers = Volunteer::with('user')->get();

        // Metro Hospital - Mondays 2-3 PM (1st, 3rd, 5th weeks of month)
        $this->createMetroHospitalMeetings($metroHospital, $volunteers);

        // County Jail - Wednesdays 7-8 PM (all weeks)
        $this->createCountyJailMeetings($countyJail, $volunteers);

        // Springfield Treatment Center - Thursdays 6-7 PM (all weeks)
        $this->createTreatmentCenterMeetings($treatmentCenter, $volunteers);

        // Community Center - Tuesdays 9-10 AM and Fridays 5-6 PM
        $this->createCommunityCenterMeetings($communityCenter, $volunteers);

        // Youth Detention Center - Mon/Wed/Fri 3-4 PM
        $this->createYouthDetentionMeetings($youthDetention, $volunteers);
    }

    private function createMetroHospitalMeetings($facility, $volunteers): void
    {
        if (!$facility) {
            return;
        }

        // Get first, third, and fifth Mondays of March, April, May 2026
        $months = [3, 4, 5];
        foreach ($months as $month) {
            $mondays = $this->getMondays(2026, $month);
            $selectedMondays = [$mondays[0], $mondays[2], $mondays[4] ?? null];

            foreach (array_filter($selectedMondays) as $monday) {
                $meetingTime = $monday->copy()->setTime(14, 0); // 2 PM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'Regular meeting scheduled',
                ]);
            }
        }
    }

    private function createCountyJailMeetings($facility, $volunteers): void
    {
        if (!$facility) {
            return;
        }

        // All Wednesdays 7 PM for March, April, May 2026
        for ($month = 3; $month <= 5; $month++) {
            $wednesdays = $this->getWednesdays(2026, $month);
            foreach ($wednesdays as $wednesday) {
                $meetingTime = $wednesday->copy()->setTime(19, 0); // 7 PM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'County Jail weekly meeting',
                ]);
            }
        }
    }

    private function createTreatmentCenterMeetings($facility, $volunteers): void
    {
        if (!$facility) {
            return;
        }

        // All Thursdays 6 PM for March, April, May 2026
        for ($month = 3; $month <= 5; $month++) {
            $thursdays = $this->getThursdays(2026, $month);
            foreach ($thursdays as $thursday) {
                $meetingTime = $thursday->copy()->setTime(18, 0); // 6 PM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'Treatment Center meeting',
                ]);
            }
        }
    }

    private function createCommunityCenterMeetings($facility, $volunteers): void
    {
        if (!$facility) {
            return;
        }

        // Tuesdays 9 AM and Fridays 5 PM for March, April, May 2026
        for ($month = 3; $month <= 5; $month++) {
            $tuesdays = $this->getTuesdays(2026, $month);
            $fridays = $this->getFridays(2026, $month);

            foreach ($tuesdays as $tuesday) {
                $meetingTime = $tuesday->copy()->setTime(9, 0); // 9 AM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'Community Center Tuesday meeting',
                ]);
            }

            foreach ($fridays as $friday) {
                $meetingTime = $friday->copy()->setTime(17, 0); // 5 PM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'Community Center Friday meeting',
                ]);
            }
        }
    }

    private function createYouthDetentionMeetings($facility, $volunteers): void
    {
        if (!$facility) {
            return;
        }

        // Mon/Wed/Fri 3 PM for March, April, May 2026
        for ($month = 3; $month <= 5; $month++) {
            $mondays = $this->getMondays(2026, $month);
            $wednesdays = $this->getWednesdays(2026, $month);
            $fridays = $this->getFridays(2026, $month);

            foreach (array_merge($mondays, $wednesdays, $fridays) as $day) {
                $meetingTime = $day->copy()->setTime(15, 0); // 3 PM

                Meeting::create([
                    'facility_id' => $facility->id,
                    'meeting_datetime' => $meetingTime,
                    'duration_minutes' => 60,
                    'status' => $this->randomMeetingStatus(),
                    'notes' => 'Youth Detention Center meeting',
                ]);
            }
        }
    }

    private function getMondays(int $year, int $month): array
    {
        $mondays = [];
        $date = Carbon::create($year, $month, 1);

        while ($date->month === $month) {
            if ($date->dayOfWeek === 1) { // Monday
                $mondays[] = $date->copy();
            }
            $date->addDay();
        }

        return $mondays;
    }

    private function getTuesdays(int $year, int $month): array
    {
        $tuesdays = [];
        $date = Carbon::create($year, $month, 1);

        while ($date->month === $month) {
            if ($date->dayOfWeek === 2) { // Tuesday
                $tuesdays[] = $date->copy();
            }
            $date->addDay();
        }

        return $tuesdays;
    }

    private function getWednesdays(int $year, int $month): array
    {
        $wednesdays = [];
        $date = Carbon::create($year, $month, 1);

        while ($date->month === $month) {
            if ($date->dayOfWeek === 3) { // Wednesday
                $wednesdays[] = $date->copy();
            }
            $date->addDay();
        }

        return $wednesdays;
    }

    private function getThursdays(int $year, int $month): array
    {
        $thursdays = [];
        $date = Carbon::create($year, $month, 1);

        while ($date->month === $month) {
            if ($date->dayOfWeek === 4) { // Thursday
                $thursdays[] = $date->copy();
            }
            $date->addDay();
        }

        return $thursdays;
    }

    private function getFridays(int $year, int $month): array
    {
        $fridays = [];
        $date = Carbon::create($year, $month, 1);

        while ($date->month === $month) {
            if ($date->dayOfWeek === 5) { // Friday
                $fridays[] = $date->copy();
            }
            $date->addDay();
        }

        return $fridays;
    }

    private function randomMeetingStatus(): string
    {
        $statuses = ['scheduled', 'assigned', 'completed'];
        return $statuses[array_rand($statuses)];
    }
}
