<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('student_id') && ! $this->has('admission_number')) {
            $mergeData['admission_number'] = $this->input('student_id');
        }

        if ($this->has('date_of_birth') && ! $this->has('dob')) {
            $mergeData['dob'] = $this->input('date_of_birth');
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    public function rules(): array
    {
        return [
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'guardian_id' => ['nullable', 'integer', 'exists:guardians,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'date_of_birth' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'photo' => ['nullable', 'string', 'max:255'],
            'admission_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,inactive,graduated,transferred'],
        ];
    }
}
