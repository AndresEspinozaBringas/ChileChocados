-- ============================================
-- CONSULTAS ÚTILES - MARCAS Y MODELOS
-- ============================================
-- Consultas para verificar, administrar y depurar el sistema

-- ============================================
-- VERIFICACIÓN BÁSICA
-- ============================================

-- Ver total de marcas
SELECT COUNT(*) as total_marcas FROM marcas;

-- Ver total de modelos
SELECT COUNT(*) as total_modelos FROM modelos;

-- Ver total de publicaciones
SELECT COUNT(*) as total_publicaciones FROM publicaciones;

-- Ver total de pendientes
SELECT COUNT(*) as total_pendientes FROM marcas_modelos_pendientes;

-- ============================================
-- MARCAS Y MODELOS
-- ============================================

-- Ver todas las marcas ordenadas alfabéticamente
SELECT id, nombre, cantidad_modelos, activa
FROM marcas
ORDER BY nombre;

-- Top 10 marcas con más modelos
SELECT m.nombre as marca, COUNT(mo.id) as total_modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
GROUP BY m.id
ORDER BY total_modelos DESC
LIMIT 10;

-- Ver modelos de una marca específica (ejemplo: Toyota)
SELECT mo.id, mo.nombre, mo.activo
FROM modelos mo
JOIN marcas m ON mo.marca_id = m.id
WHERE m.nombre = 'Toyota'
ORDER BY mo.nombre;

-- Buscar marca por nombre (búsqueda parcial)
SELECT id, nombre, cantidad_modelos
FROM marcas
WHERE nombre LIKE '%toyota%'
ORDER BY nombre;

-- Buscar modelo por nombre en todas las marcas
SELECT m.nombre as marca, mo.nombre as modelo
FROM modelos mo
JOIN marcas m ON mo.marca_id = m.id
WHERE mo.nombre LIKE '%corolla%'
ORDER BY m.nombre, mo.nombre;

-- ============================================
-- PUBLICACIONES
-- ============================================

-- Ver publicaciones con marca/modelo personalizado
SELECT id, titulo, marca, modelo, marca_personalizada, modelo_personalizado
FROM publicaciones
WHERE marca_personalizada = 1 OR modelo_personalizado = 1
ORDER BY fecha_creacion DESC
LIMIT 20;

-- Ver publicaciones pendientes de aprobación
SELECT id, titulo, marca, modelo, marca_original, modelo_original
FROM publicaciones
WHERE (marca_personalizada = 1 OR modelo_personalizado = 1)
AND marca_modelo_aprobado = 0
ORDER BY fecha_creacion DESC;

-- Ver publicaciones aprobadas
SELECT id, titulo, marca, modelo
FROM publicaciones
WHERE marca_modelo_aprobado = 1
ORDER BY fecha_creacion DESC
LIMIT 20;

-- Estadísticas de publicaciones
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN marca_personalizada = 1 THEN 1 ELSE 0 END) as con_marca_personalizada,
    SUM(CASE WHEN modelo_personalizado = 1 THEN 1 ELSE 0 END) as con_modelo_personalizado,
    SUM(CASE WHEN marca_modelo_aprobado = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN marca_modelo_aprobado = 0 THEN 1 ELSE 0 END) as pendientes
FROM publicaciones;

-- ============================================
-- MARCAS/MODELOS PENDIENTES
-- ============================================

-- Ver todos los pendientes
SELECT 
    p.id,
    p.marca_ingresada,
    p.modelo_ingresado,
    p.estado,
    pub.titulo,
    p.fecha_creacion
FROM marcas_modelos_pendientes p
JOIN publicaciones pub ON p.publicacion_id = pub.id
ORDER BY p.fecha_creacion DESC;

-- Ver pendientes por estado
SELECT estado, COUNT(*) as total
FROM marcas_modelos_pendientes
GROUP BY estado;

-- Ver marcas/modelos más solicitados (pendientes)
SELECT 
    marca_ingresada,
    modelo_ingresado,
    COUNT(*) as veces_solicitado
FROM marcas_modelos_pendientes
WHERE estado = 'pendiente'
GROUP BY marca_ingresada, modelo_ingresado
ORDER BY veces_solicitado DESC
LIMIT 20;

-- ============================================
-- MANTENIMIENTO
-- ============================================

-- Actualizar contador de modelos por marca
UPDATE marcas m
SET cantidad_modelos = (
    SELECT COUNT(*) 
    FROM modelos mo 
    WHERE mo.marca_id = m.id
);

-- Desactivar una marca (no se mostrará en el autocompletado)
UPDATE marcas SET activa = 0 WHERE nombre = 'MarcaADesactivar';

-- Activar una marca
UPDATE marcas SET activa = 1 WHERE nombre = 'MarcaAActivar';

-- Desactivar un modelo
UPDATE modelos SET activo = 0 WHERE id = 123;

-- Ver marcas sin modelos
SELECT m.id, m.nombre, m.cantidad_modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
WHERE mo.id IS NULL;

-- ============================================
-- AGREGAR DATOS MANUALMENTE
-- ============================================

