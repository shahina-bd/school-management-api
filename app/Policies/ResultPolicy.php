<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Result $result = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ?Result $result = null): bool
    {
        return true;
    }

    public function delete(User $user, ?Result $result = null): bool
    {
        return true;
    }
}
