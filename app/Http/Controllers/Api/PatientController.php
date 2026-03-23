<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    /**
     * Obtener lista de pacientes.
     */
    public function index(): JsonResponse
    {
        $patients = Patient::with('medicalRecord')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $patients->items(),
            'pagination' => [
                'total' => $patients->total(),
                'per_page' => $patients->perPage(),
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
            ],
        ]);
    }

    /**
     * Crear nuevo paciente.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        try {
            $patient = Patient::create($request->validated());
            // El expediente se crea automáticamente mediante el Observer PatientObserver

            return response()->json([
                'status' => 'success',
                'message' => 'Paciente creado exitosamente.',
                'data' => $patient->load('medicalRecord'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el paciente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener paciente específico.
     */
    public function show(Patient $patient): JsonResponse
    {
        try {
            $patient->load('medicalRecord', 'appointments');

            return response()->json([
                'status' => 'success',
                'data' => $patient,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paciente no encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Actualizar paciente.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        try {
            $patient->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Paciente actualizado exitosamente.',
                'data' => $patient->load('medicalRecord'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el paciente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar paciente.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        try {
            $patient->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Paciente eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el paciente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
