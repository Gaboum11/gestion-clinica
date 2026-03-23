<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id', 'unique:medical_records,patient_id'],
            'condition' => ['nullable', 'string', 'max:255'],
            'medications' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'last_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'El paciente es requerido.',
            'patient_id.exists' => 'El paciente seleccionado no existe.',
            'patient_id.unique' => 'Este paciente ya tiene un expediente médico.',
        ];
    }
}
