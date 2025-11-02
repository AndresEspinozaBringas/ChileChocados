-- Insertar configuraciones por defecto del sistema
-- ChileChocados - Configuraciones iniciales

-- Eliminar configuraciones existentes (opcional, comentar si no se desea)
-- DELETE FROM configuraciones WHERE clave IN (
--     'precio_destacado_15_dias',
--     'precio_destacado_30_dias',
--     'minimo_fotos',
--     'maximo_fotos',
--     'tamano_maximo_imagen_mb',
--     'tamano_maximo_adjunto_mb'
-- );

-- Insertar configuraciones por defecto
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('precio_destacado_15_dias', '15000', 'int', 'Precio en CLP para destacar una publicación por 15 días'),
('precio_destacado_30_dias', '25000', 'int', 'Precio en CLP para destacar una publicación por 30 días'),
('minimo_fotos', '1', 'int', 'Cantidad mínima de fotos requeridas por publicación'),
('maximo_fotos', '6', 'int', 'Cantidad máxima de fotos permitidas por publicación'),
('tamano_maximo_imagen_mb', '5', 'float', 'Tamaño máximo en MB para cada imagen subida'),
('tamano_maximo_adjunto_mb', '10', 'float', 'Tamaño máximo en MB para archivos adjuntos en mensajes')
ON DUPLICATE KEY UPDATE 
    valor = VALUES(valor),
    tipo = VALUES(tipo),
    descripcion = VALUES(descripcion),
    fecha_actualizacion = NOW();

-- Verificar que se insertaron correctamente
SELECT * FROM configuraciones WHERE clave IN (
    'precio_destacado_15_dias',
    'precio_destacado_30_dias',
    'minimo_fotos',
    'maximo_fotos',
    'tamano_maximo_imagen_mb',
    'tamano_maximo_adjunto_mb'
);
