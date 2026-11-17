<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_roles(): void
    {
        Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'description' => 'System admin role',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'is_active',
                    ],
                ],
            ]);
    }

    public function test_can_create_role(): void
    {
        $payload = [
            'name' => 'Accountant',
            'slug' => 'accountant',
            'description' => 'Financial manager role',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/roles', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Accountant')
            ->assertJsonPath('data.slug', 'accountant')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('roles', [
            'name' => 'Accountant',
            'slug' => 'accountant',
        ]);
    }

    public function test_can_show_role(): void
    {
        $role = Role::create([
            'name' => 'Librarian',
            'slug' => 'librarian',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $role->id)
            ->assertJsonPath('data.slug', 'librarian');
    }

    public function test_can_update_role(): void
    {
        $role = Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
        ]);

        $payload = [
            'name' => 'General Staff',
            'slug' => 'general-staff',
            'description' => 'Updated staff role description',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/roles/{$role->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'General Staff')
            ->assertJsonPath('data.slug', 'general-staff');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'General Staff',
            'slug' => 'general-staff',
        ]);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::create([
            'name' => 'Temporary Role',
            'slug' => 'temp-role',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}
