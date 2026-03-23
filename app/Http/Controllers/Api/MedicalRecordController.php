<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MedicalRecordController extends Controller
{
    /**
     * Obtener lista de expedientes médicos.
     */
    public function index(): JsonResponse
    {
        $medicalRecords = MedicalRecord::with('patient')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $medicalRecords->items(),
            'pagination' => [
                'total' => $medicalRecords->total(),
                'per_page' => $medicalRecords->perPage(),
                'current_page' => $medicalRecords->currentPage(),
                'last_page' => $medicalRecords->lastPage(),
            ],
        ]);
    }

    /**
     * Crear expediente médico.
     */
    public function store(StoreMedicalRecordRequest $request): JsonResponse
    {
        try {
            $medicalRecord = MedicalRecord::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Expediente médico creado exitosamente.',
                'data' => $medicalRecord->load('patient'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el expediente médico.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener expediente médico específico.
     */
    public function show(MedicalRecord $medicalRecord): JsonResponse
    {
        try {
            $medicalRecord->load('patient');

            return response()->json([
                'status' => 'success',
                'data' => $medicalRecord,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Expediente médico no encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Actualizar expediente médico.
     */
    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): JsonResponse
    {
        try {
            $medicalRecord->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Expediente médico actualizado exitosamente.',
                'data' => $medicalRecord->load('patient'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el expediente médico.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar expediente médico.
     */
    public function destroy(MedicalRecord $medicalRecord): JsonResponse
    {
        try {
            $medicalRecord->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Expediente médico eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el expediente médico.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener expediente médico por paciente.
     */
    public function byPatient($patientId): JsonResponse
    {
        try {
            $medicalRecord = MedicalRecord::where('patient_id', $patientId)
                ->with('patient')
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $medicalRecord,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Expediente médico no encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
