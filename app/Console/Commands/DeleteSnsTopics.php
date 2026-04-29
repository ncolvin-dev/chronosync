<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Meeting;
use Aws\Sns\SnsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Deletes SNS topics for all meetings that have a stored topic ARN,
 * then clears the ARN from the database.
 *
 * Run this before reseeding to avoid orphaned topics accumulating in AWS.
 */
class DeleteSnsTopics extends Command
{
    protected $signature = 'sns:delete-topics';

    protected $description = 'Delete all SNS topics stored in the meetings table and clear their ARNs';

    public function handle(): int
    {
        $meetings = Meeting::withoutTrashed()
            ->whereNotNull('sns_topic_arn')
            ->get();

        if ($meetings->isEmpty()) {
            $this->info('No topics found to delete.');
            return self::SUCCESS;
        }

        $client = new SnsClient([
            'version'     => 'latest',
            'region'      => config('services.sns.region', 'us-east-1'),
            'credentials' => [
                'key'    => config('services.sns.key'),
                'secret' => config('services.sns.secret'),
            ],
        ]);

        $deleted = 0;
        $failed  = 0;

        foreach ($meetings as $meeting) {
            try {
                $client->deleteTopic(['TopicArn' => $meeting->sns_topic_arn]);

                $meeting->sns_topic_arn = null;
                $meeting->save();

                $this->line("  Deleted: {$meeting->sns_topic_arn}");
                $deleted++;
            } catch (\Throwable $e) {
                $this->warn("  Failed [{$meeting->meeting_id}]: {$e->getMessage()}");
                Log::error('SNS topic deletion failed', [
                    'meeting_id' => $meeting->meeting_id,
                    'arn'        => $meeting->sns_topic_arn,
                    'error'      => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info("Done. Deleted: {$deleted}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
