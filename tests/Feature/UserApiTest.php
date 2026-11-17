<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_users(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ]);
    }

    public function test_can_create_user(): void
    {
        $payload = [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'password' => 'secret1234',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/users', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'John Smith')
            ->assertJsonPath('data.email', 'john.smith@example.com')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'john.smith@example.com',
        ]);
    }

    public function test_can_show_user(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $targetUser->id)
            ->assertJsonPath('data.email', 'alice@example.com');
    }

    public function test_can_update_user(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Bob Williams',
            'email' => 'bob@example.com',
        ]);

        $payload = [
            'name' => 'Robert Williams',
            'email' => 'robert@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/users/{$targetUser->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Robert Williams')
            ->assertJsonPath('data.email', 'robert@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'email' => 'robert@example.com',
        ]);
    }

    public function test_can_delete_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}
