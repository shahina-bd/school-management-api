<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Retrieve paginated users with search and filter parameters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $roleId = $filters['role_id'] ?? null;

        return User::query()
            ->with('role')
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            })
            ->when($roleId, function ($query, $rId) {
                $query->where('role_id', $rId);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new user record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user record.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateUser(User $user, array $data): User
    {
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh('role');
    }

    /**
     * Delete a user record.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }
}
