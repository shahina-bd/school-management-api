<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Role::class);

        $perPage = (int) $request->query('per_page', 10);
        $roles = $this->roleService->getPaginatedRoles($request->all(), $perPage);

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        Gate::authorize('create', Role::class);

        $role = $this->roleService->createRole($request->validated());

        return (new RoleResource($role))
            ->additional([
                'success' => true,
                'message' => 'Role created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): RoleResource
    {
        Gate::authorize('view', $role);

        return (new RoleResource($role))
            ->additional([
                'success' => true,
                'message' => 'Role retrieved successfully.',
            ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        Gate::authorize('update', $role);

        $updatedRole = $this->roleService->updateRole($role, $request->validated());

        return (new RoleResource($updatedRole))
            ->additional([
                'success' => true,
                'message' => 'Role updated successfully.',
            ]);
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        Gate::authorize('delete', $role);

        $this->roleService->deleteRole($role);

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }
}
