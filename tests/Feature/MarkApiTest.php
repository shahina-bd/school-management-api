<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ExamSubject $examSubject;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $exam = Exam::create(['name' => 'Midterm 2026']);
        $class = SchoolClass::create(['name' => 'Class 8', 'code' => 'C8']);
        $subject = Subject::create(['name' => 'English', 'code' => 'ENG']);

        $this->examSubject = ExamSubject::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'pass_marks' => 40.00,
            'max_marks' => 100.00,
        ]);

        $this->student = Student::create([
            'admission_number' => 'STU-1001',
            'gender' => 'female',
            'status' => 'active',
        ]);
    }

    public function test_can_list_marks(): void
    {
        Mark::create([
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 85.50,
            'remarks' => 'Excellent performance',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/marks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'exam_subject_id',
                        'student_id',
                        'marks_obtained',
                        'remarks',
                    ],
                ],
            ]);
    }

    public function test_can_create_mark(): void
    {
        $payload = [
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 92.00,
            'remarks' => 'Outstanding',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/marks', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.marks_obtained', 92)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 92.00,
        ]);
    }

    public function test_can_show_mark(): void
    {
        $mark = Mark::create([
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 78.00,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/marks/{$mark->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $mark->id)
            ->assertJsonPath('data.marks_obtained', 78);
    }

    public function test_can_update_mark(): void
    {
        $mark = Mark::create([
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 60.00,
        ]);

        $payload = [
            'marks_obtained' => 75.00,
            'remarks' => 'Re-evaluated',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/marks/{$mark->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.marks_obtained', 75);

        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'marks_obtained' => 75.00,
            'remarks' => 'Re-evaluated',
        ]);
    }

    public function test_can_delete_mark(): void
    {
        $mark = Mark::create([
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 50.00,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/marks/{$mark->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('marks', [
            'id' => $mark->id,
        ]);
    }
}
