<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        protected ExpenseCategoryService $expenseCategoryService
    ) {}

    /**
     * Display a listing of expense categories.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', ExpenseCategory::class);

        $perPage = (int) $request->query('per_page', 10);
        $categories = $this->expenseCategoryService->getPaginatedExpenseCategories($request->all(), $perPage);

        return ExpenseCategoryResource::collection($categories);
    }

    /**
     * Store a newly created expense category.
     */
    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        Gate::authorize('create', ExpenseCategory::class);

        $category = $this->expenseCategoryService->createExpenseCategory($request->validated());

        return (new ExpenseCategoryResource($category))
            ->additional([
                'success' => true,
                'message' => 'Expense category created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified expense category.
     */
    public function show(ExpenseCategory $expenseCategory): ExpenseCategoryResource
    {
        Gate::authorize('view', $expenseCategory);

        return (new ExpenseCategoryResource($expenseCategory))
            ->additional([
                'success' => true,
                'message' => 'Expense category retrieved successfully.',
            ]);
    }

    /**
     * Update the specified expense category.
     */
    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): ExpenseCategoryResource
    {
        Gate::authorize('update', $expenseCategory);

        $updatedCategory = $this->expenseCategoryService->updateExpenseCategory($expenseCategory, $request->validated());

        return (new ExpenseCategoryResource($updatedCategory))
            ->additional([
                'success' => true,
                'message' => 'Expense category updated successfully.',
            ]);
    }

    /**
     * Remove the specified expense category.
     */
    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        Gate::authorize('delete', $expenseCategory);

        $this->expenseCategoryService->deleteExpenseCategory($expenseCategory);

        return response()->json([
            'success' => true,
            'message' => 'Expense category deleted successfully.',
        ]);
    }
}
