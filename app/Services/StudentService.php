<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    /**
     * Retrieve paginated students with search and filter parameters.
     */
    public function getPaginatedStudents(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $status = $filters['status'] ?? null;

        return Student::query()
            ->with(['user', 'primaryGuardian'])
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('admission_number', 'like', "%{$searchTerm}%")
                        ->orWhere('roll_number', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('email', 'like', "%{$searchTerm}%");
                        });
                });
            })
            ->when($status, function ($query, $statusValue) {
                $query->where('status', $statusValue);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new student record.
     */
    public function createStudent(array $data): Student
    {
        if (empty($data['user_id']) && (!empty($data['name']) || !empty($data['first_name']) || !empty($data['email']))) {
            $name = $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            $email = !empty($data['email']) ? $data['email'] : ('student_' . time() . '_' . rand(100, 999) . '@school.com');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
            ]);
            $data['user_id'] = $user->id;
        }

        $studentData = array_intersect_key($data, array_flip([
            'user_id',
            'guardian_id',
            'admission_number',
            'roll_number',
            'dob',
            'gender',
            'blood_group',
            'admission_date',
            'status',
        ]));

        if (isset($data['student_id']) && empty($studentData['admission_number'])) {
            $studentData['admission_number'] = $data['student_id'];
        }

        if (isset($data['date_of_birth']) && empty($studentData['dob'])) {
            $studentData['dob'] = $data['date_of_birth'];
        }

        return Student::create($studentData);
    }

    /**
     * Update an existing student record.
     */
    public function updateStudent(Student $student, array $data): Student
    {
        if ($student->user && (!empty($data['name']) || !empty($data['email']))) {
            $student->user->update(array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]));
        }

        $studentData = array_intersect_key($data, array_flip([
            'user_id',
            'guardian_id',
            'admission_number',
            'roll_number',
            'dob',
            'gender',
            'blood_group',
            'admission_date',
            'status',
        ]));

        if (isset($data['student_id']) && empty($studentData['admission_number'])) {
            $studentData['admission_number'] = $data['student_id'];
        }

        if (isset($data['date_of_birth']) && empty($studentData['dob'])) {
            $studentData['dob'] = $data['date_of_birth'];
        }

        $student->update($studentData);

        return $student->fresh(['user', 'primaryGuardian']);
    }

    /**
     * Delete a student record.
     */
    public function deleteStudent(Student $student): bool
    {
        if ($student->user) {
            $student->user->delete();
        }

        return (bool) $student->delete();
    }
}
