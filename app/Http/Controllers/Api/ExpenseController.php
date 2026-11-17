<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Expense::class);

        $perPage = (int) $request->query('per_page', 10);
        $expenses = $this->expenseService->getPaginatedExpenses($request->all(), $perPage);

        return ExpenseResource::collection($expenses);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        Gate::authorize('create', Expense::class);

        $expense = $this->expenseService->createExpense($request->validated());

        return (new ExpenseResource($expense))
            ->additional([
                'success' => true,
                'message' => 'Expense created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): ExpenseResource
    {
        Gate::authorize('view', $expense);

        $expense->load('expenseCategory');

        return (new ExpenseResource($expense))
            ->additional([
                'success' => true,
                'message' => 'Expense retrieved successfully.',
            ]);
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        Gate::authorize('update', $expense);

        $updatedExpense = $this->expenseService->updateExpense($expense, $request->validated());

        return (new ExpenseResource($updatedExpense))
            ->additional([
                'success' => true,
                'message' => 'Expense updated successfully.',
            ]);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        Gate::authorize('delete', $expense);

        $this->expenseService->deleteExpense($expense);

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.',
        ]);
    }
}
