<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceService
{
    /**
     * Retrieve paginated attendance records with search and filter parameters.
     */
    public function getPaginatedAttendance(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $studentId = $filters['student_id'] ?? null;
        $classId = $filters['class_id'] ?? null;
        $sectionId = $filters['section_id'] ?? null;
        $date = $filters['date'] ?? null;
        $status = $filters['status'] ?? null;

        return AttendanceRecord::query()
            ->with(['student.user', 'schoolClass', 'section'])
            ->when($studentId, fn ($query, $val) => $query->where('student_id', $val))
            ->when($classId, fn ($query, $val) => $query->where('class_id', $val))
            ->when($sectionId, fn ($query, $val) => $query->where('section_id', $val))
            ->when($date, fn ($query, $val) => $query->where('date', $val))
            ->when($status, fn ($query, $val) => $query->where('status', $val))
            ->latest('date')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Create a new attendance record.
     */
    public function createAttendance(array $data): AttendanceRecord
    {
        $attendanceData = array_intersect_key($data, array_flip([
            'student_id',
            'class_id',
            'section_id',
            'date',
            'status',
            'remarks',
        ]));

        return AttendanceRecord::create($attendanceData);
    }

    /**
     * Update an existing attendance record.
     */
    public function updateAttendance(AttendanceRecord $attendanceRecord, array $data): AttendanceRecord
    {
        $attendanceData = array_intersect_key($data, array_flip([
            'student_id',
            'class_id',
            'section_id',
            'date',
            'status',
            'remarks',
        ]));

        $attendanceRecord->update($attendanceData);

        return $attendanceRecord->fresh(['student.user', 'schoolClass', 'section']);
    }

    /**
     * Delete an attendance record.
     */
    public function deleteAttendance(AttendanceRecord $attendanceRecord): bool
    {
        return (bool) $attendanceRecord->delete();
    }
}
