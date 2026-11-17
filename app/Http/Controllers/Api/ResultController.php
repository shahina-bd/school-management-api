<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\Result;
use App\Services\ResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ResultController extends Controller
{
    public function __construct(
        protected ResultService $resultService
    ) {}

    /**
     * Display a listing of results.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Result::class);

        $perPage = (int) $request->query('per_page', 10);
        $results = $this->resultService->getPaginatedResults($request->all(), $perPage);

        return ResultResource::collection($results);
    }

    /**
     * Store a newly calculated/custom result in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Result::class);

        $result = $this->resultService->storeResult($request->all());

        return (new ResultResource($result))
            ->additional([
                'success' => true,
                'message' => 'Result generated successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified result.
     */
    public function show(string $id): ResultResource
    {
        Gate::authorize('view', new Result);

        $result = $this->resultService->getResultById($id);

        return (new ResultResource($result))
            ->additional([
                'success' => true,
                'message' => 'Result retrieved successfully.',
            ]);
    }

    /**
     * Update the specified result in storage.
     */
    public function update(Request $request, string $id): ResultResource
    {
        Gate::authorize('update', new Result);

        $updatedResult = $this->resultService->updateResult($id, $request->all());

        return (new ResultResource($updatedResult))
            ->additional([
                'success' => true,
                'message' => 'Result updated successfully.',
            ]);
    }

    /**
     * Remove the specified result from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        Gate::authorize('delete', new Result);

        $this->resultService->deleteResult($id);

        return response()->json([
            'success' => true,
            'message' => 'Result deleted successfully.',
        ]);
    }
}
