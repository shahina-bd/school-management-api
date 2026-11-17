<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_name' => $this->account_name,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'entry_date' => $this->entry_date ? (is_string($this->entry_date) ? $this->entry_date : $this->entry_date->format('Y-m-d')) : null,
            'description' => $this->description,
            'reference_number' => $this->reference_number,
            'expense_category_id' => $this->expense_category_id,
            'expense_category' => new ExpenseCategoryResource($this->whenLoaded('expenseCategory')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
