<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => ['sometimes', 'exists:classes,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
