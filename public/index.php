<?php

declare(strict_types=1);

/**
 * Front controller (único punto de entrada de la aplicación).
 *
 * Todas las peticiones llegan a este archivo. Aquí se cargan las
 * dependencias, se registran las rutas y se despacha la petición
 * al controlador correspondiente (patrón MVC).
 *
 * Rutas disponibles (por query string, no requiere mod_rewrite):
 *   index.php                         -> Pantalla 1 (selector de país)
 *   index.php?country=COL             -> Pantalla 1 con el país seleccionado
 *   index.php?route=api/countries     -> JSON con todos los países
 *   index.php?route=api/cities&code=COL -> JSON con ciudades, rankings e idiomas
 */

use App\Controllers\ApiController;
use App\Controllers\HomeController;
use App\Core\Router;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/bootstrap.php';

$router = new Router();

$router->get('home', [HomeController::class, 'index']);
$router->get('api/countries', [ApiController::class, 'countries']);
$router->get('api/cities', [ApiController::class, 'cities']);

$router->dispatch(
    (string) ($_GET['route'] ?? 'home'),
    (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
);
