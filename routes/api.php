<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CertificatController;

Route::post('/certificates/parse', [CertificatController::class, 'parseCertificate']);