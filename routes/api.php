<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login API: emite un token Bearer de Sanctum para consumir endpoints protegidos.
Route::post('/token', [AuthTokenController::class, 'store']);

// Logout API: revoca el token actual autenticado en la solicitud.
Route::middleware('auth:sanctum')->delete('/token', [AuthTokenController::class, 'destroy']);

// Perfil del usuario autenticado: verifica que el token sea valido.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint principal para crear citas medicas (modulo core de citas).
Route::group(['prefix' => 'v1'], function () {
    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->middleware(['auth:sanctum', 'can:create,'.Appointment::class]);
});
