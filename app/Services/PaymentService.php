<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\StudentFee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentService
{
    /**
     * Get paginated fee payments.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedPayments(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = FeePayment::query()->with(['studentFee', 'studentFee.student', 'studentFee.feeType']);

        if (isset($filters['student_fee_id']) && ! empty($filters['student_fee_id'])) {
            $query->where('student_fee_id', $filters['student_fee_id']);
        }

        if (isset($filters['payment_method']) && ! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new fee payment and update student fee status.
     *
     * @param  array<string, mixed>  $data
     */
    public function createPayment(array $data): FeePayment
    {
        if (! isset($data['payment_method'])) {
            $data['payment_method'] = 'cash';
        }

        $payment = FeePayment::create($data);

        $studentFee = StudentFee::find($payment->student_fee_id);
        if ($studentFee !== null) {
            $this->recalculateStudentFeeStatus($studentFee);
        }

        return $payment->load(['studentFee', 'studentFee.student', 'studentFee.feeType']);
    }

    /**
     * Update an existing fee payment.
     *
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(FeePayment $payment, array $data): FeePayment
    {
        $oldStudentFeeId = $payment->student_fee_id;
        $payment->update($data);

        $studentFee = StudentFee::find($payment->student_fee_id);
        if ($studentFee !== null) {
            $this->recalculateStudentFeeStatus($studentFee);
        }

        if ($oldStudentFeeId !== $payment->student_fee_id) {
            $oldStudentFee = StudentFee::find($oldStudentFeeId);
            if ($oldStudentFee !== null) {
                $this->recalculateStudentFeeStatus($oldStudentFee);
            }
        }

        return $payment->load(['studentFee', 'studentFee.student', 'studentFee.feeType']);
    }

    /**
     * Delete a fee payment.
     */
    public function deletePayment(FeePayment $payment): bool
    {
        $studentFee = StudentFee::find($payment->student_fee_id);
        $result = (bool) $payment->delete();

        if ($studentFee !== null) {
            $this->recalculateStudentFeeStatus($studentFee);
        }

        return $result;
    }

    /**
     * Recalculate and update the status of a StudentFee based on total payments.
     */
    public function recalculateStudentFeeStatus(StudentFee $studentFee): void
    {
        $totalPaid = (float) $studentFee->payments()->sum('amount_paid');
        $feeAmount = (float) $studentFee->amount;

        if ($totalPaid >= $feeAmount) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $studentFee->update(['status' => $status]);
    }
}
