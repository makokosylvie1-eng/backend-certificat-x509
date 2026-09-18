<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function ($any = null) {
    $path = public_path($any);
    
    // Si c'est un fichier statique existant (js, css, favicon, etc.), on le sert
    if ($any && file_exists($path) && !is_dir($path)) {
        return response()->file($path);
    }

    // Sinon, on renvoie index.html pour Angular
    $indexPath = public_path('index.html');
    if (file_exists($indexPath)) {
        return file_get_contents($indexPath);
    }
    
    return response()->json(['message' => 'Application non trouvee'], 404);
})->where('any', '.*');