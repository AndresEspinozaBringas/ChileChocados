# Archivos a Eliminar del Proyecto

## 📋 Resumen

Este documento lista todos los archivos que se pueden eliminar de forma segura del proyecto ChileChocados.

---

## 1. Archivos HTML Migrados a PHP (Wireframes Antiguos)

Estos archivos HTML fueron migrados a PHP y ya no se usan:

### Raíz del proyecto:
- ❌ `index.html` → Migrado a `app/views/pages/home/index.php`
- ❌ `login.html` → Migrado a `app/views/pages/auth/login.php`
- ❌ `register.html` → Migrado a `app/views/pages/auth/register.php`
- ❌ `profile.html` → Migrado a `app/views/pages/usuarios/profile.php`
- ❌ `sell.html` → Migrado a `app/views/pages/publicaciones/sell.php`
- ❌ `publish.html` → Migrado a `app/views/pages/publicaciones/publish.php`
- ❌ `publish-category.html` → Migrado a `app/views/pages/publicaciones/publish.php`
- ❌ `list.html` → Migrado a `app/views/pages/publicaciones/list.php`
- ❌ `detail.html` → Migrado a `app/views/pages/publicaciones/detail.php`
- ❌ `favorites.html` → (Funcionalidad pendiente)
- ❌ `messages.html` → (Funcionalidad pendiente)
- ❌ `cart.html` → (No implementado)
- ❌ `share.html` → (No implementado)
- ❌ `categories.html` → Migrado a `app/views/pages/categorias/index.php`
- ❌ `post-approval.html` → Migrado a `app/views/pages/publicaciones/approval.php`

### Directorio admin/:
- ❌ `admin/index.html` → Migrado a `app/views/pages/admin/index.php`
- ❌ `admin/login.html` → Migrado a `app/views/pages/admin/login.php`
- ❌ `admin/categories.html` → (Funcionalidad en admin)
- ❌ `admin/messages.html` → (Funcionalidad en admin)
- ❌ `admin/newpost.html` → (Funcionalidad en admin)
- ❌ `admin/recovery.html` → (Funcionalidad en admin)

**Total: ~21 archivos HTML**

---

## 2. Wireframes Duplicados

### public/admin_wireframes/
- ❌ Todo el directorio `public/admin_wireframes/` (duplicado de admin/)
- ❌ `public/test-design-system.html` (archivo de prueba)

---

## 3. Directorios de Archivo

- ❌ `_archive/` - Wireframes antiguos ya migrados (104KB)
- ❌ `wireframes_reference/` - Directorio vacío

---

## 4. Imágenes de Prueba (IMPORTANTE)

**Archivos grandes que NO deben estar en Git:**

- ❌ `public/uploads/publicaciones/2025/10/pub_5_68ff6754de468.jpg` (969KB)
- ❌ `public/uploads/publicaciones/2025/10/pub_5_68ff6754df3fa.jpg` (1.0MB)
- ❌ `public/uploads/publicaciones/2025/10/pub_5_68ff6754dfae2.jpg` (1.1MB)
- ❌ `public/uploads/publicaciones/2025/10/pub_5_68ff6754e023e.jpg` (1.0MB)

**Total: ~4.1MB de imágenes**

⚠️ Las imágenes subidas por usuarios NO deben estar en Git, solo en el servidor.

---

## 5. Documentación Redundante

Documentos MD de sesiones anteriores que ya no son necesarios:

- ❌ `MIGRACION_HTML_A_PHP.md` - Proceso ya completado
- ❌ `CONFIRMACION_SISTEMA_FUNCIONANDO.md` - Ya no relevante
- ❌ `CORRECCION_DETALLE_Y_CONTEO_2025-10-26.md` - Corrección aplicada
- ❌ `CORRECCION_FOTOS_PUBLICACIONES.md` - Corrección aplicada
- ❌ `ERRORES_CORREGIDOS_2025-10-26.md` - Ya corregidos
- ❌ `FIX_LOGIN_ERROR_2025-10-26.md` - Ya corregido
- ❌ `RESUMEN_ADMIN_CORREGIDO.md` - Información obsoleta
- ❌ `RESUMEN_FINAL_SESION_2025-10-26.md` - Información obsoleta
- ❌ `RESUMEN_REVISION_FOTOS.md` - Información obsoleta

**Mantener:**
- ✅ `README.md` - Documentación principal
- ✅ `DATABASE_SCHEMA.md` - Esquema de BD
- ✅ `INSTRUCCIONES_DEPLOY.md` - Instrucciones de deploy
- ✅ `CHANGELOG.md` - Historial de cambios
- ✅ `CREDENCIALES_LOGIN.md` - Credenciales de acceso

---

## 6. Scripts de Prueba y Configuración

- ❌ `test_db_connection.php` - Script de prueba
- ❌ `test_email.php` - Script de prueba
- ❌ `test_foto_guardado.php` - Script de prueba
- ❌ `test_login_page.php` - Script de prueba
- ❌ `reset_mysql_root.sh` - Script local de MySQL
- ❌ `configurar_git.sh` - Ya configurado
- ❌ `httpd-vhosts-new.conf` - Configuración local de Apache

---

## 7. Logs

- 🔄 `logs/php_errors.log` - Vaciar contenido (mantener archivo)
- 🔄 `logs/email.log` - Vaciar contenido (mantener archivo)
- ❌ `public/logs/debug.txt` - Eliminar

---

## 📊 Resumen de Espacio a Liberar

- Archivos HTML: ~21 archivos
- Imágenes: ~4.1MB
- Documentación: ~9 archivos MD
- Scripts de prueba: ~7 archivos
- Directorios: _archive/, wireframes_reference/, admin/, public/admin_wireframes/

**Total estimado: ~4.5MB + múltiples archivos obsoletos**

---

## 🚀 Cómo Ejecutar la Limpieza

```bash
# Dar permisos al script
chmod +x cleanup_project.sh

# Ejecutar limpieza
./cleanup_project.sh

# Revisar cambios
git status

# Confirmar y subir
git add .
git commit -m "chore: Limpieza del proyecto - eliminados wireframes y archivos obsoletos"
git push origin main
```

---

## ⚠️ Archivos que NO se deben eliminar

- ✅ `.env.example` - Plantilla de configuración
- ✅ `.env.production` - Configuración de producción
- ✅ `.gitignore` - Configuración de Git
- ✅ `.htaccess` - Configuración de Apache
- ✅ `composer.json` / `composer.lock` - Dependencias PHP
- ✅ `chilechocados_database.sql` - Script de BD
- ✅ `deploy_to_dreamhost.sh` - Script de deploy
- ✅ Todos los archivos en `app/` - Código de la aplicación
- ✅ Todos los archivos en `vendor/` - Dependencias
- ✅ Archivos CSS/JS en `public/assets/` - Assets del sitio

---

**Fecha:** 27 de Octubre 2025
