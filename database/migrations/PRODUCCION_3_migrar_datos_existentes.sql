-- ============================================
-- SCRIPT 3: MIGRAR DATOS EXISTENTES
-- ============================================
-- Ejecutar en servidor de producción
-- Fecha: 2025-11-08
-- IMPORTANTE: Este script actualiza las publicaciones existentes

-- Paso 1: Marcar todas las publicaciones existentes como NO personalizadas
-- (ya que fueron creadas antes del nuevo sistema)
UPDATE publicaciones 
SET 
    marca_personalizada = 0,
    modelo_personalizado = 0,
    marca_modelo_aprobado = 1
WHERE marca_personalizada IS NULL OR modelo_personalizado IS NULL;

-- Paso 2: Verificar publicaciones actualizadas
SELECT 
    COUNT(*) AS total_publicaciones,
    SUM(CASE WHEN marca_personalizada = 0 THEN 1 ELSE 0 END) AS con_marca_catalogo,
    SUM(CASE WHEN marca_personalizada = 1 THEN 1 ELSE 0 END) AS con_marca_personalizada,
    SUM(CASE WHEN marca_modelo_aprobado = 1 THEN 1 ELSE 0 END) AS aprobadas
FROM publicaciones;

-- Paso 3: Mostrar algunas publicaciones de ejemplo
SELECT 
    id,
    titulo,
    marca,
    modelo,
    marca_personalizada,
    modelo_personalizado,
    marca_modelo_aprobado
FROM publicaciones
ORDER BY fecha_creacion DESC
LIMIT 10;
