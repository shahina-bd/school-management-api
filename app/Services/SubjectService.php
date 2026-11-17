<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubjectService
{
    public function getPaginatedSubjects(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $type = $filters['type'] ?? null;

        return Subject::query()
            ->with('classes')
            ->when($type, fn ($query, $t) => $query->where('type', $t))
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createSubject(array $data): Subject
    {
        $subjectData = array_intersect_key($data, array_flip([
            'name',
            'code',
            'type',
        ]));

        return Subject::create($subjectData);
    }

    public function updateSubject(Subject $subject, array $data): Subject
    {
        $subjectData = array_intersect_key($data, array_flip([
            'name',
            'code',
            'type',
        ]));

        $subject->update($subjectData);

        return $subject->fresh('classes');
    }

    public function deleteSubject(Subject $subject): bool
    {
        return (bool) $subject->delete();
    }
}
