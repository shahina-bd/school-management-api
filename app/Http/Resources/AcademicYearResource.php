<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date ? (is_string($this->start_date) ? $this->start_date : $this->start_date->format('Y-m-d')) : null,
            'end_date' => $this->end_date ? (is_string($this->end_date) ? $this->end_date : $this->end_date->format('Y-m-d')) : null,
            'is_current' => (bool) $this->is_current,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
