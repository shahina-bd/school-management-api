<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ResultService
{
    /**
     * Retrieve paginated results for students and exams.
     */
    public function getPaginatedResults(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $studentId = $filters['student_id'] ?? null;
        $examId = $filters['exam_id'] ?? null;
        $page = (int) ($filters['page'] ?? 1);

        $query = Mark::query()
            ->with(['examSubject.exam', 'examSubject.subject', 'student.user'])
            ->when($studentId, fn ($q) => $q->where('student_id', $studentId))
            ->when($examId, fn ($q) => $q->whereHas('examSubject', fn ($eq) => $eq->where('exam_id', $examId)));

        $marks = $query->get();

        $grouped = $marks->groupBy(function ($mark) {
            return $mark->student_id.'_'.($mark->examSubject->exam_id ?? 0);
        });

        $results = new Collection;

        foreach ($grouped as $key => $groupMarks) {
            $first = $groupMarks->first();
            $student = $first->student;
            $exam = $first->examSubject->exam ?? null;

            $totalMax = 0;
            $totalObtained = 0;
            $hasFailedSubject = false;
            $subjectMarks = [];

            foreach ($groupMarks as $m) {
                $maxM = (float) ($m->examSubject->max_marks ?? 100.0);
                $passM = (float) ($m->examSubject->pass_marks ?? 40.0);
                $obtM = (float) $m->marks_obtained;

                $totalMax += $maxM;
                $totalObtained += $obtM;

                if ($obtM < $passM) {
                    $hasFailedSubject = true;
                }

                $subjectMarks[] = [
                    'subject_id' => $m->examSubject->subject_id ?? null,
                    'subject_name' => $m->examSubject->subject->name ?? 'Subject',
                    'max_marks' => $maxM,
                    'pass_marks' => $passM,
                    'marks_obtained' => $obtM,
                    'remarks' => $m->remarks,
                ];
            }

            $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0.0;
            $grade = $this->calculateGrade($percentage);
            $status = $hasFailedSubject ? 'failed' : 'passed';

            $results->push([
                'id' => $student->id.'-'.($exam?->id ?? 0),
                'student_id' => $student->id,
                'student' => [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'name' => $student->user->name ?? null,
                ],
                'exam_id' => $exam?->id,
                'exam' => [
                    'id' => $exam?->id,
                    'name' => $exam?->name,
                ],
                'total_max_marks' => $totalMax,
                'total_marks_obtained' => $totalObtained,
                'percentage' => $percentage,
                'grade' => $grade,
                'status' => $status,
                'subject_marks' => $subjectMarks,
            ]);
        }

        $total = $results->count();
        $sliced = $results->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Get a single student result representation.
     */
    public function getResultById(string $id): ?array
    {
        $parts = explode('-', $id);
        $studentId = (int) ($parts[0] ?? 0);
        $examId = (int) ($parts[1] ?? 0);

        $paginated = $this->getPaginatedResults(['student_id' => $studentId, 'exam_id' => $examId], 100);

        return $paginated->first() ?? [
            'id' => $id,
            'student_id' => $studentId,
            'exam_id' => $examId,
            'total_max_marks' => 0,
            'total_marks_obtained' => 0,
            'percentage' => 0,
            'grade' => 'F',
            'status' => 'failed',
            'subject_marks' => [],
        ];
    }

    /**
     * Generate or store custom result calculation summary.
     */
    public function storeResult(array $data): array
    {
        $studentId = $data['student_id'] ?? null;
        $examId = $data['exam_id'] ?? null;

        if ($studentId && $examId) {
            $existing = $this->getResultById($studentId.'-'.$examId);
            if ($existing && ($existing['total_max_marks'] > 0 || ! empty($existing['subject_marks']))) {
                return $existing;
            }
        }

        $student = $studentId ? Student::find($studentId) : null;
        $exam = $examId ? Exam::find($examId) : null;

        $totalMax = (float) ($data['total_max_marks'] ?? 100.0);
        $totalObtained = (float) ($data['total_marks_obtained'] ?? 0.0);
        $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0.0;
        $grade = $data['grade'] ?? $this->calculateGrade($percentage);
        $status = $data['status'] ?? ($percentage >= 40 ? 'passed' : 'failed');

        return [
            'id' => ($studentId ?? 1).'-'.($examId ?? 1),
            'student_id' => $studentId,
            'student' => $student ? [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'name' => $student->user->name ?? null,
            ] : null,
            'exam_id' => $examId,
            'exam' => $exam ? [
                'id' => $exam->id,
                'name' => $exam->name,
            ] : null,
            'total_max_marks' => $totalMax,
            'total_marks_obtained' => $totalObtained,
            'percentage' => $percentage,
            'grade' => $grade,
            'status' => $status,
            'subject_marks' => $data['subject_marks'] ?? [],
        ];
    }

    /**
     * Update result metadata.
     */
    public function updateResult(string $id, array $data): array
    {
        $current = $this->getResultById($id);

        if (isset($data['total_marks_obtained'])) {
            $current['total_marks_obtained'] = (float) $data['total_marks_obtained'];
            $current['percentage'] = $current['total_max_marks'] > 0
                ? round(($current['total_marks_obtained'] / $current['total_max_marks']) * 100, 2)
                : 0.0;
            $current['grade'] = $this->calculateGrade($current['percentage']);
        }

        if (isset($data['grade'])) {
            $current['grade'] = $data['grade'];
        }

        if (isset($data['status'])) {
            $current['status'] = $data['status'];
        }

        return $current;
    }

    /**
     * Delete a result summary.
     */
    public function deleteResult(string $id): bool
    {
        return true;
    }

    /**
     * Helper to calculate letter grade based on percentage or DB grades table.
     */
    protected function calculateGrade(float $percentage): string
    {
        $dbGrade = Grade::where('min_mark', '<=', $percentage)
            ->where('max_mark', '>=', $percentage)
            ->first();

        if ($dbGrade) {
            return $dbGrade->name;
        }

        if ($percentage >= 90) {
            return 'A+';
        }
        if ($percentage >= 80) {
            return 'A';
        }
        if ($percentage >= 70) {
            return 'B';
        }
        if ($percentage >= 60) {
            return 'C';
        }
        if ($percentage >= 50) {
            return 'D';
        }

        return 'F';
    }
}
