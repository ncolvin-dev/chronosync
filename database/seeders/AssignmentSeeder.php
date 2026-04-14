<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $volunteers = Volunteer::withoutTrashed()->inRandomOrder()->get();

        if ($volunteers->isEmpty()) {
            $this->command->warn('No volunteers found — skipping AssignmentSeeder.');
            return;
        }

        $now = Carbon::now();

        // Support both meeting formats:
        // - Recurring pattern: day_of_week + week_of_month + meeting_time
        // - One-off: scheduled_time
        $meetings = Meeting::withoutTrashed()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'inactive')
            ->where(function ($q) use ($now) {
                // Future recurring meetings
                $q->whereNotNull('day_of_week')
                  // Future one-off meetings
                  ->orWhere('scheduled_time', '>=', $now->toDateString());
            })
            ->get();

        if ($meetings->isEmpty()) {
            $this->command->warn('No upcoming meetings found — skipping AssignmentSeeder.');
            return;
        }

        $created  = 0;
        $volCount = $volunteers->count();
        $volIndex = 0;

        foreach ($meetings as $meeting) {
            // Resolve the assignment date
            if ($meeting->day_of_week !== null && $meeting->week_of_month !== null && $meeting->meeting_time) {
                $occurrenceDate = $meeting->occurrenceInMonth($now->year, $now->month)
                    ?? $meeting->occurrenceInMonth($now->copy()->addMonth()->year, $now->copy()->addMonth()->month);

                if (!$occurrenceDate || $occurrenceDate->lt($now->startOfDay())) {
                    $next = $now->copy()->addMonth();
                    $occurrenceDate = $meeting->occurrenceInMonth($next->year, $next->month);
                }
            } elseif ($meeting->scheduled_time) {
                $occurrenceDate = Carbon::parse($meeting->scheduled_time);
                // Skip if already past
                if ($occurrenceDate->lt($now->startOfDay())) {
                    continue;
                }
            } else {
                continue;
            }

            if (!$occurrenceDate) {
                continue;
            }

            $dateStr = $occurrenceDate->toDateString();

            // Skip ~25% of meetings so there's a mix of assigned / unassigned
            if (rand(1, 4) === 1) {
                continue;
            }

            $volunteersNeeded = $meeting->volunteers_needed ?? 1;
            $spotsToFill      = rand(1, $volunteersNeeded);

            for ($i = 0; $i < $spotsToFill; $i++) {
                $volunteer = $volunteers[$volIndex % $volCount];
                $volIndex++;

                $exists = MeetingAssignment::where('meeting_id', $meeting->meeting_id)
                    ->where('volunteer_id', $volunteer->volunteer_id)
                    ->where('assignment_date', $dateStr)
                    ->whereIn('status', ['confirmed', 'pending_confirmation'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Mix of confirmed and pending so the page shows both badge colours
                $status = rand(0, 2) > 0 ? 'confirmed' : 'pending_confirmation';

                MeetingAssignment::create([
                    'meeting_id'      => $meeting->meeting_id,
                    'volunteer_id'    => $volunteer->volunteer_id,
                    'assignment_date' => $dateStr,
                    'status'          => $status,
                    'assignment_type' => 'manual',
                    'confirmed_at'    => $status === 'confirmed' ? now() : null,
                ]);

                $created++;
            }
        }

        $this->command->info("AssignmentSeeder: created {$created} assignment(s) across {$meetings->count()} meetings.");
    }
}
