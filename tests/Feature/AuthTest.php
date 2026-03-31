<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-AUTH-001: Successful volunteer login redirects to volunteer dashboard
     */
    public function test_successful_volunteer_login_redirects_to_dashboard(): void
    {
        $this->seed();

        $volunteer = User::where('email', 'john@example.com')->first();

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/volunteer/dashboard');
    }

    /**
     * TC-AUTH-002: Coordinator login redirects to coordinator dashboard
     */
    public function test_successful_coordinator_login_redirects_to_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'coord@example.com',
            'password' => 'CoordPass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/coordinator/dashboard');
    }

    /**
     * TC-AUTH-003: Wrong password shows error message and no session created
     */
    public function test_login_with_wrong_password_fails(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * TC-AUTH-004: Non-existent email returns same error as wrong password (no enumeration)
     */
    public function test_login_with_nonexistent_email_shows_generic_error(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'SomePassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * TC-AUTH-005: Empty email field shows validation error
     */
    public function test_login_with_empty_email_shows_validation_error(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'SomePassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * TC-AUTH-006: Invalid email format shows validation error
     */
    public function test_login_with_invalid_email_format_shows_validation_error(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'SomePassword123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * TC-AUTH-007: Forgot password shows success message regardless of email existence
     */
    public function test_forgot_password_with_existing_email_shows_success(): void
    {
        $this->seed();

        $response = $this->post('/forgot-password', [
            'email' => 'john@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    /**
     * TC-AUTH-008: Forgot password with non-existent email shows success (no enumeration)
     */
    public function test_forgot_password_with_nonexistent_email_shows_success(): void
    {
        $this->seed();

        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    /**
     * TC-AUTH-009: Session timeout redirects to login with timeout message
     */
    public function test_session_timeout_redirects_to_login(): void
    {
        $this->seed();

        // Login user
        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();

        // Simulate session expiration by modifying session timeout
        session()->flush();

        $response = $this->get('/volunteer/dashboard');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * TC-AUTH-010: Logout destroys session and redirects to login
     */
    public function test_logout_destroys_session_and_redirects(): void
    {
        $this->seed();

        // Login user
        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();

        // Logout
        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * TC-AUTH-011: Admin login redirects to admin dashboard
     */
    public function test_successful_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'AdminPass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * TC-AUTH-012: Authenticated user accessing login page redirects to dashboard
     */
    public function test_authenticated_user_accessing_login_redirects(): void
    {
        $this->seed();

        $user = User::where('email', 'john@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/login');

        $response->assertRedirect('/volunteer/dashboard');
    }
}
