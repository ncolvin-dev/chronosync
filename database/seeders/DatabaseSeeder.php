<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            VolunteerSeeder::class,
            FacilitySeeder::class,
            CredentialSeeder::class,
            MeetingSeeder::class,
            AvailabilitySeeder::class,
        ]);
    }
}
