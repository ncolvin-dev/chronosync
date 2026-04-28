<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     *
     * Staff accounts (non-volunteer):
     *   admin@example.com         — admin + coordinator
     *   coord@example.com         — coordinator
     *   kevin.harris@example.com  — coordinator
     *   nicole.clark@example.com  — coordinator
     *   brian.lewis@example.com   — coordinator
     *   rachel.walker@example.com — coordinator
     *
     * Volunteer accounts (30 total across 4 clean-time groups):
     *   Group 1 — 5 volunteers  (3 days – 6 months clean)
     *   Group 2 — 5 volunteers  (6 months – 2 years clean)
     *   Group 3 — 10 volunteers (2 – 5 years clean)
     *   Group 4 — 10 volunteers (5 – 30 years clean)
     *
     * Dual-role (volunteer + coordinator):
     *   steven.young@example.com, stephanie.king@example.com  (Group 3)
     */
    public function run(): void
    {
        // ── Admin ────────────────────────────────────────────
        User::create([
            'email'             => 'admin@example.com',
            'password_hash'     => Hash::make('AdminPass123!'),
            'email_verified_at' => now(),
            'roles'             => ['admin', 'coordinator'],
        ]);

        // ── Coordinators ─────────────────────────────────────
        foreach ([
            ['coord@example.com',         'CoordPass123!'],
            ['kevin.harris@example.com',  'password123'],
            ['nicole.clark@example.com',  'password123'],
            ['brian.lewis@example.com',   'password123'],
            ['rachel.walker@example.com', 'password123'],
        ] as [$email, $pass]) {
            User::create([
                'email'             => $email,
                'password_hash'     => Hash::make($pass),
                'email_verified_at' => now(),
                'roles'             => ['coordinator'],
            ]);
        }

        // ── Group 1 — 5 volunteers (3 days – 6 months clean) ─
        foreach ([
            'tyler.bennett@example.com',
            'kayla.foster@example.com',
            'devon.reyes@example.com',
            'brittney.cole@example.com',
            'anthony.price@example.com',
        ] as $email) {
            User::create([
                'email'             => $email,
                'password_hash'     => Hash::make('password123'),
                'email_verified_at' => now(),
                'roles'             => ['volunteer'],
            ]);
        }

        // ── Group 2 — 5 volunteers (6 months – 2 years clean) ─
        foreach ([
            'david.anderson@example.com',
            'jennifer.white@example.com',
            'christopher.miller@example.com',
            'michelle.taylor@example.com',
            'daniel.thomas@example.com',
        ] as $email) {
            User::create([
                'email'             => $email,
                'password_hash'     => Hash::make('password123'),
                'email_verified_at' => now(),
                'roles'             => ['volunteer'],
            ]);
        }

        // ── Group 3 — 10 volunteers (2 – 5 years clean) ──────
        $group3Emails = [
            'jessica.moore@example.com',
            'matthew.jackson@example.com',
            'ashley.martin@example.com',
            'ryan.lee@example.com',
            'lauren.perez@example.com',
            'steven.young@example.com',    // dual-role
            'stephanie.king@example.com',  // dual-role
            'marcus.webb@example.com',
            'tanya.owens@example.com',
            'raymond.cruz@example.com',
        ];
        $dualRole = ['steven.young@example.com', 'stephanie.king@example.com'];

        foreach ($group3Emails as $email) {
            User::create([
                'email'             => $email,
                'password_hash'     => Hash::make('password123'),
                'email_verified_at' => now(),
                'roles'             => in_array($email, $dualRole)
                                        ? ['volunteer', 'coordinator']
                                        : ['volunteer'],
            ]);
        }

        // ── Group 4 — 10 volunteers (5 – 30 years clean) ─────
        foreach ([
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
        ] as $email) {
            User::create([
                'email'             => $email,
                'password_hash'     => Hash::make('password123'),
                'email_verified_at' => now(),
                'roles'             => ['volunteer'],
            ]);
        }
    }
}
