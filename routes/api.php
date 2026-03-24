<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\UserController;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ========== AUTENTICACIÓN ==========
// Login API: emite un token Bearer de Sanctum para consumir endpoints protegidos.
Route::post('/token', [AuthTokenController::class, 'store']);

// Logout API: revoca el token actual autenticado en la solicitud.
Route::middleware('auth:sanctum')->delete('/token', [AuthTokenController::class, 'destroy']);

// Perfil del usuario autenticado: verifica que el token sea valido.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========== API V1 ==========
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    // ========== PACIENTES ==========
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);

    // ========== EXPEDIENTES MÉDICOS ==========
    Route::get('/medical-records', [MedicalRecordController::class, 'index']);
    Route::post('/medical-records', [MedicalRecordController::class, 'store']);
    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show']);
    Route::put('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'update']);
    Route::delete('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'destroy']);

    // Endpoint para obtener expediente por paciente
    Route::get('/patients/{patientId}/medical-record', [MedicalRecordController::class, 'byPatient']);

    // ========== USUARIOS ==========
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // ========== CITAS ==========
    Route::post('/appointments', [AppointmentController::class, 'store'])
        // ->withoutMiddleware('auth:sanctum')
        ->middleware(['can:create,'.Appointment::class]);
});
