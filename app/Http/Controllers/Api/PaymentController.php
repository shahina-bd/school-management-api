<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\FeePayment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Display a listing of fee payments.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', FeePayment::class);

        $perPage = (int) $request->query('per_page', 10);
        $payments = $this->paymentService->getPaginatedPayments($request->all(), $perPage);

        return PaymentResource::collection($payments);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        Gate::authorize('create', FeePayment::class);

        $payment = $this->paymentService->createPayment($request->validated());

        return (new PaymentResource($payment))
            ->additional([
                'success' => true,
                'message' => 'Payment recorded successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified payment.
     */
    public function show(FeePayment $feePayment): PaymentResource
    {
        Gate::authorize('view', $feePayment);

        $feePayment->load(['studentFee', 'studentFee.student', 'studentFee.feeType']);

        return (new PaymentResource($feePayment))
            ->additional([
                'success' => true,
                'message' => 'Payment retrieved successfully.',
            ]);
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(UpdatePaymentRequest $request, FeePayment $feePayment): PaymentResource
    {
        Gate::authorize('update', $feePayment);

        $updatedPayment = $this->paymentService->updatePayment($feePayment, $request->validated());

        return (new PaymentResource($updatedPayment))
            ->additional([
                'success' => true,
                'message' => 'Payment updated successfully.',
            ]);
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(FeePayment $feePayment): JsonResponse
    {
        Gate::authorize('delete', $feePayment);

        $this->paymentService->deletePayment($feePayment);

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully.',
        ]);
    }
}
