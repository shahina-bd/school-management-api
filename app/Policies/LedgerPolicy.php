<?php

namespace App\Policies;

use App\Models\LedgerEntry;
use App\Models\User;

class LedgerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LedgerEntry $ledger): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LedgerEntry $ledger): bool
    {
        return true;
    }

    public function delete(User $user, LedgerEntry $ledger): bool
    {
        return true;
    }
}
