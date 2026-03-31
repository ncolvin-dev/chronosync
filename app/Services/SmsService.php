<?php

namespace App\Services;

use App\Models\MeetingAssignment;
use App\Models\SmsLog;

class SmsService
{
    /**
     * Send confirmation request SMS.
     */
    public function sendConfirmationRequest(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting = $assignment->meeting;

        $message = "Hi {$volunteer->user->first_name}, confirm your H&I meeting on {$meeting->date_scheduled->format('M d, H:i')}? Reply YES or NO.";

        return $this->sendSms($volunteer->phone, $message, $assignment->id, 'confirmation_request');
    }

    /**
     * Send reminder SMS.
     */
    public function sendReminder(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting = $assignment->meeting;

        $message = "Reminder: H&I meeting {$meeting->date_scheduled->format('M d, H:i')} at {$meeting->facility->name}. Reply if questions.";

        return $this->sendSms($volunteer->phone, $message, $assignment->id, 'reminder');
    }

    /**
     * Send cancellation SMS.
     */
    public function sendCancellation(MeetingAssignment $assignment): bool
    {
        $volunteer = $assignment->volunteer;
        $meeting = $assignment->meeting;

        $message = "Your H&I meeting on {$meeting->date_scheduled->format('M d, H:i')} has been cancelled. Thank you.";

        return $this->sendSms($volunteer->phone, $message, $assignment->id, 'cancellation');
    }

    /**
     * Retry failed send.
     */
    public function retrySend(SmsLog $log): bool
    {
        return $this->sendSms($log->to_phone, $log->message, $log->assignment_id, $log->sms_type);
    }

    /**
     * Send SMS via configured provider.
     */
    private function sendSms(string $phone, string $message, ?int $assignmentId, string $type): bool
    {
        if (!config('services.sms.enabled')) {
            return false;
        }

        try {
            $provider = config('services.sms.provider');

            $result = match ($provider) {
                'twilio' => $this->sendViaTwilio($phone, $message),
                'aws_sns' => $this->sendViaAwsSns($phone, $message),
                'custom' => $this->sendViaCustom($phone, $message),
                default => false,
            };

            // Log the SMS send attempt
            SmsLog::create([
                'assignment_id' => $assignmentId,
                'to_phone' => $phone,
                'message' => $message,
                'sms_type' => $type,
                'is_sent' => $result,
                'sent_at' => $result ? now() : null,
            ]);

            return $result;
        } catch (\Exception $e) {
            \Log::error('SMS Send Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Twilio.
     */
    private function sendViaTwilio(string $phone, string $message): bool
    {
        try {
            $client = new \Twilio\Rest\Client(
                config('services.sms.account_sid'),
                config('services.sms.auth_token')
            );

            $client->messages->create(
                $phone,
                ['from' => config('services.sms.from_number')]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Twilio Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via AWS SNS.
     */
    private function sendViaAwsSns(string $phone, string $message): bool
    {
        try {
            $sns = \Aws\Sns\SnsClient::factory(['region' => config('services.sms.region')]);

            $sns->publish([
                'Message' => $message,
                'PhoneNumber' => $phone,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('AWS SNS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via custom provider.
     */
    private function sendViaCustom(string $phone, string $message): bool
    {
        try {
            $response = \Http::post(config('services.sms.custom_endpoint'), [
                'phone' => $phone,
                'message' => $message,
                'api_key' => config('services.sms.api_key'),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Custom SMS Error: ' . $e->getMessage());
            return false;
        }
    }
}
