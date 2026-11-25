<?php
/**
 * Layout principal (estructura HTML común a todas las pantallas).
 *
 * @var string $appName Nombre de la aplicación (config/config.php)
 * @var string $content HTML de la vista renderizada
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Consulta las ciudades de cada país y su población: las 10 más pobladas y las 10 menos pobladas.">
    <title><?= e($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Public+Sans:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🧭</text></svg>">
</head>
<body>
    <a class="skip" href="#contenido">Saltar al contenido</a>

    <header class="site-header">
        <a class="brand" href="index.php"><?= e($appName) ?></a>
        <span class="brand-note">Población por ciudad para planear tu viaje</span>
    </header>

    <main id="contenido" class="page">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <p>Datos de la base de datos World de MySQL. Son cifras de referencia, no censos actualizados.</p>
    </footer>

    <script src="assets/js/app.js" defer></script>
</body>
</html>
