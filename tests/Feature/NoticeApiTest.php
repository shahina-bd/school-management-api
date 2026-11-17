<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_notices(): void
    {
        Notice::create([
            'title' => 'School Holiday Announcement',
            'content' => 'School will remain closed on Monday.',
            'target_audience' => 'all',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/notices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'target_audience',
                    ],
                ],
            ]);
    }

    public function test_can_create_notice(): void
    {
        $payload = [
            'title' => 'Parent Teacher Meeting',
            'content' => 'PTM will be held on Friday at 10 AM.',
            'target_audience' => 'parents',
            'published_at' => '2026-10-05 09:00:00',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/notices', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Parent Teacher Meeting')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('notices', [
            'title' => 'Parent Teacher Meeting',
            'target_audience' => 'parents',
        ]);
    }

    public function test_can_show_notice(): void
    {
        $notice = Notice::create([
            'title' => 'Exam Schedule Released',
            'content' => 'Please check the examination portal for details.',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/notices/{$notice->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $notice->id)
            ->assertJsonPath('data.title', 'Exam Schedule Released');
    }

    public function test_can_update_notice(): void
    {
        $notice = Notice::create([
            'title' => 'Original Notice Title',
            'content' => 'Original content',
        ]);

        $payload = [
            'title' => 'Updated Notice Title',
            'content' => 'Updated notice content body.',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/notices/{$notice->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Notice Title');

        $this->assertDatabaseHas('notices', [
            'id' => $notice->id,
            'title' => 'Updated Notice Title',
        ]);
    }

    public function test_can_delete_notice(): void
    {
        $notice = Notice::create([
            'title' => 'Notice to delete',
            'content' => 'Content to delete',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/notices/{$notice->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('notices', [
            'id' => $notice->id,
        ]);
    }
}
