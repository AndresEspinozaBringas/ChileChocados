-- Agregar nuevos estados a la tabla pagos_flow
-- Para manejar mejor el ciclo de vida de los pagos

-- Modificar el ENUM de estado para incluir más estados
ALTER TABLE pagos_flow 
MODIFY COLUMN estado ENUM(
    'pendiente',      -- Pago creado, esperando que el usuario vaya a Flow
    'en_proceso',     -- Usuario redirigido a Flow, pago en proceso
    'aprobado',       -- Pago confirmado por Flow
    'rechazado',      -- Pago rechazado por Flow o banco
    'expirado',       -- Pago expiró sin completarse
    'cancelado',      -- Usuario canceló el pago
    'error'           -- Error técnico al procesar
) NOT NULL DEFAULT 'pendiente';

-- Agregar índice para búsquedas por estado
CREATE INDEX idx_pagos_estado_fecha ON pagos_flow(estado, fecha_creacion);

-- Agregar columna para intentos de pago
ALTER TABLE pagos_flow 
ADD COLUMN intentos INT UNSIGNED DEFAULT 1 COMMENT 'Número de intentos de pago';

-- Agregar columna para fecha de expiración
ALTER TABLE pagos_flow 
ADD COLUMN fecha_expiracion DATETIME NULL COMMENT 'Fecha límite para completar el pago';

-- Agregar columna para notas/observaciones
ALTER TABLE pagos_flow 
ADD COLUMN notas TEXT NULL COMMENT 'Notas adicionales sobre el pago';

-- Comentarios en la tabla
ALTER TABLE pagos_flow COMMENT = 'Registro de pagos procesados con Flow para publicaciones destacadas';
