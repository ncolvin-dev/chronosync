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
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('AdminPass123!'),
            'email_verified_at' => now(),
            'roles' => ['admin', 'coordinator'],
        ]);

        // Coordinator user
        User::create([
            'email' => 'coord@example.com',
            'password_hash' => Hash::make('CoordPass123!'),
            'email_verified_at' => now(),
            'roles' => ['coordinator'],
        ]);

        // John Smith - volunteer
        User::create([
            'email' => 'john@example.com',
            'password_hash' => Hash::make('SecurePass123!'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Sarah Johnson - volunteer
        User::create([
            'email' => 'sarah@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Marcus Williams - volunteer
        User::create([
            'email' => 'marcus@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Emily Chen - volunteer
        User::create([
            'email' => 'emily@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Robert Davis - volunteer
        User::create([
            'email' => 'robert@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Lisa Martinez - volunteer
        User::create([
            'email' => 'lisa@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // James Wilson - volunteer
        User::create([
            'email' => 'james@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Amanda Thompson - volunteer
        User::create([
            'email' => 'amanda@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Carlos Garcia - volunteer
        User::create([
            'email' => 'carlos@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Patricia Brown - volunteer
        User::create([
            'email' => 'patricia@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);
    }
}
