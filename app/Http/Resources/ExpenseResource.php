<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'expense_category_id' => $this->expense_category_id,
            'title' => $this->title,
            'amount' => (float) $this->amount,
            'date' => $this->date ? (is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) : null,
            'description' => $this->description,
            'receipt_image' => $this->receipt_image,
            'expense_category' => new ExpenseCategoryResource($this->whenLoaded('expenseCategory')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
