-- Migración para corregir tabla publicaciones y publicaciones_fotos
-- Fecha: 2025-10-25

-- Agregar campo tipificacion a publicaciones si no existe
ALTER TABLE publicaciones 
ADD COLUMN IF NOT EXISTS tipificacion VARCHAR(50) DEFAULT NULL COMMENT 'chocado o mecanico' 
AFTER titulo;

-- Crear tabla publicaciones_fotos si no existe
CREATE TABLE IF NOT EXISTS publicaciones_fotos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT UNSIGNED NOT NULL,
    url VARCHAR(255) NOT NULL COMMENT 'Ruta relativa de la imagen',
    orden TINYINT UNSIGNED DEFAULT 1 COMMENT 'Orden de visualización',
    es_principal TINYINT(1) DEFAULT 0 COMMENT '1 si es la foto principal',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    INDEX idx_publicacion (publicacion_id),
    INDEX idx_principal (es_principal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asegurar que el campo comuna_id existe y permite NULL
ALTER TABLE publicaciones 
MODIFY COLUMN comuna_id INT UNSIGNED NULL COMMENT 'ID de la comuna';

-- Asegurar que el campo foto_principal existe
ALTER TABLE publicaciones 
ADD COLUMN IF NOT EXISTS foto_principal VARCHAR(255) DEFAULT NULL COMMENT 'URL de la foto principal' 
AFTER descripcion;

-- Crear directorio de uploads si no existe (esto debe hacerse desde PHP)
-- Las imágenes se guardarán en: public/uploads/publicaciones/YYYY/MM/
