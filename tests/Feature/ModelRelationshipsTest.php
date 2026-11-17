<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Guardian;
use App\Models\LedgerEntry;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_year_relationships(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);
        $class = SchoolClass::create(['name' => 'Class 1', 'code' => 'C1']);
        $student = Student::create(['admission_number' => 'ADM100', 'gender' => 'male', 'dob' => '2015-01-01']);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $exam = Exam::create([
            'name' => 'Midterm',
            'academic_year_id' => $academicYear->id,
        ]);

        $this->assertCount(1, $academicYear->enrollments);
        $this->assertTrue($academicYear->enrollments->contains($enrollment));
        $this->assertCount(1, $academicYear->exams);
        $this->assertTrue($academicYear->exams->contains($exam));
    }

    public function test_school_class_and_section_relationships(): void
    {
        $class = SchoolClass::create(['name' => 'Class 5', 'code' => 'C5']);
        $section = Section::create(['class_id' => $class->id, 'name' => 'Section A']);
        $subject = Subject::create(['name' => 'Science', 'code' => 'SCI']);

        $class->subjects()->attach($subject->id);

        $this->assertCount(1, $class->sections);
        $this->assertTrue($class->sections->contains($section));
        $this->assertTrue($section->schoolClass->is($class));

        $this->assertCount(1, $class->subjects);
        $this->assertTrue($class->subjects->contains($subject));
        $this->assertTrue($subject->classes->contains($class));
    }

    public function test_expense_category_expenses_and_ledger_entries(): void
    {
        $category = ExpenseCategory::create(['name' => 'Supplies']);

        $expense = Expense::create([
            'expense_category_id' => $category->id,
            'title' => 'Stationery',
            'amount' => 150.00,
            'date' => '2026-02-01',
        ]);

        $ledgerEntry = LedgerEntry::create([
            'expense_category_id' => $category->id,
            'type' => 'debit',
            'amount' => 150.00,
            'entry_date' => '2026-02-01',
            'account_name' => 'Office Expense',
        ]);

        $this->assertCount(1, $category->expenses);
        $this->assertTrue($category->expenses->contains($expense));
        $this->assertTrue($expense->expenseCategory->is($category));

        $this->assertCount(1, $category->ledgerEntries);
        $this->assertTrue($category->ledgerEntries->contains($ledgerEntry));
        $this->assertTrue($ledgerEntry->expenseCategory->is($category));
    }

    public function test_user_relationships_with_teacher_staff_guardian(): void
    {
        $userTeacher = User::factory()->create();
        $teacher = Teacher::create(['user_id' => $userTeacher->id, 'employee_id' => 'TCH001']);

        $userStaff = User::factory()->create();
        $staff = Staff::create(['user_id' => $userStaff->id, 'employee_id' => 'STF001']);

        $userGuardian = User::factory()->create();
        $guardian = Guardian::create(['user_id' => $userGuardian->id, 'father_name' => 'David Miller', 'phone' => '1122334455']);

        $this->assertTrue($userTeacher->teacher->is($teacher));
        $this->assertTrue($teacher->user->is($userTeacher));

        $this->assertTrue($userStaff->staff->is($staff));
        $this->assertTrue($staff->user->is($userStaff));

        $this->assertTrue($userGuardian->guardian->is($guardian));
        $this->assertTrue($guardian->user->is($userGuardian));
    }

    public function test_fee_type_and_student_fee_and_fee_payment_relationships(): void
    {
        $student = Student::create(['admission_number' => 'ADM101', 'gender' => 'male', 'dob' => '2014-07-10']);
        $feeType = FeeType::create(['name' => 'Library Fee']);
        $studentFee = StudentFee::create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'amount' => 100.00,
            'due_date' => '2026-05-01',
        ]);
        $payment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'amount_paid' => 100.00,
            'payment_date' => '2026-04-20',
        ]);

        $this->assertCount(1, $feeType->studentFees);
        $this->assertTrue($feeType->studentFees->contains($studentFee));
        $this->assertTrue($studentFee->feeType->is($feeType));
        $this->assertTrue($studentFee->student->is($student));

        $this->assertCount(1, $studentFee->payments);
        $this->assertTrue($studentFee->payments->contains($payment));
        $this->assertTrue($payment->studentFee->is($studentFee));
    }
}
