<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123!'),
            'email_verified_at' => now(),
            'roles' => json_encode(['admin', 'coordinator']),
        ]);

        // Coordinator user
        User::create([
            'name' => 'Coordinator',
            'email' => 'coord@example.com',
            'password' => Hash::make('CoordPass123!'),
            'email_verified_at' => now(),
            'roles' => json_encode(['coordinator']),
        ]);

        // John Smith - volunteer
        User::create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => Hash::make('SecurePass123!'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Sarah Johnson - volunteer
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Marcus Williams - volunteer
        User::create([
            'name' => 'Marcus Williams',
            'email' => 'marcus@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Emily Chen - volunteer
        User::create([
            'name' => 'Emily Chen',
            'email' => 'emily@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Robert Davis - volunteer
        User::create([
            'name' => 'Robert Davis',
            'email' => 'robert@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Lisa Martinez - volunteer
        User::create([
            'name' => 'Lisa Martinez',
            'email' => 'lisa@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // James Wilson - volunteer
        User::create([
            'name' => 'James Wilson',
            'email' => 'james@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Amanda Thompson - volunteer
        User::create([
            'name' => 'Amanda Thompson',
            'email' => 'amanda@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Carlos Garcia - volunteer
        User::create([
            'name' => 'Carlos Garcia',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);

        // Patricia Brown - volunteer
        User::create([
            'name' => 'Patricia Brown',
            'email' => 'patricia@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => json_encode(['volunteer']),
        ]);
    }
}
