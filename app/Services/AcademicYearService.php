<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AcademicYearService
{
    public function getPaginatedAcademicYears(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $isCurrent = $filters['is_current'] ?? null;

        return AcademicYear::query()
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            })
            ->when($isCurrent !== null, function ($query) use ($isCurrent) {
                $query->where('is_current', filter_var($isCurrent, FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createAcademicYear(array $data): AcademicYear
    {
        $yearData = array_intersect_key($data, array_flip([
            'name',
            'start_date',
            'end_date',
            'is_current',
        ]));

        if (! empty($yearData['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        return AcademicYear::create($yearData);
    }

    public function updateAcademicYear(AcademicYear $academicYear, array $data): AcademicYear
    {
        $yearData = array_intersect_key($data, array_flip([
            'name',
            'start_date',
            'end_date',
            'is_current',
        ]));

        if (! empty($yearData['is_current'])) {
            AcademicYear::query()->where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        }

        $academicYear->update($yearData);

        return $academicYear->fresh();
    }

    public function deleteAcademicYear(AcademicYear $academicYear): bool
    {
        return (bool) $academicYear->delete();
    }
}
