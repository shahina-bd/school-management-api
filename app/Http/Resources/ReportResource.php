<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'title' => $resource->title ?? 'Report Summary',
            'type' => $resource->type ?? 'general',
            'summary' => $resource->summary ?? [],
            'parameters' => $resource->parameters ?? [],
            'generated_at' => $resource->generated_at ?? now()->toIso8601String(),
            'created_at' => $resource->created_at ?? now()->toIso8601String(),
            'updated_at' => $resource->updated_at ?? now()->toIso8601String(),
        ];
    }
}
