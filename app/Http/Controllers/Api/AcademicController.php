<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AcademicController extends Controller
{
    public function __construct(
        protected AcademicYearService $academicYearService
    ) {}

    /**
     * Display a listing of academic years.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', AcademicYear::class);

        $perPage = (int) $request->query('per_page', 10);
        $academicYears = $this->academicYearService->getPaginatedAcademicYears($request->all(), $perPage);

        return AcademicYearResource::collection($academicYears);
    }

    /**
     * Store a newly created academic year.
     */
    public function store(StoreAcademicYearRequest $request): JsonResponse
    {
        Gate::authorize('create', AcademicYear::class);

        $academicYear = $this->academicYearService->createAcademicYear($request->validated());

        return (new AcademicYearResource($academicYear))
            ->additional([
                'success' => true,
                'message' => 'Academic year created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified academic year.
     */
    public function show(AcademicYear $academic): AcademicYearResource
    {
        Gate::authorize('view', $academic);

        return (new AcademicYearResource($academic))
            ->additional([
                'success' => true,
                'message' => 'Academic year retrieved successfully.',
            ]);
    }

    /**
     * Update the specified academic year in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academic): AcademicYearResource
    {
        Gate::authorize('update', $academic);

        $updatedYear = $this->academicYearService->updateAcademicYear($academic, $request->validated());

        return (new AcademicYearResource($updatedYear))
            ->additional([
                'success' => true,
                'message' => 'Academic year updated successfully.',
            ]);
    }

    /**
     * Remove the specified academic year from storage.
     */
    public function destroy(AcademicYear $academic): JsonResponse
    {
        Gate::authorize('delete', $academic);

        $this->academicYearService->deleteAcademicYear($academic);

        return response()->json([
            'success' => true,
            'message' => 'Academic year deleted successfully.',
        ]);
    }
}
