<?php

declare(strict_types=1);

use App\Core\Env;

/**
 * Configuración central de la aplicación.
 * Los valores reales se leen del archivo .env (ver .env.example).
 */
return [
    'app' => [
        'name' => Env::get('APP_NAME', 'Atlas de ciudades'),
        'debug' => Env::get('APP_DEBUG', 'false') === 'true',
        'timezone' => Env::get('APP_TIMEZONE', 'America/Bogota'),
    ],
    'db' => [
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => (int) Env::get('DB_PORT', '3306'),
        'name' => Env::get('DB_DATABASE', 'world'),
        'user' => Env::get('DB_USERNAME', 'root'),
        'pass' => Env::get('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
];
