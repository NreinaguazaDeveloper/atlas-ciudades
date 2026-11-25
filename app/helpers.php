<?php

declare(strict_types=1);

/**
 * Funciones auxiliares usadas en las vistas.
 */

/**
 * Escapa un valor para imprimirlo de forma segura en HTML (previene XSS).
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Formatea un número entero con separador de miles colombiano (1.234.567).
 */
function num(int|float|null $value, int $decimals = 0): string
{
    return number_format((float) $value, $decimals, ',', '.');
}

/**
 * Traduce al español el nombre del continente almacenado en la BD (en inglés).
 */
function continent_es(string $continent): string
{
    return match ($continent) {
        'Asia' => 'Asia',
        'Europe' => 'Europa',
        'North America' => 'América del Norte',
        'South America' => 'América del Sur',
        'Africa' => 'África',
        'Oceania' => 'Oceanía',
        'Antarctica' => 'Antártida',
        default => $continent,
    };
}
