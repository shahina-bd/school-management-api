<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSubject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Retrieve paginated exams with search and filter parameters.
     */
    public function getPaginatedExams(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $academicYearId = $filters['academic_year_id'] ?? null;

        return Exam::query()
            ->with(['academicYear', 'examSubjects.subject', 'examSubjects.schoolClass'])
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            })
            ->when($academicYearId, function ($query, $yearId) {
                $query->where('academic_year_id', $yearId);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new exam record, optionally attaching exam subjects.
     */
    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            $examData = array_intersect_key($data, array_flip([
                'name',
                'academic_year_id',
                'start_date',
                'end_date',
            ]));

            $exam = Exam::create($examData);

            if (! empty($data['subjects']) && is_array($data['subjects'])) {
                foreach ($data['subjects'] as $subjectItem) {
                    $exam->examSubjects()->create([
                        'subject_id' => $subjectItem['subject_id'],
                        'class_id' => $subjectItem['class_id'],
                        'pass_marks' => $subjectItem['pass_marks'] ?? 40.00,
                        'max_marks' => $subjectItem['max_marks'] ?? 100.00,
                        'exam_date' => $subjectItem['exam_date'] ?? null,
                    ]);
                }
            }

            return $exam->fresh(['academicYear', 'examSubjects.subject', 'examSubjects.schoolClass']);
        });
    }

    /**
     * Update an existing exam record.
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $examData = array_intersect_key($data, array_flip([
                'name',
                'academic_year_id',
                'start_date',
                'end_date',
            ]));

            $exam->update($examData);

            if (isset($data['subjects']) && is_array($data['subjects'])) {
                $exam->examSubjects()->delete();
                foreach ($data['subjects'] as $subjectItem) {
                    $exam->examSubjects()->create([
                        'subject_id' => $subjectItem['subject_id'],
                        'class_id' => $subjectItem['class_id'],
                        'pass_marks' => $subjectItem['pass_marks'] ?? 40.00,
                        'max_marks' => $subjectItem['max_marks'] ?? 100.00,
                        'exam_date' => $subjectItem['exam_date'] ?? null,
                    ]);
                }
            }

            return $exam->fresh(['academicYear', 'examSubjects.subject', 'examSubjects.schoolClass']);
        });
    }

    /**
     * Delete an exam record.
     */
    public function deleteExam(Exam $exam): bool
    {
        return (bool) $exam->delete();
    }

    /**
     * Add a subject to an exam.
     */
    public function addSubjectToExam(Exam $exam, array $subjectData): ExamSubject
    {
        return $exam->examSubjects()->create([
            'subject_id' => $subjectData['subject_id'],
            'class_id' => $subjectData['class_id'],
            'pass_marks' => $subjectData['pass_marks'] ?? 40.00,
            'max_marks' => $subjectData['max_marks'] ?? 100.00,
            'exam_date' => $subjectData['exam_date'] ?? null,
        ]);
    }

    /**
     * Remove a subject from an exam.
     */
    public function removeSubjectFromExam(ExamSubject $examSubject): bool
    {
        return (bool) $examSubject->delete();
    }
}
