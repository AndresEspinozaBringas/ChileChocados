-- ============================================
-- MIGRACIÓN: Agregar campo avatar a usuarios
-- ============================================
-- Fecha: 2025-11-08
-- Descripción: Agrega el campo avatar para almacenar la foto de perfil

-- Agregar campo avatar si no existe
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL COMMENT 'Nombre del archivo de avatar (thumbnail 200x200)';

-- Agregar índice para búsquedas
ALTER TABLE usuarios 
ADD INDEX IF NOT EXISTS idx_avatar (avatar);

-- Verificar cambios
SELECT 'Campo avatar agregado exitosamente' AS resultado;
SHOW COLUMNS FROM usuarios LIKE 'avatar';
