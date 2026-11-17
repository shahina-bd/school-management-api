<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_students(): void
    {
        Student::create([
            'admission_number' => 'STU-001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'admission_number',
                        'student_id',
                        'gender',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_can_create_student(): void
    {
        $payload = [
            'student_id' => 'STU-002',
            'gender' => 'female',
            'dob' => '2012-05-10',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/students', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.admission_number', 'STU-002')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('students', [
            'admission_number' => 'STU-002',
            'gender' => 'female',
        ]);
    }

    public function test_can_show_student(): void
    {
        $student = Student::create([
            'admission_number' => 'STU-003',
            'gender' => 'other',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $student->id)
            ->assertJsonPath('data.admission_number', 'STU-003');
    }

    public function test_can_update_student(): void
    {
        $student = Student::create([
            'admission_number' => 'STU-004',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $payload = [
            'student_id' => 'STU-004',
            'gender' => 'female',
            'status' => 'graduated',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/students/{$student->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.gender', 'female')
            ->assertJsonPath('data.status', 'graduated');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'graduated',
        ]);
    }

    public function test_can_delete_student(): void
    {
        $student = Student::create([
            'admission_number' => 'STU-005',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }
}
