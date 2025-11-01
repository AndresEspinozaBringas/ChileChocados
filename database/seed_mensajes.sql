-- Script para insertar mensajes de prueba
-- Asegúrate de tener usuarios y publicaciones existentes

-- Insertar mensajes de prueba entre usuarios
-- Conversación 1: Usuario 1 (comprador) contacta al vendedor de la publicación 1
INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje, leido, fecha_envio) VALUES
(1, 1, 2, 'Hola, me interesa este vehículo. ¿Está disponible?', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 2, 1, 'Sí, está disponible. ¿Te gustaría verlo?', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR 45 MINUTE)),
(1, 1, 2, '¿Cuál es el estado exacto del motor?', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR 30 MINUTE)),
(1, 2, 1, 'El motor está en perfectas condiciones. El choque fue solo frontal en la carrocería.', 0, DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- Conversación 2: Otro usuario contacta al mismo vendedor
INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje, leido, fecha_envio) VALUES
(1, 3, 2, '¿Acepta permuta?', 1, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(1, 2, 3, 'Depende del vehículo. ¿Qué tienes para ofrecer?', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR 30 MINUTE));

-- Conversación 3: Usuario contacta a otro vendedor
INSERT INTO mensajes (publicacion_id, remitente_id, destinatario_id, mensaje, leido, fecha_envio) VALUES
(2, 1, 3, 'Me interesan las piezas del motor', 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 3, 1, 'Perfecto, quedamos en contacto', 1, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Nota: Ajusta los IDs de publicacion_id, remitente_id y destinatario_id según tu base de datos
