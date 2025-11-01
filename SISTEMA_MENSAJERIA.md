# Sistema de Mensajería - ChileChocados

## Descripción General

Sistema de mensajería interna que permite la comunicación entre compradores y vendedores sobre publicaciones específicas. Un vendedor puede tener múltiples conversaciones con diferentes usuarios interesados en sus publicaciones.

## Características Implementadas

### Para Usuarios (Compradores y Vendedores)

- ✅ Vista de bandeja de mensajes con lista de conversaciones
- ✅ Chat en tiempo real con interfaz moderna
- ✅ Agrupación de mensajes por publicación y usuario
- ✅ Contador de mensajes no leídos
- ✅ Marcado automático de mensajes como leídos
- ✅ Botón "Contactar vendedor" en detalle de publicación
- ✅ Redirección a login si el usuario no está autenticado
- ✅ Prevención de auto-mensajes (no puedes contactarte a ti mismo)

### Para Administradores

- ✅ Vista de todas las conversaciones del sistema
- ✅ Monitoreo de mensajes entre usuarios
- ✅ Indicador de mensajes no leídos
- ✅ Acceso directo a publicaciones desde el chat
- ✅ Vista de información completa de remitente y destinatario

## Estructura de la Base de Datos

### Tabla: `mensajes`

```sql
CREATE TABLE mensajes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT UNSIGNED NOT NULL,
    remitente_id INT UNSIGNED NOT NULL,
    destinatario_id INT UNSIGNED NOT NULL,
    mensaje TEXT NOT NULL,
    archivo_adjunto VARCHAR(255) NULL,
    leido TINYINT(1) NOT NULL DEFAULT 0,
    fecha_lectura DATETIME NULL,
    fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_publicacion (publicacion_id),
    INDEX idx_remitente (remitente_id),
    INDEX idx_destinatario (destinatario_id),
    INDEX idx_leido (leido),
    
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

## Rutas Disponibles

### Rutas Públicas (Usuarios)

- `GET /mensajes` - Bandeja de mensajes del usuario
- `GET /mensajes?publicacion={id}&usuario={id}` - Iniciar conversación desde publicación
- `GET /mensajes?conversacion={key}` - Ver conversación específica
- `POST /mensajes/enviar` - Enviar mensaje (AJAX)
- `POST /mensajes/marcar-leido` - Marcar conversación como leída (AJAX)

### Rutas Admin

- `GET /admin/mensajes` - Vista de todas las conversaciones
- `GET /admin/mensajes?conversacion={key}` - Ver conversación específica

## Archivos Principales

### Controladores

- `app/controllers/MensajeController.php` - Controlador principal de mensajería
- `app/controllers/AdminController.php` - Método `mensajes()` para vista admin

### Modelos

- `app/models/Mensaje.php` - Modelo con métodos para gestionar mensajes

### Vistas

- `app/views/pages/mensajes/index.php` - Vista de mensajería para usuarios
- `app/views/pages/admin/mensajes.php` - Vista de mensajería para admin

## Flujo de Uso

### 1. Usuario Interesado Contacta al Vendedor

```
Usuario ve publicación → Click en "Contactar vendedor" → 
Redirige a /mensajes?publicacion=X&usuario=Y → 
Se crea/abre conversación → Usuario envía mensaje
```

### 2. Vendedor Recibe y Responde

```
Vendedor accede a /mensajes → 
Ve lista de conversaciones agrupadas por publicación → 
Selecciona conversación → 
Ve mensajes y responde
```

### 3. Múltiples Interesados en la Misma Publicación

```
Publicación ID 1 del Vendedor A:
├── Conversación con Comprador B
├── Conversación con Comprador C
└── Conversación con Comprador D
```

Cada conversación es independiente y privada entre el vendedor y cada comprador.

## Características Técnicas

### Agrupación de Conversaciones

Las conversaciones se agrupan por:
- `publicacion_id` - La publicación sobre la que se habla
- `usuario1_id` y `usuario2_id` - Los dos usuarios que conversan

Clave única: `{publicacion_id}-{usuario1_id}-{usuario2_id}`

### Formato de Fechas Relativas

- "Justo ahora" - Menos de 1 minuto
- "Hace X minutos" - Menos de 1 hora
- "Hace X horas" - Menos de 1 día
- "Hace X días" - Menos de 1 semana
- "Hace X semanas" - Menos de 1 mes
- "DD/MM/YYYY" - Más de 1 mes

### Seguridad

- ✅ Validación de sesión de usuario
- ✅ Verificación de permisos (solo participantes pueden ver conversación)
- ✅ Prevención de XSS con `htmlspecialchars()`
- ✅ Validación de datos en servidor
- ✅ Uso de prepared statements para prevenir SQL injection

## Métodos del Modelo Mensaje

```php
// Enviar mensaje
$mensajeModel->enviar($publicacionId, $remitenteId, $destinatarioId, $mensaje);

// Obtener conversación entre dos usuarios
$mensajeModel->getConversacion($publicacionId, $usuario1Id, $usuario2Id);

// Obtener conversaciones de un usuario
$mensajeModel->getConversacionesUsuario($usuarioId);

// Marcar conversación como leída
$mensajeModel->marcarConversacionLeida($publicacionId, $destinatarioId);

// Contar mensajes no leídos
$mensajeModel->contarNoLeidos($usuarioId);

// Obtener mensajes recientes
$mensajeModel->getRecientes($usuarioId, $limit);
```

## Datos de Prueba

Para insertar mensajes de prueba, ejecuta:

```bash
mysql -u usuario -p chilechocados < database/seed_mensajes.sql
```

Asegúrate de ajustar los IDs de usuarios y publicaciones según tu base de datos.

## Mejoras Futuras (Opcional)

- [ ] Notificaciones en tiempo real con WebSockets
- [ ] Adjuntar archivos/imágenes en mensajes
- [ ] Búsqueda de mensajes
- [ ] Archivar conversaciones
- [ ] Bloquear usuarios
- [ ] Mensajes automáticos del sistema
- [ ] Indicador de "escribiendo..."
- [ ] Confirmación de lectura en tiempo real

## Notas Importantes

1. **Un vendedor puede tener múltiples conversaciones**: Si 5 usuarios están interesados en la misma publicación, el vendedor tendrá 5 conversaciones separadas.

2. **Las conversaciones son privadas**: Solo el vendedor y el comprador específico pueden ver sus mensajes.

3. **El admin puede ver todo**: El administrador tiene acceso a todas las conversaciones para moderación.

4. **Marcado automático como leído**: Cuando un usuario abre una conversación, todos los mensajes que recibió se marcan automáticamente como leídos.

5. **Eliminación en cascada**: Si se elimina una publicación, se eliminan todos sus mensajes asociados.

## Soporte

Para dudas o problemas con el sistema de mensajería, contacta al equipo de desarrollo.

---

**Última actualización**: Noviembre 2025
**Versión**: 1.0.0
