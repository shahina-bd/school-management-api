<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseService
{
    /**
     * Get paginated expenses.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedExpenses(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Expense::query()->with('expenseCategory');

        if (isset($filters['expense_category_id']) && ! empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new expense.
     *
     * @param  array<string, mixed>  $data
     */
    public function createExpense(array $data): Expense
    {
        $expense = Expense::create($data);

        return $expense->load('expenseCategory');
    }

    /**
     * Update an existing expense.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateExpense(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense->load('expenseCategory');
    }

    /**
     * Delete an expense.
     */
    public function deleteExpense(Expense $expense): bool
    {
        return (bool) $expense->delete();
    }
}
