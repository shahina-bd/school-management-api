<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Http\Resources\NoticeResource;
use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class NoticeController extends Controller
{
    public function __construct(
        protected NoticeService $noticeService
    ) {}

    /**
     * Display a listing of notices.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Notice::class);

        $perPage = (int) $request->query('per_page', 10);
        $notices = $this->noticeService->getPaginatedNotices($request->all(), $perPage);

        return NoticeResource::collection($notices);
    }

    /**
     * Store a newly created notice.
     */
    public function store(StoreNoticeRequest $request): JsonResponse
    {
        Gate::authorize('create', Notice::class);

        $notice = $this->noticeService->createNotice($request->validated());

        return (new NoticeResource($notice))
            ->additional([
                'success' => true,
                'message' => 'Notice created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified notice.
     */
    public function show(Notice $notice): NoticeResource
    {
        Gate::authorize('view', $notice);

        return (new NoticeResource($notice))
            ->additional([
                'success' => true,
                'message' => 'Notice retrieved successfully.',
            ]);
    }

    /**
     * Update the specified notice in storage.
     */
    public function update(UpdateNoticeRequest $request, Notice $notice): NoticeResource
    {
        Gate::authorize('update', $notice);

        $updatedNotice = $this->noticeService->updateNotice($notice, $request->validated());

        return (new NoticeResource($updatedNotice))
            ->additional([
                'success' => true,
                'message' => 'Notice updated successfully.',
            ]);
    }

    /**
     * Remove the specified notice from storage.
     */
    public function destroy(Notice $notice): JsonResponse
    {
        Gate::authorize('delete', $notice);

        $this->noticeService->deleteNotice($notice);

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully.',
        ]);
    }
}
