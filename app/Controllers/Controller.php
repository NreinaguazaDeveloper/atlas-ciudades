<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Controlador base: utilidades para responder con vistas HTML o con JSON.
 */
abstract class Controller
{
    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $view Ruta relativa dentro de app/Views (sin .php).
     * @param array<string, mixed> $data Variables disponibles en la vista.
     */
    protected function view(string $view, array $data = []): void
    {
        $config = require BASE_PATH . '/config/config.php';
        $appName = $config['app']['name'];

        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . "/app/Views/{$view}.php";
        $content = ob_get_clean();

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /**
     * Responde JSON con el código HTTP indicado.
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
