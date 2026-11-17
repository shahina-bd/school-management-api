<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSubjectResource extends JsonResource
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
            'exam_id' => $this->exam_id,
            'subject_id' => $this->subject_id,
            'class_id' => $this->class_id,
            'pass_marks' => (float) $this->pass_marks,
            'max_marks' => (float) $this->max_marks,
            'exam_date' => $this->exam_date ? $this->exam_date->format('Y-m-d') : null,
            'exam' => new ExamResource($this->whenLoaded('exam')),
            'subject' => $this->whenLoaded('subject'),
            'school_class' => $this->whenLoaded('schoolClass'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
