<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerRequest;
use App\Http\Requests\UpdateLedgerRequest;
use App\Http\Resources\LedgerResource;
use App\Models\LedgerEntry;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class LedgerController extends Controller
{
    public function __construct(
        protected LedgerService $ledgerService
    ) {}

    /**
     * Display a listing of ledger entries.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', LedgerEntry::class);

        $perPage = (int) $request->query('per_page', 10);
        $entries = $this->ledgerService->getPaginatedLedgerEntries($request->all(), $perPage);

        return LedgerResource::collection($entries);
    }

    /**
     * Store a newly created ledger entry in storage.
     */
    public function store(StoreLedgerRequest $request): JsonResponse
    {
        Gate::authorize('create', LedgerEntry::class);

        $entry = $this->ledgerService->createLedgerEntry($request->validated());

        return (new LedgerResource($entry))
            ->additional([
                'success' => true,
                'message' => 'Ledger entry created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified ledger entry.
     */
    public function show(LedgerEntry $ledger): LedgerResource
    {
        Gate::authorize('view', $ledger);

        $ledger->load('expenseCategory');

        return (new LedgerResource($ledger))
            ->additional([
                'success' => true,
                'message' => 'Ledger entry retrieved successfully.',
            ]);
    }

    /**
     * Update the specified ledger entry in storage.
     */
    public function update(UpdateLedgerRequest $request, LedgerEntry $ledger): LedgerResource
    {
        Gate::authorize('update', $ledger);

        $updatedEntry = $this->ledgerService->updateLedgerEntry($ledger, $request->validated());

        return (new LedgerResource($updatedEntry))
            ->additional([
                'success' => true,
                'message' => 'Ledger entry updated successfully.',
            ]);
    }

    /**
     * Remove the specified ledger entry from storage.
     */
    public function destroy(LedgerEntry $ledger): JsonResponse
    {
        Gate::authorize('delete', $ledger);

        $this->ledgerService->deleteLedgerEntry($ledger);

        return response()->json([
            'success' => true,
            'message' => 'Ledger entry deleted successfully.',
        ]);
    }
}
