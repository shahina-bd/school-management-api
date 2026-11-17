<?php

namespace App\Services;

use App\Models\FeeType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeeTypeService
{
    /**
     * Get paginated fee types.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedFeeTypes(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = FeeType::query();

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new fee type.
     *
     * @param  array<string, mixed>  $data
     */
    public function createFeeType(array $data): FeeType
    {
        return FeeType::create($data);
    }

    /**
     * Update an existing fee type.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateFeeType(FeeType $feeType, array $data): FeeType
    {
        $feeType->update($data);

        return $feeType;
    }

    /**
     * Delete a fee type.
     */
    public function deleteFeeType(FeeType $feeType): bool
    {
        return (bool) $feeType->delete();
    }
}
