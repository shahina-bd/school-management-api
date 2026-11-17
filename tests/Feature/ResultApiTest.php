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

class ResultApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Exam $exam;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->exam = Exam::create(['name' => 'Annual Exam 2026']);
        $class = SchoolClass::create(['name' => 'Class 9', 'code' => 'C9']);
        $subject = Subject::create(['name' => 'Physics', 'code' => 'PHY']);

        $examSubject = ExamSubject::create([
            'exam_id' => $this->exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'pass_marks' => 40.00,
            'max_marks' => 100.00,
        ]);

        $this->student = Student::create([
            'admission_number' => 'STU-2001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        Mark::create([
            'exam_subject_id' => $examSubject->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 88.00,
        ]);
    }

    public function test_can_list_results(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/results');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'student_id',
                        'exam_id',
                        'total_max_marks',
                        'total_marks_obtained',
                        'percentage',
                        'grade',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_can_create_result(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'total_max_marks' => 100.00,
            'total_marks_obtained' => 88.00,
            'status' => 'passed',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/results', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.total_marks_obtained', 88)
            ->assertJsonPath('success', true);
    }

    public function test_can_show_result(): void
    {
        $resultId = "{$this->student->id}-{$this->exam->id}";

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/results/{$resultId}");

        $response->assertStatus(200)
            ->assertJsonPath('data.student_id', $this->student->id)
            ->assertJsonPath('data.exam_id', $this->exam->id);
    }

    public function test_can_update_result(): void
    {
        $resultId = "{$this->student->id}-{$this->exam->id}";

        $payload = [
            'total_marks_obtained' => 95.00,
            'grade' => 'A+',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/results/{$resultId}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.grade', 'A+');
    }

    public function test_can_delete_result(): void
    {
        $resultId = "{$this->student->id}-{$this->exam->id}";

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/results/{$resultId}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
