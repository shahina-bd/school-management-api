<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class StaffController extends Controller
{
    public function __construct(
        protected StaffService $staffService
    ) {}

    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Staff::class);

        $perPage = (int) $request->query('per_page', 10);
        $staffMembers = $this->staffService->getPaginatedStaff($request->all(), $perPage);

        return StaffResource::collection($staffMembers);
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        Gate::authorize('create', Staff::class);

        $staff = $this->staffService->createStaff($request->validated());

        return (new StaffResource($staff))
            ->additional([
                'success' => true,
                'message' => 'Staff member created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified staff member.
     */
    public function show(Staff $staff): StaffResource
    {
        Gate::authorize('view', $staff);

        $staff->load('user');

        return (new StaffResource($staff))
            ->additional([
                'success' => true,
                'message' => 'Staff member retrieved successfully.',
            ]);
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): StaffResource
    {
        Gate::authorize('update', $staff);

        $updatedStaff = $this->staffService->updateStaff($staff, $request->validated());

        return (new StaffResource($updatedStaff))
            ->additional([
                'success' => true,
                'message' => 'Staff member updated successfully.',
            ]);
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(Staff $staff): JsonResponse
    {
        Gate::authorize('delete', $staff);

        $this->staffService->deleteStaff($staff);

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully.',
        ]);
    }
}
