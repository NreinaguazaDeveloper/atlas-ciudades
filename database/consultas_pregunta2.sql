-- =====================================================================
-- PRUEBA ANALISTA DE SOPORTE NIVEL 1 - PREGUNTA 2
-- Base de datos: world (MySQL)
-- Probado en MySQL/MariaDB con el world.sql estándar
-- (tablas city, country, countrylanguage).
-- =====================================================================

USE world;

-- ---------------------------------------------------------------------
-- 1. Todas las ciudades con una población menor o igual a 100.000 habitantes.
--    Resultado esperado con el world.sql estándar: 521 filas.
-- ---------------------------------------------------------------------
SELECT ci.ID,
       ci.Name        AS ciudad,
       co.Name        AS pais,
       ci.District    AS distrito,
       ci.Population  AS poblacion
  FROM city ci
  JOIN country co ON co.Code = ci.CountryCode
 WHERE ci.Population <= 100000
 ORDER BY ci.Population DESC, ci.Name;

-- ---------------------------------------------------------------------
-- 2. Conteo de las ciudades cuyo nombre comienza con la letra "R".
--    Resultado esperado con el world.sql estándar: 110.
--    LIKE 'R%' usa el índice si existe y, con la intercalación por defecto
--    (latin1_swedish_ci), no distingue mayúsculas de minúsculas.
-- ---------------------------------------------------------------------
SELECT COUNT(*) AS total_ciudades_con_r
  FROM city
 WHERE Name LIKE 'R%';

-- ---------------------------------------------------------------------
-- 3. Top 10 de las ciudades (por población) donde se habla español.
--
--    Versión A (recomendada): países donde el español es idioma OFICIAL.
-- ---------------------------------------------------------------------
SELECT ci.Name        AS ciudad,
       co.Name        AS pais,
       ci.Population  AS poblacion,
       cl.Percentage  AS porcentaje_hablantes
  FROM city ci
  JOIN country co          ON co.Code = ci.CountryCode
  JOIN countrylanguage cl  ON cl.CountryCode = ci.CountryCode
 WHERE cl.Language   = 'Spanish'
   AND cl.IsOfficial = 'T'
 ORDER BY ci.Population DESC
 LIMIT 10;

-- ---------------------------------------------------------------------
--    Versión B: cualquier país donde se hable español, aunque no sea oficial.
--    (Incluye ciudades de Estados Unidos, donde el 7,5 % habla español.)
-- ---------------------------------------------------------------------
SELECT ci.Name        AS ciudad,
       co.Name        AS pais,
       ci.Population  AS poblacion,
       cl.IsOfficial  AS es_oficial,
       cl.Percentage  AS porcentaje_hablantes
  FROM city ci
  JOIN country co          ON co.Code = ci.CountryCode
  JOIN countrylanguage cl  ON cl.CountryCode = ci.CountryCode
 WHERE cl.Language = 'Spanish'
 ORDER BY ci.Population DESC
 LIMIT 10;
