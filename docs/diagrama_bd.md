# Diagrama de la base de datos `world`

![Diagrama entidad-relación](diagrama_bd.png)

```mermaid
erDiagram
    COUNTRY ||--o{ CITY : "tiene"
    COUNTRY ||--o{ COUNTRYLANGUAGE : "habla"
    COUNTRY |o--o| CITY : "capital"

    COUNTRY {
        char(3) Code PK
        char(52) Name
        enum Continent
        char(26) Region
        float SurfaceArea
        smallint IndepYear
        int Population
        float LifeExpectancy
        float GNP
        float GNPOld
        char(45) LocalName
        char(45) GovernmentForm
        char(60) HeadOfState
        int Capital FK "city.ID"
        char(2) Code2
    }

    CITY {
        int ID PK
        char(35) Name
        char(3) CountryCode FK "country.Code"
        char(20) District
        int Population
    }

    COUNTRYLANGUAGE {
        char(3) CountryCode PK, FK "country.Code"
        char(30) Language PK
        enum IsOfficial "T / F"
        float Percentage
    }
```

## Relaciones

| Relación | Tipo | Llave |
|---|---|---|
| country → city | 1 a muchos (un país tiene muchas ciudades; cada ciudad pertenece a un solo país) | `city.CountryCode` → `country.Code` (FK) |
| country → countrylanguage | 1 a muchos (un país habla varios idiomas) | `countrylanguage.CountryCode` → `country.Code` (FK) |
| country → city (capital) | Cada país puede tener una capital | `country.Capital` → `city.ID` (relación lógica, sin FK declarada) |

## Tablas que usa la aplicación

| Tabla | Para qué la usa la app |
|---|---|
| `country` | Llenar el selector de países y mostrar población, región y capital |
| `city` | Listar las ciudades del país, el Top 10 más y menos pobladas |
| `countrylanguage` | Mostrar los idiomas que se hablan en el país |
