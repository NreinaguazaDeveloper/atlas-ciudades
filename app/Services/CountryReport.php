<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\City;
use App\Models\Country;
use App\Models\CountryLanguage;

/**
 * Arma el "reporte" de un país: datos generales, idiomas,
 * Top 10 más pobladas, Top 10 menos pobladas y listado completo.
 *
 * Lo usan tanto la vista HTML (HomeController) como la API JSON
 * (ApiController), así la lógica de negocio vive en un solo lugar.
 */
final class CountryReport
{
    /** Tamaño de la escala pedida en el requerimiento (Top 10). */
    public const SCALE = 10;

    private Country $countries;
    private City $cities;
    private CountryLanguage $languages;

    /**
     * Los modelos se pueden inyectar (útil para pruebas); si no, se crean aquí.
     * Compatible con PHP 8.0+.
     */
    public function __construct(?Country $countries = null, ?City $cities = null, ?CountryLanguage $languages = null)
    {
        $this->countries = $countries ?? new Country();
        $this->cities = $cities ?? new City();
        $this->languages = $languages ?? new CountryLanguage();
    }

    /**
     * Valida un código de país (3 letras A-Z). Devuelve el código en mayúsculas o null.
     */
    public static function sanitizeCode(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return preg_match('/^[A-Z]{3}$/', $code) === 1 ? $code : null;
    }

    /**
     * @return array<string, mixed>|null null si el país no existe.
     */
    public function build(string $code): ?array
    {
        $country = $this->countries->find($code);

        if ($country === null) {
            return null;
        }

        $allCities = $this->cities->byCountry($code);
        $citiesPopulation = array_sum(array_column($allCities, 'population'));
        $countryPopulation = (int) $country['population'];

        return [
            'country' => $country,
            'languages' => $this->languages->byCountry($code),
            'stats' => [
                'cities' => count($allCities),
                'cities_population' => $citiesPopulation,
                // % de la población del país que vive en las ciudades registradas.
                'urban_share' => $countryPopulation > 0
                    ? round($citiesPopulation * 100 / $countryPopulation, 1)
                    : null,
            ],
            'top_most' => $this->cities->rankingByCountry($code, self::SCALE, 'DESC'),
            'top_least' => $this->cities->rankingByCountry($code, self::SCALE, 'ASC'),
            'cities' => $allCities,
        ];
    }
}
