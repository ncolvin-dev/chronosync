<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\Meeting;
use Illuminate\Console\Command;

/**
 * Create SNS topics for all active meetings and sync their subscriber lists.
 *
 * Run this on the server after deploying to bootstrap topics for existing
 * meetings. Safe to run repeatedly — topic creation is idempotent and
 * subscriptions are only added when missing.
 *
 * With SNS_FAKE=true (the default in .env.testing) no AWS calls are made;
 * the command still prints the table, confirming which volunteers would be
 * subscribed without any credentials required.
 */
class SyncSnsTopics extends Command
{
    protected $signature = 'sns:sync {meeting_id? : ULID of a specific meeting to sync}';

    protected $description = 'Create SNS topics for meetings and sync volunteer subscriptions';

    public function handle(SnsTopicServiceInterface $sns): int
    {
        $meetingId = $this->argument('meeting_id');

        $query = Meeting::withoutTrashed()->where('status', 'active');

        if ($meetingId) {
            $query->where('meeting_id', $meetingId);
        }

        $meetings = $query->with(['facility', 'assignments.volunteer'])->get();

        if ($meetings->isEmpty()) {
            $this->warn('No active meetings found.');
            return self::SUCCESS;
        }

        $rows = [];

        foreach ($meetings as $meeting) {
            $arn = $sns->ensureTopicExists($meeting);
            $sns->syncSubscriptions($meeting);

            $subscriberCount = $meeting->assignments()
                ->whereIn('status', ['pending_confirmation', 'confirmed'])
                ->whereHas('volunteer', fn($q) => $q->where('is_sms_deliverable', true))
                ->count();

            $rows[] = [
                $meeting->meeting_id,
                $meeting->facility?->facility_name ?? '—',
                $meeting->schedule_label,
                $subscriberCount,
                $arn,
            ];
        }

        $this->table(
            ['Meeting ID', 'Facility', 'Schedule', 'Subscribers', 'Topic ARN'],
            $rows
        );

        $this->info("Synced {$meetings->count()} meeting(s).");

        return self::SUCCESS;
    }
}
