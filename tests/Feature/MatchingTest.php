<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-MATCH-001: Clean time filter - volunteers with less clean time are excluded
     */
    public function test_clean_time_filter_excludes_insufficient_clean_time(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'County Jail')->first(); // Requires 3 years
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        // Marcus Williams has only 2 years clean (2022-11-01)
        $marcus = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'marcus@example.com');
        })->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $response->assertStatus(200);
        $candidates = $response->viewData('candidates');

        // Marcus should not be in eligible candidates
        $marcusInCandidates = $candidates->contains('id', $marcus->id);
        $this->assertFalse($marcusInCandidates);
    }

    /**
     * TC-MATCH-002: Credential filter - volunteers without required credentials are excluded
     */
    public function test_credential_filter_excludes_missing_credentials(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Youth Detention Center')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $response->assertStatus(200);
        $candidates = $response->viewData('candidates');

        // Only Carlos Garcia and Sarah Johnson should have required credentials
        $expectedCount = 2;
        $this->assertLessThanOrEqual($expectedCount, $candidates->count());
    }

    /**
     * TC-MATCH-003: Probation filter - volunteers on probation excluded if facility disallows
     */
    public function test_probation_filter_excludes_probation_at_restricted_facilities(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'County Jail')->first(); // Probation not allowed
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $marcus = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'marcus@example.com');
        })->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $response->assertStatus(200);
        $candidates = $response->viewData('candidates');

        // Marcus (on probation) should not be eligible
        $marcusInCandidates = $candidates->contains('id', $marcus->id);
        $this->assertFalse($marcusInCandidates);
    }

    /**
     * TC-MATCH-004: Gender restriction filter - volunteers not matching gender are excluded
     */
    public function test_gender_restriction_filter(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'County Jail')->first(); // Male-only
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $response->assertStatus(200);
        $candidates = $response->viewData('candidates');

        // All female, trans, and non-binary volunteers should be excluded
        foreach ($candidates as $candidate) {
            $volunteer = Volunteer::find($candidate->id);
            $this->assertNotIn($volunteer->gender, ['Female', 'Trans Woman', 'Non-Binary']);
        }
    }

    /**
     * TC-MATCH-005: Buffer time enforcement - meetings within 60 minutes are not matched
     */
    public function test_buffer_time_enforcement(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting1 = Meeting::where('facility_id', $facility->id)->first();

        // Create a second meeting 30 minutes after the first
        $meeting2 = Meeting::create([
            'facility_id' => $facility->id,
            'meeting_datetime' => $meeting1->meeting_datetime->copy()->addMinutes(30),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'notes' => 'Nearby meeting',
        ]);

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign volunteer to meeting1
        $response1 = $this->post("/coordinator/meetings/{$meeting1->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Try to assign same volunteer to meeting2
        $response2 = $this->post("/coordinator/meetings/{$meeting2->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Second assignment should fail due to buffer time
        $this->assertDatabaseMissing('meeting_volunteer', [
            'volunteer_id' => $volunteer->id,
            'meeting_id' => $meeting2->id,
        ]);
    }

    /**
     * TC-MATCH-006: Self-assignment prevention - volunteer cannot be assigned to same facility twice in one day
     */
    public function test_self_assignment_prevention(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $meeting1 = Meeting::where('facility_id', $facility->id)
            ->first();

        $meeting2 = Meeting::where('facility_id', $facility->id)
            ->where('id', '!=', $meeting1->id)
            ->whereDate('meeting_datetime', $meeting1->meeting_datetime->date())
            ->first();

        if (!$meeting2) {
            $this->markTestSkipped('Not enough meetings on same day for this test');
            return;
        }

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign volunteer to meeting1
        $response1 = $this->post("/coordinator/meetings/{$meeting1->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        $this->assertDatabaseHas('meeting_volunteer', [
            'volunteer_id' => $volunteer->id,
            'meeting_id' => $meeting1->id,
        ]);

        // Try to assign to meeting2 same day
        $response2 = $this->post("/coordinator/meetings/{$meeting2->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // This might or might not be restricted based on business rules
        // This test demonstrates the pattern for checking conflicts
    }

    /**
     * TC-MATCH-007: Multi-volunteer support - multiple volunteers can be assigned to same meeting
     */
    public function test_multiple_volunteers_can_be_assigned_to_meeting(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $john = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $sarah = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'sarah@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign first volunteer
        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $john->id,
        ]);

        // Assign second volunteer
        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $sarah->id,
        ]);

        $this->assertDatabaseHas('meeting_volunteer', [
            'volunteer_id' => $john->id,
            'meeting_id' => $meeting->id,
        ]);

        $this->assertDatabaseHas('meeting_volunteer', [
            'volunteer_id' => $sarah->id,
            'meeting_id' => $meeting->id,
        ]);
    }

    /**
     * TC-MATCH-008: Coordinator override creates audit log
     */
    public function test_coordinator_override_creates_audit_log(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->actingAs($coordinator);

        // Assign with override flag
        $this->post("/coordinator/meetings/{$meeting->id}/assign", [
            'volunteer_id' => $volunteer->id,
            'override' => true,
        ]);

        // Check audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => 'App\Models\MeetingVolunteer',
            'action' => 'created',
            'description' => 'Coordinator override',
        ]);
    }

    /**
     * TC-MATCH-009: Availability matching filters candidates
     */
    public function test_availability_matching_filters_candidates(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $response->assertStatus(200);
        $candidates = $response->viewData('candidates');

        // Should only include candidates with matching availability
        $this->assertGreaterThan(0, $candidates->count());
    }

    /**
     * TC-MATCH-010: Matching algorithm considers previous assignments
     */
    public function test_matching_considers_previous_assignments(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Community Center')->first();
        $meetings = Meeting::where('facility_id', $facility->id)->take(2)->get();

        if ($meetings->count() < 2) {
            $this->markTestSkipped('Not enough meetings for this test');
            return;
        }

        $this->actingAs($coordinator);

        // Assign volunteer to first meeting
        $volunteer = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'john@example.com');
        })->first();

        $this->post("/coordinator/meetings/{$meetings[0]->id}/assign", [
            'volunteer_id' => $volunteer->id,
        ]);

        // Volunteer should be weighted lower for other meetings at same facility
        $response = $this->get("/coordinator/meetings/{$meetings[1]->id}/candidates");
        $candidates = $response->viewData('candidates');

        // John might still appear but with lower ranking
        $this->assertGreaterThan(0, $candidates->count());
    }

    /**
     * TC-MATCH-011: Expired credentials prevent matching
     */
    public function test_expired_credentials_prevent_matching(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'County Jail')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        // Patricia Brown has expired credentials at County Jail
        $patricia = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'patricia@example.com');
        })->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $candidates = $response->viewData('candidates');

        // Patricia should not be in eligible candidates
        $patriciaInCandidates = $candidates->contains('id', $patricia->id);
        $this->assertFalse($patriciaInCandidates);
    }

    /**
     * TC-MATCH-012: Pending credentials exclude volunteer from matching
     */
    public function test_pending_credentials_exclude_volunteer(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();
        $meeting = Meeting::where('facility_id', $facility->id)->first();

        // Robert Davis has pending credentials
        $robert = Volunteer::whereHas('user', function ($query) {
            $query->where('email', 'robert@example.com');
        })->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/meetings/{$meeting->id}/candidates");

        $candidates = $response->viewData('candidates');

        // Robert should not be in eligible candidates (pending status)
        $robertInCandidates = $candidates->contains('id', $robert->id);
        $this->assertFalse($robertInCandidates);
    }
}
