<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeeResource extends JsonResource
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
            'student_id' => $this->student_id,
            'fee_type_id' => $this->fee_type_id,
            'amount' => (float) $this->amount,
            'due_date' => $this->due_date ? (is_string($this->due_date) ? $this->due_date : $this->due_date->format('Y-m-d')) : null,
            'status' => $this->status,
            'student' => new StudentResource($this->whenLoaded('student')),
            'fee_type' => new FeeTypeResource($this->whenLoaded('feeType')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
