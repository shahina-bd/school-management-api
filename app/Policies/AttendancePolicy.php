<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any attendance records.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the attendance record.
     */
    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create attendance records.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the attendance record.
     */
    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the attendance record.
     */
    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return true;
    }
}
