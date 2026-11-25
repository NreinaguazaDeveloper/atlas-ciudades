<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Enrutador sencillo basado en el parámetro ?route=.
 *
 * Se eligió el query string (en lugar de URLs "bonitas") para que la app
 * funcione igual en XAMPP/WAMP, Apache sin mod_rewrite o el servidor
 * embebido de PHP, sin configuración adicional.
 */
final class Router
{
    /** @var array<string, array{0: class-string, 1: string}> Rutas GET registradas */
    private array $routes = [];

    /**
     * Registra una ruta GET.
     *
     * @param string $route   Nombre de la ruta (ej. "api/cities").
     * @param array{0: class-string, 1: string} $handler [Controlador::class, 'método']
     */
    public function get(string $route, array $handler): void
    {
        $this->routes[$this->normalize($route)] = $handler;
    }

    /**
     * Ejecuta el controlador asociado a la ruta o responde 404/405.
     */
    public function dispatch(string $route, string $method): void
    {
        $route = $this->normalize($route);

        if (!isset($this->routes[$route])) {
            $this->abort(404, 'La ruta solicitada no existe.');
            return;
        }

        if (strtoupper($method) !== 'GET') {
            header('Allow: GET');
            $this->abort(405, 'Método no permitido. Esta aplicación solo acepta GET.');
            return;
        }

        [$controllerClass, $action] = $this->routes[$route];
        (new $controllerClass())->{$action}();
    }

    private function normalize(string $route): string
    {
        $route = trim($route, "/ \t\n\r\0\x0B");

        return $route === '' ? 'home' : strtolower($route);
    }

    private function abort(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    }
}
