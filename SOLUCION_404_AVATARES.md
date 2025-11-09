# 🚨 Solución Error 404: Avatares no se encuentran

## Error
```
GET https://chilechocados.cl/uploads/avatars/avatar_3_1762652692.jpg 404 (Not Found)
```

## Causa
La carpeta `public/uploads/avatars` no existe en el servidor de producción o no tiene los permisos correctos.

## ✅ Soluciones

### Solución 1: Script Automático (Recomendado)

1. **Sube el archivo `verificar_carpetas_uploads.php` a la raíz de tu sitio**

2. **Accede desde tu navegador:**
   ```
   https://chilechocados.cl/verificar_carpetas_uploads.php
   ```

3. **El script automáticamente:**
   - ✅ Verifica si las carpetas existen
   - ✅ Crea las carpetas si no existen
   - ✅ Configura los permisos correctos
   - ✅ Hace pruebas de escritura
   - ✅ Muestra un reporte completo

4. **Elimina el archivo después de usarlo:**
   ```bash
   rm verificar_carpetas_uploads.php
   ```

### Solución 2: Comandos SSH (Manual)

Si tienes acceso SSH a tu servidor:

```bash
# 1. Ir a la carpeta del proyecto
cd /ruta/a/tu/proyecto

# 2. Crear carpetas
mkdir -p public/uploads/avatars
mkdir -p public/uploads/publicaciones

# 3. Dar permisos de escritura
chmod 777 public/uploads
chmod 777 public/uploads/avatars
chmod 777 public/uploads/publicaciones

# 4. Verificar que se crearon
ls -la public/uploads/

# 5. Verificar permisos
ls -la public/uploads/avatars/
```

### Solución 3: Desde cPanel / Administrador de Archivos

1. **Accede a cPanel o tu administrador de archivos**

2. **Navega a:** `public/uploads/`

3. **Crea la carpeta `avatars`:**
   - Click derecho → Nueva carpeta
   - Nombre: `avatars`

4. **Configura permisos:**
   - Click derecho en `avatars` → Permisos
   - Marca todas las casillas (777)
   - Aplica cambios

5. **Verifica la estructura:**
   ```
   public/
   └── uploads/
       ├── avatars/        ← Debe existir con permisos 777
       └── publicaciones/  ← Debe existir con permisos 777
   ```

### Solución 4: Desde FTP

1. **Conecta por FTP a tu servidor**

2. **Navega a:** `public/uploads/`

3. **Crea la carpeta `avatars`**

4. **Cambia permisos:**
   - Click derecho en `avatars`
   - Permisos / CHMOD
   - Establece: `777` (rwxrwxrwx)

## 🔍 Verificación

### Verificar que la carpeta existe:

**Desde navegador:**
```
https://chilechocados.cl/uploads/avatars/
```
- Debe mostrar un listado vacío o "403 Forbidden" (es normal)
- NO debe mostrar "404 Not Found"

**Desde SSH:**
```bash
ls -la public/uploads/avatars/
```

**Resultado esperado:**
```
drwxrwxrwx  2 usuario grupo 4096 Nov  8 20:00 .
drwxrwxrwx  3 usuario grupo 4096 Nov  8 20:00 ..
-rw-r--r--  1 usuario grupo  123 Nov  8 20:00 .gitkeep
-rw-r--r--  1 usuario grupo  456 Nov  8 20:00 .htaccess
```

### Verificar permisos:

```bash
stat public/uploads/avatars/
```

Debe mostrar: `Access: (0777/drwxrwxrwx)`

### Probar subida de avatar:

1. Ve a tu perfil: `https://chilechocados.cl/perfil`
2. Click en el avatar
3. Selecciona una imagen
4. Debe subirse sin errores
5. Verifica que aparece en: `https://chilechocados.cl/uploads/avatars/avatar_X_TIMESTAMP.jpg`

## 🐛 Problemas Comunes

### Problema 1: "Permission denied"
**Solución:**
```bash
chmod 777 public/uploads/avatars
chown www-data:www-data public/uploads/avatars  # En Ubuntu/Debian
chown apache:apache public/uploads/avatars       # En CentOS/RHEL
```

### Problema 2: "No such file or directory"
**Solución:**
```bash
mkdir -p public/uploads/avatars
```

### Problema 3: Carpeta existe pero sigue dando 404
**Posibles causas:**
1. **Ruta incorrecta en el código**
   - Verifica que `BASE_URL` esté configurado correctamente
   - Debe ser: `https://chilechocados.cl` (sin barra final)

2. **Problema con .htaccess**
   - Verifica que existe `public/uploads/avatars/.htaccess`
   - Verifica que mod_rewrite está habilitado

3. **Problema con el servidor web**
   - Reinicia Apache/Nginx:
     ```bash
     sudo service apache2 restart
     # o
     sudo service nginx restart
     ```

### Problema 4: Imagen se sube pero no se muestra
**Solución:**
```bash
# Verificar que el archivo existe
ls -la public/uploads/avatars/avatar_*.jpg

# Verificar permisos del archivo
chmod 644 public/uploads/avatars/avatar_*.jpg

# Verificar que el archivo no está corrupto
file public/uploads/avatars/avatar_*.jpg
```

## 📋 Checklist de Verificación

- [ ] Carpeta `public/uploads/avatars` existe
- [ ] Permisos de carpeta: 777 (rwxrwxrwx)
- [ ] Archivo `.htaccess` en la carpeta
- [ ] Archivo `.gitkeep` en la carpeta
- [ ] Usuario de PHP puede escribir en la carpeta
- [ ] No hay restricciones de `open_basedir`
- [ ] `upload_max_filesize` es al menos 2M
- [ ] `post_max_size` es al menos 2M
- [ ] Servidor web reiniciado

## 🔒 Seguridad

Después de crear las carpetas, asegúrate de:

1. **Archivo .htaccess está presente** (evita ejecución de PHP)
2. **Solo imágenes son accesibles** (JPG, PNG, WebP)
3. **Scripts de verificación eliminados** (verificar_carpetas_uploads.php)

## 📊 Estructura Final

```
public/
└── uploads/
    ├── .gitkeep
    ├── avatars/
    │   ├── .gitkeep
    │   ├── .htaccess
    │   └── avatar_*.jpg  (archivos subidos)
    └── publicaciones/
        ├── .gitkeep
        └── *.jpg  (fotos de publicaciones)
```

## 🎯 Resumen Rápido

**Comando más rápido (SSH):**
```bash
mkdir -p public/uploads/avatars && chmod 777 public/uploads/avatars
```

**Verificación:**
```bash
ls -la public/uploads/avatars/
```

**Resultado esperado:**
```
✓ Carpeta existe
✓ Permisos 777
✓ Avatares se suben correctamente
✓ Avatares se muestran en el sitio
```

## 📞 Soporte Adicional

Si después de seguir todos los pasos el problema persiste:

1. **Revisa logs del servidor:**
   ```bash
   tail -f /var/log/apache2/error.log
   tail -f /var/log/nginx/error.log
   ```

2. **Verifica configuración de PHP:**
   ```bash
   php -i | grep upload
   ```

3. **Contacta a tu proveedor de hosting** si:
   - No tienes acceso SSH
   - No puedes cambiar permisos
   - Hay restricciones de seguridad
