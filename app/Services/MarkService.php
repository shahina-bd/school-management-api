<?php

namespace App\Services;

use App\Models\Mark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarkService
{
    /**
     * Retrieve paginated marks with optional filtering.
     */
    public function getPaginatedMarks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $examSubjectId = $filters['exam_subject_id'] ?? null;
        $studentId = $filters['student_id'] ?? null;
        $examId = $filters['exam_id'] ?? null;

        return Mark::query()
            ->with(['examSubject.exam', 'examSubject.subject', 'examSubject.schoolClass', 'student.user'])
            ->when($examSubjectId, function ($query, $subjectId) {
                $query->where('exam_subject_id', $subjectId);
            })
            ->when($studentId, function ($query, $stId) {
                $query->where('student_id', $stId);
            })
            ->when($examId, function ($query, $exId) {
                $query->whereHas('examSubject', function ($q) use ($exId) {
                    $q->where('exam_id', $exId);
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new mark record.
     */
    public function createMark(array $data): Mark
    {
        $markData = array_intersect_key($data, array_flip([
            'exam_subject_id',
            'student_id',
            'marks_obtained',
            'remarks',
        ]));

        $mark = Mark::create($markData);

        return $mark->fresh(['examSubject.exam', 'examSubject.subject', 'student.user']);
    }

    /**
     * Update an existing mark record.
     */
    public function updateMark(Mark $mark, array $data): Mark
    {
        $markData = array_intersect_key($data, array_flip([
            'exam_subject_id',
            'student_id',
            'marks_obtained',
            'remarks',
        ]));

        $mark->update($markData);

        return $mark->fresh(['examSubject.exam', 'examSubject.subject', 'student.user']);
    }

    /**
     * Delete a mark record.
     */
    public function deleteMark(Mark $mark): bool
    {
        return (bool) $mark->delete();
    }
}
