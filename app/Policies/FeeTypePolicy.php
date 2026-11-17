<?php

namespace App\Policies;

use App\Models\FeeType;
use App\Models\User;

class FeeTypePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FeeType $feeType): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FeeType $feeType): bool
    {
        return true;
    }

    public function delete(User $user, FeeType $feeType): bool
    {
        return true;
    }
}
