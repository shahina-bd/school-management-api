<?php

namespace App\Services;

use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SectionService
{
    public function getPaginatedSections(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $classId = $filters['class_id'] ?? null;

        return Section::query()
            ->with('schoolClass')
            ->when($classId, fn ($query, $id) => $query->where('class_id', $id))
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createSection(array $data): Section
    {
        $sectionData = array_intersect_key($data, array_flip([
            'class_id',
            'name',
            'capacity',
        ]));

        return Section::create($sectionData);
    }

    public function updateSection(Section $section, array $data): Section
    {
        $sectionData = array_intersect_key($data, array_flip([
            'class_id',
            'name',
            'capacity',
        ]));

        $section->update($sectionData);

        return $section->fresh('schoolClass');
    }

    public function deleteSection(Section $section): bool
    {
        return (bool) $section->delete();
    }
}
