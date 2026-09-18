<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Toutes les routes qui ne commencent pas par /api affichent l'interface Angular
Route::get('/{any?}', function () {
    $path = public_path('index.html');
    if (file_exists($path)) {
        return file_get_contents($path);
    }
    return response()->json(['message' => 'Interface Angular non compilée dans public/'], 404);
})->where('any', '.*');
