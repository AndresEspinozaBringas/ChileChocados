-- Migración: Crear tablas de marcas y modelos
-- Fecha: 2025-11-08
-- Descripción: Crea tablas para almacenar marcas y modelos en BD

-- Paso 1: Crear tabla de marcas
CREATE TABLE IF NOT EXISTS marcas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    cantidad_modelos INT UNSIGNED DEFAULT 0,
    activa TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paso 2: Crear tabla de modelos
CREATE TABLE IF NOT EXISTS modelos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marca_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE CASCADE,
    INDEX idx_marca_id (marca_id),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo),
    UNIQUE KEY unique_marca_modelo (marca_id, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paso 3: Agregar campos a tabla publicaciones
-- Nota: Si ya existen, MySQL dará error pero el script continuará
ALTER TABLE publicaciones 
ADD COLUMN marca_personalizada TINYINT(1) DEFAULT 0 COMMENT 'Indica si la marca fue ingresada manualmente';

ALTER TABLE publicaciones 
ADD COLUMN modelo_personalizado TINYINT(1) DEFAULT 0 COMMENT 'Indica si el modelo fue ingresado manualmente';

ALTER TABLE publicaciones 
ADD COLUMN marca_original VARCHAR(100) NULL COMMENT 'Marca ingresada por usuario antes de aprobación';

ALTER TABLE publicaciones 
ADD COLUMN modelo_original VARCHAR(100) NULL COMMENT 'Modelo ingresado por usuario antes de aprobación';

ALTER TABLE publicaciones 
ADD COLUMN marca_modelo_aprobado TINYINT(1) DEFAULT 0 COMMENT 'Indica si admin aprobó marca/modelo personalizado';

-- Agregar índices
ALTER TABLE publicaciones 
ADD INDEX idx_marca_personalizada (marca_personalizada);

ALTER TABLE publicaciones 
ADD INDEX idx_modelo_personalizado (modelo_personalizado);

ALTER TABLE publicaciones 
ADD INDEX idx_marca_modelo_aprobado (marca_modelo_aprobado);

-- Paso 4: Crear tabla para tracking de marcas/modelos pendientes (CORREGIDA)
CREATE TABLE IF NOT EXISTS marcas_modelos_pendientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT UNSIGNED NOT NULL,
    marca_ingresada VARCHAR(100) NOT NULL,
    modelo_ingresado VARCHAR(100) NOT NULL,
    marca_sugerida VARCHAR(100) NULL COMMENT 'Marca sugerida por admin',
    modelo_sugerido VARCHAR(100) NULL COMMENT 'Modelo sugerido por admin',
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'modificado') DEFAULT 'pendiente',
    notas_admin TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    admin_id INT UNSIGNED NULL,
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
