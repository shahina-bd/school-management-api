<?php

namespace App\Services;

use App\Models\Notice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NoticeService
{
    /**
     * Retrieve paginated notices with optional filter.
     */
    public function getPaginatedNotices(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $targetAudience = $filters['target_audience'] ?? null;

        return Notice::query()
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('content', 'like', "%{$searchTerm}%");
                });
            })
            ->when($targetAudience, function ($query, $audience) {
                $query->where('target_audience', $audience);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new notice record.
     */
    public function createNotice(array $data): Notice
    {
        $noticeData = array_intersect_key($data, array_flip([
            'title',
            'content',
            'target_audience',
            'published_at',
        ]));

        if (empty($noticeData['target_audience'])) {
            $noticeData['target_audience'] = 'all';
        }

        return Notice::create($noticeData);
    }

    /**
     * Update an existing notice record.
     */
    public function updateNotice(Notice $notice, array $data): Notice
    {
        $noticeData = array_intersect_key($data, array_flip([
            'title',
            'content',
            'target_audience',
            'published_at',
        ]));

        $notice->update($noticeData);

        return $notice->fresh();
    }

    /**
     * Delete a notice record.
     */
    public function deleteNotice(Notice $notice): bool
    {
        return (bool) $notice->delete();
    }
}
