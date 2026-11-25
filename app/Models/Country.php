<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de la tabla `country` (País).
 *
 * Clave primaria: `Code` (código ISO de 3 letras, ej. COL).
 */
final class Country extends Model
{
    /**
     * Lista todos los países de la base de datos, ordenados por continente y nombre,
     * junto con la cantidad de ciudades registradas de cada uno.
     *
     * @return list<array{code: string, name: string, continent: string, cities: int}>
     */
    public function all(): array
    {
        $sql = 'SELECT co.Code AS code, co.Name AS name, co.Continent AS continent,
                       COUNT(ci.ID) AS cities
                  FROM country co
             LEFT JOIN city ci ON ci.CountryCode = co.Code
              GROUP BY co.Code, co.Name, co.Continent
              ORDER BY co.Continent, co.Name';

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca un país por su código e incluye el nombre de su capital.
     *
     * @return array<string, mixed>|null null si el código no existe.
     */
    public function find(string $code): ?array
    {
        $sql = 'SELECT co.Code AS code, co.Code2 AS code2, co.Name AS name,
                       co.Continent AS continent, co.Region AS region,
                       co.Population AS population, cap.Name AS capital
                  FROM country co
             LEFT JOIN city cap ON cap.ID = co.Capital
                 WHERE co.Code = :code';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        $country = $stmt->fetch();

        return $country === false ? null : $country;
    }
}
