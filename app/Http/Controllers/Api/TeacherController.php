<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherService $teacherService
    ) {}

    /**
     * Display a listing of teachers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Teacher::class);

        $perPage = (int) $request->query('per_page', 10);
        $teachers = $this->teacherService->getPaginatedTeachers($request->all(), $perPage);

        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created teacher.
     */
    public function store(StoreTeacherRequest $request): JsonResponse
    {
        Gate::authorize('create', Teacher::class);

        $teacher = $this->teacherService->createTeacher($request->validated());

        return (new TeacherResource($teacher))
            ->additional([
                'success' => true,
                'message' => 'Teacher created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): TeacherResource
    {
        Gate::authorize('view', $teacher);

        $teacher->load('user');

        return (new TeacherResource($teacher))
            ->additional([
                'success' => true,
                'message' => 'Teacher retrieved successfully.',
            ]);
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): TeacherResource
    {
        Gate::authorize('update', $teacher);

        $updatedTeacher = $this->teacherService->updateTeacher($teacher, $request->validated());

        return (new TeacherResource($updatedTeacher))
            ->additional([
                'success' => true,
                'message' => 'Teacher updated successfully.',
            ]);
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher): JsonResponse
    {
        Gate::authorize('delete', $teacher);

        $this->teacherService->deleteTeacher($teacher);

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully.',
        ]);
    }
}
