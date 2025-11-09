-- ============================================
-- SCRIPT 1: CREAR TABLAS DE MARCAS Y MODELOS
-- ============================================
-- Ejecutar en servidor de producción
-- Fecha: 2025-11-08

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

-- Paso 3: Crear tabla para tracking de marcas/modelos pendientes
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

-- Verificar creación
SELECT 'Tablas creadas exitosamente' AS resultado;
SELECT COUNT(*) AS total_marcas FROM marcas;
SELECT COUNT(*) AS total_modelos FROM modelos;
