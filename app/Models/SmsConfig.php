<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SmsConfig extends Model
{
    use HasUlids;

    protected $table      = 'sms_config';
    protected $primaryKey = 'config_id';

    protected $fillable = [
        'hours_before_meeting',
        'daytime_start',
        'daytime_end',
        'buffer_minutes',
        'is_active',
        'message_template',
    ];

    protected $casts = [
        'hours_before_meeting' => 'integer',
        'daytime_start'        => 'integer',
        'daytime_end'          => 'integer',
        'buffer_minutes'       => 'integer',
        'is_active'            => 'boolean',
    ];

    /**
     * Return the single config row, creating it with defaults if it doesn't exist.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'hours_before_meeting' => 24,
            'daytime_start'        => 8,
            'daytime_end'          => 20,
            'buffer_minutes'       => 60,
            'is_active'            => true,
            'message_template'     => 'Reminder: H&I meeting at {facility_name} on {meeting_date} at {meeting_time}. Thank you!',
        ]);
    }

    /**
     * Expose daytime_start as HH:00 for <input type="time"> in the view.
     */
    public function getWindowStartAttribute(): string
    {
        return sprintf('%02d:00', $this->daytime_start);
    }

    /**
     * Expose daytime_end as HH:00 for <input type="time"> in the view.
     */
    public function getWindowEndAttribute(): string
    {
        return sprintf('%02d:00', $this->daytime_end);
    }
}
