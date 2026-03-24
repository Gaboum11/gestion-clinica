<?php

namespace App\Observers;

use App\Models\Patient;
use App\Models\MedicalRecord;

class PatientObserver
{
    /**
     * Escuchar el evento "created" del modelo Patient.
     */
    public function created(Patient $patient): void
    {
        // Crear expediente médico automáticamente
        MedicalRecord::create([
            'patient_id' => $patient->id,
        ]);
    }

    /**
     * Escuchar el evento "deleted" del modelo Patient.
     */
    public function deleted(Patient $patient): void
    {
        // Eliminar expediente médico cuando se elimina el paciente
        $patient->medicalRecord()?->delete();
    }
}
