<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ChronoSync Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the ChronoSync volunteer
    | scheduling and management system.
    |
    */

    // SMS Notification Settings
    'sms' => [
        'enabled' => env('SMS_ENABLED', true),
        'provider' => env('SMS_PROVIDER', 'aws'), // 'aws' or 'twilio'
        'hours_before_meeting' => env('SMS_HOURS_BEFORE', 24),
        'daytime_start' => env('SMS_DAYTIME_START', '09:00'),
        'daytime_end' => env('SMS_DAYTIME_END', '21:00'),
        'buffer_minutes' => env('SMS_BUFFER_MINUTES', 60),
    ],

    // Matching Algorithm Settings
    'matching' => [
        'enabled' => true,
        'weights' => [
            'availability_match' => 0.30,
            'credential_match' => 0.25,
            'previous_assignments' => 0.20,
            'geographic_proximity' => 0.15,
            'volunteer_preference' => 0.10,
        ],
        'min_buffer_minutes' => env('MATCH_BUFFER_MINUTES', 60),
        'max_volunteers_per_meeting' => env('MAX_VOLUNTEERS_PER_MEETING', 3),
    ],

    // Password Security Settings
    'password' => [
        'min_length' => 10,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'special_chars' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
    ],

    // Session Settings
    'session' => [
        'timeout_minutes' => env('SESSION_TIMEOUT', 120),
        'inactivity_warning_minutes' => env('INACTIVITY_WARNING', 15),
    ],

    // Credential Settings
    'credentials' => [
        'background_check' => [
            'name' => 'Background Check',
            'validity_years' => 3,
            'renewal_notice_days' => 30,
        ],
        'tb_test' => [
            'name' => 'TB Test',
            'validity_years' => 1,
            'renewal_notice_days' => 14,
        ],
        'reference_check' => [
            'name' => 'Reference Check',
            'validity_years' => 2,
            'renewal_notice_days' => 30,
        ],
        'orientation' => [
            'name' => 'Orientation',
            'validity_years' => null, // Permanent
            'renewal_notice_days' => 0,
        ],
    ],

    // Meeting Settings
    'meetings' => [
        'default_duration_minutes' => 60,
        'min_time_between_meetings' => 30, // in minutes
        'registration_deadline_hours' => 24,
    ],

    // Facility Settings
    'facilities' => [
        'min_clean_time_years' => 0,
        'max_clean_time_years' => 50,
    ],

    // Audit Logging
    'audit' => [
        'enabled' => true,
        'log_volunteer_assignments' => true,
        'log_credential_changes' => true,
        'log_profile_updates' => true,
        'log_coordinator_overrides' => true,
    ],

    // Feature Flags
    'features' => [
        'auto_matching_enabled' => true,
        'email_notifications' => true,
        'sms_notifications' => true,
        'volunteer_self_registration' => true,
        'export_to_pdf' => true,
        'export_to_excel' => true,
    ],
];
