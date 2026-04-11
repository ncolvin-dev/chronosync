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

        // ===== ADDITIONAL VOLUNTEERS (10) =====

        // David Anderson - volunteer
        User::create([
            'email' => 'david.anderson@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Jennifer White - volunteer
        User::create([
            'email' => 'jennifer.white@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Christopher Miller - volunteer
        User::create([
            'email' => 'christopher.miller@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Michelle Taylor - volunteer
        User::create([
            'email' => 'michelle.taylor@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Daniel Thomas - volunteer
        User::create([
            'email' => 'daniel.thomas@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Jessica Moore - volunteer
        User::create([
            'email' => 'jessica.moore@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Matthew Jackson - volunteer
        User::create([
            'email' => 'matthew.jackson@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Ashley Martin - volunteer
        User::create([
            'email' => 'ashley.martin@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Ryan Lee - volunteer
        User::create([
            'email' => 'ryan.lee@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // Lauren Perez - volunteer
        User::create([
            'email' => 'lauren.perez@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer'],
        ]);

        // ===== ADDITIONAL COORDINATORS (4) =====

        // Kevin Harris - coordinator
        User::create([
            'email' => 'kevin.harris@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['coordinator'],
        ]);

        // Nicole Clark - coordinator
        User::create([
            'email' => 'nicole.clark@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['coordinator'],
        ]);

        // Brian Lewis - coordinator
        User::create([
            'email' => 'brian.lewis@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['coordinator'],
        ]);

        // Rachel Walker - coordinator
        User::create([
            'email' => 'rachel.walker@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['coordinator'],
        ]);

        // ===== DUAL-ROLE USERS (Volunteer + Coordinator) (2) =====

        // Steven Young - volunteer + coordinator
        User::create([
            'email' => 'steven.young@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer', 'coordinator'],
        ]);

        // Stephanie King - volunteer + coordinator
        User::create([
            'email' => 'stephanie.king@example.com',
            'password_hash' => Hash::make('password123'),
            'email_verified_at' => now(),
            'roles' => ['volunteer', 'coordinator'],
        ]);
    }
}
