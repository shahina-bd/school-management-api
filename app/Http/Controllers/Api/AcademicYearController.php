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

class AcademicYearController extends Controller
{
    public function __construct(
        protected AcademicYearService $academicYearService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', AcademicYear::class);

        $perPage = (int) $request->query('per_page', 10);
        $academicYears = $this->academicYearService->getPaginatedAcademicYears($request->all(), $perPage);

        return AcademicYearResource::collection($academicYears);
    }

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

    public function show(AcademicYear $academicYear): AcademicYearResource
    {
        Gate::authorize('view', $academicYear);

        return (new AcademicYearResource($academicYear))
            ->additional([
                'success' => true,
                'message' => 'Academic year retrieved successfully.',
            ]);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): AcademicYearResource
    {
        Gate::authorize('update', $academicYear);

        $updatedYear = $this->academicYearService->updateAcademicYear($academicYear, $request->validated());

        return (new AcademicYearResource($updatedYear))
            ->additional([
                'success' => true,
                'message' => 'Academic year updated successfully.',
            ]);
    }

    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        Gate::authorize('delete', $academicYear);

        $this->academicYearService->deleteAcademicYear($academicYear);

        return response()->json([
            'success' => true,
            'message' => 'Academic year deleted successfully.',
        ]);
    }
}
