<?php

namespace App\Services;

use App\Models\SchoolClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SchoolClassService
{
    public function getPaginatedClasses(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        return SchoolClass::query()
            ->with(['sections', 'subjects'])
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createClass(array $data): SchoolClass
    {
        $classData = array_intersect_key($data, array_flip([
            'name',
            'code',
            'description',
        ]));

        return SchoolClass::create($classData);
    }

    public function updateClass(SchoolClass $schoolClass, array $data): SchoolClass
    {
        $classData = array_intersect_key($data, array_flip([
            'name',
            'code',
            'description',
        ]));

        $schoolClass->update($classData);

        return $schoolClass->fresh(['sections', 'subjects']);
    }

    public function deleteClass(SchoolClass $schoolClass): bool
    {
        return (bool) $schoolClass->delete();
    }
}
