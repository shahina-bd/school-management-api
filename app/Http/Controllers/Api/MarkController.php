<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Resources\MarkResource;
use App\Models\Mark;
use App\Services\MarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class MarkController extends Controller
{
    public function __construct(
        protected MarkService $markService
    ) {}

    /**
     * Display a listing of marks.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Mark::class);

        $perPage = (int) $request->query('per_page', 10);
        $marks = $this->markService->getPaginatedMarks($request->all(), $perPage);

        return MarkResource::collection($marks);
    }

    /**
     * Store a newly created mark.
     */
    public function store(StoreMarkRequest $request): JsonResponse
    {
        Gate::authorize('create', Mark::class);

        $mark = $this->markService->createMark($request->validated());

        return (new MarkResource($mark))
            ->additional([
                'success' => true,
                'message' => 'Mark recorded successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified mark.
     */
    public function show(Mark $mark): MarkResource
    {
        Gate::authorize('view', $mark);

        $mark->load(['examSubject.exam', 'examSubject.subject', 'student.user']);

        return (new MarkResource($mark))
            ->additional([
                'success' => true,
                'message' => 'Mark retrieved successfully.',
            ]);
    }

    /**
     * Update the specified mark in storage.
     */
    public function update(UpdateMarkRequest $request, Mark $mark): MarkResource
    {
        Gate::authorize('update', $mark);

        $updatedMark = $this->markService->updateMark($mark, $request->validated());

        return (new MarkResource($updatedMark))
            ->additional([
                'success' => true,
                'message' => 'Mark updated successfully.',
            ]);
    }

    /**
     * Remove the specified mark from storage.
     */
    public function destroy(Mark $mark): JsonResponse
    {
        Gate::authorize('delete', $mark);

        $this->markService->deleteMark($mark);

        return response()->json([
            'success' => true,
            'message' => 'Mark deleted successfully.',
        ]);
    }
}
