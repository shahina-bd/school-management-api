<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_v1_login_endpoint_validation(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_v1_protected_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/students')->assertStatus(401);
        $this->getJson('/api/v1/attendance')->assertStatus(401);
        $this->getJson('/api/v1/fees')->assertStatus(401);
        $this->getJson('/api/v1/fee-payments')->assertStatus(401);
        $this->getJson('/api/v1/expenses')->assertStatus(401);
        $this->getJson('/api/v1/ledger')->assertStatus(401);
        $this->getJson('/api/v1/exams')->assertStatus(401);
        $this->getJson('/api/v1/marks')->assertStatus(401);
        $this->getJson('/api/v1/results')->assertStatus(401);
        $this->getJson('/api/v1/teachers')->assertStatus(401);
        $this->getJson('/api/v1/notices')->assertStatus(401);
        $this->getJson('/api/v1/events')->assertStatus(401);
        $this->getJson('/api/v1/reports')->assertStatus(401);
    }

    public function test_authenticated_user_can_access_v1_dashboard_and_api_resources(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure(['students', 'attendance', 'fees', 'staff']);
    }
}
