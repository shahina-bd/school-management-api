<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'student_fee_id' => ['sometimes', 'required', 'integer', 'exists:student_fees,id'],
            'amount_paid' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_date' => ['sometimes', 'required', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
