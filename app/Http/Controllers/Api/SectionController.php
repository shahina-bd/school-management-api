<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class SectionController extends Controller
{
    public function __construct(
        protected SectionService $sectionService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Section::class);

        $perPage = (int) $request->query('per_page', 10);
        $sections = $this->sectionService->getPaginatedSections($request->all(), $perPage);

        return SectionResource::collection($sections);
    }

    public function store(StoreSectionRequest $request): JsonResponse
    {
        Gate::authorize('create', Section::class);

        $section = $this->sectionService->createSection($request->validated());

        return (new SectionResource($section))
            ->additional([
                'success' => true,
                'message' => 'Section created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Section $section): SectionResource
    {
        Gate::authorize('view', $section);

        $section->load('schoolClass');

        return (new SectionResource($section))
            ->additional([
                'success' => true,
                'message' => 'Section retrieved successfully.',
            ]);
    }

    public function update(UpdateSectionRequest $request, Section $section): SectionResource
    {
        Gate::authorize('update', $section);

        $updatedSection = $this->sectionService->updateSection($section, $request->validated());

        return (new SectionResource($updatedSection))
            ->additional([
                'success' => true,
                'message' => 'Section updated successfully.',
            ]);
    }

    public function destroy(Section $section): JsonResponse
    {
        Gate::authorize('delete', $section);

        $this->sectionService->deleteSection($section);

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully.',
        ]);
    }
}
