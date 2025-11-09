-- ============================================
-- MIGRACIÓN URGENTE: Agregar campo avatar
-- ============================================
-- Ejecutar en producción inmediatamente
-- Fecha: 2025-11-08

-- Agregar campo avatar a tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN avatar VARCHAR(255) NULL COMMENT 'Nombre del archivo de avatar (thumbnail 200x200)';

-- Verificar que se agregó correctamente
SELECT 'Campo avatar agregado exitosamente' AS resultado;
SHOW COLUMNS FROM usuarios LIKE 'avatar';
