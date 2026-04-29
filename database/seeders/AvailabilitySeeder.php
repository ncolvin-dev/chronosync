<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

/**
 * AvailabilitySeeder
 *
 * Populates availability for all 30 volunteers across 4 groups.
 * Slots are inserted for all 4 weeks of the month.
 *
 * Day of week (Carbon convention):
 *   0 = Sunday    1 = Monday    2 = Tuesday   3 = Wednesday
 *   4 = Thursday  5 = Friday    6 = Saturday
 *
 * ─────────────────────────────────────────────────────────────
 * GROUP 1 (3 days – 6 months clean) — no constraint specified.
 *   Mixed limited availability reflecting early recovery schedules.
 *
 * GROUP 2 (6 months – 2 years clean) — weekday evenings after 5 pm.
 *   Mon–Fri, hours 17–21.
 *
 * GROUP 3 (2 – 5 years clean) — weekdays after 4 pm
 *   and/or weekend mornings/afternoons (NOT weekend evenings).
 *   Weekday hours: 16–21.  Weekend hours: 8–14 only.
 *
 * GROUP 4 (5 – 30 years clean) — weekends only, 8 am – 10 pm.
 *   Saturday + Sunday, hours 8–21.
 * ─────────────────────────────────────────────────────────────
 */
class AvailabilitySeeder extends Seeder
{
    // Day constants
    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    public function run(): void
    {
        $patterns = $this->buildPatterns();

        foreach ($patterns as $email => $slots) {
            $volunteer = Volunteer::where('email', $email)->first();
            if (!$volunteer) {
                continue;
            }

            foreach (range(1, 4) as $week) {
                foreach ($slots as [$day, $hours]) {
                    foreach ($hours as $hour) {
                        Availability::create([
                            'volunteer_id'  => $volunteer->volunteer_id,
                            'week_of_month' => $week,
                            'day_of_week'   => $dayOfWeek,
                            'hour_start'    => $hourStart,
                            'hour_end'      => $hourStart + 1,
                            'is_available'  => true,
                        ]);
                    }
                }
            }
        }
    }

    // ─── Pattern builder ─────────────────────────────────────

    private function buildPatterns(): array
    {
        return array_merge(
            $this->group1Patterns(),
            $this->group2Patterns(),
            $this->group3Patterns(),
            $this->group4Patterns()
        );
    }

    // ─── GROUP 1 — limited / mixed (3 days – 6 months clean) ─

    private function group1Patterns(): array
    {
        return [
            // Tyler Bennett — Tue evenings + Sat mornings
            'tyler.bennett@example.com' => [
                [self::TUE, [18, 19]],
                [self::SAT, [10, 11]],
            ],
            // Kayla Foster — Wed evenings + Sun afternoons
            'kayla.foster@example.com' => [
                [self::WED, [18, 19]],
                [self::SUN, [13, 14]],
            ],
            // Devon Reyes — Thu evenings + Sat afternoons
            'devon.reyes@example.com' => [
                [self::THU, [19, 20]],
                [self::SAT, [13, 14]],
            ],
            // Brittney Cole — Mon evenings + Sun mornings
            'brittney.cole@example.com' => [
                [self::MON, [18, 19, 20]],
                [self::SUN, [10, 11]],
            ],
            // Anthony Price — Fri evenings + Sat mornings
            'anthony.price@example.com' => [
                [self::FRI, [17, 18]],
                [self::SAT, [9, 10]],
            ],
        ];
    }

    // ─── GROUP 2 — weekday evenings after 5 pm only ───────────

