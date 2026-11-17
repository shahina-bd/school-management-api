<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_reports(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'summary',
                        'generated_at',
                    ],
                ],
            ]);
    }

    public function test_can_create_report(): void
    {
        $payload = [
            'title' => 'End of Year Performance Summary',
            'type' => 'academic',
            'parameters' => [
                'year' => '2026',
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/reports', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'End of Year Performance Summary')
            ->assertJsonPath('data.type', 'academic')
            ->assertJsonPath('success', true);
    }

    public function test_can_show_report(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/reports/1');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', 1);
    }

    public function test_can_update_report(): void
    {
        $payload = [
            'title' => 'Updated Report Title',
            'type' => 'financial',
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/reports/1', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Report Title')
            ->assertJsonPath('data.type', 'financial');
    }

    public function test_can_delete_report(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/reports/1');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
