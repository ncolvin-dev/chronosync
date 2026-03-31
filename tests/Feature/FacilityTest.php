<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-FAC-001: Coordinator can view list of all facilities
     */
    public function test_coordinator_can_view_facilities_list(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $response = $this->get('/coordinator/facilities');

        $response->assertStatus(200);
        $response->assertViewHas('facilities');
    }

    /**
     * TC-FAC-002: Coordinator can view facility details including requirements
     */
    public function test_coordinator_can_view_facility_details(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/facilities/{$facility->id}");

        $response->assertStatus(200);
        $response->assertViewHas('facility');
    }

    /**
     * TC-FAC-003: Volunteer can view public facility information
     */
    public function test_volunteer_can_view_facility_information(): void
    {
        $this->seed();

        $volunteer = User::where('email', 'john@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $this->actingAs($volunteer);

        $response = $this->get("/facilities/{$facility->id}");

        $response->assertStatus(200);
    }

    /**
     * TC-FAC-004: Coordinator can create new facility
     */
    public function test_coordinator_can_create_facility(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $response = $this->post('/coordinator/facilities', [
            'name' => 'New Facility',
            'address' => '999 Test St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62706',
            'min_clean_years' => 1,
            'requires_background_check' => true,
            'requires_tb_test' => false,
            'requires_reference_check' => false,
            'gender_restriction' => null,
            'probation_allowed' => true,
        ]);

        $this->assertDatabaseHas('facilities', [
            'name' => 'New Facility',
            'address' => '999 Test St',
        ]);
    }

    /**
     * TC-FAC-005: Volunteer cannot create facility
     */
    public function test_volunteer_cannot_create_facility(): void
    {
        $this->seed();

        $volunteer = User::where('email', 'john@example.com')->first();
        $this->actingAs($volunteer);

        $response = $this->post('/coordinator/facilities', [
            'name' => 'Unauthorized Facility',
            'address' => '999 Test St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62706',
        ]);

        $response->assertForbidden();
    }

    /**
     * TC-FAC-006: Coordinator can update facility information
     */
    public function test_coordinator_can_update_facility(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $this->actingAs($coordinator);

        $response = $this->put("/coordinator/facilities/{$facility->id}", [
            'name' => 'Metro Hospital Updated',
            'address' => '123 Medical Drive',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62701',
            'min_clean_years' => 2,
            'requires_background_check' => true,
            'requires_tb_test' => true,
            'requires_reference_check' => false,
            'gender_restriction' => null,
            'probation_allowed' => false,
        ]);

        $this->assertDatabaseHas('facilities', [
            'id' => $facility->id,
            'name' => 'Metro Hospital Updated',
        ]);
    }

    /**
     * TC-FAC-007: Facility address must be valid format
     */
    public function test_facility_address_validation(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $response = $this->post('/coordinator/facilities', [
            'name' => 'New Facility',
            'address' => '', // Invalid empty address
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62706',
        ]);

        $response->assertSessionHasErrors('address');
    }

    /**
     * TC-FAC-008: Facility minimum clean years must be non-negative integer
     */
    public function test_facility_min_clean_years_validation(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $response = $this->post('/coordinator/facilities', [
            'name' => 'New Facility',
            'address' => '999 Test St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62706',
            'min_clean_years' => -1, // Invalid negative value
        ]);

        $response->assertSessionHasErrors('min_clean_years');
    }

    /**
     * TC-FAC-009: Facility name must be unique
     */
    public function test_facility_name_must_be_unique(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $this->actingAs($coordinator);

        $response = $this->post('/coordinator/facilities', [
            'name' => 'Metro Hospital', // Duplicate name
            'address' => '999 Test St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip_code' => '62706',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * TC-FAC-010: Coordinator can view facility meeting schedule
     */
    public function test_coordinator_can_view_facility_meeting_schedule(): void
    {
        $this->seed();

        $coordinator = User::where('email', 'coord@example.com')->first();
        $facility = Facility::where('name', 'Metro Hospital')->first();

        $this->actingAs($coordinator);

        $response = $this->get("/coordinator/facilities/{$facility->id}/meetings");

        $response->assertStatus(200);
        $response->assertViewHas('meetings');
    }
}
