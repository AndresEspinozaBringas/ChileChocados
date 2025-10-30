-- Script para corregir las rutas de imágenes en la base de datos
-- Las rutas deben ser relativas desde /uploads/publicaciones/
-- Ejemplo: "2025/10/archivo.jpg" en lugar de "publicaciones/2025/10/archivo.jpg"

-- Actualizar tabla publicacion_fotos
UPDATE publicacion_fotos 
SET ruta = REPLACE(ruta, 'publicaciones/', '')
WHERE ruta LIKE 'publicaciones/%';

-- Actualizar tabla publicaciones (foto_principal)
UPDATE publicaciones 
SET foto_principal = REPLACE(foto_principal, 'publicaciones/', '')
WHERE foto_principal LIKE 'publicaciones/%';

-- Verificar los cambios
SELECT id, ruta, es_principal FROM publicacion_fotos LIMIT 10;
SELECT id, titulo, foto_principal FROM publicaciones WHERE foto_principal IS NOT NULL LIMIT 10;
