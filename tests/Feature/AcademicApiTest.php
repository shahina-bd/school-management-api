<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_academic_years(): void
    {
        AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_current' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/academics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'start_date',
                        'end_date',
                        'is_current',
                    ],
                ],
            ]);
    }

    public function test_can_create_academic_year(): void
    {
        $payload = [
            'name' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_current' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/academics', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', '2026-2027')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('academic_years', [
            'name' => '2026-2027',
            'is_current' => 1,
        ]);
    }

    public function test_can_show_academic_year(): void
    {
        $year = AcademicYear::create([
            'name' => '2027-2028',
            'start_date' => '2027-09-01',
            'end_date' => '2028-06-30',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/academics/{$year->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $year->id)
            ->assertJsonPath('data.name', '2027-2028');
    }

    public function test_can_update_academic_year(): void
    {
        $year = AcademicYear::create([
            'name' => '2028-2029',
            'start_date' => '2028-09-01',
            'end_date' => '2029-06-30',
        ]);

        $payload = [
            'name' => '2028-2029 Updated',
            'is_current' => true,
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/academics/{$year->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', '2028-2029 Updated');

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'name' => '2028-2029 Updated',
        ]);
    }

    public function test_can_delete_academic_year(): void
    {
        $year = AcademicYear::create([
            'name' => '2029-2030',
            'start_date' => '2029-09-01',
            'end_date' => '2030-06-30',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/academics/{$year->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('academic_years', [
            'id' => $year->id,
        ]);
    }

    public function test_can_manage_classes(): void
    {
        $payload = [
            'name' => 'Grade 10',
            'code' => 'G10',
            'description' => 'Tenth Grade',
        ];

        $createRes = $this->actingAs($this->user)
            ->postJson('/api/v1/classes', $payload);

        $createRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Grade 10');

        $classId = $createRes->json('data.id');

        $this->getJson('/api/v1/classes')->assertStatus(200);
        $this->getJson("/api/v1/classes/{$classId}")->assertStatus(200);

        $this->putJson("/api/v1/classes/{$classId}", ['name' => 'Grade 10 Senior'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Grade 10 Senior');

        $this->deleteJson("/api/v1/classes/{$classId}")
            ->assertStatus(200);
    }

    public function test_can_manage_sections(): void
    {
        $schoolClass = SchoolClass::create(['name' => 'Grade 9']);

        $payload = [
            'class_id' => $schoolClass->id,
            'name' => 'Section B',
            'capacity' => 25,
        ];

        $createRes = $this->actingAs($this->user)
            ->postJson('/api/v1/sections', $payload);

        $createRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Section B');

        $sectionId = $createRes->json('data.id');

        $this->getJson('/api/v1/sections')->assertStatus(200);
        $this->getJson("/api/v1/sections/{$sectionId}")->assertStatus(200);

        $this->putJson("/api/v1/sections/{$sectionId}", ['capacity' => 35])
            ->assertStatus(200)
            ->assertJsonPath('data.capacity', 35);

        $this->deleteJson("/api/v1/sections/{$sectionId}")
            ->assertStatus(200);
    }

    public function test_can_manage_subjects(): void
    {
        $payload = [
            'name' => 'Physics',
            'code' => 'PHY101',
            'type' => 'theory',
        ];

        $createRes = $this->actingAs($this->user)
            ->postJson('/api/v1/subjects', $payload);

        $createRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Physics');

        $subjectId = $createRes->json('data.id');

        $this->getJson('/api/v1/subjects')->assertStatus(200);
        $this->getJson("/api/v1/subjects/{$subjectId}")->assertStatus(200);

        $this->putJson("/api/v1/subjects/{$subjectId}", ['type' => 'practical'])
            ->assertStatus(200)
            ->assertJsonPath('data.type', 'practical');

        $this->deleteJson("/api/v1/subjects/{$subjectId}")
            ->assertStatus(200);
    }
}
