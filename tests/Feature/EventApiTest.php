<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_events(): void
    {
        Event::create([
            'title' => 'Annual Sports Day',
            'description' => 'Track and field events.',
            'start_date' => '2026-11-10 09:00:00',
            'end_date' => '2026-11-10 17:00:00',
            'location' => 'School Ground',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'start_date',
                        'end_date',
                        'location',
                    ],
                ],
            ]);
    }

    public function test_can_create_event(): void
    {
        $payload = [
            'title' => 'Science Exhibition',
            'description' => 'Students project displays.',
            'start_date' => '2026-12-01 10:00:00',
            'end_date' => '2026-12-01 16:00:00',
            'location' => 'Main Auditorium',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/events', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Science Exhibition')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('events', [
            'title' => 'Science Exhibition',
            'location' => 'Main Auditorium',
        ]);
    }

    public function test_can_show_event(): void
    {
        $event = Event::create([
            'title' => 'Music Concert',
            'start_date' => '2026-10-20 18:00:00',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $event->id)
            ->assertJsonPath('data.title', 'Music Concert');
    }

    public function test_can_update_event(): void
    {
        $event = Event::create([
            'title' => 'Draft Event',
            'start_date' => '2026-10-25 10:00:00',
        ]);

        $payload = [
            'title' => 'Updated Event Title',
            'location' => 'Library Hall',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/events/{$event->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Event Title');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
            'location' => 'Library Hall',
        ]);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::create([
            'title' => 'Cancelled Event',
            'start_date' => '2026-10-30 10:00:00',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
