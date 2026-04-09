<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MeetingAssignment;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued job that sends a single SMS message to a volunteer.
 *
 * Placing SMS delivery in a queue means the HTTP response returns immediately,
 * keeping request latency low regardless of provider speed. If the send fails,
 * the job retries automatically with increasing delays before giving up.
 *
 * The $smsType string determines which SmsService method is called, keeping this
 * class responsible only for dispatch logistics — not message content.
 */
class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of attempts before the job is marked as permanently failed.
     * Attempts: 1 immediate + 2 retries.
     */
    public int $tries = 3;

    /**
     * Backoff delays in seconds between retries.
     * Increases with each attempt: 30 seconds, 2 minutes, 5 minutes.
     */
    public array $backoff = [30, 120, 300];

    /**
     * @param MeetingAssignment $assignment The assignment this message is about.
     *                                       SerializesModels re-fetches it fresh when the job runs.
     * @param string $smsType               One of: 'confirmation_request', 'reminder', 'cancellation'.
     */
    public function __construct(
        private readonly MeetingAssignment $assignment,
        private readonly string $smsType
    ) {}

    /**
     * Execute the job.
     *
     * Laravel resolves SmsService from the container automatically.
     * Each SMS type maps to a dedicated method on the service so the service
     * controls message content and this job stays focused on delivery.
     */
    public function handle(SmsService $smsService): void
    {
        match ($this->smsType) {
            'confirmation_request' => $smsService->sendConfirmationRequest($this->assignment),
            'reminder'             => $smsService->sendReminder($this->assignment),
            'cancellation'         => $smsService->sendCancellation($this->assignment),
        };
    }

    /**
     * Handle permanent failure after all retry attempts are exhausted.
     *
     * Logs enough context to locate the assignment and investigate manually.
     * The failed_jobs table will also contain the full exception trace.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SMS job permanently failed after all retries', [
            'assignment_id' => $this->assignment->meeting_assignment_id,
            'sms_type'      => $this->smsType,
            'error'         => $exception->getMessage(),
        ]);
    }
}
