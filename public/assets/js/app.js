/**
 * Atlas de ciudades - comportamiento del lado del cliente.
 *
 * La aplicación funciona sin JavaScript (el formulario se envía con el botón).
 * Este archivo solo agrega mejoras:
 *   1. Envía el formulario automáticamente al cambiar el país.
 *   2. Filtra la tabla de ciudades mientras se escribe.
 *   3. Ordena la tabla al hacer clic en los encabezados.
 *   4. Consulta el clima actual de la capital con la API pública Open-Meteo.
 */
(function () {
    'use strict';

    /* 1. Envío automático al seleccionar un país */
    const form = document.getElementById('country-form');
    const select = document.getElementById('country');
    if (form && select) {
        select.addEventListener('change', function () {
            if (select.value) {
                form.submit();
            }
        });
    }

    /* Escalona la animación de las barras del ranking */
    document.querySelectorAll('.ranking li').forEach(function (li, index) {
        li.style.setProperty('--i', String(index % 10));
    });

    const table = document.getElementById('cities-table');
    if (table) {
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        /* 2. Filtro por ciudad o distrito (sin distinguir tildes ni mayúsculas) */
        const normalize = function (text) {
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
        };
        const filter = document.getElementById('city-filter');
        const noResults = document.getElementById('no-results');

        if (filter) {
            filter.addEventListener('input', function () {
                const term = normalize(filter.value);
                let visible = 0;
                rows.forEach(function (row) {
                    const haystack = normalize(row.dataset.name + ' ' + row.dataset.district);
                    const match = term === '' || haystack.includes(term);
                    row.hidden = !match;
                    if (match) visible++;
                });
                noResults.hidden = visible !== 0;
            });
        }

        /* 3. Ordenamiento por columna */
        table.querySelectorAll('th button[data-sort]').forEach(function (button) {
            button.addEventListener('click', function () {
                const th = button.closest('th');
                const key = button.dataset.sort;
                const isNumber = button.dataset.type === 'number';
                const ascending = th.getAttribute('aria-sort') !== 'ascending';

                table.querySelectorAll('th').forEach(function (cell) {
                    cell.removeAttribute('aria-sort');
                });
                th.setAttribute('aria-sort', ascending ? 'ascending' : 'descending');

                const sorted = rows.slice().sort(function (a, b) {
                    const x = a.dataset[key];
                    const y = b.dataset[key];
                    const result = isNumber ? Number(x) - Number(y) : x.localeCompare(y, 'es');
                    return ascending ? result : -result;
                });
                sorted.forEach(function (row) {
                    tbody.appendChild(row);
                });
            });
        });
    }

    /* 4. Clima actual de la capital (Open-Meteo, sin API key) */
    const weather = document.querySelector('[data-weather]');
    if (weather) {
        loadWeather(weather);
    }

    async function loadWeather(element) {
        const output = element.querySelector('.weather-value');
        const city = element.dataset.city;
        const countryCode = element.dataset.cc;

        // Algunos nombres de la BD son largos ("Santafé de Bogotá"): si no se
        // encuentra el nombre completo, se intenta con la última palabra.
        const candidates = Array.from(new Set([city, city.split(' ').pop()]));

        try {
            let place = null;
            for (const name of candidates) {
                const geoUrl = 'https://geocoding-api.open-meteo.com/v1/search?count=1&language=es&format=json'
                    + '&name=' + encodeURIComponent(name)
                    + '&countryCode=' + encodeURIComponent(countryCode);
                const geo = await fetchJson(geoUrl);
                if (geo.results && geo.results.length) {
                    place = geo.results[0];
                    break;
                }
            }
            if (!place) {
                output.textContent = 'no hay datos de clima para esta ciudad.';
                return;
            }

            const forecastUrl = 'https://api.open-meteo.com/v1/forecast?timezone=auto'
                + '&latitude=' + place.latitude
                + '&longitude=' + place.longitude
                + '&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code';
            const data = await fetchJson(forecastUrl);
            const now = data.current;

            output.textContent = Math.round(now.temperature_2m) + ' °C, '
                + describeWeather(now.weather_code)
                + ', humedad ' + now.relative_humidity_2m + ' %'
                + ', viento ' + Math.round(now.wind_speed_10m) + ' km/h';
        } catch (error) {
            console.warn('Open-Meteo:', error);
            output.textContent = 'no se pudo consultar ahora. Revisa tu conexión a internet.';
        }
    }

    async function fetchJson(url) {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ' en ' + url);
        }
        return response.json();
    }

    /** Traduce el código meteorológico WMO que devuelve Open-Meteo. */
    function describeWeather(code) {
        if (code === 0) return 'despejado';
        if (code <= 3) return 'parcialmente nublado';
        if (code <= 48) return 'niebla';
        if (code <= 57) return 'llovizna';
        if (code <= 67) return 'lluvia';
        if (code <= 77) return 'nieve';
        if (code <= 82) return 'chubascos';
        if (code <= 86) return 'chubascos de nieve';
        return 'tormenta';
    }
})();
