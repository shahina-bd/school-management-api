<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'student_fee_id' => $this->student_fee_id,
            'amount_paid' => (float) $this->amount_paid,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date ? (is_string($this->payment_date) ? $this->payment_date : $this->payment_date->format('Y-m-d')) : null,
            'receipt_number' => $this->receipt_number,
            'student_fee' => new StudentFeeResource($this->whenLoaded('studentFee')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
