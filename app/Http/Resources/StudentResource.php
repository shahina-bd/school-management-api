<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'guardian_id' => $this->guardian_id,
            'admission_number' => $this->admission_number,
            'student_id' => $this->admission_number,
            'roll_number' => $this->roll_number,
            'dob' => $this->dob,
            'date_of_birth' => $this->dob,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'admission_date' => $this->admission_date,
            'status' => $this->status,
            'user' => $this->whenLoaded('user'),
            'primary_guardian' => $this->whenLoaded('primaryGuardian'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
