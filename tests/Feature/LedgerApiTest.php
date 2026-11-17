<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->category = ExpenseCategory::create([
            'name' => 'General Maintenance',
        ]);
    }

    public function test_can_list_ledger_entries(): void
    {
        LedgerEntry::create([
            'account_name' => 'Cash Account',
            'type' => 'credit',
            'amount' => 1200.00,
            'entry_date' => '2026-10-01',
            'description' => 'Initial capital',
            'reference_number' => 'REF-101',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/ledger');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'account_name',
                        'type',
                        'amount',
                        'entry_date',
                        'description',
                        'reference_number',
                    ],
                ],
            ]);
    }

    public function test_can_create_ledger_entry(): void
    {
        $payload = [
            'account_name' => 'Petty Cash',
            'type' => 'debit',
            'amount' => 250.00,
            'entry_date' => '2026-10-02',
            'description' => 'Office snacks',
            'reference_number' => 'REF-102',
            'expense_category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/ledger', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.account_name', 'Petty Cash')
            ->assertJsonPath('data.amount', 250)
            ->assertJsonPath('data.type', 'debit')
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('ledger_entries', [
            'account_name' => 'Petty Cash',
            'type' => 'debit',
            'amount' => 250.00,
            'reference_number' => 'REF-102',
        ]);
    }

    public function test_can_show_ledger_entry(): void
    {
        $entry = LedgerEntry::create([
            'account_name' => 'Bank Account',
            'type' => 'credit',
            'amount' => 5000.00,
            'entry_date' => '2026-10-01',
            'reference_number' => 'REF-103',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/ledger/{$entry->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $entry->id)
            ->assertJsonPath('data.reference_number', 'REF-103');
    }

    public function test_can_update_ledger_entry(): void
    {
        $entry = LedgerEntry::create([
            'account_name' => 'Accounts Payable',
            'type' => 'debit',
            'amount' => 300.00,
            'entry_date' => '2026-10-01',
            'reference_number' => 'REF-104',
        ]);

        $payload = [
            'amount' => 350.00,
            'description' => 'Adjusted amount',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/ledger/{$entry->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.amount', 350)
            ->assertJsonPath('data.description', 'Adjusted amount');

        $this->assertDatabaseHas('ledger_entries', [
            'id' => $entry->id,
            'amount' => 350.00,
            'description' => 'Adjusted amount',
        ]);
    }

    public function test_can_delete_ledger_entry(): void
    {
        $entry = LedgerEntry::create([
            'account_name' => 'Miscellaneous Account',
            'type' => 'debit',
            'amount' => 100.00,
            'entry_date' => '2026-10-01',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/ledger/{$entry->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('ledger_entries', [
            'id' => $entry->id,
        ]);
    }
}
