<?php
/**
 * Pantalla 1: selector de país y ciudades con su población.
 *
 * @var list<array{code: string, name: string, continent: string, cities: int}> $countries
 * @var string|null $selected Código del país seleccionado
 * @var array<string, mixed>|null $report Datos del país (ver App\Services\CountryReport)
 * @var string|null $error Mensaje de validación
 * @var int $scale Tamaño de los rankings (10)
 */

// Agrupa los países por continente para mostrarlos en <optgroup>.
$groups = [];
foreach ($countries as $item) {
    $groups[continent_es($item['continent'])][] = $item;
}
ksort($groups);

// Accesos rápidos para el estado vacío.
$quickPicks = ['COL' => 'Colombia', 'MEX' => 'México', 'ESP' => 'España', 'PER' => 'Perú', 'JPN' => 'Japón', 'ITA' => 'Italia'];
?>

<section class="finder" aria-labelledby="finder-title">
    <h1 id="finder-title">¿A qué país viajas?</h1>
    <p class="lede">Elige un destino y mira sus ciudades ordenadas por población.</p>

    <form method="get" action="index.php" class="finder-form" id="country-form">
        <label for="country">País de destino</label>
        <div class="finder-row">
            <select id="country" name="country" required>
                <option value="">Selecciona un país</option>
                <?php foreach ($groups as $continent => $items): ?>
                    <optgroup label="<?= e($continent) ?>">
                        <?php foreach ($items as $item): ?>
                            <option value="<?= e($item['code']) ?>" <?= $selected === $item['code'] ? 'selected' : '' ?>>
                                <?= e($item['name']) ?><?= (int) $item['cities'] === 0 ? ' (sin ciudades registradas)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ver ciudades</button>
        </div>
    </form>

    <?php if ($error !== null): ?>
        <p class="alert" role="alert"><?= e($error) ?></p>
    <?php endif; ?>
</section>

<?php if ($report === null): ?>

    <section class="starter" aria-label="Destinos sugeridos">
        <p>Algunos destinos para empezar:</p>
        <ul class="quick-picks">
            <?php foreach ($quickPicks as $code => $name): ?>
                <li><a href="index.php?country=<?= e($code) ?>"><?= e($name) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>

<?php else:
    $country = $report['country'];
    $stats = $report['stats'];
    $total = (int) $stats['cities'];
?>

    <section class="country" aria-labelledby="country-name">
        <header class="country-head">
            <p class="country-where">
                <?= e(continent_es($country['continent'])) ?><?= $country['region'] !== $country['continent'] ? ', ' . e($country['region']) : '' ?>
            </p>
            <h2 id="country-name" class="country-name"><?= e($country['name']) ?></h2>

            <dl class="facts">
                <div><dt>Población del país</dt><dd><?= num($country['population']) ?></dd></div>
                <div><dt>Ciudades registradas</dt><dd><?= num($total) ?></dd></div>
                <div>
                    <dt>Vive en esas ciudades</dt>
                    <dd><?= $stats['urban_share'] !== null ? num($stats['urban_share'], 1) . ' %' : 'Sin dato' ?></dd>
                </div>
                <div><dt>Capital</dt><dd><?= e($country['capital'] ?? 'Sin dato') ?></dd></div>
            </dl>

            <?php if (!empty($country['capital'])): ?>
                <p class="weather" data-weather data-city="<?= e($country['capital']) ?>" data-cc="<?= e($country['code2']) ?>">
                    <span class="weather-label">Clima ahora en <?= e($country['capital']) ?>:</span>
                    <span class="weather-value">consultando…</span>
                </p>
            <?php endif; ?>
        </header>

        <?php if ($report['languages'] !== []): ?>
            <section class="languages" aria-labelledby="lang-title">
                <h3 id="lang-title">Idiomas que se hablan</h3>
                <ul class="lang-list">
                    <?php foreach ($report['languages'] as $lang): ?>
                        <li class="<?= $lang['official'] ? 'is-official' : '' ?>">
                            <span class="lang-name"><?= e($lang['language']) ?></span>
                            <span class="lang-pct"><?= num($lang['percentage'], 1) ?> %</span>
                            <?php if ($lang['official']): ?><span class="lang-tag">oficial</span><?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($total === 0): ?>

            <p class="empty-note"><?= e($country['name']) ?> no tiene ciudades registradas en la base de datos. Prueba con otro país.</p>

        <?php else: ?>

            <div class="rankings">
                <?php
                $lists = $total === 1
                    ? [['Única ciudad registrada', $report['top_most'], 'most']]
                    : [
                        [sprintf('Las %d más pobladas', count($report['top_most'])), $report['top_most'], 'most'],
                        [sprintf('Las %d menos pobladas', count($report['top_least'])), $report['top_least'], 'least'],
                    ];

                foreach ($lists as [$title, $list, $kind]):
                    // Las barras se dibujan en proporción a la ciudad más grande de cada lista.
                    $max = max(1, ...array_column($list, 'population'));
                ?>
                    <section class="ranking ranking--<?= $kind ?>">
                        <h3><?= e($title) ?></h3>
                        <ol>
                            <?php foreach ($list as $city):
                                $width = max(2, round((int) $city['population'] * 100 / $max, 1));
                            ?>
                                <li style="--w: <?= $width ?>%">
                                    <span class="r-name"><?= e($city['name']) ?><small><?= e($city['district']) ?></small></span>
                                    <span class="r-pop"><?= num($city['population']) ?></span>
                                    <span class="r-bar" aria-hidden="true"></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </section>
                <?php endforeach; ?>
            </div>

            <?php if ($total > 1 && $total < $scale * 2): ?>
                <p class="note"><?= e($country['name']) ?> tiene <?= $total ?> ciudades registradas, por eso algunas aparecen en las dos listas.</p>
            <?php endif; ?>

            <section class="all-cities" aria-labelledby="all-title">
                <div class="all-head">
                    <h3 id="all-title">Todas las ciudades (<?= num($total) ?>)</h3>
                    <label class="filter">
                        <span>Buscar ciudad o distrito</span>
                        <input type="search" id="city-filter" autocomplete="off" placeholder="Escribe un nombre">
                    </label>
                </div>

                <div class="table-wrap">
                    <table id="cities-table">
                        <thead>
                            <tr>
                                <th scope="col" class="num" aria-sort="ascending"><button type="button" data-sort="rank" data-type="number">Puesto</button></th>
                                <th scope="col"><button type="button" data-sort="name" data-type="text">Ciudad</button></th>
                                <th scope="col"><button type="button" data-sort="district" data-type="text">Distrito</button></th>
                                <th scope="col" class="num"><button type="button" data-sort="pop" data-type="number">Población</button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['cities'] as $i => $city): ?>
                                <tr data-rank="<?= $i + 1 ?>" data-name="<?= e($city['name']) ?>" data-district="<?= e($city['district']) ?>" data-pop="<?= (int) $city['population'] ?>">
                                    <td class="num"><?= $i + 1 ?></td>
                                    <td><?= e($city['name']) ?></td>
                                    <td><?= e($city['district']) ?></td>
                                    <td class="num"><?= num($city['population']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p id="no-results" class="note" hidden>Ninguna ciudad coincide con esa búsqueda.</p>
            </section>

        <?php endif; ?>
    </section>

<?php endif; ?>
