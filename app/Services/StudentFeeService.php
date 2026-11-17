<?php

namespace App\Services;

use App\Models\StudentFee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentFeeService
{
    /**
     * Get paginated student fees.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedFees(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StudentFee::query()->with(['student', 'feeType']);

        if (isset($filters['student_id']) && ! empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['status']) && ! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['fee_type_id']) && ! empty($filters['fee_type_id'])) {
            $query->where('fee_type_id', $filters['fee_type_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new student fee.
     *
     * @param  array<string, mixed>  $data
     */
    public function createFee(array $data): StudentFee
    {
        if (! isset($data['status'])) {
            $data['status'] = 'unpaid';
        }

        $fee = StudentFee::create($data);

        return $fee->load(['student', 'feeType']);
    }

    /**
     * Update an existing student fee.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateFee(StudentFee $fee, array $data): StudentFee
    {
        $fee->update($data);

        return $fee->load(['student', 'feeType']);
    }

    /**
     * Delete a student fee.
     */
    public function deleteFee(StudentFee $fee): bool
    {
        return (bool) $fee->delete();
    }
}
