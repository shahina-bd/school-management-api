<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentFeeRequest extends FormRequest
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
            'student_id' => ['sometimes', 'required', 'integer', 'exists:students,id'],
            'fee_type_id' => ['sometimes', 'required', 'integer', 'exists:fee_types,id'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'required', 'date'],
            'status' => ['nullable', 'string', 'in:paid,unpaid,partial'],
        ];
    }
}
