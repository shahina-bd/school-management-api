<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    /**
     * Display a listing of students.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);

        $perPage = (int) $request->query('per_page', 10);
        $students = $this->studentService->getPaginatedStudents($request->all(), $perPage);

        return StudentResource::collection($students);
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        Gate::authorize('create', Student::class);

        $student = $this->studentService->createStudent($request->validated());

        return (new StudentResource($student))
            ->additional([
                'success' => true,
                'message' => 'Student created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): StudentResource
    {
        Gate::authorize('view', $student);

        $student->load(['user', 'primaryGuardian']);

        return (new StudentResource($student))
            ->additional([
                'success' => true,
                'message' => 'Student retrieved successfully.',
            ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        Gate::authorize('update', $student);

        $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

        return (new StudentResource($updatedStudent))
            ->additional([
                'success' => true,
                'message' => 'Student updated successfully.',
            ]);
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        Gate::authorize('delete', $student);

        $this->studentService->deleteStudent($student);

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
        ]);
    }
}
