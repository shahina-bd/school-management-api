<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_staff(): void
    {
        Staff::create([
            'employee_id' => 'STF-101',
            'department' => 'Administration',
            'designation' => 'Accountant',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/staff');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'employee_id',
                        'department',
                        'designation',
                    ],
                ],
            ]);
    }

    public function test_can_create_staff(): void
    {
        $payload = [
            'employee_id' => 'STF-102',
            'department' => 'IT',
            'designation' => 'System Admin',
            'joining_date' => '2024-02-01',
            'salary' => 45000.00,
            'phone' => '1122334455',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/staff', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.employee_id', 'STF-102')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('staff', [
            'employee_id' => 'STF-102',
            'department' => 'IT',
        ]);
    }

    public function test_can_show_staff(): void
    {
        $staff = Staff::create([
            'employee_id' => 'STF-103',
            'department' => 'HR',
            'designation' => 'HR Manager',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/staff/{$staff->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $staff->id)
            ->assertJsonPath('data.employee_id', 'STF-103');
    }

    public function test_can_update_staff(): void
    {
        $staff = Staff::create([
            'employee_id' => 'STF-104',
            'department' => 'Finance',
        ]);

        $payload = [
            'employee_id' => 'STF-104',
            'department' => 'Finance & Accounts',
            'designation' => 'Senior Accountant',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/staff/{$staff->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.department', 'Finance & Accounts')
            ->assertJsonPath('data.designation', 'Senior Accountant');

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'department' => 'Finance & Accounts',
        ]);
    }

    public function test_can_delete_staff(): void
    {
        $staff = Staff::create([
            'employee_id' => 'STF-105',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/staff/{$staff->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('staff', [
            'id' => $staff->id,
        ]);
    }
}
