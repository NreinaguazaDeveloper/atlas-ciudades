<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Conexión única (singleton) a MySQL mediante PDO.
 *
 * - ERRMODE_EXCEPTION: cualquier error SQL lanza una excepción controlable.
 * - EMULATE_PREPARES=false: sentencias preparadas reales (protección contra inyección SQL).
 * - charset utf8mb4: la BD World está en latin1; MySQL convierte a UTF-8 al leer,
 *   así nombres como "Santafé de Bogotá" se muestran correctamente.
 */
final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = (require BASE_PATH . '/config/config.php')['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        self::$connection = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$connection;
    }
}
