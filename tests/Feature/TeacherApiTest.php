<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_teachers(): void
    {
        Teacher::create([
            'employee_id' => 'EMP-101',
            'designation' => 'Senior Lecturer',
            'phone' => '1234567890',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/teachers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'employee_id',
                        'designation',
                        'phone',
                    ],
                ],
            ]);
    }

    public function test_can_create_teacher(): void
    {
        $payload = [
            'employee_id' => 'EMP-102',
            'designation' => 'Assistant Professor',
            'qualification' => 'M.Sc. Mathematics',
            'joining_date' => '2024-01-15',
            'salary' => 50000.00,
            'phone' => '9876543210',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/teachers', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.employee_id', 'EMP-102')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('teachers', [
            'employee_id' => 'EMP-102',
            'designation' => 'Assistant Professor',
        ]);
    }

    public function test_can_show_teacher(): void
    {
        $teacher = Teacher::create([
            'employee_id' => 'EMP-103',
            'designation' => 'Head Teacher',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/teachers/{$teacher->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $teacher->id)
            ->assertJsonPath('data.employee_id', 'EMP-103');
    }

    public function test_can_update_teacher(): void
    {
        $teacher = Teacher::create([
            'employee_id' => 'EMP-104',
            'designation' => 'Lecturer',
        ]);

        $payload = [
            'employee_id' => 'EMP-104',
            'designation' => 'Senior Lecturer',
            'phone' => '5551234567',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/teachers/{$teacher->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.designation', 'Senior Lecturer')
            ->assertJsonPath('data.phone', '5551234567');

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'designation' => 'Senior Lecturer',
        ]);
    }

    public function test_can_delete_teacher(): void
    {
        $teacher = Teacher::create([
            'employee_id' => 'EMP-105',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/teachers/{$teacher->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('teachers', [
            'id' => $teacher->id,
        ]);
    }
}
