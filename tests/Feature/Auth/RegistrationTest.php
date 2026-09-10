<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'Fname' => 'Test',
            'Lname' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890', // Assuming phone is required or good to have
            'account_state' => 'Active' // Just in case
        ]);

        $this->assertAuthenticated();
        // Redirect could be to role redirect or dashboard
        // $response->assertRedirect(route('dashboard', absolute: false)); 
        // Note: The route might redirect to /user-role-redirect then dashboard, assertRedirect handles final destination mostly or immediate.
        // Let's rely on standard logic.
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
