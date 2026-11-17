<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $examService
    ) {}

    /**
     * Display a listing of exams.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Exam::class);

        $perPage = (int) $request->query('per_page', 10);
        $exams = $this->examService->getPaginatedExams($request->all(), $perPage);

        return ExamResource::collection($exams);
    }

    /**
     * Store a newly created exam.
     */
    public function store(StoreExamRequest $request): JsonResponse
    {
        Gate::authorize('create', Exam::class);

        $exam = $this->examService->createExam($request->validated());

        return (new ExamResource($exam))
            ->additional([
                'success' => true,
                'message' => 'Exam created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam): ExamResource
    {
        Gate::authorize('view', $exam);

        $exam->load(['academicYear', 'examSubjects.subject', 'examSubjects.schoolClass']);

        return (new ExamResource($exam))
            ->additional([
                'success' => true,
                'message' => 'Exam retrieved successfully.',
            ]);
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam): ExamResource
    {
        Gate::authorize('update', $exam);

        $updatedExam = $this->examService->updateExam($exam, $request->validated());

        return (new ExamResource($updatedExam))
            ->additional([
                'success' => true,
                'message' => 'Exam updated successfully.',
            ]);
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam): JsonResponse
    {
        Gate::authorize('delete', $exam);

        $this->examService->deleteExam($exam);

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully.',
        ]);
    }
}
