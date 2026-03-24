<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condition' => ['nullable', 'string', 'max:255'],
            'medications' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'last_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
