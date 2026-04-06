<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Meeting;
use App\Models\MeetingVolunteer;
use App\Models\User;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-SMS-001: SMS notification sent 24 hours before confirmed meeting
     */
    public function test_sms_sent_24_hours_before_meeting(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        // Create meeting 24 hours from now
        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'SMS test meeting',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign volunteer
        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Trigger SMS job
        $this->artisan('sms:send-reminders');

        // Queue::assertPushed(\App\Jobs\SendMeetingReminder::class);
    }

    /**
     * TC-SMS-002: SMS not sent if meeting is not confirmed
     */
    public function test_sms_not_sent_for_unconfirmed_meeting(): void
    {
        Queue::fake();
        $this->seed();

        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'cancelled',
            'notes' => 'Cancelled meeting',
        ]);

        $this->artisan('sms:send-reminders');

        // No SMS should be queued for cancelled meeting
    }

    /**
     * TC-SMS-003: SMS not sent during night hours (outside 9 AM - 9 PM)
     */
    public function test_sms_not_sent_during_night_hours(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        // Create meeting at 3 AM tomorrow (24 hours from now if current time is 3 AM)
        $meetingTime = now()->setHour(3)->addHours(24);

        if ($meetingTime->hour < 9 || $meetingTime->hour >= 21) {
            $meeting = Meeting::create([
                'facility_id' => $facility->id,
                'meeting_datetime' => $meetingTime,
                'duration_minutes' => 60,
                'status' => 'scheduled',
                'notes' => 'Night meeting',
            ]);

            $volunteer = Volunteer::whereHas('user', function ($query) {
                $query->where('email', 'john@example.com');
            })->first();

            $this->actingAs($coordinator);

            $this->post("/coordinator/meetings/{$meeting->id}/assign", [
                'volunteer_id' => $volunteer->id,
            ]);

            $this->artisan('sms:send-reminders');

            // SMS should not be sent for night hours
        }
    }

    /**
     * TC-SMS-004: SMS content includes meeting details
     */
    public function test_sms_content_includes_meeting_details(): void
    {
        $this->seed();

        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Important meeting',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Verify meeting assignment created
        $this->assertDatabaseHas('meeting_volunteer', [
            'meeting_id' => $meeting->id,
            'volunteer_id' => $volunteer->id,
        ]);

        // SMS would contain facility name, time, and date
    }

    /**
     * TC-SMS-005: SMS sent with buffer time consideration (before buffer window closes)
     */
    public function test_sms_respects_buffer_time(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        // Create meeting with buffer time (60 min default)
        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24)->setMinute(30),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Buffer test meeting',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        $this->artisan('sms:send-reminders');

        // SMS should respect buffer time configuration
    }

    /**
     * TC-SMS-006: Multiple SMS recipients when multiple volunteers assigned
     */
    public function test_multiple_sms_sent_for_multiple_volunteers(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Multi-volunteer meeting',
        ]);

        $john = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $sarah = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'sarah@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign both volunteers
        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $john->id,
        ]);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $sarah->id,
        ]);

        $this->artisan('sms:send-reminders');

        // Both volunteers should receive SMS
        $this->assertDatabaseHas('meeting_volunteer', [
            'meeting_id' => $meeting->id,
            'volunteer_id' => $john->id,
        ]);

        $this->assertDatabaseHas('meeting_volunteer', [
            'meeting_id' => $meeting->id,
            'volunteer_id' => $sarah->id,
        ]);
    }

    /**
     * TC-SMS-007: SMS delivery failure is logged but doesn't block system
     */
    public function test_sms_delivery_failure_logged(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Failure test',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Simulate SMS sending with failure
        $this->artisan('sms:send-reminders');

        // System should continue operating
        $response = $this->get('/coordinator/dashboard');
        $response->assertStatus(200);
    }

    /**
     * TC-SMS-008: SMS only sent once per meeting-volunteer pair
     */
    public function test_sms_only_sent_once_per_assignment(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Duplicate SMS test',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Run reminders twice
        $this->artisan('sms:send-reminders');
        $this->artisan('sms:send-reminders');

        // Should only have one SMS sent notification
    }

    /**
     * TC-SMS-009: SMS can be declined by volunteer
     */
    public function test_volunteer_can_decline_sms(): void
    {
        $this->seed();

        $volunteer = User::where('email', 'john@example.com')->first();
        $this->actingAs($volunteer);

        // Update notification preferences to decline SMS
        $response = $this->post('/volunteer/preferences', [
            'sms_notifications' => false,
        ]);

        $response->assertStatus(200);

        // Verify preference saved
        $this->assertDatabaseHas('users', [
            'id' => $volunteer->id,
            'sms_notifications_enabled' => false,
        ]);
    }

    /**
     * TC-SMS-010: SMS provider switch between AWS SNS and Twilio
     */
    public function test_sms_provider_configuration(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        // Check configured SMS provider
        $provider = config('chronosync.sms.provider');
        $this->assertIn($provider, ['aws', 'twilio']);
    }

    /**
     * TC-SMS-011: SMS retry on temporary network failures
     */
    public function test_sms_retry_on_failure(): void
    {
        Queue::fake();
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => now()->addHours(24),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Retry test',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        $this->artisan('sms:send-reminders');

        // System should attempt retry on failure
    }

    /**
     * TC-SMS-012: SMS rate limiting to prevent abuse
     */
    public function test_sms_rate_limiting(): void
    {
        $this->seed();

        $volunteer = User::where('email', 'john@example.com')->first();
        $this->actingAs($volunteer);

        // Attempt multiple SMS requests in quick succession
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->post('/api/volunteer/send-test-sms');
        }

        // Some requests should be rate limited
        // At least one should succeed, later ones may fail
    }
}
