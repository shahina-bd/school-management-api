<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'subjects' => 'nullable|array',
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'subjects.*.class_id' => 'required|exists:classes,id',
            'subjects.*.pass_marks' => 'nullable|numeric|min:0',
            'subjects.*.max_marks' => 'nullable|numeric|min:0',
            'subjects.*.exam_date' => 'nullable|date',
        ];
    }
}
