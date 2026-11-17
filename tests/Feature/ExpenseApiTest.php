<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->category = ExpenseCategory::create([
            'name' => 'Utilities',
        ]);
    }

    public function test_can_list_expenses(): void
    {
        Expense::create([
            'expense_category_id' => $this->category->id,
            'title' => 'Electricity Bill',
            'amount' => 150.00,
            'date' => '2026-10-01',
            'description' => 'Monthly power bill',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/expenses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'expense_category_id',
                        'title',
                        'amount',
                        'date',
                        'description',
                    ],
                ],
            ]);
    }

    public function test_can_create_expense(): void
    {
        $payload = [
            'expense_category_id' => $this->category->id,
            'title' => 'Internet Bill',
            'amount' => 80.00,
            'date' => '2026-10-02',
            'description' => 'High speed fiber',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/expenses', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Internet Bill')
            ->assertJsonPath('data.amount', 80)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('expenses', [
            'expense_category_id' => $this->category->id,
            'title' => 'Internet Bill',
            'amount' => 80.00,
        ]);
    }

    public function test_can_show_expense(): void
    {
        $expense = Expense::create([
            'expense_category_id' => $this->category->id,
            'title' => 'Water Bill',
            'amount' => 45.00,
            'date' => '2026-10-01',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $expense->id)
            ->assertJsonPath('data.title', 'Water Bill');
    }

    public function test_can_update_expense(): void
    {
        $expense = Expense::create([
            'expense_category_id' => $this->category->id,
            'title' => 'Office Supplies',
            'amount' => 120.00,
            'date' => '2026-10-01',
        ]);

        $payload = [
            'title' => 'Updated Office Supplies',
            'amount' => 150.00,
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/expenses/{$expense->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Office Supplies')
            ->assertJsonPath('data.amount', 150);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated Office Supplies',
            'amount' => 150.00,
        ]);
    }

    public function test_can_delete_expense(): void
    {
        $expense = Expense::create([
            'expense_category_id' => $this->category->id,
            'title' => 'Paper Shredder',
            'amount' => 60.00,
            'date' => '2026-10-01',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_can_crud_expense_categories(): void
    {
        // Index
        $this->actingAs($this->user)->getJson('/api/v1/expense-categories')->assertStatus(200);

        // Store
        $storeRes = $this->actingAs($this->user)->postJson('/api/v1/expense-categories', [
            'name' => 'Maintenance',
        ]);
        $storeRes->assertStatus(201);
        $catId = $storeRes->json('data.id');

        // Show
        $this->actingAs($this->user)->getJson("/api/v1/expense-categories/{$catId}")->assertStatus(200);

        // Update
        $this->actingAs($this->user)->putJson("/api/v1/expense-categories/{$catId}", [
            'name' => 'Building Maintenance',
        ])->assertStatus(200);

        // Destroy
        $this->actingAs($this->user)->deleteJson("/api/v1/expense-categories/{$catId}")->assertStatus(200);
    }
}
