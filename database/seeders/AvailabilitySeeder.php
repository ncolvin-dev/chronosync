<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Seed the volunteer availability.
     */
    public function run(): void
    {
        $volunteers = Volunteer::all();

        // Define availability patterns for different volunteer profiles
        $availabilityPatterns = [
            // John Smith - available most times
            [
                'emails' => ['john@example.com'],
                'slots' => $this->getAllSlots(),
            ],
            // Sarah Johnson - limited availability
            [
                'emails' => ['sarah@example.com'],
                'slots' => [
                    ['day' => 'Monday', 'start_time' => '14:00', 'end_time' => '16:00'],
                    ['day' => 'Wednesday', 'start_time' => '19:00', 'end_time' => '21:00'],
                    ['day' => 'Friday', 'start_time' => '17:00', 'end_time' => '19:00'],
                ],
            ],
            // Marcus Williams - evening availability mostly
            [
                'emails' => ['marcus@example.com'],
                'slots' => [
                    ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '11:00'],
                    ['day' => 'Wednesday', 'start_time' => '19:00', 'end_time' => '21:00'],
                    ['day' => 'Thursday', 'start_time' => '18:00', 'end_time' => '20:00'],
                ],
            ],
            // Emily Chen - flexible
            [
                'emails' => ['emily@example.com'],
                'slots' => $this->getAllSlots(),
            ],
            // Robert Davis - morning preference
            [
                'emails' => ['robert@example.com'],
                'slots' => [
                    ['day' => 'Monday', 'start_time' => '14:00', 'end_time' => '16:00'],
                    ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '11:00'],
                    ['day' => 'Friday', 'start_time' => '14:00', 'end_time' => '16:00'],
                ],
            ],
            // Lisa Martinez - weekday only
            [
                'emails' => ['lisa@example.com'],
                'slots' => [
                    ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '11:00'],
                    ['day' => 'Thursday', 'start_time' => '18:00', 'end_time' => '20:00'],
                    ['day' => 'Friday', 'start_time' => '17:00', 'end_time' => '19:00'],
                ],
            ],
            // James Wilson - very flexible
            [
                'emails' => ['james@example.com'],
                'slots' => $this->getAllSlots(),
            ],
            // Amanda Thompson - evening focused
            [
                'emails' => ['amanda@example.com'],
                'slots' => [
                    ['day' => 'Monday', 'start_time' => '14:00', 'end_time' => '16:00'],
                    ['day' => 'Wednesday', 'start_time' => '19:00', 'end_time' => '21:00'],
                    ['day' => 'Thursday', 'start_time' => '18:00', 'end_time' => '20:00'],
                ],
            ],
            // Carlos Garcia - all slots
            [
                'emails' => ['carlos@example.com'],
                'slots' => $this->getAllSlots(),
            ],
            // Patricia Brown - specific times
            [
                'emails' => ['patricia@example.com'],
                'slots' => [
                    ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '11:00'],
                    ['day' => 'Wednesday', 'start_time' => '19:00', 'end_time' => '21:00'],
                    ['day' => 'Friday', 'start_time' => '17:00', 'end_time' => '19:00'],
                ],
            ],
        ];

        foreach ($availabilityPatterns as $pattern) {
            foreach ($pattern['emails'] as $email) {
                $volunteer = Volunteer::whereHas('user', function ($query) use ($email) {
                    $query->where('email', $email);
                })->first();

                if ($volunteer) {
                    foreach ($pattern['slots'] as $slot) {
                        Availability::create([
                            'volunteer_id' => $volunteer->id,
                            'day_of_week' => $slot['day'],
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ]);
                    }
                }
            }
        }
    }

    private function getAllSlots(): array
    {
        return [
            ['day' => 'Monday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['day' => 'Monday', 'start_time' => '17:00', 'end_time' => '21:00'],
            ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['day' => 'Tuesday', 'start_time' => '17:00', 'end_time' => '21:00'],
            ['day' => 'Wednesday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['day' => 'Wednesday', 'start_time' => '17:00', 'end_time' => '21:00'],
            ['day' => 'Thursday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['day' => 'Thursday', 'start_time' => '17:00', 'end_time' => '21:00'],
            ['day' => 'Friday', 'start_time' => '09:00', 'end_time' => '17:00'],
            ['day' => 'Friday', 'start_time' => '17:00', 'end_time' => '21:00'],
        ];
    }
}
