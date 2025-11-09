-- Migración: Agregar soporte para marcas y modelos personalizados
-- Fecha: 2025-11-08
-- Descripción: Permite a usuarios ingresar marcas/modelos no listados que requieren aprobación admin

-- Paso 1: Agregar campos a tabla publicaciones
ALTER TABLE publicaciones 
ADD COLUMN marca_personalizada TINYINT(1) DEFAULT 0 COMMENT 'Indica si la marca fue ingresada manualmente',
ADD COLUMN modelo_personalizado TINYINT(1) DEFAULT 0 COMMENT 'Indica si el modelo fue ingresado manualmente',
ADD COLUMN marca_original VARCHAR(100) NULL COMMENT 'Marca ingresada por usuario antes de aprobación',
ADD COLUMN modelo_original VARCHAR(100) NULL COMMENT 'Modelo ingresado por usuario antes de aprobación',
ADD COLUMN marca_modelo_aprobado TINYINT(1) DEFAULT 0 COMMENT 'Indica si admin aprobó marca/modelo personalizado',
ADD INDEX idx_marca_personalizada (marca_personalizada),
ADD INDEX idx_modelo_personalizado (modelo_personalizado),
ADD INDEX idx_marca_modelo_aprobado (marca_modelo_aprobado);

-- Paso 2: Crear tabla para tracking de marcas/modelos pendientes
CREATE TABLE IF NOT EXISTS marcas_modelos_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT NOT NULL,
    marca_ingresada VARCHAR(100) NOT NULL,
    modelo_ingresado VARCHAR(100) NOT NULL,
    marca_sugerida VARCHAR(100) NULL COMMENT 'Marca sugerida por admin',
    modelo_sugerido VARCHAR(100) NULL COMMENT 'Modelo sugerido por admin',
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'modificado') DEFAULT 'pendiente',
    notas_admin TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    admin_id INT NULL,
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
