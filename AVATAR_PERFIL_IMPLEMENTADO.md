# ✅ Avatar de Perfil Implementado

## 🎯 Funcionalidad

Los usuarios ahora pueden subir una foto de perfil que se guarda como thumbnail de 200x200 píxeles.

## 🔧 Características

### 1. Subida de Imagen
- **Formatos soportados:** JPG, PNG, WebP
- **Tamaño máximo:** 2MB
- **Procesamiento:** Automático a thumbnail 200x200px
- **Crop:** Centrado y cuadrado
- **Calidad:** JPG 90%, PNG compresión 9

### 2. Interfaz de Usuario
- Click en el avatar para cambiar foto
- Preview inmediato antes de subir
- Indicador de progreso durante la subida
- Mensaje de confirmación al completar
- Hover muestra opción "Cambiar foto"

### 3. Almacenamiento
- **Carpeta:** `public/uploads/avatars/`
- **Nombre:** `avatar_{user_id}_{timestamp}.{ext}`
- **Base de datos:** Campo `avatar` en tabla `usuarios`
- **Sesión:** `$_SESSION['user_avatar']` actualizada

### 4. Seguridad
- Validación de tipo MIME real (no solo extensión)
- Validación de tamaño de archivo
- Token CSRF requerido
- Solo usuarios autenticados
- Eliminación automática de avatar anterior

## 📁 Archivos Modificados

### 1. `app/views/pages/usuarios/profile.php`
**Cambios:**
- ✅ Estilos CSS para avatar con imagen
- ✅ HTML para mostrar avatar o inicial
- ✅ Input file oculto para subir imagen
- ✅ JavaScript para manejar subida y preview
- ✅ Actualización automática del avatar en header

### 2. `app/controllers/UsuarioController.php`
**Nuevo método:**
- ✅ `actualizarAvatar()` - Procesa y guarda el avatar
  - Valida tipo y tamaño
  - Crea thumbnail 200x200
  - Guarda en disco
  - Actualiza base de datos
  - Elimina avatar anterior
  - Retorna JSON con resultado

### 3. `public/index.php`
**Nueva ruta:**
- ✅ `POST /perfil/actualizar-avatar` → `UsuarioController::actualizarAvatar()`

### 4. Base de Datos
**Nueva columna:**
- ✅ `usuarios.avatar` VARCHAR(255) NULL

### 5. Carpeta de Uploads
**Creada:**
- ✅ `public/uploads/avatars/` con permisos 777

## 🚀 Cómo Usar

### Para Usuarios

1. **Ir a Perfil**
   ```
   http://chilechocados.local:8080/perfil
   ```

2. **Click en el avatar**
   - Aparece selector de archivos

3. **Seleccionar imagen**
   - JPG, PNG o WebP
   - Máximo 2MB

4. **Esperar confirmación**
   - Preview inmediato
   - Subida automática
   - Mensaje "¡Actualizado!"

5. **Ver resultado**
   - Avatar actualizado en perfil
   - Avatar actualizado en header
   - Avatar visible en publicaciones

### Para Desarrolladores

**Obtener avatar de un usuario:**
```php
// En vistas
<?php if (!empty($usuario['avatar'])): ?>
    <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $usuario['avatar']; ?>" 
         alt="<?php echo $usuario['nombre']; ?>">
<?php else: ?>
    <div class="avatar-placeholder">
        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
    </div>
<?php endif; ?>
```

**Desde sesión:**
```php
$avatar = $_SESSION['user_avatar'] ?? null;
```

**Desde base de datos:**
```php
$stmt = $db->prepare("SELECT avatar FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$avatar = $stmt->fetchColumn();
```

## 🎨 Estilos CSS

### Avatar con Imagen
```css
.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid var(--cc-primary);
    overflow: hidden;
    cursor: pointer;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
```

### Hover Effect
```css
.profile-avatar-upload {
    position: absolute;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    opacity: 0;
    transition: opacity 0.2s;
}

.profile-avatar:hover .profile-avatar-upload {
    opacity: 1;
}
```

## 📊 Procesamiento de Imagen

### Algoritmo de Thumbnail

1. **Cargar imagen original**
   ```php
   $imagen = imagecreatefromjpeg($file['tmp_name']);
   ```

2. **Calcular dimensiones para crop cuadrado**
   ```php
   $lado = min($anchoOriginal, $altoOriginal);
   $x = ($anchoOriginal - $lado) / 2;
   $y = ($altoOriginal - $lado) / 2;
   ```

3. **Crear thumbnail 200x200**
   ```php
   $thumbnail = imagecreatetruecolor(200, 200);
   ```

