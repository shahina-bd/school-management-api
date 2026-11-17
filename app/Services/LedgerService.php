<?php

namespace App\Services;

use App\Models\LedgerEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LedgerService
{
    /**
     * Get paginated ledger entries.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedLedgerEntries(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = LedgerEntry::query()->with('expenseCategory');

        if (isset($filters['type']) && ! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['expense_category_id']) && ! empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new ledger entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function createLedgerEntry(array $data): LedgerEntry
    {
        $ledger = LedgerEntry::create($data);

        return $ledger->load('expenseCategory');
    }

    /**
     * Update an existing ledger entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateLedgerEntry(LedgerEntry $ledger, array $data): LedgerEntry
    {
        $ledger->update($data);

        return $ledger->load('expenseCategory');
    }

    /**
     * Delete a ledger entry.
     */
    public function deleteLedgerEntry(LedgerEntry $ledger): bool
    {
        return (bool) $ledger->delete();
    }
}
