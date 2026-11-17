<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerRequest extends FormRequest
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
            'account_name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:debit,credit'],
            'amount' => ['required', 'numeric', 'min:0'],
            'entry_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'expense_category_id' => ['nullable', 'integer', 'exists:expense_categories,id'],
        ];
    }
}
