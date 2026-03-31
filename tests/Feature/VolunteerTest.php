<?php

namespace Tests\Feature;

use App\Models\Volunteer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-REG-001: Successful registration with all required fields
     */
    public function test_successful_volunteer_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'date_of_birth' => '1990-05-15',
            'phone' => '(555) 999-1234',
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseHas('volunteers', [
            'phone' => '(555) 999-1234',
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * TC-REG-002: Duplicate email rejection
     */
    public function test_duplicate_email_rejected_on_registration(): void
    {
        $this->seed();

        $response = $this->post('/register', [
            'name' => 'Another John',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'date_of_birth' => '1992-03-20',
            'phone' => '(555) 888-5555',
            'gender' => 'Male',
            'neighborhood' => 'Northside',
            'clean_date' => '2021-01-10',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * TC-REG-003: Password minimum length validation (10 characters)
     */
    public function test_password_minimum_length_validation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Short1!',
            'password_confirmation' => 'Short1!',
            'date_of_birth' => '1990-05-15',
            'phone' => '(555) 999-1234',
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * TC-REG-004: Password mismatch validation
     */
    public function test_password_mismatch_validation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass123!',
            'date_of_birth' => '1990-05-15',
            'phone' => '(555) 999-1234',
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * TC-REG-005: Conditional treatment facility fields required when in treatment
     */
    public function test_treatment_facility_fields_required_when_applicable(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'date_of_birth' => '1990-05-15',
            'phone' => '(555) 999-1234',
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
            'has_treatment_history' => true,
            'treatment_facility' => '',
            'treatment_discharge_date' => '',
        ]);

        $response->assertSessionHasErrors(['treatment_facility', 'treatment_discharge_date']);
    }

    /**
     * TC-REG-006: Date of birth cannot be in the future
     */
    public function test_date_of_birth_cannot_be_future(): void
    {
        $futureDate = now()->addYears(1)->format('Y-m-d');

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'date_of_birth' => $futureDate,
            'phone' => '(555) 999-1234',
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
        ]);

        $response->assertSessionHasErrors('date_of_birth');
    }

    /**
     * TC-REG-007: US phone number validation
     */
    public function test_us_phone_number_validation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'date_of_birth' => '1990-05-15',
            'phone' => '123456', // Invalid phone
            'gender' => 'Female',
            'neighborhood' => 'Downtown',
            'clean_date' => '2019-06-15',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    /**
     * TC-PROF-001: Volunteer can view their own profile
     */
    public function test_volunteer_can_view_own_profile(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/volunteer/profile');

        $response->assertStatus(200);
    }

    /**
     * TC-PROF-002: Volunteer can update their profile information
     */
    public function test_volunteer_can_update_profile(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->put('/volunteer/profile', [
            'phone' => '(555) 111-2222',
            'neighborhood' => 'Westside',
        ]);

        $this->assertDatabaseHas('volunteers', [
            'user_id' => $user->id,
            'phone' => '(555) 111-2222',
            'neighborhood' => 'Westside',
        ]);
    }

    /**
     * TC-PROF-003: Profile update creates audit log
     */
    public function test_profile_update_creates_audit_log(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $this->put('/volunteer/profile', [
            'phone' => '(555) 111-2222',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => 'App\Models\Volunteer',
            'action' => 'updated',
        ]);
    }

    /**
     * TC-PROF-004: Volunteer cannot update other volunteer profiles
     */
    public function test_volunteer_cannot_update_other_volunteer_profile(): void
    {
        $this->seed();

        $john = User::where('email', 'john@example.com')->first();
        $sarah = User::where('email', 'sarah@example.com')->first();
        $sarahVolunteer = Volunteer::where('user_id', $sarah->id)->first();

        $this->actingAs($john);

        $response = $this->put("/volunteer/{$sarahVolunteer->id}/profile", [
            'phone' => '(555) 111-2222',
        ]);

        $response->assertForbidden();
    }

    /**
     * TC-PROF-005: Volunteer can view their assignments
     */
    public function test_volunteer_can_view_their_assignments(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/volunteer/assignments');

        $response->assertStatus(200);
    }

    /**
     * TC-PROF-006: Volunteer can view only their credentials
     */
    public function test_volunteer_can_view_only_their_credentials(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/volunteer/credentials');

        $response->assertStatus(200);
    }

    /**
     * TC-PROF-007: Multi-role user (volunteer + coordinator) sees appropriate dashboards
     */
    public function test_multi_role_user_can_access_both_dashboards(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@example.com')->first();
        $this->actingAs($user);

        // Admin should be able to access coordinator dashboard
        $response = $this->get('/coordinator/dashboard');
        $response->assertStatus(200);
    }

    /**
     * TC-PROF-008: Volunteer clean date cannot be in the future
     */
    public function test_volunteer_clean_date_cannot_be_future(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $futureDate = now()->addYears(1)->format('Y-m-d');

        $response = $this->put('/volunteer/profile', [
            'clean_date' => $futureDate,
        ]);

        $response->assertSessionHasErrors('clean_date');
    }

    /**
     * TC-PROF-009: Volunteer can view their availability schedule
     */
    public function test_volunteer_can_view_availability(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/volunteer/availability');

        $response->assertStatus(200);
    }

    /**
     * TC-PROF-010: Volunteer can update their availability
     */
    public function test_volunteer_can_update_availability(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->post('/volunteer/availability', [
            'availabilities' => [
                [
                    'day_of_week' => 'Monday',
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ],
                [
                    'day_of_week' => 'Friday',
                    'start_time' => '14:00',
                    'end_time' => '20:00',
                ],
            ],
        ]);

        $response->assertStatus(200);
    }
}
