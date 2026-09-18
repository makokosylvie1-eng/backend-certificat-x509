<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function ($any = null) {
    // Si la requête demande un fichier statique existant (js, css, png, etc.)
    $path = public_path('browser/' . $any);
    if ($any && file_exists($path) && !is_dir($path)) {
        return response()->file($path);
    }

    // Sinon, on renvoie index.html pour que le routeur d'Angular prenne le relais
    $indexPath = public_path('browser/index.html');
    if (file_exists($indexPath)) {
        return file_get_contents($indexPath);
    }
    
    return response()->json(['message' => 'Interface Angular non compilée dans public/browser/'], 404);
})->where('any', '.*');