-- Agregar una nueva marca
INSERT INTO marcas (nombre, cantidad_modelos, activa) 
VALUES ('Nueva Marca', 0, 1);

-- Agregar un nuevo modelo (necesitas el ID de la marca)
INSERT INTO modelos (marca_id, nombre, activo) 
VALUES (1, 'Nuevo Modelo', 1);

-- Agregar marca con sus modelos
INSERT INTO marcas (nombre, cantidad_modelos, activa) 
VALUES ('Tesla', 4, 1);

SET @marca_id = LAST_INSERT_ID();

INSERT INTO modelos (marca_id, nombre, activo) VALUES
(@marca_id, 'Model S', 1),
(@marca_id, 'Model 3', 1),
(@marca_id, 'Model X', 1),
(@marca_id, 'Model Y', 1);

-- ============================================
-- APROBAR MARCA/MODELO PERSONALIZADO
-- ============================================

-- Aprobar una publicación con marca/modelo personalizado
UPDATE publicaciones 
SET marca_modelo_aprobado = 1 
WHERE id = 123;

-- Aprobar y agregar al catálogo
-- Paso 1: Agregar marca si no existe
INSERT INTO marcas (nombre, cantidad_modelos, activa) 
VALUES ('Marca Nueva', 1, 1)
ON DUPLICATE KEY UPDATE cantidad_modelos = cantidad_modelos + 1;

-- Paso 2: Obtener ID de marca
SET @marca_id = (SELECT id FROM marcas WHERE nombre = 'Marca Nueva');

-- Paso 3: Agregar modelo
INSERT INTO modelos (marca_id, nombre, activo) 
VALUES (@marca_id, 'Modelo Nuevo', 1)
ON DUPLICATE KEY UPDATE activo = 1;

-- Paso 4: Aprobar publicación
UPDATE publicaciones 
SET marca_modelo_aprobado = 1 
WHERE id = 123;

-- ============================================
-- REPORTES Y ESTADÍSTICAS
-- ============================================

-- Marcas más usadas en publicaciones
SELECT marca, COUNT(*) as total_publicaciones
FROM publicaciones
GROUP BY marca
ORDER BY total_publicaciones DESC
LIMIT 20;

-- Modelos más usados en publicaciones
SELECT marca, modelo, COUNT(*) as total_publicaciones
FROM publicaciones
GROUP BY marca, modelo
ORDER BY total_publicaciones DESC
LIMIT 20;

-- Publicaciones por marca (con nombre de marca del catálogo)
SELECT m.nombre as marca, COUNT(p.id) as total_publicaciones
FROM marcas m
LEFT JOIN publicaciones p ON m.nombre = p.marca
GROUP BY m.id
ORDER BY total_publicaciones DESC;

-- Ver publicaciones sin marca/modelo en el catálogo
SELECT DISTINCT p.marca, p.modelo
FROM publicaciones p
LEFT JOIN marcas m ON p.marca = m.nombre
WHERE m.id IS NULL
ORDER BY p.marca, p.modelo;

-- ============================================
-- LIMPIEZA Y MANTENIMIENTO
-- ============================================

-- Eliminar modelos duplicados (mantener el más antiguo)
DELETE mo1 FROM modelos mo1
INNER JOIN modelos mo2 
WHERE mo1.id > mo2.id 
AND mo1.marca_id = mo2.marca_id 
AND mo1.nombre = mo2.nombre;

-- Eliminar marcas sin modelos y sin publicaciones
DELETE m FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
LEFT JOIN publicaciones p ON m.nombre = p.marca
WHERE mo.id IS NULL AND p.id IS NULL;

-- Limpiar pendientes antiguos (más de 6 meses)
DELETE FROM marcas_modelos_pendientes
WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL 6 MONTH)
AND estado IN ('aprobado', 'rechazado');

-- ============================================
-- BACKUP Y EXPORTACIÓN
-- ============================================

-- Exportar marcas a CSV (ejecutar desde línea de comandos)
-- mysql -u usuario -p -e "SELECT * FROM marcas" base_datos > marcas.csv

-- Exportar modelos a CSV
-- mysql -u usuario -p -e "SELECT m.nombre as marca, mo.nombre as modelo FROM modelos mo JOIN marcas m ON mo.marca_id = m.id ORDER BY m.nombre, mo.nombre" base_datos > modelos.csv

-- ============================================
-- ÍNDICES Y OPTIMIZACIÓN
-- ============================================

-- Ver índices de una tabla
SHOW INDEX FROM marcas;
SHOW INDEX FROM modelos;
SHOW INDEX FROM publicaciones;

-- Analizar tabla (optimización)
ANALYZE TABLE marcas;
ANALYZE TABLE modelos;
ANALYZE TABLE publicaciones;

-- Optimizar tabla
OPTIMIZE TABLE marcas;
OPTIMIZE TABLE modelos;
OPTIMIZE TABLE publicaciones;

-- Ver tamaño de las tablas
SELECT 
    table_name AS 'Tabla',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Tamaño (MB)'
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
AND table_name IN ('marcas', 'modelos', 'publicaciones', 'marcas_modelos_pendientes')
ORDER BY (data_length + index_length) DESC;
