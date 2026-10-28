<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Mode maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader de Composer
require __DIR__.'/../vendor/autoload.php';

// Démarrage de Laravel et traitement de la requête
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());