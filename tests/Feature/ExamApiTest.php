<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_exams(): void
    {
        Exam::create([
            'name' => 'Midterm 2026',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-10',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/exams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'start_date',
                        'end_date',
                    ],
                ],
            ]);
    }

    public function test_can_create_exam(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $class = SchoolClass::create(['name' => 'Class 10', 'code' => 'C10']);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH10']);

        $payload = [
            'name' => 'Final Examination',
            'academic_year_id' => $academicYear->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-15',
            'subjects' => [
                [
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'pass_marks' => 40.00,
                    'max_marks' => 100.00,
                    'exam_date' => '2026-11-02',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/exams', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Final Examination')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('exams', [
            'name' => 'Final Examination',
            'academic_year_id' => $academicYear->id,
        ]);

        $this->assertDatabaseHas('exam_subjects', [
            'subject_id' => $subject->id,
            'class_id' => $class->id,
        ]);
    }

    public function test_can_show_exam(): void
    {
        $exam = Exam::create([
            'name' => 'First Term Exam',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-10',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/exams/{$exam->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $exam->id)
            ->assertJsonPath('data.name', 'First Term Exam');
    }

    public function test_can_update_exam(): void
    {
        $exam = Exam::create([
            'name' => 'Old Exam Name',
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-10',
        ]);

        $payload = [
            'name' => 'Updated Exam Name',
            'start_date' => '2026-05-02',
            'end_date' => '2026-05-12',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/exams/{$exam->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Exam Name');

        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'name' => 'Updated Exam Name',
        ]);
    }

    public function test_can_delete_exam(): void
    {
        $exam = Exam::create([
            'name' => 'Exam to Delete',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/exams/{$exam->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('exams', [
            'id' => $exam->id,
        ]);
    }
}
