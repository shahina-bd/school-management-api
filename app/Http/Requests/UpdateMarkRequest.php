<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarkRequest extends FormRequest
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
            'exam_subject_id' => 'sometimes|required|exists:exam_subjects,id',
            'student_id' => 'sometimes|required|exists:students,id',
            'marks_obtained' => 'sometimes|required|numeric|min:0',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
