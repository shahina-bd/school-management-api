<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseCategoryService
{
    /**
     * Get paginated expense categories.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedExpenseCategories(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ExpenseCategory::query();

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new expense category.
     *
     * @param  array<string, mixed>  $data
     */
    public function createExpenseCategory(array $data): ExpenseCategory
    {
        return ExpenseCategory::create($data);
    }

    /**
     * Update an existing expense category.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateExpenseCategory(ExpenseCategory $expenseCategory, array $data): ExpenseCategory
    {
        $expenseCategory->update($data);

        return $expenseCategory;
    }

    /**
     * Delete an expense category.
     */
    public function deleteExpenseCategory(ExpenseCategory $expenseCategory): bool
    {
        return (bool) $expenseCategory->delete();
    }
}