    private function group2Patterns(): array
    {
        $evenings = [17, 18, 19, 20]; // 5 pm – 8 pm
        return [
            // David Anderson — Mon-Fri evenings
            'david.anderson@example.com' => $this->weekdaySlots([17, 18, 19, 20]),

            // Jennifer White — Mon-Fri evenings (slightly shorter)
            'jennifer.white@example.com' => $this->weekdaySlots([17, 18, 19]),

            // Christopher Miller — Mon, Wed, Fri evenings
            'christopher.miller@example.com' => [
                [self::MON, [17, 18, 19, 20]],
                [self::WED, [17, 18, 19, 20]],
                [self::FRI, [17, 18, 19, 20]],
            ],

            // Michelle Taylor — Tue/Thu evenings
            'michelle.taylor@example.com' => [
                [self::TUE, [17, 18, 19, 20, 21]],
                [self::THU, [17, 18, 19, 20, 21]],
            ],

            // Daniel Thomas — Mon-Fri evenings
            'daniel.thomas@example.com' => $this->weekdaySlots([17, 18, 19, 20]),
        ];
    }

    // ─── GROUP 3 — weekdays after 4 pm and/or weekend mornings/afternoons ─

    private function group3Patterns(): array
    {
        return [
            // Jessica Moore — weekdays after 4 pm
            'jessica.moore@example.com' => $this->weekdaySlots(range(16, 20)),

            // Matthew Jackson — weekend mornings only
            'matthew.jackson@example.com' => [
                [self::SAT, [8, 9, 10, 11]],
                [self::SUN, [8, 9, 10, 11]],
            ],

            // Ashley Martin — weekend mornings and afternoons
            'ashley.martin@example.com' => [
                [self::SAT, range(8, 14)],
                [self::SUN, range(8, 14)],
            ],

            // Ryan Lee — weekdays after 4 pm + weekend mornings
            'ryan.lee@example.com' => array_merge(
                $this->weekdaySlots(range(16, 20)),
                [
                    [self::SAT, [8, 9, 10, 11]],
                    [self::SUN, [8, 9, 10, 11]],
                ]
            ),

            // Lauren Perez — weekend mornings and afternoons
            'lauren.perez@example.com' => [
                [self::SAT, range(8, 14)],
                [self::SUN, range(8, 14)],
            ],

            // Steven Young — weekdays after 4 pm (dual-role)
            'steven.young@example.com' => $this->weekdaySlots(range(16, 21)),

            // Stephanie King — weekdays after 4 pm + weekend mornings/afternoons (dual-role)
            'stephanie.king@example.com' => array_merge(
                $this->weekdaySlots(range(16, 20)),
                [
                    [self::SAT, range(8, 14)],
                    [self::SUN, range(8, 14)],
                ]
            ),

            // Marcus Webb — weekend mornings only
            'marcus.webb@example.com' => [
                [self::SAT, [8, 9, 10, 11, 12]],
                [self::SUN, [8, 9, 10, 11, 12]],
            ],

            // Tanya Owens — weekdays after 4 pm + weekend afternoons
            'tanya.owens@example.com' => array_merge(
                $this->weekdaySlots(range(16, 20)),
                [
                    [self::SAT, [12, 13, 14]],
                    [self::SUN, [12, 13, 14]],
                ]
            ),

            // Raymond Cruz — weekend mornings and afternoons
            'raymond.cruz@example.com' => [
                [self::SAT, range(9, 14)],
                [self::SUN, range(9, 14)],
            ],
        ];
    }

    // ─── GROUP 4 — weekends only, 8 am – 10 pm ───────────────

    private function group4Patterns(): array
    {
        $weekendHours = range(8, 21); // 8 am through 9 pm start (covers 10 pm end)
        $weekendSlots = [
            [self::SAT, $weekendHours],
            [self::SUN, $weekendHours],
        ];

        $emails = [
            'john@example.com',
            'sarah@example.com',
            'marcus@example.com',
            'emily@example.com',
            'robert@example.com',
            'lisa@example.com',
            'james@example.com',
            'amanda@example.com',
            'carlos@example.com',
            'patricia@example.com',
        ];

        $patterns = [];
        foreach ($emails as $email) {
            $patterns[$email] = $weekendSlots;
        }
        return $patterns;
    }

    // ─── Helper: Mon–Fri with given hours ────────────────────

    private function weekdaySlots(array $hours): array
    {
        $slots = [];
        foreach ([self::MON, self::TUE, self::WED, self::THU, self::FRI] as $day) {
            $slots[] = [$day, $hours];
        }
        return $slots;
    }
}
