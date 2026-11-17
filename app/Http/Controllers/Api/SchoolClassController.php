<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Http\Resources\SchoolClassResource;
use App\Models\SchoolClass;
use App\Services\SchoolClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SchoolClassController extends Controller
{
    public function __construct(
        protected SchoolClassService $schoolClassService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', SchoolClass::class);

        $perPage = (int) $request->query('per_page', 10);
        $classes = $this->schoolClassService->getPaginatedClasses($request->all(), $perPage);

        return SchoolClassResource::collection($classes);
    }

    public function store(StoreSchoolClassRequest $request): JsonResponse
    {
        Gate::authorize('create', SchoolClass::class);

        $class = $this->schoolClassService->createClass($request->validated());

        return (new SchoolClassResource($class))
            ->additional([
                'success' => true,
                'message' => 'Class created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(SchoolClass $class): SchoolClassResource
    {
        Gate::authorize('view', $class);

        $class->load(['sections', 'subjects']);

        return (new SchoolClassResource($class))
            ->additional([
                'success' => true,
                'message' => 'Class retrieved successfully.',
            ]);
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class): SchoolClassResource
    {
        Gate::authorize('update', $class);

        $updatedClass = $this->schoolClassService->updateClass($class, $request->validated());

        return (new SchoolClassResource($updatedClass))
            ->additional([
                'success' => true,
                'message' => 'Class updated successfully.',
            ]);
    }

    public function destroy(SchoolClass $class): JsonResponse
    {
        Gate::authorize('delete', $class);

        $this->schoolClassService->deleteClass($class);

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully.',
        ]);
    }
}
