<?php

namespace App\Policies;

use App\Models\FeePayment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FeePayment $payment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FeePayment $payment): bool
    {
        return true;
    }

    public function delete(User $user, FeePayment $payment): bool
    {
        return true;
    }
}
