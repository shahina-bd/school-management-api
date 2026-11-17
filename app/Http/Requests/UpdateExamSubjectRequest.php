<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamSubjectRequest extends FormRequest
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
            'exam_id' => 'sometimes|required|exists:exams,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'class_id' => 'sometimes|required|exists:classes,id',
            'pass_marks' => 'nullable|numeric|min:0',
            'max_marks' => 'nullable|numeric|min:0',
            'exam_date' => 'nullable|date',
        ];
    }
}
