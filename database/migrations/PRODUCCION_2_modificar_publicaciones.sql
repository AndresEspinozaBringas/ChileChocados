-- ============================================
-- SCRIPT 2: MODIFICAR TABLA PUBLICACIONES
-- ============================================
-- Ejecutar en servidor de producción
-- Fecha: 2025-11-08
-- NOTA: Si alguna columna ya existe, simplemente ignora el error y continúa

-- Agregar campos para marcas/modelos personalizados
-- Si ya existen, recibirás error #1060 "Duplicate column name" - es normal, ignóralo

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
-- Si ya existen, recibirás error #1061 "Duplicate key name" - es normal, ignóralo

ALTER TABLE publicaciones 
ADD INDEX idx_marca_personalizada (marca_personalizada);

ALTER TABLE publicaciones 
ADD INDEX idx_modelo_personalizado (modelo_personalizado);

ALTER TABLE publicaciones 
ADD INDEX idx_marca_modelo_aprobado (marca_modelo_aprobado);

-- Verificar cambios
SELECT 'Columnas agregadas exitosamente' AS resultado;
SHOW COLUMNS FROM publicaciones LIKE '%marca%';
SHOW COLUMNS FROM publicaciones LIKE '%modelo%';
