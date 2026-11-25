<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Modelo de la tabla `city` (Ciudad).
 *
 * Relación: muchas ciudades pertenecen a un país (city.CountryCode -> country.Code).
 */
final class City extends Model
{
    /**
     * Todas las ciudades de un país, de la más poblada a la menos poblada.
     *
     * @return list<array{id: int, name: string, district: string, population: int}>
     */
    public function byCountry(string $countryCode): array
    {
        $sql = 'SELECT ID AS id, Name AS name, District AS district, Population AS population
                  FROM city
                 WHERE CountryCode = :code
              ORDER BY Population DESC, Name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $countryCode]);

        return $stmt->fetchAll();
    }

    /**
     * Ranking de ciudades de un país en escala de N (por defecto 10).
     *
     * @param string $order 'DESC' = más pobladas, 'ASC' = menos pobladas.
     * @return list<array{id: int, name: string, district: string, population: int}>
     */
    public function rankingByCountry(string $countryCode, int $limit = 10, string $order = 'DESC'): array
    {
        // ORDER BY no admite parámetros preparados: se valida contra una lista blanca.
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException('El orden debe ser ASC o DESC.');
        }

        $sql = "SELECT ID AS id, Name AS name, District AS district, Population AS population
                  FROM city
                 WHERE CountryCode = :code
              ORDER BY Population {$order}, Name ASC
                 LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('code', $countryCode);
        $stmt->bindValue('limit', max(1, $limit), \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
