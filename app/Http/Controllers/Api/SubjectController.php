<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectService $subjectService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Subject::class);

        $perPage = (int) $request->query('per_page', 10);
        $subjects = $this->subjectService->getPaginatedSubjects($request->all(), $perPage);

        return SubjectResource::collection($subjects);
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        Gate::authorize('create', Subject::class);

        $subject = $this->subjectService->createSubject($request->validated());

        return (new SubjectResource($subject))
            ->additional([
                'success' => true,
                'message' => 'Subject created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Subject $subject): SubjectResource
    {
        Gate::authorize('view', $subject);

        $subject->load('classes');

        return (new SubjectResource($subject))
            ->additional([
                'success' => true,
                'message' => 'Subject retrieved successfully.',
            ]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): SubjectResource
    {
        Gate::authorize('update', $subject);

        $updatedSubject = $this->subjectService->updateSubject($subject, $request->validated());

        return (new SubjectResource($updatedSubject))
            ->additional([
                'success' => true,
                'message' => 'Subject updated successfully.',
            ]);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        Gate::authorize('delete', $subject);

        $this->subjectService->deleteSubject($subject);

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully.',
        ]);
    }
}
