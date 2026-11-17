<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeeTypeRequest;
use App\Http\Requests\UpdateFeeTypeRequest;
use App\Http\Resources\FeeTypeResource;
use App\Models\FeeType;
use App\Services\FeeTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class FeeTypeController extends Controller
{
    public function __construct(
        protected FeeTypeService $feeTypeService
    ) {}

    /**
     * Display a listing of fee types.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', FeeType::class);

        $perPage = (int) $request->query('per_page', 10);
        $feeTypes = $this->feeTypeService->getPaginatedFeeTypes($request->all(), $perPage);

        return FeeTypeResource::collection($feeTypes);
    }

    /**
     * Store a newly created fee type.
     */
    public function store(StoreFeeTypeRequest $request): JsonResponse
    {
        Gate::authorize('create', FeeType::class);

        $feeType = $this->feeTypeService->createFeeType($request->validated());

        return (new FeeTypeResource($feeType))
            ->additional([
                'success' => true,
                'message' => 'Fee type created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified fee type.
     */
    public function show(FeeType $feeType): FeeTypeResource
    {
        Gate::authorize('view', $feeType);

        return (new FeeTypeResource($feeType))
            ->additional([
                'success' => true,
                'message' => 'Fee type retrieved successfully.',
            ]);
    }

    /**
     * Update the specified fee type.
     */
    public function update(UpdateFeeTypeRequest $request, FeeType $feeType): FeeTypeResource
    {
        Gate::authorize('update', $feeType);

        $updatedFeeType = $this->feeTypeService->updateFeeType($feeType, $request->validated());

        return (new FeeTypeResource($updatedFeeType))
            ->additional([
                'success' => true,
                'message' => 'Fee type updated successfully.',
            ]);
    }

    /**
     * Remove the specified fee type.
     */
    public function destroy(FeeType $feeType): JsonResponse
    {
        Gate::authorize('delete', $feeType);

        $this->feeTypeService->deleteFeeType($feeType);

        return response()->json([
            'success' => true,
            'message' => 'Fee type deleted successfully.',
        ]);
    }
}
