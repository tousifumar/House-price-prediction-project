<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee("Know Your Property's Worth", false);
        $response->assertSee('Bangalore');
        $response->assertSee('Whitefield');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Your Account');
    }

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'demo@houseprice.ai',
            'password' => 'Demo@12345',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->post('/login', [
            'email' => 'demo@houseprice.ai',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_1_click_demo_login_works_for_user(): void
    {
        $response = $this->post('/auth/demo-login', [
            'role' => 'demo',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_1_click_demo_login_works_for_admin(): void
    {
        $response = $this->post('/auth/demo-login', [
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
        $response->assertRedirect('/admin');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Operations Hub');
    }

    public function test_user_can_register_new_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Real Estate Investor',
            'email' => 'newinvestor@example.com',
            'phone' => '+91 99887 76655',
            'password' => 'Password@1234',
            'password_confirmation' => 'Password@1234',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newinvestor@example.com',
            'role' => 'user',
        ]);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_log_out(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
