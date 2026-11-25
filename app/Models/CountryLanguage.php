<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de la tabla `countrylanguage` (Idioma por país).
 *
 * Clave primaria compuesta: (CountryCode, Language).
 */
final class CountryLanguage extends Model
{
    /**
     * Idiomas hablados en un país, del más al menos hablado.
     *
     * @return list<array{language: string, official: bool, percentage: float}>
     */
    public function byCountry(string $countryCode): array
    {
        $sql = 'SELECT Language AS language, IsOfficial AS official, Percentage AS percentage
                  FROM countrylanguage
                 WHERE CountryCode = :code
              ORDER BY Percentage DESC, Language ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $countryCode]);

        return array_map(static fn (array $row): array => [
            'language' => $row['language'],
            'official' => $row['official'] === 'T',
            'percentage' => (float) $row['percentage'],
        ], $stmt->fetchAll());
    }
}
