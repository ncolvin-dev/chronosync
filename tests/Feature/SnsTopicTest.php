<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\SnsTopicServiceInterface;
use App\Models\Facility;
use App\Models\Meeting;
use App\Models\MeetingAssignment;
use App\Models\User;
use App\Models\Volunteer;
use App\Services\FakeSnsTopicService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Verifies the SNS topic feature using FakeSnsTopicService.
 *
 * No AWS credentials are required. SNS_FAKE=true is set in phpunit.xml so the
 * container automatically binds FakeSnsTopicService; these tests grab that
 * instance and assert on its recorded state.
 *
 * All test data is created inline so these tests have no seeder dependency.
 */
class SnsTopicTest extends TestCase
{
    use RefreshDatabase;

    private FakeSnsTopicService $fakeSns;
    private User $coordinator;
    private Facility $facility;
    private Meeting $meeting;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent SMS jobs from running so they cannot mask SNS failures.
        Queue::fake();

        // Grab the fake service that was registered by AppServiceProvider.
        $this->fakeSns = app(SnsTopicServiceInterface::class);
        $this->fakeSns->reset();

        $this->coordinator = User::create([
            'email'             => 'coord@test.com',
            'password_hash'     => Hash::make('CoordPass123!'),
            'email_verified_at' => now(),
            'roles'             => ['coordinator'],
        ]);

        $this->facility = Facility::create([
            'facility_name'          => 'Test Facility',
            'address'                => '1 Main St',
            'city'                   => 'Springfield',
            'state'                  => 'IL',
            'zip'                    => '62701',
            'main_phone'             => '555-000-0001',
            'contact_email'          => 'test@facility.org',
            'clean_time_requirement' => 0,
            'credentialing_types'    => [],
            'gender_restriction'     => false,
            'probation_allowed'      => true,
            'status'                 => 'active',
        ]);

