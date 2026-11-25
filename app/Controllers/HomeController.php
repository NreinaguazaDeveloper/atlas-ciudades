<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Country;
use App\Services\CountryReport;

/**
 * Pantalla 1: selector de país + ciudades y población del país elegido.
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $countries = (new Country())->all();

        $requested = $_GET['country'] ?? null;
        $code = CountryReport::sanitizeCode($requested);
        $report = null;
        $error = null;

        if ($requested !== null && $requested !== '') {
            if ($code === null) {
                $error = 'Selecciona un país de la lista.';
            } else {
                $report = (new CountryReport())->build($code);
                $error = $report === null ? 'Ese país no está en la base de datos.' : null;
            }
        }

        $this->view('home', [
            'countries' => $countries,
            'selected' => $code,
            'report' => $report,
            'error' => $error,
            'scale' => CountryReport::SCALE,
        ]);
    }
}
