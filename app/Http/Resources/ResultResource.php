<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = is_array($this->resource) ? (object) $this->resource : $this->resource;

        return [
            'id' => $resource->id ?? null,
            'student_id' => $resource->student_id ?? null,
            'student' => $resource->student ?? null,
            'exam_id' => $resource->exam_id ?? null,
            'exam' => $resource->exam ?? null,
            'total_max_marks' => isset($resource->total_max_marks) ? (float) $resource->total_max_marks : 0.0,
            'total_marks_obtained' => isset($resource->total_marks_obtained) ? (float) $resource->total_marks_obtained : 0.0,
            'percentage' => isset($resource->percentage) ? (float) $resource->percentage : 0.0,
            'grade' => $resource->grade ?? 'N/A',
            'status' => $resource->status ?? 'pending',
            'subject_marks' => $resource->subject_marks ?? [],
            'created_at' => isset($resource->created_at) ? $resource->created_at : null,
            'updated_at' => isset($resource->updated_at) ? $resource->updated_at : null,
        ];
    }
}
