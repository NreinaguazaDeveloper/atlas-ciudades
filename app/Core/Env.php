<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Lector mínimo de archivos .env.
 *
 * Permite que las credenciales de la base de datos no queden escritas
 * en el código fuente ni en el repositorio (el .env está en .gitignore).
 */
final class Env
{
    /** @var array<string, string> Valores leídos del archivo .env */
    private static array $values = [];

    /**
     * Carga un archivo .env con líneas CLAVE=valor.
     * Ignora líneas vacías y comentarios (#). Si el archivo no existe, no falla:
     * se usarán los valores por defecto definidos en config/config.php.
     */
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            self::$values[$key] = trim($value, "\"'");
        }
    }

    /**
     * Devuelve el valor de una variable de entorno o el valor por defecto.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $fromServer = getenv($key);

        return self::$values[$key] ?? ($fromServer !== false ? $fromServer : $default);
    }
}
