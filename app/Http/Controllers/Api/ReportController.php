<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display a listing of reports.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Report::class);

        $perPage = (int) $request->query('per_page', 10);
        $reports = $this->reportService->getPaginatedReports($request->all(), $perPage);

        return ReportResource::collection($reports);
    }

    /**
     * Store/generate a newly created report.
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        Gate::authorize('create', Report::class);

        $report = $this->reportService->generateReport($request->validated());

        return (new ReportResource($report))
            ->additional([
                'success' => true,
                'message' => 'Report generated successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified report.
     */
    public function show(string $id): ReportResource
    {
        Gate::authorize('view', new Report);

        $report = $this->reportService->getReportById($id);

        return (new ReportResource($report))
            ->additional([
                'success' => true,
                'message' => 'Report retrieved successfully.',
            ]);
    }

    /**
     * Update the specified report in storage.
     */
    public function update(UpdateReportRequest $request, string $id): ReportResource
    {
        Gate::authorize('update', new Report);

        $updatedReport = $this->reportService->updateReport($id, $request->validated());

        return (new ReportResource($updatedReport))
            ->additional([
                'success' => true,
                'message' => 'Report updated successfully.',
            ]);
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        Gate::authorize('delete', new Report);

        $this->reportService->deleteReport($id);

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully.',
        ]);
    }
}
