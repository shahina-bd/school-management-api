<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', AttendanceRecord::class);

        $perPage = (int) $request->query('per_page', 10);
        $records = $this->attendanceService->getPaginatedAttendance($request->all(), $perPage);

        return AttendanceResource::collection($records);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        Gate::authorize('create', AttendanceRecord::class);

        $record = $this->attendanceService->createAttendance($request->validated());

        return (new AttendanceResource($record))
            ->additional([
                'success' => true,
                'message' => 'Attendance record created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified attendance record.
     */
    public function show(AttendanceRecord $attendance): AttendanceResource
    {
        Gate::authorize('view', $attendance);

        $attendance->load(['student.user', 'schoolClass', 'section']);

        return (new AttendanceResource($attendance))
            ->additional([
                'success' => true,
                'message' => 'Attendance record retrieved successfully.',
            ]);
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function update(UpdateAttendanceRequest $request, AttendanceRecord $attendance): AttendanceResource
    {
        Gate::authorize('update', $attendance);

        $updatedRecord = $this->attendanceService->updateAttendance($attendance, $request->validated());

        return (new AttendanceResource($updatedRecord))
            ->additional([
                'success' => true,
                'message' => 'Attendance record updated successfully.',
            ]);
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroy(AttendanceRecord $attendance): JsonResponse
    {
        Gate::authorize('delete', $attendance);

        $this->attendanceService->deleteAttendance($attendance);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully.',
        ]);
    }
}
