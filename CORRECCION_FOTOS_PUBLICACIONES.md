# Corrección: Sistema de Fotos en Publicaciones

**Fecha:** 26 de octubre de 2025  
**Problema:** Las fotos no se estaban guardando al crear publicaciones

---

## 🔍 PROBLEMAS IDENTIFICADOS

### 1. **Duplicación de Tablas**
Existían DOS tablas para almacenar fotos:
- `publicacion_fotos` (sin 'es')
- `publicaciones_fotos` (con 'es')

### 2. **Inconsistencia en Nombres de Campos**
- Tabla `publicacion_fotos` usa: `ruta`
- Código del modelo usaba: `url`
- Las migraciones creaban tabla con: `url`

### 3. **Conflicto en el Código**
- El modelo `Publicacion.php` intentaba insertar en campo `url`
- La tabla real tenía campo `ruta`
- Resultado: Las fotos NO se guardaban en la base de datos

---

## ✅ CORRECCIONES APLICADAS

### 1. **Consolidación de Tablas**
```bash
php database/migrations/consolidar_tablas_fotos.php
```

**Resultado:**
- ✓ Tabla `publicaciones_fotos` ELIMINADA
- ✓ Tabla `publicacion_fotos` es la ÚNICA tabla activa
- ✓ Estructura final:
  - `id` (int unsigned)
  - `publicacion_id` (int unsigned)
  - `ruta` (varchar 255) ← Campo correcto
  - `es_principal` (tinyint 1)
  - `orden` (int)
  - `fecha_subida` (datetime)

### 2. **Actualización del Modelo**
**Archivo:** `app/models/Publicacion.php`

**Cambios:**
```php
// ANTES (incorrecto)
$sql = "INSERT INTO publicacion_fotos (publicacion_id, url, orden, es_principal, fecha_creacion)
        VALUES (?, ?, ?, ?, NOW())";
$datos['url']

// DESPUÉS (correcto)
$sql = "INSERT INTO publicacion_fotos (publicacion_id, ruta, orden, es_principal, fecha_subida)
        VALUES (?, ?, ?, ?, NOW())";
$datos['ruta']
```

### 3. **Actualización del Controlador**
**Archivo:** `app/controllers/PublicacionController.php`

**Cambios:**
```php
// ANTES
$url_relativa = 'publicaciones/' . date('Y') . '/' . date('m') . '/' . $nombre_archivo;
$this->publicacionModel->agregarImagen($publicacion_id, [
    'url' => $url_relativa,
    ...
]);

// DESPUÉS
$ruta_relativa = 'publicaciones/' . date('Y') . '/' . date('m') . '/' . $nombre_archivo;
$this->publicacionModel->agregarImagen($publicacion_id, [
    'ruta' => $ruta_relativa,
    ...
]);
```

### 4. **Logging Mejorado**
Se agregó logging detallado en el método `procesarImagenes()` para facilitar el debugging:
- Log de cada archivo procesado
- Estado de validación
- Resultado de guardado en BD
- Errores específicos

---

## 🧪 CÓMO PROBAR

### 1. **Verificar Estado de la Base de Datos**
```bash
mysql -u root -p'root' -e "USE chilechocados; SHOW TABLES LIKE '%foto%';"
```
**Resultado esperado:** Solo debe aparecer `publicacion_fotos`

### 2. **Verificar Estructura de la Tabla**
```bash
mysql -u root -p'root' -e "USE chilechocados; DESCRIBE publicacion_fotos;"
```
**Resultado esperado:** Debe tener el campo `ruta` (no `url`)

### 3. **Crear una Publicación de Prueba**
1. Ir a: http://chilechocados.local:8080/publicar
2. Llenar el formulario completo
3. Subir al menos 1 foto
4. Enviar el formulario

### 4. **Verificar que se Guardó en la BD**
```bash
# Ver última publicación
mysql -u root -p'root' -e "USE chilechocados; SELECT id, titulo, foto_principal FROM publicaciones ORDER BY id DESC LIMIT 1;"

# Ver fotos de la última publicación (reemplazar X con el ID)
mysql -u root -p'root' -e "USE chilechocados; SELECT * FROM publicacion_fotos WHERE publicacion_id = X;"
```

### 5. **Verificar Archivos Físicos**
```bash
ls -la /opt/homebrew/var/www/chilechocados/public/uploads/publicaciones/2025/10/
```
**Resultado esperado:** Deben aparecer los archivos de imagen subidos

### 6. **Revisar Logs de Debug**
```bash
tail -100 /opt/homebrew/var/www/chilechocados/public/logs/debug.txt
```
**Buscar:**
- "=== PROCESANDO IMÁGENES ==="
- "✓ Archivo movido exitosamente"
- "Guardado en BD: SÍ"

---

## 📊 ESTADO ACTUAL

### ✅ Funcionando Correctamente
- ✓ Guardado de publicaciones en tabla `publicaciones`
- ✓ Estructura de base de datos consolidada
- ✓ Código actualizado para usar campos correctos
- ✓ Logging detallado implementado

### ⚠️ Pendiente de Validación
- Subir fotos reales desde el formulario
- Verificar que se guarden en `publicacion_fotos`
- Verificar que se actualice `foto_principal` en `publicaciones`
- Verificar que los archivos se guarden físicamente

---

## 🔧 ARCHIVOS MODIFICADOS

1. `database/migrations/consolidar_tablas_fotos.php` (NUEVO)
2. `app/models/Publicacion.php` (MODIFICADO)
3. `app/controllers/PublicacionController.php` (MODIFICADO)

---

## 📝 NOTAS IMPORTANTES

1. **NO eliminar la tabla `publicacion_fotos`** - Es la tabla correcta y única
2. **Permisos:** El directorio `public/uploads/` tiene permisos 777
3. **Logging:** Los logs se guardan en `public/logs/debug.txt`
4. **Validación:** El sistema valida:
   - Tipo de archivo (jpg, jpeg, png, webp)
   - Tamaño máximo: 5MB
   - Máximo 6 fotos por publicación

---

## 🚀 PRÓXIMOS PASOS

1. **Probar el formulario** con fotos reales
2. **Verificar** que las fotos se guarden correctamente
3. **Revisar logs** para confirmar que no hay errores
4. **Validar** que la foto principal se actualice
5. **Crear vistas** para mostrar las fotos en el detalle de publicación

---

## 📞 SOPORTE

Si encuentras algún problema:
1. Revisa los logs en `public/logs/debug.txt`
2. Verifica la estructura de la BD con los comandos SQL arriba
3. Confirma que los permisos del directorio uploads sean correctos
