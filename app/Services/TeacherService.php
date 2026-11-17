<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    /**
     * Retrieve paginated teachers with search and filter parameters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPaginatedTeachers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $designation = $filters['designation'] ?? null;

        return Teacher::query()
            ->with('user')
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('employee_id', 'like', "%{$searchTerm}%")
                        ->orWhere('designation', 'like', "%{$searchTerm}%")
                        ->orWhere('qualification', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('email', 'like', "%{$searchTerm}%");
                        });
                });
            })
            ->when($designation, function ($query, $desig) {
                $query->where('designation', $desig);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new teacher record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createTeacher(array $data): Teacher
    {
        if (empty($data['user_id']) && (!empty($data['name']) || !empty($data['email']))) {
            $name = $data['name'] ?? 'Teacher';
            $email = !empty($data['email']) ? $data['email'] : ('teacher_' . time() . '_' . rand(100, 999) . '@school.com');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
            ]);
            $data['user_id'] = $user->id;
        }

        $teacherData = array_intersect_key($data, array_flip([
            'user_id',
            'employee_id',
            'designation',
            'qualification',
            'joining_date',
            'salary',
            'phone',
        ]));

        return Teacher::create($teacherData);
    }

    /**
     * Update an existing teacher record.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateTeacher(Teacher $teacher, array $data): Teacher
    {
        if ($teacher->user && (!empty($data['name']) || !empty($data['email']))) {
            $teacher->user->update(array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]));
        }

        $teacherData = array_intersect_key($data, array_flip([
            'user_id',
            'employee_id',
            'designation',
            'qualification',
            'joining_date',
            'salary',
            'phone',
        ]));

        $teacher->update($teacherData);

        return $teacher->fresh('user');
    }

    /**
     * Delete a teacher record.
     */
    public function deleteTeacher(Teacher $teacher): bool
    {
        if ($teacher->user) {
            $teacher->user->delete();
        }

        return (bool) $teacher->delete();
    }
}
