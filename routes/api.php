<?php

use App\Http\Controllers\Api\CertificatController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Routes publiques, avec limitation du nombre de tentatives par minute.
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Routes réservées aux utilisateurs connectés (jeton Bearer).
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/certificates/parse', [CertificatController::class, 'parseCertificate'])
        ->middleware('throttle:30,1');
});