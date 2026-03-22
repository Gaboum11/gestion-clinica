<?php

use App\Http\Controllers\Api\AuthTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthTokenController::class, 'store']);

Route::middleware('auth:sanctum')->delete('/token', [AuthTokenController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
