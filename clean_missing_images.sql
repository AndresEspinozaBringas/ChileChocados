-- Script para eliminar registros de imágenes que no existen físicamente
-- Solo mantener la imagen que sí existe: pub_5_68ff6754dfae2.jpg

-- Ver qué imágenes están registradas para la publicación 5
SELECT id, publicacion_id, ruta, es_principal, orden 
FROM publicacion_fotos 
WHERE publicacion_id = 5;

-- Eliminar las imágenes que NO existen físicamente
DELETE FROM publicacion_fotos 
WHERE publicacion_id = 5 
AND ruta IN (
    '2025/10/pub_5_68ff6754de468.jpg',
    '2025/10/pub_5_68ff6754df3fa.jpg',
    '2025/10/pub_5_68ff6754e023e.jpg'
);

-- Actualizar la imagen que sí existe como principal
UPDATE publicacion_fotos 
SET es_principal = 1, orden = 1
WHERE publicacion_id = 5 
AND ruta = '2025/10/pub_5_68ff6754dfae2.jpg';

-- Actualizar la foto principal en la tabla publicaciones
UPDATE publicaciones 
SET foto_principal = '2025/10/pub_5_68ff6754dfae2.jpg'
WHERE id = 5;

-- Verificar los cambios
SELECT id, ruta, es_principal, orden FROM publicacion_fotos WHERE publicacion_id = 5;
SELECT id, titulo, foto_principal FROM publicaciones WHERE id = 5;
