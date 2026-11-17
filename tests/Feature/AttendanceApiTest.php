<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Student $student;

    protected SchoolClass $schoolClass;

    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->student = Student::create([
            'admission_number' => 'STU-101',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $this->schoolClass = SchoolClass::create([
            'name' => 'Class 10',
            'code' => 'C10',
        ]);

        $this->section = Section::create([
            'class_id' => $this->schoolClass->id,
            'name' => 'Section A',
            'capacity' => 30,
        ]);
    }

    public function test_can_list_attendance_records(): void
    {
        AttendanceRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'date' => '2026-10-01',
            'status' => 'present',
            'remarks' => 'On time',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/attendance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'class_id',
                        'section_id',
                        'date',
                        'status',
                        'remarks',
                    ],
                ],
            ]);
    }

    public function test_can_create_attendance_record(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'date' => '2026-10-02',
            'status' => 'present',
            'remarks' => 'Good performance',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/attendance', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'present')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'date' => '2026-10-02',
            'status' => 'present',
        ]);
    }

    public function test_can_show_attendance_record(): void
    {
        $record = AttendanceRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'date' => '2026-10-03',
            'status' => 'absent',
            'remarks' => 'Sick leave',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/attendance/{$record->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $record->id)
            ->assertJsonPath('data.status', 'absent');
    }

    public function test_can_update_attendance_record(): void
    {
        $record = AttendanceRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'date' => '2026-10-04',
            'status' => 'late',
        ]);

        $payload = [
            'status' => 'present',
            'remarks' => 'Corrected status',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/attendance/{$record->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'present')
            ->assertJsonPath('data.remarks', 'Corrected status');

        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'present',
            'remarks' => 'Corrected status',
        ]);
    }

    public function test_can_delete_attendance_record(): void
    {
        $record = AttendanceRecord::create([
            'student_id' => $this->student->id,
            'class_id' => $this->schoolClass->id,
            'date' => '2026-10-05',
            'status' => 'leave',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/attendance/{$record->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('attendance_records', [
            'id' => $record->id,
        ]);
    }
}
