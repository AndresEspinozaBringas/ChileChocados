# Archivos Modificados en el Servidor

## ⚠️ IMPORTANTE
Estos archivos fueron modificados SOLO en el servidor (chilechocados.cl).
Tu proyecto local NO ha sido modificado.

---

## Archivos Modificados

### 1. `public/index.php`
**Estado:** MODIFICADO (múltiples veces)
**Razón:** El index.php original era muy complejo y causaba errores
**Última versión:** index_test.php (versión mínima que funciona)

### 2. `app/core/Database.php`
**Estado:** MODIFICADO
**Razón:** Usaba variables incorrectas (DB_NAME, DB_USER, DB_PASS) en lugar de (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
**Cambio:** Actualizado para usar las constantes correctas definidas en config.php

### 3. `app/config/config.php`
**Estado:** MODIFICADO
**Razón:** Mejor manejo de errores y validación de variables requeridas
**Cambio:** Versión más robusta con try-catch y validaciones

### 4. `.env`
**Estado:** CREADO
**Razón:** No existía en el servidor
**Origen:** Copiado desde .env.production con las credenciales correctas

### 5. `.htaccess` (raíz)
**Estado:** SIMPLIFICADO
**Razón:** El original tenía configuraciones que podían causar errores
**Contenido actual:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
```

### 6. `public/.htaccess`
**Estado:** SIMPLIFICADO
**Razón:** Simplificado para evitar conflictos
**Contenido actual:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

---

## Archivos de Prueba Creados (pueden eliminarse)

- `public/test.php`
- `public/test-simple.php`
- `public/test-public.php`
- `public/diagnostico.php`
- `test-root.php` (en raíz)

---

## Base de Datos

### Datos Insertados:
1. **Regiones:** 16 regiones de Chile
2. **Usuario Admin:**
   - Email: admin@chilechocados.cl
   - Password: admin123
   - Rol: admin

---

## Cómo Restaurar los Archivos Originales

Si quieres restaurar los archivos originales del proyecto local al servidor:

```bash
# 1. Subir index.php original
scp -P 22 public/index.php lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/public/

# 2. Subir Database.php original
scp -P 22 app/core/Database.php lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/app/core/

# 3. Subir config.php original
scp -P 22 app/config/config.php lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/app/config/

# 4. Subir .htaccess originales
scp -P 22 .htaccess lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/
scp -P 22 public/.htaccess lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/public/
```

---

## Problema Principal Encontrado

El error principal era que **`app/core/Database.php`** usaba nombres de variables incorrectos:

```php
// INCORRECTO (original)
$dbname = $_ENV['DB_NAME'] ?? 'chilechocados';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

// CORRECTO (corregido)
$dbname = $_ENV['DB_DATABASE'] ?? DB_DATABASE ?? 'chilechocados';
$username = $_ENV['DB_USERNAME'] ?? DB_USERNAME ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? DB_PASSWORD ?? '';
```

---

## Recomendación

Para que el proyecto funcione correctamente en el servidor, necesitas:

1. **Corregir `app/core/Database.php`** en tu proyecto local con los nombres correctos de variables
2. **Simplificar `public/index.php`** o asegurarte de que cargue todos los helpers necesarios
3. **Crear `.env`** en el servidor con las credenciales correctas

---

**Fecha:** 27 de Octubre 2025
