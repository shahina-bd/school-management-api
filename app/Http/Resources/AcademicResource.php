<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            return [
                'academic_years' => AcademicYearResource::collection($this->resource['academic_years'] ?? []),
                'classes' => SchoolClassResource::collection($this->resource['classes'] ?? []),
                'sections' => SectionResource::collection($this->resource['sections'] ?? []),
                'subjects' => SubjectResource::collection($this->resource['subjects'] ?? []),
            ];
        }

        return parent::toArray($request);
    }
}
