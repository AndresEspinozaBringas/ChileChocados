# Instrucciones para Ver los Cambios

## El problema
Los cambios están correctamente aplicados en los archivos, pero el navegador está mostrando versiones en caché.

## Cambios realizados:

### 1. Iconos actualizados ✅
- **Náutica**: Icono de barco con vela (línea 47 de home.php)
- **Aéreo**: Icono de avión (línea 48 de home.php)

### 2. Hero Banner sin borde ✅
- Eliminado el borde blanco
- Agregado estilo inline en el HTML (línea 22 de home.php)
- CSS actualizado en home.css

## Cómo ver los cambios:

### Opción 1: Hard Refresh (Recomendado)
1. En Chrome/Edge: `Cmd + Shift + R` (Mac) o `Ctrl + Shift + R` (Windows)
2. En Firefox: `Cmd + Shift + R` (Mac) o `Ctrl + F5` (Windows)
3. En Safari: `Cmd + Option + R`

### Opción 2: Limpiar caché del navegador
1. Abrir DevTools (F12)
2. Click derecho en el botón de recargar
3. Seleccionar "Empty Cache and Hard Reload"

### Opción 3: Modo incógnito
1. Abrir una ventana de incógnito
2. Navegar a http://chilechocados.local:8080/

### Opción 4: Limpiar caché de PHP (si aplica)
```bash
# Si usas opcache
php -r "opcache_reset();"

# O reiniciar el servidor
# Dependiendo de tu configuración
```

## Verificación de cambios:

### Iconos:
- Náutica debe mostrar un triángulo (vela de barco)
- Aéreo debe mostrar un avión con alas

### Hero Banner:
- NO debe tener borde blanco/claro
- Solo debe verse el fondo gris (#F9FAFB en modo claro, #1E293B en modo oscuro)

## Si aún no ves los cambios:
1. Verifica que el archivo home.css tenga `?v=` con timestamp en la URL
2. Revisa la consola del navegador por errores
3. Verifica que no haya un proxy o CDN cacheando los archivos
