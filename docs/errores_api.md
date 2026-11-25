# Pregunta 4 · Errores identificados al consumir las APIs

Colección: [`postman/Prueba_Soporte_APIs_Clima_Moneda.postman_collection.json`](postman/Prueba_Soporte_APIs_Clima_Moneda.postman_collection.json)

> Las respuestas de ejemplo siguen la documentación de cada proveedor.
> Al ejecutar la colección, reemplaza cada bloque por tu propia captura de Postman (evidencia).

## Cómo reproducir

1. Postman → **Import** → seleccionar el archivo `.json` de la colección.
2. Abrir la colección → pestaña **Variables** → revisar `city`, `base_currency`, `target_currency`, `owm_api_key`.
3. Clic derecho en la colección → **Run collection** para ejecutar todas las peticiones y sus tests.

---

## E1 · OpenWeatherMap responde `401 Unauthorized`

**Petición:** `GET https://api.openweathermap.org/data/2.5/weather?q=Bogota,CO&units=metric&lang=es&appid={{owm_api_key}}`

**Respuesta:**

```json
{ "cod": 401, "message": "Invalid API key. Please see https://openweathermap.org/faq#error401 for more info." }
```

**Diagnóstico:** el servidor rechaza la autenticación. Causas posibles, en orden de probabilidad:

1. La variable `owm_api_key` está vacía (la colección se entrega sin llave por seguridad).
2. La llave es nueva: OpenWeatherMap tarda un tiempo en activarla después del registro.
3. La llave está mal copiada (espacios, caracteres de más) o se envió en un parámetro con otro nombre (`apikey` en lugar de `appid`).

**Solución:** crear una cuenta gratuita en openweathermap.org, copiar la llave desde *My API keys*, pegarla en el **Current value** de `owm_api_key`, esperar a que se active y volver a ejecutar. Mientras tanto, la colección usa **Open-Meteo**, que no requiere llave.

---

## E2 · Open-Meteo responde `400 Bad Request`

**Petición:** `GET https://api.open-meteo.com/v1/forecast?latitude=999&longitude=-74.08&current=temperature_2m`

**Respuesta:**

```json
{ "error": true, "reason": "Latitude must be in range of -90 to 90°. Given: 999.0." }
```

**Diagnóstico:** error del cliente (4xx). El parámetro `latitude` está fuera del rango válido. La API lo indica en el campo `reason`.

**Solución:** enviar coordenadas válidas (latitud −90 a 90, longitud −180 a 180) o usar primero la petición de geocodificación, que guarda `lat` y `lon` correctos en las variables de la colección.

---

## E3 · ExchangeRate-API devuelve `"result": "error"`

**Petición:** `GET https://open.er-api.com/v6/latest/XYZ`

**Respuesta:**

```json
{ "result": "error", "error-type": "unsupported-code" }
```

**Diagnóstico:** `XYZ` no es un código de moneda ISO 4217 soportado. Importante para soporte: esta API puede informar el error dentro del cuerpo, así que no basta con revisar el código HTTP; hay que validar el campo `result`.

**Solución:** usar códigos válidos de 3 letras (`USD`, `COP`, `EUR`). La colección valida `result === "success"` en el caso exitoso.

---

## E4 · Postman: `Could not get response` / `getaddrinfo ENOTFOUND`

**Diagnóstico:** la petición no llegó al servidor. Suele deberse a un dominio mal escrito, falta de conexión a internet o un proxy/firewall corporativo.

**Solución:** verificar la URL, probarla en el navegador, y revisar en Postman *Settings → Proxy* si la red usa proxy.

---

## E5 · App local: `connect ECONNREFUSED 127.0.0.1:8000`

**Diagnóstico:** el servidor de la aplicación no está encendido o corre en otro puerto.

**Solución:** iniciar la app con `php -S localhost:8000 -t public` (o encender Apache en XAMPP) y ajustar la variable `app_url`.

---

## E6 · App local: `422 Unprocessable Entity`

**Petición:** `GET {{app_url}}/index.php?route=api/cities&code=12`

**Respuesta:**

```json
{ "error": "El parámetro \"code\" es obligatorio y debe tener 3 letras (ej. COL)." }
```

**Diagnóstico:** error de validación esperado; el código de país debe ser ISO de 3 letras.

**Solución:** enviar un código válido, por ejemplo `COL`.
