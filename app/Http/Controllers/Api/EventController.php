<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * Display a listing of events.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Event::class);

        $perPage = (int) $request->query('per_page', 10);
        $events = $this->eventService->getPaginatedEvents($request->all(), $perPage);

        return EventResource::collection($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        Gate::authorize('create', Event::class);

        $event = $this->eventService->createEvent($request->validated());

        return (new EventResource($event))
            ->additional([
                'success' => true,
                'message' => 'Event created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): EventResource
    {
        Gate::authorize('view', $event);

        return (new EventResource($event))
            ->additional([
                'success' => true,
                'message' => 'Event retrieved successfully.',
            ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        Gate::authorize('update', $event);

        $updatedEvent = $this->eventService->updateEvent($event, $request->validated());

        return (new EventResource($updatedEvent))
            ->additional([
                'success' => true,
                'message' => 'Event updated successfully.',
            ]);
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        Gate::authorize('delete', $event);

        $this->eventService->deleteEvent($event);

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully.',
        ]);
    }
}
