<?php

namespace Tests\Feature;

use App\Models\FeeType;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Student $student;

    protected FeeType $feeType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->student = Student::create([
            'admission_number' => 'STU-FEE-01',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $this->feeType = FeeType::create([
            'name' => 'Tuition Fee',
            'description' => 'Monthly tuition fee',
        ]);
    }

    public function test_can_list_fees(): void
    {
        StudentFee::create([
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 500.00,
            'due_date' => '2026-10-31',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/fees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'fee_type_id',
                        'amount',
                        'due_date',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_can_create_fee(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 750.50,
            'due_date' => '2026-11-15',
            'status' => 'unpaid',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/fees', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', 750.5)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('student_fees', [
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 750.50,
        ]);
    }

    public function test_can_show_fee(): void
    {
        $fee = StudentFee::create([
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 300.00,
            'due_date' => '2026-10-25',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/fees/{$fee->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $fee->id)
            ->assertJsonPath('data.amount', 300);
    }

    public function test_can_update_fee(): void
    {
        $fee = StudentFee::create([
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 400.00,
            'due_date' => '2026-10-25',
            'status' => 'unpaid',
        ]);

        $payload = [
            'amount' => 450.00,
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/fees/{$fee->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.amount', 450)
            ->assertJsonPath('data.status', 'paid');

        $this->assertDatabaseHas('student_fees', [
            'id' => $fee->id,
            'amount' => 450.00,
            'status' => 'paid',
        ]);
    }

    public function test_can_delete_fee(): void
    {
        $fee = StudentFee::create([
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 200.00,
            'due_date' => '2026-10-25',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/fees/{$fee->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('student_fees', [
            'id' => $fee->id,
        ]);
    }

    public function test_can_crud_fee_types(): void
    {
        // Index
        $this->actingAs($this->user)->getJson('/api/v1/fee-types')->assertStatus(200);

        // Store
        $storeRes = $this->actingAs($this->user)->postJson('/api/v1/fee-types', [
            'name' => 'Exam Fee',
            'description' => 'Final Exam Fee',
        ]);
        $storeRes->assertStatus(201);
        $typeId = $storeRes->json('data.id');

        // Show
        $this->actingAs($this->user)->getJson("/api/v1/fee-types/{$typeId}")->assertStatus(200);

        // Update
        $this->actingAs($this->user)->putJson("/api/v1/fee-types/{$typeId}", [
            'name' => 'Updated Exam Fee',
        ])->assertStatus(200);

        // Destroy
        $this->actingAs($this->user)->deleteJson("/api/v1/fee-types/{$typeId}")->assertStatus(200);
    }
}
