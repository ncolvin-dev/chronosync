<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\Meeting;

/**
 * In-memory stand-in for SnsTopicService.
 *
 * Used automatically in the testing environment (phpunit.xml sets SNS_FAKE=true)
 * and can be enabled locally via SNS_FAKE=true in .env. Records every operation
 * so tests can assert on which topics were created, who got subscribed, and what
 * messages were published — without any AWS credentials or network calls.
 */
class FakeSnsTopicService implements SnsTopicServiceInterface
{
    private array $topics        = [];
    private array $subscriptions = [];
    private array $published     = [];

    public function ensureTopicExists(Meeting $meeting): string
    {
        $id = $meeting->meeting_id;

        if (!isset($this->topics[$id])) {
            $this->topics[$id] = "arn:aws:sns:us-east-1:000000000000:chronosync-meeting-{$id}";
            $meeting->sns_topic_arn = $this->topics[$id];
            $meeting->save();
        }

        return $this->topics[$id];
    }

    public function syncSubscriptions(Meeting $meeting): void
    {
        $this->ensureTopicExists($meeting);

        $id = $meeting->meeting_id;
        $this->subscriptions[$id] = [];

        $assignments = $meeting->assignments()
            ->whereIn('status', ['pending_confirmation', 'confirmed'])
            ->with('volunteer')
            ->get();

        foreach ($assignments as $assignment) {
            $volunteer = $assignment->volunteer;

            if (!$volunteer || !$volunteer->is_sms_deliverable || empty($volunteer->phone)) {
                continue;
            }

            $fakeSubArn = "arn:aws:sns:us-east-1:000000000000:chronosync-meeting-{$id}:{$volunteer->volunteer_id}";
            $this->subscriptions[$id][] = $volunteer->phone;

            $assignment->sns_subscription_arn = $fakeSubArn;
            $assignment->save();
        }
    }

    public function publish(Meeting $meeting, string $message): void
    {
        $this->ensureTopicExists($meeting);

        $id = $meeting->meeting_id;

        if (!isset($this->published[$id])) {
            $this->published[$id] = [];
        }

        $this->published[$id][] = $message;
    }

    // -------------------------------------------------------------------------
    // Inspection helpers for tests
    // -------------------------------------------------------------------------

    public function wasTopicCreated(Meeting $meeting): bool
    {
        return isset($this->topics[$meeting->meeting_id]);
    }

    /** @return string[] Phone numbers currently subscribed to this meeting's topic. */
    public function getSubscriptions(Meeting $meeting): array
    {
        return $this->subscriptions[$meeting->meeting_id] ?? [];
    }

    /** @return string[] Messages published to this meeting's topic. */
    public function getPublishedMessages(Meeting $meeting): array
    {
        return $this->published[$meeting->meeting_id] ?? [];
    }

    public function reset(): void
    {
        $this->topics        = [];
        $this->subscriptions = [];
        $this->published     = [];
    }
}
