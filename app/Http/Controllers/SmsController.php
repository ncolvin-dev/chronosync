<?php

namespace App\Http\Controllers;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\AuditLog;
use App\Models\Meeting;
use App\Models\SmsConfig;
use App\Models\SmsLog;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    protected SmsService $smsService;
    protected SnsTopicServiceInterface $snsTopicService;

    public function __construct(SmsService $smsService, SnsTopicServiceInterface $snsTopicService)
    {
        $this->smsService      = $smsService;
        $this->snsTopicService = $snsTopicService;
    }

    /**
     * Show the SMS configuration form.
     */
    public function configure()
    {
        $this->authorizeAdmin();

        $config = SmsConfig::current();

        return view('coordinator.sms-config', compact('config'));
    }

    /**
     * Persist SMS configuration to the database.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'hours_before'     => 'required|integer|min:1|max:72',
            'minutes_buffer'   => 'required|integer|min:0|max:120',
            'window_start'     => 'required|date_format:H:i',
            'window_end'       => 'required|date_format:H:i',
            'message_template' => 'required|string|max:320',
        ]);

        $config = SmsConfig::current();

        $config->update([
            'hours_before_meeting' => $validated['hours_before'],
            'buffer_minutes'       => $validated['minutes_buffer'],
            'daytime_start'        => (int) substr($validated['window_start'], 0, 2),
            'daytime_end'          => (int) substr($validated['window_end'], 0, 2),
            'message_template'     => $validated['message_template'],
        ]);

        AuditLog::create([
            'actor_user_id' => auth()->id(),
            'action'        => 'sms_config_updated',
            'entity_type'   => SmsConfig::class,
            'entity_id'     => $config->config_id,
            'change_details' => [
                'hours_before_meeting' => $config->hours_before_meeting,
                'buffer_minutes'       => $config->buffer_minutes,
                'daytime_start'        => $config->daytime_start,
                'daytime_end'          => $config->daytime_end,
            ],
        ]);

        return back()->with('success', 'SMS configuration saved.');
    }

    /**
     * Publish a reminder to all confirmed volunteers on a meeting's SNS topic.
     */
    public function sendReminder(Request $request, Meeting $meeting)
    {
        $this->authorizeCoordinatorOrAdmin();

        $confirmedCount = $meeting->assignments()
            ->where('status', 'confirmed')
            ->count();

        if ($confirmedCount === 0) {
            return back()->with('error', 'No confirmed volunteers on this meeting.');
        }

        $config  = SmsConfig::current();
        $message = $this->buildReminderMessage($meeting, $config->message_template);

        $this->snsTopicService->publish($meeting, $message);

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'reminder_sms_published',
            'entity_type'    => Meeting::class,
            'entity_id'      => $meeting->meeting_id,
            'change_details' => ['sent_at' => now(), 'message' => $message],
        ]);

        return back()->with('success', 'Reminder sent to all confirmed volunteers.');
    }

    /**
     * View SMS audit log.
     */
    public function getLog(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = SmsLog::query();

        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->assignment_id);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'sent'      => $query->where('is_sent', true),
                'failed'    => $query->where('is_sent', false),
                'responded' => $query->where('response_received', true),
                default     => null,
            };
        }

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

        $failedLogs   = SmsLog::where('is_sent', false)
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
                'actor_user_id'  => auth()->id(),
                'action'         => 'sms_retry_batch',
                'entity_type'    => 'SmsLog',
                'change_details' => [
                    'retried_count' => count($failedLogs),
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                ],
            ]);

            return redirect()->route('sms.log')
                ->with('success', "Retry complete: {$successCount} sent, {$failureCount} failed.");
        });
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildReminderMessage(Meeting $meeting, string $template): string
    {
        $next = $meeting->nextOccurrence();

        $date = $next?->format('M j, Y')
            ?? ($meeting->scheduled_time ? Carbon::parse($meeting->scheduled_time)->format('M j, Y') : '—');

        $time = $next?->format('g:i A')
            ?? ($meeting->meeting_time ? Carbon::parse($meeting->meeting_time)->format('g:i A') : '—');

        return str_replace(
            ['{facility_name}', '{meeting_date}', '{meeting_time}', '{facility_address}'],
            [
                $meeting->facility->facility_name ?? '—',
                $date,
                $time,
                $meeting->facility->address ?? '—',
            ],
            $template
        );
    }

}
