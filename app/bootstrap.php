<?php

declare(strict_types=1);

/**
 * Arranque de la aplicación.
 *
 * - Registra un autoloader PSR-4 sencillo (App\ -> app/), sin depender de Composer.
 * - Carga las variables del archivo .env.
 * - Carga las funciones auxiliares de las vistas.
 * - Define un manejador global de excepciones para no exponer detalles
 *   técnicos al usuario final (salvo que APP_DEBUG=true).
 */

use App\Core\Env;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

Env::load(BASE_PATH . '/.env');

require BASE_PATH . '/app/helpers.php';

$config = require BASE_PATH . '/config/config.php';
date_default_timezone_set($config['app']['timezone']);

set_exception_handler(static function (Throwable $e) use ($config): void {
    // Se registra el error completo en el log del servidor para soporte.
    error_log(sprintf('[%s] %s en %s:%d', $e::class, $e->getMessage(), $e->getFile(), $e->getLine()));

    $wantsJson = str_starts_with((string) ($_GET['route'] ?? ''), 'api/');
    $detail = $config['app']['debug'] ? $e->getMessage() : null;

    http_response_code(500);

    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'No se pudo completar la consulta. Revisa la conexión a la base de datos.',
            'detail' => $detail,
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    $title = 'No se pudo cargar la información';
    require BASE_PATH . '/app/Views/error.php';
});
