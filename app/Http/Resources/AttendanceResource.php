<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'class_id' => $this->class_id,
            'section_id' => $this->section_id,
            'date' => $this->date ? (is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) : null,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'student' => $this->whenLoaded('student'),
            'class' => $this->whenLoaded('schoolClass'),
            'section' => $this->whenLoaded('section'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
