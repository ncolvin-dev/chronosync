<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    // Day of week constants (Carbon: 0=Sunday, 1=Monday...6=Saturday)
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;

    public function run(): void
    {
        // Availability patterns per volunteer email
        // Each slot: [day_of_week (int), hours (array of hour_start ints)]
        $patterns = [
            'john@example.com' => $this->allWeekdayHours(),
            'emily@example.com' => $this->allWeekdayHours(),
            'james@example.com' => $this->allWeekdayHours(),
            'carlos@example.com' => $this->allWeekdayHours(),

            'sarah@example.com' => [
                [self::MONDAY,    [14, 15]],
                [self::WEDNESDAY, [19, 20]],
                [self::FRIDAY,    [17, 18]],
            ],
            'marcus@example.com' => [
                [self::TUESDAY,   [9, 10]],
                [self::WEDNESDAY, [19, 20]],
                [self::THURSDAY,  [18, 19]],
            ],
            'robert@example.com' => [
                [self::MONDAY,    [14, 15]],
                [self::TUESDAY,   [9, 10]],
                [self::FRIDAY,    [14, 15]],
            ],
            'lisa@example.com' => [
                [self::TUESDAY,  [9, 10]],
                [self::THURSDAY, [18, 19]],
                [self::FRIDAY,   [17, 18]],
            ],
            'amanda@example.com' => [
                [self::MONDAY,    [14, 15]],
                [self::WEDNESDAY, [19, 20]],
                [self::THURSDAY,  [18, 19]],
            ],
            'patricia@example.com' => [
                [self::TUESDAY,   [9, 10]],
                [self::WEDNESDAY, [19, 20]],
                [self::FRIDAY,    [17, 18]],
            ],
        ];

        foreach ($patterns as $email => $slots) {
            $volunteer = Volunteer::where('email', $email)->first();
            if (!$volunteer) continue;

            // Insert for all 4 weeks of the month
            foreach (range(1, 4) as $week) {
                foreach ($slots as [$dayOfWeek, $hours]) {
                    foreach ($hours as $hourStart) {
                        Availability::create([
                            'volunteer_id' => $volunteer->volunteer_id,
                            'week_of_month' => $week,
                            'day_of_week'   => $dayOfWeek,
                            'hour_start'    => $hourStart,
                            'is_available'  => true,
                        ]);
                    }
                }
            }
        }
    }

    // All weekday business + evening hours (9-20) for each weekday
    private function allWeekdayHours(): array
    {
        $slots = [];
        $hours = range(9, 20);
        foreach ([self::MONDAY, self::TUESDAY, self::WEDNESDAY, self::THURSDAY, self::FRIDAY] as $day) {
            $slots[] = [$day, $hours];
        }
        return $slots;
    }
}
