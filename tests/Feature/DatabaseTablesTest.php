<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_required_database_tables_exist(): void
    {
        $expectedTables = [
            // Authentication
            'users',
            'roles',
            'permissions',
            'role_permission',

            // Academic
            'academic_years',
            'classes',
            'sections',
            'subjects',
            'class_subjects',

            // People
            'guardians',
            'students',
            'student_guardian',
            'teachers',
            'staff',

            // Enrollment
            'enrollments',

            // Attendance
            'attendance_records',

            // Fees
            'fee_types',
            'student_fees',
            'fee_payments',

            // Accounting
            'expense_categories',
            'expenses',
            'ledger_entries',

            // Exams
            'exams',
            'exam_subjects',
            'marks',
            'grades',

            // Communication
            'notices',
            'events',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Failed asserting that table [{$table}] exists in the database."
            );
        }
    }
}
