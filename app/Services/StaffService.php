<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    /**
     * Retrieve paginated staff with search and filter parameters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedStaff(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $department = $filters['department'] ?? null;

        return Staff::query()
            ->with('user')
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('employee_id', 'like', "%{$searchTerm}%")
                        ->orWhere('department', 'like', "%{$searchTerm}%")
                        ->orWhere('designation', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('email', 'like', "%{$searchTerm}%");
                        });
                });
            })
            ->when($department, function ($query, $dept) {
                $query->where('department', $dept);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new staff record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createStaff(array $data): Staff
    {
        if (empty($data['user_id']) && (!empty($data['name']) || !empty($data['email']))) {
            $name = $data['name'] ?? 'Staff Member';
            $email = !empty($data['email']) ? $data['email'] : ('staff_' . time() . '_' . rand(100, 999) . '@school.com');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
            ]);
            $data['user_id'] = $user->id;
        }

        $staffData = array_intersect_key($data, array_flip([
            'user_id',
            'employee_id',
            'department',
            'designation',
            'joining_date',
            'salary',
            'phone',
        ]));

        return Staff::create($staffData);
    }

    /**
     * Update an existing staff record.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateStaff(Staff $staff, array $data): Staff
    {
        if ($staff->user && (!empty($data['name']) || !empty($data['email']))) {
            $staff->user->update(array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]));
        }

        $staffData = array_intersect_key($data, array_flip([
            'user_id',
            'employee_id',
            'department',
            'designation',
            'joining_date',
            'salary',
            'phone',
        ]));

        $staff->update($staffData);

        return $staff->fresh('user');
    }

    /**
     * Delete a staff record.
     */
    public function deleteStaff(Staff $staff): bool
    {
        if ($staff->user) {
            $staff->user->delete();
        }

        return (bool) $staff->delete();
    }
}
