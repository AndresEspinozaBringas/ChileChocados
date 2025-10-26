-- Migración manual para corregir tabla publicaciones
-- Ejecutar en phpMyAdmin o cliente MySQL

-- 1. Agregar campo tipificacion (ignorar error si ya existe)
ALTER TABLE publicaciones 
ADD COLUMN tipificacion VARCHAR(50) DEFAULT NULL COMMENT 'chocado o mecanico' 
AFTER titulo;

-- 2. Crear tabla publicaciones_fotos
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

-- 3. Asegurar que foto_principal existe (ignorar error si ya existe)
ALTER TABLE publicaciones 
ADD COLUMN foto_principal VARCHAR(255) DEFAULT NULL COMMENT 'URL de la foto principal' 
AFTER descripcion;
