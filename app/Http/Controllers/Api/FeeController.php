<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentFeeRequest;
use App\Http\Requests\UpdateStudentFeeRequest;
use App\Http\Resources\StudentFeeResource;
use App\Models\StudentFee;
use App\Services\StudentFeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class FeeController extends Controller
{
    public function __construct(
        protected StudentFeeService $feeService
    ) {}

    /**
     * Display a listing of fees.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', StudentFee::class);

        $perPage = (int) $request->query('per_page', 10);
        $fees = $this->feeService->getPaginatedFees($request->all(), $perPage);

        return StudentFeeResource::collection($fees);
    }

    /**
     * Store a newly created fee in storage.
     */
    public function store(StoreStudentFeeRequest $request): JsonResponse
    {
        Gate::authorize('create', StudentFee::class);

        $fee = $this->feeService->createFee($request->validated());

        return (new StudentFeeResource($fee))
            ->additional([
                'success' => true,
                'message' => 'Fee created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified fee.
     */
    public function show(StudentFee $fee): StudentFeeResource
    {
        Gate::authorize('view', $fee);

        $fee->load(['student', 'feeType', 'payments']);

        return (new StudentFeeResource($fee))
            ->additional([
                'success' => true,
                'message' => 'Fee retrieved successfully.',
            ]);
    }

    /**
     * Update the specified fee in storage.
     */
    public function update(UpdateStudentFeeRequest $request, StudentFee $fee): StudentFeeResource
    {
        Gate::authorize('update', $fee);

        $updatedFee = $this->feeService->updateFee($fee, $request->validated());

        return (new StudentFeeResource($updatedFee))
            ->additional([
                'success' => true,
                'message' => 'Fee updated successfully.',
            ]);
    }

    /**
     * Remove the specified fee from storage.
     */
    public function destroy(StudentFee $fee): JsonResponse
    {
        Gate::authorize('delete', $fee);

        $this->feeService->deleteFee($fee);

        return response()->json([
            'success' => true,
            'message' => 'Fee deleted successfully.',
        ]);
    }
}
