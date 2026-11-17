<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class RoleService
{
    /**
     * Retrieve paginated roles with search and filter parameters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedRoles(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $isActive = $filters['is_active'] ?? null;

        return Role::query()
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('slug', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            })
            ->when(! is_null($isActive), function ($query) use ($isActive) {
                $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new role record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createRole(array $data): Role
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Role::create($data);
    }

    /**
     * Update an existing role record.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateRole(Role $role, array $data): Role
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $role->update($data);

        return $role->fresh();
    }

    /**
     * Delete a role record.
     */
    public function deleteRole(Role $role): bool
    {
        return (bool) $role->delete();
    }
}
