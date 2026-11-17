<?php

namespace App\Policies;

use App\Models\StudentFee;
use App\Models\User;

class StudentFeePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StudentFee $fee): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StudentFee $fee): bool
    {
        return true;
    }

    public function delete(User $user, StudentFee $fee): bool
    {
        return true;
    }
}
