<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\SmsLog;
use App\Models\AuditLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Configure SMS settings.
     */
    public function configure(Request $request)
    {
        $this->authorizeAdmin();

        if ($request->method === 'GET') {
            $settings = [
                'sms_provider' => config('services.sms.provider'),
                'sms_enabled' => config('services.sms.enabled'),
                'reminder_hours_before' => config('services.sms.reminder_hours_before'),
                'api_key' => '****' . substr(config('services.sms.api_key'), -4),
            ];

            return view('coordinator.sms-config', compact('settings'));
        }

        $validated = $request->validate([
            'sms_provider' => 'required|in:twilio,aws_sns,custom',
            'sms_enabled' => 'boolean',
            'reminder_hours_before' => 'integer|min:1|max:72',
            'api_key' => 'nullable|string|min:10',
        ]);

        // Update environment variables or config
        $this->updateSmsConfig($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'sms_settings_updated',
            'model_type' => 'SmsConfiguration',
            'model_id' => 1,
            'changes' => [
                'provider' => $validated['sms_provider'],
                'enabled' => $validated['sms_enabled'],
                'reminder_hours' => $validated['reminder_hours_before'],
            ],
        ]);

        return back()->with('success', 'SMS settings updated successfully.');
    }

    /**
     * Manually trigger reminder SMS.
     */
    public function sendReminder(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $assignment = $meeting->assignments()
            ->where('status', 'confirmed')
            ->first();

        if (!$assignment) {
            return back()->with('error', 'No volunteer assigned to this meeting.');
        }

        return DB::transaction(function () use ($assignment) {
            $result = $this->smsService->sendReminder($assignment);

            if ($result) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'reminder_sms_sent',
                    'model_type' => MeetingAssignment::class,
                    'model_id' => $assignment->id,
                    'changes' => ['sent_at' => now()],
                ]);

                return back()->with('success', 'Reminder SMS sent successfully.');
            }

            return back()->with('error', 'Failed to send reminder SMS.');
        });
    }

    /**
     * Handle incoming SMS responses (webhook).
     */
    public function handleResponse(Request $request)
    {
        // Validate webhook authenticity
        $this->validateWebhookSignature($request);

        $phoneNumber = $request->input('from');
        $responseText = trim(strtoupper($request->input('body')));

        $smsLog = SmsLog::where('to_phone', $phoneNumber)
            ->where('sms_type', 'confirmation_request')
            ->latest()
            ->first();

        if (!$smsLog) {
            return response()->json(['success' => false, 'message' => 'No matching SMS record']);
        }

        return DB::transaction(function () use ($smsLog, $responseText) {
            $assignment = MeetingAssignment::find($smsLog->assignment_id);

            if (!$assignment) {
                return response()->json(['success' => false, 'message' => 'Assignment not found']);
            }

            if ($responseText === 'YES' || $responseText === 'Y') {
                $assignment->status = 'confirmed';
                $assignment->confirmed_at = now();

                AuditLog::create([
                    'user_id' => $assignment->volunteer_id,
                    'action' => 'volunteer_confirmed_assignment',
                    'model_type' => MeetingAssignment::class,
                    'model_id' => $assignment->id,
                    'changes' => ['confirmation' => 'yes_via_sms'],
                ]);
            } elseif ($responseText === 'NO' || $responseText === 'N') {
                $assignment->status = 'declined';
                $assignment->declined_at = now();

                AuditLog::create([
                    'user_id' => $assignment->volunteer_id,
                    'action' => 'volunteer_declined_assignment',
                    'model_type' => MeetingAssignment::class,
                    'model_id' => $assignment->id,
                    'changes' => ['confirmation' => 'no_via_sms'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response. Reply YES or NO.',
                ]);
            }

            $assignment->save();

            $smsLog->response_received = true;
            $smsLog->response_text = $responseText;
            $smsLog->response_received_at = now();
            $smsLog->save();

            return response()->json(['success' => true, 'message' => 'Response recorded']);
        });
    }

    /**
     * View SMS audit log.
     */
    public function getLog(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = SmsLog::query();

        // Filter by assignment
        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->assignment_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'sent') {
                $query->where('is_sent', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_sent', false);
            } elseif ($request->status === 'responded') {
                $query->where('response_received', true);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('placeholder.coming-soon', compact('logs'));
    }

    /**
     * Retry failed SMS sends.
     */
    public function retryFailed(Request $request)
    {
        $this->authorizeAdmin();

        $failedLogs = SmsLog::where('is_sent', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $successCount = 0;
        $failureCount = 0;

        return DB::transaction(function () use ($failedLogs, &$successCount, &$failureCount) {
            foreach ($failedLogs as $log) {
                $result = $this->smsService->retrySend($log);

                if ($result) {
                    $log->is_sent = true;
                    $log->sent_at = now();
                    $log->save();
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'sms_retry_batch',
                'model_type' => 'SmsLog',
                'model_id' => 0,
                'changes' => [
                    'retried_count' => count($failedLogs),
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                ],
            ]);

            return redirect()->route('sms.log')
                ->with('success', "Retry complete: {$successCount} sent, {$failureCount} failed.");
        });
    }

    /**
     * Validate webhook signature for SMS provider.
     */
    private function validateWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Webhook-Signature');
        $expectedSignature = hash_hmac(
            'sha256',
            $request->getContent(),
            config('services.sms.webhook_secret')
        );

        if ($signature !== $expectedSignature) {
            abort(401, 'Invalid webhook signature');
        }

        return true;
    }

    /**
     * Update SMS configuration.
     */
    private function updateSmsConfig(array $settings): void
    {
        // Implementation would write to .env or config file
        // For now, this is a placeholder for the actual implementation
    }

    /**
     * Authorize coordinator or admin.
     */
    private function authorizeCoordinatorOrAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }

    /**
     * Authorize admin only.
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('admin', $roles)) {
            abort(403);
        }
    }
}
