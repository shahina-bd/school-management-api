<?php

namespace App\Policies;

use App\Models\ExamSubject;
use App\Models\User;

class ExamSubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ExamSubject $examSubject): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ExamSubject $examSubject): bool
    {
        return true;
    }

    public function delete(User $user, ExamSubject $examSubject): bool
    {
        return true;
    }
}
