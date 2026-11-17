<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventService
{
    /**
     * Retrieve paginated events with optional filtering.
     */
    public function getPaginatedEvents(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        return Event::query()
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('location', 'like', "%{$searchTerm}%");
                });
            })
            ->when($fromDate, function ($query, $date) {
                $query->where('start_date', '>=', $date);
            })
            ->when($toDate, function ($query, $date) {
                $query->where('start_date', '<=', $date);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new event record.
     */
    public function createEvent(array $data): Event
    {
        $eventData = array_intersect_key($data, array_flip([
            'title',
            'description',
            'start_date',
            'end_date',
            'location',
        ]));

        return Event::create($eventData);
    }

    /**
     * Update an existing event record.
     */
    public function updateEvent(Event $event, array $data): Event
    {
        $eventData = array_intersect_key($data, array_flip([
            'title',
            'description',
            'start_date',
            'end_date',
            'location',
        ]));

        $event->update($eventData);

        return $event->fresh();
    }

    /**
     * Delete an event record.
     */
    public function deleteEvent(Event $event): bool
    {
        return (bool) $event->delete();
    }
}