        $this->meeting = Meeting::create([
            'facility_id'       => $this->facility->facility_id,
            'day_of_week'       => 1,
            'week_of_month'     => 2,
            'meeting_time'      => '14:00:00',
            'duration_minutes'  => 60,
            'format'            => 'in_person',
            'volunteers_needed' => 2,
            'status'            => 'active',
        ]);
    }

    // -------------------------------------------------------------------------
    // Helper to build a volunteer + user pair
    // -------------------------------------------------------------------------

    private function makeVolunteer(string $email, string $phone, bool $smsDeliverable = true): Volunteer
    {
        User::create([
            'email'             => $email,
            'password_hash'     => Hash::make('Pass123!'),
            'email_verified_at' => now(),
            'roles'             => ['volunteer'],
        ]);

        return Volunteer::create([
            'email'              => $email,
            'first_name'         => 'Test',
            'last_name'          => 'Volunteer',
            'dob'                => '1990-01-01',
            'phone'              => $phone,
            'clean_date'         => now()->subYears(3)->toDateString(),
            'probation_status'   => 'not_probation',
            'gender'             => 'male',
            'is_sms_deliverable' => $smsDeliverable,
        ]);
    }

    private function makeAssignment(Volunteer $volunteer, string $status = 'pending_confirmation'): MeetingAssignment
    {
        return MeetingAssignment::create([
            'meeting_id'      => $this->meeting->meeting_id,
            'volunteer_id'    => $volunteer->volunteer_id,
            'assignment_date' => now()->addWeek()->toDateString(),
            'status'          => $status,
            'assignment_type' => 'manual',
        ]);
    }

    // -------------------------------------------------------------------------
    // Tests
    // -------------------------------------------------------------------------

    /** TC-SNS-001: ensureTopicExists creates a topic and stores the ARN on the meeting. */
    public function test_ensure_topic_exists_creates_topic_and_stores_arn(): void
    {
        $this->assertNull($this->meeting->sns_topic_arn);

        $arn = $this->fakeSns->ensureTopicExists($this->meeting);

        $this->assertNotEmpty($arn);
        $this->assertTrue($this->fakeSns->wasTopicCreated($this->meeting));

        // ARN should be persisted on the model.
        $this->meeting->refresh();
        $this->assertSame($arn, $this->meeting->sns_topic_arn);
    }

    /** TC-SNS-002: ensureTopicExists is idempotent — calling it twice does not change the ARN. */
    public function test_ensure_topic_exists_is_idempotent(): void
    {
        $first  = $this->fakeSns->ensureTopicExists($this->meeting);
        $second = $this->fakeSns->ensureTopicExists($this->meeting);

        $this->assertSame($first, $second);
    }

    /** TC-SNS-003: Creating a meeting via the controller creates an SNS topic automatically. */
    public function test_creating_meeting_via_controller_creates_sns_topic(): void
    {
        $this->actingAs($this->coordinator);

        $existingIds = Meeting::pluck('meeting_id')->toArray();

        $response = $this->post('/meetings', [
            'facility_id'       => $this->facility->facility_id,
            'meeting_type'      => 'recurring',
            'day_of_week'       => 3,
            'week_of_month'     => 1,
            'meeting_time'      => '10:00',
            'format'            => 'in_person',
            'volunteers_needed' => 1,
        ]);

        $response->assertRedirect();

        $newMeeting = Meeting::whereNotIn('meeting_id', $existingIds)->first();
        $this->assertNotNull($newMeeting, 'A new meeting should have been created.');

        $this->assertTrue($this->fakeSns->wasTopicCreated($newMeeting));
        $this->assertNotNull($newMeeting->sns_topic_arn);
    }

    /** TC-SNS-004: Opted-in volunteer is subscribed when syncSubscriptions runs. */
    public function test_opted_in_volunteer_is_subscribed(): void
    {
        $volunteer = $this->makeVolunteer('vol1@test.com', '+15555550001', smsDeliverable: true);
        $this->makeAssignment($volunteer);

        $this->fakeSns->syncSubscriptions($this->meeting);

        $subscriptions = $this->fakeSns->getSubscriptions($this->meeting);
        $this->assertContains('+15555550001', $subscriptions);
    }

    /** TC-SNS-005: Opted-out volunteer is NOT subscribed even with an active assignment. */
    public function test_opted_out_volunteer_is_not_subscribed(): void
    {
        $volunteer = $this->makeVolunteer('vol2@test.com', '+15555550002', smsDeliverable: false);
        $this->makeAssignment($volunteer);

        $this->fakeSns->syncSubscriptions($this->meeting);

        $subscriptions = $this->fakeSns->getSubscriptions($this->meeting);
        $this->assertNotContains('+15555550002', $subscriptions);
    }

    /** TC-SNS-006: Only opted-in volunteers are subscribed when the pool is mixed. */
    public function test_only_opted_in_volunteers_are_subscribed_in_mixed_pool(): void
    {
        $optedIn  = $this->makeVolunteer('vol3@test.com', '+15555550003', smsDeliverable: true);
        $optedOut = $this->makeVolunteer('vol4@test.com', '+15555550004', smsDeliverable: false);

        $this->makeAssignment($optedIn);
        $this->makeAssignment($optedOut);

        $this->fakeSns->syncSubscriptions($this->meeting);

        $subscriptions = $this->fakeSns->getSubscriptions($this->meeting);
        $this->assertContains('+15555550003', $subscriptions);
        $this->assertNotContains('+15555550004', $subscriptions);
        $this->assertCount(1, $subscriptions);
    }

    /** TC-SNS-007: Cancelled assignment removes volunteer from subscriber list after re-sync. */
    public function test_cancelled_assignment_is_not_included_in_subscriptions(): void
    {
        $volunteer = $this->makeVolunteer('vol5@test.com', '+15555550005', smsDeliverable: true);
        $this->makeAssignment($volunteer, 'cancelled');

        $this->fakeSns->syncSubscriptions($this->meeting);

        $subscriptions = $this->fakeSns->getSubscriptions($this->meeting);
        $this->assertNotContains('+15555550005', $subscriptions);
    }

    /** TC-SNS-008: Assigning a volunteer via the controller triggers subscription sync. */
    public function test_assigning_volunteer_via_controller_syncs_subscriptions(): void
    {
        $volunteer = $this->makeVolunteer('vol6@test.com', '+15555550006', smsDeliverable: true);

        $this->actingAs($this->coordinator);

        $this->post("/meetings/{$this->meeting->meeting_id}/assign", [
            'volunteer_id'    => $volunteer->volunteer_id,
            'assignment_date' => now()->addWeek()->toDateString(),
            'assignment_type' => 'manual',
        ]);

        $subscriptions = $this->fakeSns->getSubscriptions($this->meeting);
        $this->assertContains('+15555550006', $subscriptions);
    }

    /** TC-SNS-009: Publish sends the message to the meeting's topic. */
    public function test_publish_records_message_for_meeting(): void
    {
        $message = 'Your H&I meeting is tomorrow at 2:00 PM.';

        $this->fakeSns->publish($this->meeting, $message);

        $published = $this->fakeSns->getPublishedMessages($this->meeting);
        $this->assertCount(1, $published);
        $this->assertSame($message, $published[0]);
    }

    /** TC-SNS-010: sns:sync Artisan command creates topics and reports subscriber counts. */
    public function test_sns_sync_command_creates_topics_and_reports_counts(): void
    {
        $volunteer = $this->makeVolunteer('vol7@test.com', '+15555550007', smsDeliverable: true);
        $this->makeAssignment($volunteer);

        $this->artisan('sns:sync')
            ->assertExitCode(0)
            ->expectsOutputToContain($this->meeting->meeting_id);

        $this->assertTrue($this->fakeSns->wasTopicCreated($this->meeting));
    }

    /** TC-SNS-011: sns:notify Artisan command publishes to the correct meeting. */
    public function test_sns_notify_command_publishes_message(): void
    {
        $customMessage = 'Test notification from artisan';

        $this->artisan("sns:notify {$this->meeting->meeting_id} --message=\"{$customMessage}\"")
            ->assertExitCode(0);

        $published = $this->fakeSns->getPublishedMessages($this->meeting);
        $this->assertCount(1, $published);
        $this->assertSame($customMessage, $published[0]);
    }

    /** TC-SNS-012: sns:notify returns failure for an unknown meeting ID. */
    public function test_sns_notify_command_fails_for_unknown_meeting(): void
    {
        $this->artisan('sns:notify nonexistent-id')
            ->assertExitCode(1);
    }

    /** TC-SNS-013: sns:sync with a specific meeting_id only processes that meeting. */
    public function test_sns_sync_with_meeting_id_processes_only_that_meeting(): void
    {
        $otherMeeting = Meeting::create([
            'facility_id'       => $this->facility->facility_id,
            'day_of_week'       => 5,
            'week_of_month'     => 1,
            'meeting_time'      => '09:00:00',
            'duration_minutes'  => 60,
            'format'            => 'virtual',
            'volunteers_needed' => 1,
            'status'            => 'active',
        ]);

        $this->artisan("sns:sync {$this->meeting->meeting_id}")
            ->assertExitCode(0);

        $this->assertTrue($this->fakeSns->wasTopicCreated($this->meeting));
        $this->assertFalse($this->fakeSns->wasTopicCreated($otherMeeting));
    }

    // -------------------------------------------------------------------------
    // One-off meeting tests
    // -------------------------------------------------------------------------

    /** TC-SNS-014: A one-off meeting can be created via the controller. */
    public function test_one_off_meeting_can_be_created(): void
    {
        $this->actingAs($this->coordinator);

        $existingIds = Meeting::pluck('meeting_id')->toArray();

        $response = $this->post('/meetings', [
            'facility_id'       => $this->facility->facility_id,
            'meeting_type'      => 'one_off',
            'scheduled_time'    => now()->addDays(7)->format('Y-m-d H:i:s'),
            'format'            => 'in_person',
            'volunteers_needed' => 1,
        ]);

        $response->assertRedirect();

        $newMeeting = Meeting::whereNotIn('meeting_id', $existingIds)->first();
        $this->assertNotNull($newMeeting, 'A one-off meeting should have been created.');
        $this->assertTrue($newMeeting->isOneOff());
        $this->assertFalse($newMeeting->isRecurring());
        $this->assertNotNull($newMeeting->scheduled_time);
        $this->assertNull($newMeeting->day_of_week);
        $this->assertNull($newMeeting->week_of_month);
    }

    /** TC-SNS-015: schedule_label for a one-off meeting shows the specific date and time. */
    public function test_one_off_meeting_schedule_label_shows_date(): void
    {
        $meeting = Meeting::create([
            'facility_id'       => $this->facility->facility_id,
            'scheduled_time'    => '2025-04-30 19:00:00',
            'duration_minutes'  => 60,
            'format'            => 'in_person',
            'volunteers_needed' => 1,
            'status'            => 'active',
        ]);

        $this->assertSame('Apr 30, 2025 at 7:00 PM', $meeting->schedule_label);
    }

    /** TC-SNS-016: One-off meeting SNS topic is created and subscribers are synced. */
    public function test_one_off_meeting_sns_topic_and_subscriptions_work(): void
    {
        $oneOffMeeting = Meeting::create([
            'facility_id'       => $this->facility->facility_id,
            'scheduled_time'    => now()->addDays(3)->toDateTimeString(),
            'duration_minutes'  => 60,
            'format'            => 'in_person',
            'volunteers_needed' => 1,
            'status'            => 'active',
        ]);

        $volunteer = $this->makeVolunteer('vol8@test.com', '+15555550008', smsDeliverable: true);

        MeetingAssignment::create([
            'meeting_id'      => $oneOffMeeting->meeting_id,
            'volunteer_id'    => $volunteer->volunteer_id,
            'assignment_date' => now()->addDays(3)->toDateString(),
            'status'          => 'confirmed',
            'assignment_type' => 'manual',
        ]);

        $this->fakeSns->syncSubscriptions($oneOffMeeting);

        $this->assertTrue($this->fakeSns->wasTopicCreated($oneOffMeeting));
        $this->assertContains('+15555550008', $this->fakeSns->getSubscriptions($oneOffMeeting));
    }

    /** TC-SNS-017: sns:notify uses the date-based label in the default message for one-off meetings. */
    public function test_one_off_meeting_notify_uses_date_label(): void
    {
        $oneOffMeeting = Meeting::create([
            'facility_id'       => $this->facility->facility_id,
            'scheduled_time'    => '2025-12-25 14:00:00',
            'duration_minutes'  => 60,
            'format'            => 'in_person',
            'volunteers_needed' => 1,
            'status'            => 'active',
        ]);

        $this->artisan("sns:notify {$oneOffMeeting->meeting_id}")
            ->assertExitCode(0);

        $published = $this->fakeSns->getPublishedMessages($oneOffMeeting);
        $this->assertCount(1, $published);
        $this->assertStringContainsString('Dec 25, 2025', $published[0]);
    }
}
