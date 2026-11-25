<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Country;
use App\Services\CountryReport;

/**
 * API JSON de solo lectura (útil para probar desde Postman o integrar otro frontend).
 */
final class ApiController extends Controller
{
    /** GET index.php?route=api/countries */
    public function countries(): void
    {
        $this->json(['data' => (new Country())->all()]);
    }

    /** GET index.php?route=api/cities&code=COL */
    public function cities(): void
    {
        $code = CountryReport::sanitizeCode($_GET['code'] ?? null);

        if ($code === null) {
            $this->json(['error' => 'El parámetro "code" es obligatorio y debe tener 3 letras (ej. COL).'], 422);
            return;
        }

        $report = (new CountryReport())->build($code);

        if ($report === null) {
            $this->json(['error' => "No existe un país con el código {$code}."], 404);
            return;
        }

        $this->json(['data' => $report]);
    }
}
