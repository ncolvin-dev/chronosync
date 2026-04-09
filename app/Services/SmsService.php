<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MeetingAssignment;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

/**
 * Handles sending SMS messages to volunteers through the configured provider.
 *
 * Public methods map to message types (confirmation, reminder, cancellation).
 * Each one builds the message text and delegates to the private send pipeline.
 * Provider selection, logging, and error handling are centralised in sendSms()
 * so none of that logic repeats across message types.
 */
class SmsService
{
    /**
     * Send a confirmation request to the assigned volunteer.
     *
     * The volunteer is asked to reply YES or NO so the coordinator knows
     * whether to find a replacement.
     */
    public function sendConfirmationRequest(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting   = $assignment->meeting;

        $message = "Hi {$volunteer->user->first_name}, confirm your H&I meeting on "
            . "{$meeting->date_scheduled->format('M d, H:i')}? Reply YES or NO.";

        return $this->sendSms($volunteer->phone, $message, $assignment->meeting_assignment_id, 'confirmation_request');
    }

    /**
     * Send a day-of reminder to the assigned volunteer.
     *
     * Includes the facility name so the volunteer can look up the address
     * without calling the coordinator.
     */
    public function sendReminder(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting   = $assignment->meeting;

        $message = "Reminder: H&I meeting {$meeting->date_scheduled->format('M d, H:i')} "
            . "at {$meeting->facility->name}. Reply if questions.";

        return $this->sendSms($volunteer->phone, $message, $assignment->meeting_assignment_id, 'reminder');
    }

    /**
     * Notify the volunteer that their assignment has been cancelled.
     *
     * Sent when a coordinator removes the volunteer from an occurrence so they
     * are not waiting at a facility unnecessarily.
     */
    public function sendCancellation(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting   = $assignment->meeting;

        $message = "Your H&I meeting on {$meeting->date_scheduled->format('M d, H:i')} "
            . "has been cancelled. Thank you.";

        return $this->sendSms($volunteer->phone, $message, $assignment->meeting_assignment_id, 'cancellation');
    }

    /**
     * Re-send a previously failed message using the details stored in the log.
     *
     * Used by the admin retry UI to re-attempt without recreating the log record.
     */
    public function retrySend(SmsLog $log): bool
    {
        return $this->sendSms($log->to_phone, $log->message, $log->assignment_id, $log->sms_type);
    }

    // -------------------------------------------------------------------------
    // Private — send pipeline shared by all message types
    // -------------------------------------------------------------------------

    /**
     * Route the message through the configured provider and record the attempt.
     *
     * Checks the feature flag first so SMS can be globally disabled without
     * removing provider credentials. Every attempt — success or failure — is
     * written to sms_logs for audit and retry purposes.
     *
     * @param string      $phone        Recipient phone number in E.164 format.
     * @param string      $message      Plain-text message content.
     * @param string|null $assignmentId ULID of the related MeetingAssignment, or null for retries.
     * @param string      $type         Message type label stored in sms_logs.
     */
    private function sendSms(string $phone, string $message, ?string $assignmentId, string $type): bool
    {
        // Guard: respect the feature flag so SMS can be disabled in any environment.
        if (!config('chronosync.sms.enabled')) {
            return false;
        }

        try {
            $provider = config('chronosync.sms.provider');

            $result = match ($provider) {
                'twilio'  => $this->sendViaTwilio($phone, $message),
                'aws_sns' => $this->sendViaAwsSns($phone, $message),
                'custom'  => $this->sendViaCustom($phone, $message),
                default   => false,
            };

            // Record every attempt so coordinators can see delivery status
            // and the retry system can re-queue failures automatically.
            SmsLog::create([
                'assignment_id' => $assignmentId,
                'to_phone'      => $phone,
                'message'       => $message,
                'sms_type'      => $type,
                'is_sent'       => $result,
                'sent_at'       => $result ? now() : null,
            ]);

            return $result;
        } catch (\Throwable $e) {
            // Log as a structured array so log aggregators can filter by phone or type.
            Log::error('SMS send failed', [
                'phone' => $phone,
                'type'  => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deliver the message via the Twilio REST API.
     *
     * Credentials are stored in config/services.php under the 'twilio' key,
     * which is the Laravel convention for third-party service secrets.
     * The 'body' key carries the message text — omitting it sends a blank SMS.
     */
    private function sendViaTwilio(string $phone, string $message): bool
    {
        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $client->messages->create($phone, [
                'from' => config('services.twilio.from_number'),
                'body' => $message,
            ]);

            return true;
        } catch (\Twilio\Exceptions\TwilioException $e) {
            Log::error('Twilio delivery failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deliver the message via AWS Simple Notification Service.
     *
     * SNS handles phone number formatting and carrier routing automatically.
     * Region is read from the shared AWS config in config/services.php.
     */
    private function sendViaAwsSns(string $phone, string $message): bool
    {
        try {
            $sns = \Aws\Sns\SnsClient::factory([
                'region' => config('services.ses.region'),
            ]);

            $sns->publish([
                'Message'     => $message,
                'PhoneNumber' => $phone,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('AWS SNS delivery failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deliver the message via a custom HTTP SMS gateway.
     *
     * The endpoint and API key are configured in config/chronosync.php.
     * The gateway is expected to return an HTTP 2xx status on success.
     */
    private function sendViaCustom(string $phone, string $message): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::post(config('chronosync.sms.custom_endpoint'), [
                'phone'   => $phone,
                'message' => $message,
                'api_key' => config('chronosync.sms.api_key'),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Custom SMS gateway delivery failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
