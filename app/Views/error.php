<?php
/**
 * Página de error amigable (sin detalles técnicos salvo APP_DEBUG=true).
 *
 * @var string $title
 * @var string|null $detail
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="page">
        <section class="error-page">
            <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p>La aplicación no pudo conectarse a la base de datos <strong>world</strong>. Verifica que MySQL esté encendido y que los datos del archivo <code>.env</code> sean correctos.</p>
            <?php if (!empty($detail)): ?>
                <pre class="error-detail"><?= htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') ?></pre>
            <?php endif; ?>
            <p><a href="index.php">Volver a intentar</a></p>
        </section>
    </main>
</body>
</html>
