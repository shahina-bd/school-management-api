<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Guardian;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_belongs_to_user_and_primary_guardian(): void
    {
        $user = User::factory()->create();
        $guardian = Guardian::create([
            'father_name' => 'John Doe',
            'phone' => '1234567890',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'guardian_id' => $guardian->id,
            'admission_number' => 'ADM001',
            'gender' => 'female',
            'dob' => '2010-01-01',
        ]);

        $this->assertTrue($student->user->is($user));
        $this->assertTrue($student->primaryGuardian->is($guardian));
        $this->assertTrue($user->student->is($student));
    }

    public function test_student_belongs_to_many_guardians_with_pivot(): void
    {
        $student = Student::create([
            'admission_number' => 'ADM002',
            'gender' => 'female',
            'dob' => '2012-05-15',
        ]);

        $guardian = Guardian::create([
            'father_name' => 'Robert Smith',
            'phone' => '0987654321',
        ]);

        $student->guardians()->attach($guardian->id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);

        $this->assertCount(1, $student->guardians);
        $this->assertEquals('Father', $student->guardians->first()->pivot->relationship);
        $this->assertTrue((bool) $student->guardians->first()->pivot->is_primary);
    }

    public function test_student_has_many_enrollments_attendance_fees_feepayments_and_marks(): void
    {
        $student = Student::create([
            'admission_number' => 'ADM003',
            'gender' => 'male',
            'dob' => '2011-03-20',
        ]);

        $class = SchoolClass::create(['name' => 'Class 10', 'code' => 'C10']);
        $section = Section::create(['class_id' => $class->id, 'name' => 'A']);
        $academicYear = AcademicYear::create(['name' => '2026-2027', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);

        // Enrollment
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'roll_number' => '101',
        ]);

        // Attendance Record
        $attendance = AttendanceRecord::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'date' => '2026-03-01',
            'status' => 'present',
        ]);

        // Fee and Fee Payment
        $feeType = FeeType::create(['name' => 'Tuition Fee']);
        $studentFee = StudentFee::create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'amount' => 500.00,
            'due_date' => '2026-04-01',
            'status' => 'unpaid',
        ]);

        $feePayment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'amount_paid' => 500.00,
            'payment_date' => '2026-03-15',
            'payment_method' => 'cash',
        ]);

        // Exam, ExamSubject and Mark
        $exam = Exam::create(['name' => 'Final Exam', 'academic_year_id' => $academicYear->id]);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH101']);
        $examSubject = ExamSubject::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'pass_marks' => 40.00,
            'max_marks' => 100.00,
        ]);
        $mark = Mark::create([
            'exam_subject_id' => $examSubject->id,
            'student_id' => $student->id,
            'marks_obtained' => 95.00,
        ]);

        $this->assertCount(1, $student->enrollments);
        $this->assertTrue($student->enrollments->contains($enrollment));

        $this->assertCount(1, $student->attendanceRecords);
        $this->assertTrue($student->attendanceRecords->contains($attendance));

        $this->assertCount(1, $student->fees);
        $this->assertTrue($student->fees->contains($studentFee));

        $this->assertCount(1, $student->feePayments);
        $this->assertTrue($student->feePayments->contains($feePayment));

        $this->assertCount(1, $student->marks);
        $this->assertTrue($student->marks->contains($mark));
    }
}
