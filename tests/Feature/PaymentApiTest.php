<?php

namespace Tests\Feature;

use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Student $student;

    protected FeeType $feeType;

    protected StudentFee $studentFee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->student = Student::create([
            'admission_number' => 'STU-PAY-01',
            'gender' => 'female',
            'status' => 'active',
        ]);

        $this->feeType = FeeType::create([
            'name' => 'Registration Fee',
            'description' => 'Annual registration',
        ]);

        $this->studentFee = StudentFee::create([
            'student_id' => $this->student->id,
            'fee_type_id' => $this->feeType->id,
            'amount' => 1000.00,
            'due_date' => '2026-12-01',
            'status' => 'unpaid',
        ]);
    }

    public function test_can_list_payments(): void
    {
        FeePayment::create([
            'student_fee_id' => $this->studentFee->id,
            'amount_paid' => 200.00,
            'payment_method' => 'cash',
            'payment_date' => '2026-10-02',
            'receipt_number' => 'REC-001',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/fee-payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_fee_id',
                        'amount_paid',
                        'payment_method',
                        'payment_date',
                        'receipt_number',
                    ],
                ],
            ]);
    }

    public function test_can_create_payment_and_updates_fee_status(): void
    {
        $payload = [
            'student_fee_id' => $this->studentFee->id,
            'amount_paid' => 500.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-10-02',
            'receipt_number' => 'REC-002',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/fee-payments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount_paid', 500)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('fee_payments', [
            'student_fee_id' => $this->studentFee->id,
            'receipt_number' => 'REC-002',
        ]);

        // Student fee status should now be 'partial'
        $this->assertDatabaseHas('student_fees', [
            'id' => $this->studentFee->id,
            'status' => 'partial',
        ]);
    }

    public function test_can_show_payment(): void
    {
        $payment = FeePayment::create([
            'student_fee_id' => $this->studentFee->id,
            'amount_paid' => 300.00,
            'payment_method' => 'card',
            'payment_date' => '2026-10-02',
            'receipt_number' => 'REC-003',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/fee-payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $payment->id)
            ->assertJsonPath('data.receipt_number', 'REC-003');
    }

    public function test_can_update_payment(): void
    {
        $payment = FeePayment::create([
            'student_fee_id' => $this->studentFee->id,
            'amount_paid' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => '2026-10-02',
            'receipt_number' => 'REC-004',
        ]);

        $payload = [
            'amount_paid' => 1000.00,
            'payment_method' => 'check',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/fee-payments/{$payment->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.amount_paid', 1000)
            ->assertJsonPath('data.payment_method', 'check');

        // Student fee status should now be 'paid' because total payments equal amount
        $this->assertDatabaseHas('student_fees', [
            'id' => $this->studentFee->id,
            'status' => 'paid',
        ]);
    }

    public function test_can_delete_payment(): void
    {
        $payment = FeePayment::create([
            'student_fee_id' => $this->studentFee->id,
            'amount_paid' => 1000.00,
            'payment_method' => 'cash',
            'payment_date' => '2026-10-02',
            'receipt_number' => 'REC-005',
        ]);

        $this->studentFee->update(['status' => 'paid']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/fee-payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('fee_payments', [
            'id' => $payment->id,
        ]);

        // Fee status should revert to 'unpaid'
        $this->assertDatabaseHas('student_fees', [
            'id' => $this->studentFee->id,
            'status' => 'unpaid',
        ]);
    }
}
