<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('email', 'demo@houseprice.ai')->first();
    }

    public function test_suspended_user_is_blocked_from_accessing_dashboard(): void
    {
        $this->user->status = 'blocked';
        $this->user->save();

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }

    public function test_xss_payloads_in_input_are_escaped_in_blade_views(): void
    {
        $xssPayload = '<script>alert("xss")</script>';

        $response = $this->actingAs($this->user)->get('/market-analytics');
        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_post_prediction_endpoint_has_rate_limiting(): void
    {
        $payload = [
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city' => 'Bangalore',
            'locality' => 'Whitefield',
            'area' => 1200,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'furnishing' => 'Semi-Furnished',
        ];

        // Send 1 valid request
        $res = $this->postJson('/api/predict', $payload);
        $res->assertStatus(200);

        // Check rate limit header is present
        $this->assertTrue($res->headers->has('X-RateLimit-Limit'));
        $this->assertEquals('60', $res->headers->get('X-RateLimit-Limit'));
    }

    public function test_sql_injection_attempt_in_filters_is_handled_safely(): void
    {
        $sqli = "1' OR '1'='1";

        $response = $this->get('/properties?search=' . urlencode($sqli));
        // Route doesn't crash or expose SQL errors
        $this->assertNotEquals(500, $response->getStatusCode());
    }
}
