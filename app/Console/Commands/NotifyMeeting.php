<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\Meeting;
use Illuminate\Console\Command;

/**
 * Publish a notification message to all volunteers subscribed to a meeting's SNS topic.
 *
 * With SNS_FAKE=true the message is recorded in memory and nothing is sent to AWS,
 * making this safe to run locally without credentials to verify the publish flow.
 */
class NotifyMeeting extends Command
{
    protected $signature = 'sns:notify
        {meeting_id : ULID of the meeting to notify}
        {--message= : Custom message text (defaults to a standard reminder)}';

    protected $description = 'Publish a notification to all subscribers of a meeting\'s SNS topic';

    public function handle(SnsTopicServiceInterface $sns): int
    {
        $meetingId = $this->argument('meeting_id');
        $meeting   = Meeting::withoutTrashed()->find($meetingId);

        if (!$meeting) {
            $this->error("Meeting [{$meetingId}] not found.");
            return self::FAILURE;
        }

        $message = $this->option('message')
            ?? "Reminder: H&I meeting coming up — {$meeting->schedule_label}. Check your schedule.";

        $sns->publish($meeting, $message);

        $this->info("Message published to meeting [{$meetingId}].");
        $this->line("  Message : {$message}");
        $this->line("  Topic   : " . ($meeting->fresh()->sns_topic_arn ?? 'created on publish'));

        return self::SUCCESS;
    }
}
