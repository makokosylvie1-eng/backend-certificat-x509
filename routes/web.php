<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
    //return view('welcome');
//});

// Toutes les autres routes affichent l'interface Angular depuis public/browser/
Route::get('/{any?}', function () {
    $path = public_path('browser/index.html');
    if (file_exists($path)) {
        return file_get_contents($path);
    }
    return response()->json(['message' => 'Interface Angular non compilée dans public/browser/'], 404);
})->where('any', '.*');