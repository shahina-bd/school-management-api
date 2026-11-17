<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', User::class);

        $perPage = (int) $request->query('per_page', 10);
        $users = $this->userService->getPaginatedUsers($request->all(), $perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $user = $this->userService->createUser($request->validated());

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): UserResource
    {
        Gate::authorize('view', $user);

        $user->load('role');

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User retrieved successfully.',
            ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        Gate::authorize('update', $user);

        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return (new UserResource($updatedUser))
            ->additional([
                'success' => true,
                'message' => 'User updated successfully.',
            ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $this->userService->deleteUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
