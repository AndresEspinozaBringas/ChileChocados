# Información sobre Rutas Admin
**Fecha:** 26 de Octubre 2025

## ❌ URL Incorrecta que estabas usando:
```
http://localhost:8080/chilechocados/admin/publicaciones
```

## ✅ URL Correcta que debes usar:
```
http://chilechocados.local:8080/admin
```

---

## Explicación

### Configuración del VirtualHost
Tu Apache está configurado con un VirtualHost que apunta a:
- **ServerName:** `chilechocados.local`
- **Puerto:** `8080`
- **DocumentRoot:** `/opt/homebrew/var/www/chilechocados/public`

### Por qué la URL incorrecta no funciona:

1. **`localhost:8080`** - No está configurado para servir ChileChocados
2. **`/chilechocados/`** - No es necesario, ya está en el DocumentRoot
3. **Resultado:** Apache busca el archivo en una ruta que no existe

### Por qué la URL correcta funciona:

1. **`chilechocados.local:8080`** - Apunta al VirtualHost correcto
2. **`/admin`** - Se procesa por el router de `index.php`
3. **Resultado:** Se ejecuta `AdminController::index()`

---

## Estado Actual de las Rutas Admin

### ✅ Rutas Implementadas

#### 1. Ruta principal
- **URL:** http://chilechocados.local:8080/admin
- **Controlador:** `AdminController::index()`
- **Acción:** Redirige a `/admin/publicaciones`

#### 2. Gestión de publicaciones (en desarrollo)
- **URL:** http://chilechocados.local:8080/admin/publicaciones
- **Controlador:** `AdminController::publicaciones()`
- **Estado:** Muestra mensaje temporal "En desarrollo"

### ⚠️ Archivos HTML Estáticos

Existe una carpeta `public/admin/` con archivos HTML estáticos:
- `index.html` - Panel admin estático
- `login.html` - Login admin estático
- `categories.html`
- `messages.html`
- `newpost.html`
- `recovery.html`

**Problema:** Apache sirve estos archivos directamente sin pasar por el router PHP.

**Solución:** Hay dos opciones:

#### Opción 1: Eliminar archivos HTML estáticos
```bash
rm -rf public/admin/*.html
```

#### Opción 2: Renombrar la carpeta
```bash
mv public/admin public/admin_old
```

#### Opción 3: Configurar .htaccess para ignorarlos
Agregar en `public/.htaccess`:
```apache
# Forzar que /admin pase por index.php
RewriteCond %{REQUEST_URI} ^/admin
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## Funcionalidades Pendientes

### TODO: AdminController completo

El archivo `app/controllers/AdminController.php` actual es básico. Necesita:

1. **Autenticación de admin**
   - Verificar sesión de admin
   - Redirigir a login si no está autenticado

2. **Panel de publicaciones**
   - Listar publicaciones con filtros
   - Ver detalle de publicación
   - Aprobar/rechazar publicaciones
   - Eliminar publicaciones

3. **Estadísticas**
   - Total de publicaciones por estado
   - Publicaciones de hoy/semana
   - Gráficos y métricas

4. **Gestión de usuarios**
   - Listar usuarios
   - Suspender/activar usuarios
   - Ver historial de actividad

5. **Gestión de categorías**
   - CRUD de categorías
   - CRUD de subcategorías

6. **Configuraciones**
   - Editar configuraciones del sistema
   - Precios de destacados
   - Emails y notificaciones

---

## Cómo Acceder al Admin

### Paso 1: Asegúrate de usar la URL correcta
```
http://chilechocados.local:8080/admin
```

### Paso 2: Si ves HTML estático
Significa que Apache está sirviendo `public/admin/index.html`.

**Solución temporal:**
```bash
# Renombrar la carpeta
mv public/admin public/admin_wireframes
```

### Paso 3: Verificar que funciona
Deberías ver el mensaje:
```
Panel de Administración - Publicaciones
Esta funcionalidad está en desarrollo.
```

---

## Próximos Pasos

1. ✅ Ruta `/admin` agregada a `specialRoutes`
2. ✅ `AdminController` básico creado
3. ⚠️ Resolver conflicto con archivos HTML estáticos
4. ⚠️ Implementar autenticación de admin
5. ⚠️ Implementar funcionalidades del panel
6. ⚠️ Crear vistas PHP para el admin

---

## Comandos Útiles

### Ver qué está sirviendo Apache
```bash
curl -I http://chilechocados.local:8080/admin
```

### Ver el contenido HTML
```bash
curl -s http://chilechocados.local:8080/admin | head -20
```

### Verificar configuración de VirtualHost
```bash
cat /opt/homebrew/etc/httpd/extra/httpd-vhosts.conf | grep -A 10 "chilechocados"
```

---

**Resumen:**
- ❌ No uses `localhost:8080/chilechocados/`
- ✅ Usa `chilechocados.local:8080/`
- ⚠️ Resuelve conflicto con archivos HTML estáticos
- 🚧 Panel admin en desarrollo