4. **Redimensionar con crop centrado**
   ```php
   imagecopyresampled(
       $thumbnail, $imagen,
       0, 0, $x, $y,
       200, 200, $lado, $lado
   );
   ```

5. **Guardar con calidad optimizada**
   ```php
   imagejpeg($thumbnail, $rutaDestino, 90);
   ```

## 🔒 Seguridad

### Validaciones Implementadas

1. **Autenticación**
   - Solo usuarios logueados
   - Verificación de sesión

2. **CSRF Protection**
   - Token requerido en cada subida

3. **Tipo de Archivo**
   - Validación MIME real (no extensión)
   - Solo JPG, PNG, WebP permitidos

4. **Tamaño**
   - Máximo 2MB
   - Validación antes de procesar

5. **Nombres de Archivo**
   - Generados automáticamente
   - No se usa nombre original del usuario
   - Incluye timestamp para unicidad

6. **Permisos**
   - Archivos guardados con permisos seguros
   - Carpeta con permisos 777 (ajustar en producción)

## 🐛 Manejo de Errores

### Errores Capturados

```javascript
// Cliente (JavaScript)
- Tipo de archivo inválido
- Tamaño excedido
- Error de red
- Error del servidor

// Servidor (PHP)
- Token CSRF inválido
- Usuario no autenticado
- Archivo no recibido
- Tipo MIME inválido
- Tamaño excedido
- Error al procesar imagen
- Error al guardar archivo
- Error de base de datos
```

### Respuestas JSON

**Éxito:**
```json
{
    "success": true,
    "message": "Avatar actualizado correctamente",
    "avatar": "avatar_123_1699456789.jpg"
}
```

**Error:**
```json
{
    "success": false,
    "message": "El archivo no debe superar 2MB"
}
```

## 📱 Responsive

- ✅ Funciona en desktop
- ✅ Funciona en tablet
- ✅ Funciona en móvil
- ✅ Touch-friendly
- ✅ Preview adaptativo

## 🌙 Dark Mode

- ✅ Estilos adaptados para modo oscuro
- ✅ Contraste adecuado
- ✅ Bordes visibles
- ✅ Hover states claros

## 🔄 Actualización en Tiempo Real

Cuando se sube un avatar:
1. ✅ Preview inmediato en perfil
2. ✅ Subida al servidor
3. ✅ Actualización en base de datos
4. ✅ Actualización en sesión
5. ✅ Actualización en header (sin reload)
6. ✅ Eliminación de avatar anterior

## 📝 Migraciones

### Para Desarrollo (Ya ejecutada)
```bash
php database/migrations/add_avatar_field.php
```

### Para Producción
```bash
# Opción 1: SQL directo
mysql -u usuario -p base_datos < database/migrations/add_avatar_to_usuarios.sql

# Opción 2: Script PHP
php database/migrations/add_avatar_field.php
```

## ✨ Mejoras Futuras (Opcionales)

- [ ] Recorte manual de imagen (crop tool)
- [ ] Filtros y efectos
- [ ] Múltiples tamaños de thumbnail
- [ ] Compresión WebP automática
- [ ] Galería de avatares predefinidos
- [ ] Integración con Gravatar
- [ ] Historial de avatares anteriores
- [ ] Avatar desde URL externa

## 🎯 Testing

### Casos de Prueba

1. **Subir JPG válido** ✅
2. **Subir PNG válido** ✅
3. **Subir WebP válido** ✅
4. **Subir archivo muy grande** ✅ (rechazado)
5. **Subir tipo inválido** ✅ (rechazado)
6. **Sin autenticación** ✅ (rechazado)
7. **Token CSRF inválido** ✅ (rechazado)
8. **Reemplazar avatar existente** ✅
9. **Ver avatar en header** ✅
10. **Ver avatar en perfil** ✅

## 📞 Soporte

Si encuentras problemas:

1. **Verificar permisos de carpeta**
   ```bash
   chmod 777 public/uploads/avatars
   ```

2. **Verificar extensión GD de PHP**
   ```bash
   php -m | grep gd
   ```

3. **Verificar logs**
   ```bash
   tail -f logs/database_errors.log
   ```

4. **Verificar campo en BD**
   ```sql
   SHOW COLUMNS FROM usuarios LIKE 'avatar';
   ```

## 🎉 Resultado Final

Los usuarios ahora pueden:
- ✅ Subir foto de perfil
- ✅ Ver preview inmediato
- ✅ Cambiar foto cuando quieran
- ✅ Ver su foto en todo el sitio
- ✅ Experiencia fluida y rápida
