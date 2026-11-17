<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Exam;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Retrieve paginated report summaries.
     */
    public function getPaginatedReports(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $type = $filters['type'] ?? 'general';
        $page = (int) ($filters['page'] ?? 1);

        $reports = new Collection([
            $this->generateReport(['title' => 'General School Summary Report', 'type' => 'general', 'id' => 1]),
            $this->generateReport(['title' => 'Academic & Exam Performance Report', 'type' => 'academic', 'id' => 2]),
            $this->generateReport(['title' => 'Communication & Notices Report', 'type' => 'communication', 'id' => 3]),
        ]);

        if ($type !== 'all') {
            $filtered = $reports->filter(fn ($r) => $r['type'] === $type || $type === 'general');
            if ($filtered->isNotEmpty()) {
                $reports = $filtered->values();
            }
        }

        $total = $reports->count();
        $sliced = $reports->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Generate dynamic report data based on current system data.
     */
    public function generateReport(array $data): array
    {
        $id = $data['id'] ?? rand(100, 999);
        $title = $data['title'] ?? 'Generated School Report';
        $type = $data['type'] ?? 'general';
        $params = $data['parameters'] ?? [];

        $studentCount = Student::count();
        $examCount = Exam::count();
        $markCount = Mark::count();
        $noticeCount = Notice::count();
        $eventCount = Event::count();
        $feePaymentTotal = (float) FeePayment::sum('amount_paid');
        $expenseTotal = (float) Expense::sum('amount');

        $summary = [
            'total_students' => $studentCount,
            'total_exams' => $examCount,
            'total_marks_recorded' => $markCount,
            'total_notices' => $noticeCount,
            'total_events' => $eventCount,
            'total_fee_collections' => $feePaymentTotal,
            'total_expenses' => $expenseTotal,
            'net_financial_balance' => $feePaymentTotal - $expenseTotal,
        ];

        return [
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'summary' => $summary,
            'parameters' => $params,
            'generated_at' => now()->toIso8601String(),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get a report by ID.
     */
    public function getReportById(string $id): array
    {
        return $this->generateReport([
            'id' => (int) $id,
            'title' => "Report #{$id}",
            'type' => 'general',
        ]);
    }

    /**
     * Update report data.
     */
    public function updateReport(string $id, array $data): array
    {
        $current = $this->getReportById($id);

        if (isset($data['title'])) {
            $current['title'] = $data['title'];
        }

        if (isset($data['type'])) {
            $current['type'] = $data['type'];
        }

        if (isset($data['parameters'])) {
            $current['parameters'] = $data['parameters'];
        }

        $current['updated_at'] = now()->toIso8601String();

        return $current;
    }

    /**
     * Delete report.
     */
    public function deleteReport(string $id): bool
    {
        return true;
    }
}
