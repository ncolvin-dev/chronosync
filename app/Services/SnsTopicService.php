<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\Meeting;
use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Log;

/**
 * Manages SNS topics for recurring meeting slots.
 *
 * One topic is created per Meeting record. Opted-in volunteers with active
 * assignments are subscribed via the 'sms' protocol so a single publish()
 * call reaches all of them simultaneously. The subscription ARN is stored
 * on the MeetingAssignment so it can be cleanly removed if the assignment
 * is cancelled or declined.
 */
class SnsTopicService implements SnsTopicServiceInterface
{
    private SnsClient $client;

    public function __construct()
    {
        $this->client = new SnsClient([
            'version'     => 'latest',
            'region'      => config('services.sns.region', 'us-east-1'),
            'credentials' => [
                'key'    => config('services.sns.key'),
                'secret' => config('services.sns.secret'),
            ],
        ]);
    }

    /**
     * Create the SNS topic for this meeting if it does not already exist.
     *
     * The topic ARN is stored on the meeting so subsequent calls are free
     * (no AWS round-trip). SNS createTopic is idempotent, but we skip the
     * call entirely once we have a stored ARN.
     */
    public function ensureTopicExists(Meeting $meeting): string
    {
        if (!empty($meeting->sns_topic_arn)) {
            return $meeting->sns_topic_arn;
        }

        $prefix    = config('chronosync.sns.topic_prefix', 'chronosync-meeting');
        $topicName = "{$prefix}-{$meeting->meeting_id}";

        $result = $this->client->createTopic(['Name' => $topicName]);
        $arn    = $result->get('TopicArn');

        $meeting->sns_topic_arn = $arn;
        $meeting->save();

        Log::info('SNS topic created', ['meeting_id' => $meeting->meeting_id, 'arn' => $arn]);

        return $arn;
    }

    /**
     * Subscribe all opted-in assigned volunteers to the meeting's topic.
     *
     * Only assignments with status pending_confirmation or confirmed are
     * considered active. For each active, SMS-deliverable volunteer:
     *   - Subscribe their phone to the topic
     *   - Store the subscription ARN on the assignment for later clean removal
     *
     * Assignments that are no longer active (cancelled, declined) have their
     * subscription ARN unsubscribed and cleared.
     */
    public function syncSubscriptions(Meeting $meeting): void
    {
        $topicArn = $this->ensureTopicExists($meeting);

        $allAssignments = $meeting->assignments()->with('volunteer')->get();

        foreach ($allAssignments as $assignment) {
            $volunteer = $assignment->volunteer;

            if (in_array($assignment->status, ['pending_confirmation', 'confirmed'])
                && $volunteer
                && $volunteer->is_sms_deliverable
                && !empty($volunteer->phone)
                && empty($assignment->sns_subscription_arn)
            ) {
                try {
                    $result = $this->client->subscribe([
                        'TopicArn'              => $topicArn,
                        'Protocol'              => 'sms',
                        'Endpoint'              => $volunteer->phone,
                        'ReturnSubscriptionArn' => true,
                    ]);

                    $assignment->sns_subscription_arn = $result->get('SubscriptionArn');
                    $assignment->save();

                    Log::info('SNS subscription added', [
                        'meeting_id'   => $meeting->meeting_id,
                        'volunteer_id' => $volunteer->volunteer_id,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('SNS subscribe failed', [
                        'meeting_id'   => $meeting->meeting_id,
                        'volunteer_id' => $volunteer->volunteer_id ?? null,
                        'error'        => $e->getMessage(),
                    ]);
                }
            }

            if (in_array($assignment->status, ['cancelled', 'declined'])
                && !empty($assignment->sns_subscription_arn)
            ) {
                try {
                    $this->client->unsubscribe([
                        'SubscriptionArn' => $assignment->sns_subscription_arn,
                    ]);

                    $assignment->sns_subscription_arn = null;
                    $assignment->save();
                } catch (\Throwable $e) {
                    Log::warning('SNS unsubscribe failed', [
                        'assignment_id' => $assignment->meeting_assignment_id,
                        'error'         => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Publish a message to the meeting's SNS topic.
     *
     * All subscribed phone numbers receive an SMS. The topic is created
     * first if it does not exist yet.
     */
    public function publish(Meeting $meeting, string $message): void
    {
        $topicArn = $this->ensureTopicExists($meeting);

        $this->client->publish([
            'TopicArn' => $topicArn,
            'Message'  => $message,
        ]);

        Log::info('SNS message published', [
            'meeting_id' => $meeting->meeting_id,
            'message'    => $message,
        ]);
    }
}
