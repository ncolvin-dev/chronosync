<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SmsLog Model
 *
 * Records all SMS messages sent to volunteers for meetings and responses.
 *
 * @property string $sms_id
 * @property string|null $meeting_id
 * @property string $volunteer_id
 * @property string $message_type
 * @property string $content
 * @property \Carbon\Carbon $sent_time
 * @property string|null $response
 * @property \Carbon\Carbon|null $response_time
 * @property string $status
 * @property int $retry_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SmsLog extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'sms_id';

    protected $fillable = [
        'meeting_id',
        'volunteer_id',
        'message_type',
        'content',
        'sent_time',
        'response',
        'response_time',
        'status',
        'retry_count',
    ];

    protected $casts = [
        'sent_time' => 'datetime',
        'response_time' => 'datetime',
    ];

    /**
     * Check if SMS was delivered.
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if SMS failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if SMS is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if SMS has a response.
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * Check if response was YES.
     *
     * @return bool
     */
    public function isYesResponse(): bool
    {
        return $this->response === 'YES';
    }

    /**
     * Check if response was NO.
     *
     * @return bool
     */
    public function isNoResponse(): bool
    {
        return $this->response === 'NO';
    }

    /**
     * Check if SMS is a reminder.
     *
     * @return bool
     */
    public function isReminder(): bool
    {
        return $this->message_type === 'reminder';
    }

    /**
     * Check if SMS is a confirmation request.
     *
     * @return bool
     */
    public function isConfirmation(): bool
    {
        return $this->message_type === 'confirmation';
    }

    /**
     * Check if SMS is a replacement request.
     *
     * @return bool
     */
    public function isReplacement(): bool
    {
        return $this->message_type === 'replacement';
    }

    /**
     * Check if SMS is a cancellation notice.
     *
     * @return bool
     */
    public function isCancellation(): bool
    {
        return $this->message_type === 'cancellation';
    }

    /**
     * Get time since sent.
     *
     * @return string
     */
    public function getTimeSinceSentAttribute(): string
    {
        return $this->sent_time->diffForHumans();
    }

    /**
     * Scope: Get delivered messages.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope: Get failed messages.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Get pending messages.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get messages with responses.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithResponse(Builder $query): Builder
    {
        return $query->whereNotNull('response');
    }

    /**
     * Scope: Get messages without responses.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutResponse(Builder $query): Builder
    {
        return $query->whereNull('response');
    }

    /**
     * Scope: Get YES responses.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeYesResponse(Builder $query): Builder
    {
        return $query->where('response', 'YES');
    }

    /**
     * Scope: Get NO responses.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNoResponse(Builder $query): Builder
    {
        return $query->where('response', 'NO');
    }

    /**
     * Scope: Get reminders.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeReminders(Builder $query): Builder
    {
        return $query->where('message_type', 'reminder');
    }

    /**
     * Scope: Get confirmation requests.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeConfirmations(Builder $query): Builder
    {
        return $query->where('message_type', 'confirmation');
    }

    /**
     * Scope: Get replacement requests.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeReplacements(Builder $query): Builder
    {
        return $query->where('message_type', 'replacement');
    }

    /**
     * Scope: Get cancellation notices.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCancellations(Builder $query): Builder
    {
        return $query->where('message_type', 'cancellation');
    }

    /**
     * Scope: Get messages for a volunteer.
     *
     * @param Builder $query
     * @param string $volunteerId
     * @return Builder
     */
    public function scopeForVolunteer(Builder $query, string $volunteerId): Builder
    {
        return $query->where('volunteer_id', $volunteerId);
    }

    /**
     * Scope: Get messages for a meeting.
     *
     * @param Builder $query
     * @param string $meetingId
     * @return Builder
     */
    public function scopeForMeeting(Builder $query, string $meetingId): Builder
    {
        return $query->where('meeting_id', $meetingId);
    }

    /**
     * Scope: Get messages sent in a date range.
     *
     * @param Builder $query
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return Builder
     */
    public function scopeSentBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('sent_time', [$startDate, $endDate]);
    }

    /**
     * Relationship: Meeting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id', 'meeting_id');
    }

    /**
     * Relationship: Volunteer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id', 'volunteer_id');
    }
}
