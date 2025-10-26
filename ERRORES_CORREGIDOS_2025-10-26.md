# Errores Corregidos - 26 de Octubre 2025

## Resumen de Problemas Encontrados y Solucionados

### 1. Error 500 - Función `icon()` no definida
**Ubicación:** `app/views/layouts/header.php` línea 61  
**Error:** `Call to undefined function icon()`

**Causa:** La función `icon()` no existía en `app/helpers/functions.php`

**Solución:** Agregué la función `icon()` al archivo de helpers:
```php
function icon($name, $size = 24, $class = '') {
    $classAttr = $class ? ' class="' . $class . '"' : '';
    return '<i data-lucide="' . $name . '" width="' . $size . '" height="' . $size . '"' . $classAttr . '></i>';
}
```

---

### 2. Error 500 - Función `layout()` no definida
**Ubicación:** `app/views/pages/home/index.php`  
**Error:** `Call to undefined function layout()`

**Causa:** La función `layout()` no existía en `app/helpers/functions.php`. Estaba en `includes/helpers.php` pero no se cargaba.

**Solución:** Agregué la función `layout()` al archivo de helpers:
```php
function layout($name) {
    $layoutPath = APP_PATH . '/views/layouts/' . $name . '.php';
    if (file_exists($layoutPath)) {
        require $layoutPath;
    } else {
        error_log("Layout no encontrado: $layoutPath");
    }
}
```

---

### 3. Error - Acceso a objetos como arrays
**Ubicación:** `app/views/pages/home.php` línea 58  
**Error:** `Cannot use object of type stdClass as array`

**Causa:** El modelo `Categoria` devuelve objetos (`stdClass`) pero la vista intentaba acceder a ellos como arrays usando `$cat['id']`

**Solución:** Cambié el acceso de arrays a objetos en la vista:
```php
// ANTES:
$cat['id']
$cat['nombre']
$cat['icon']

// DESPUÉS:
$cat->id
$cat->nombre
$cat->icon ?? 'car'
```

---

### 4. Error SQL - Columna `p.activo` no existe
**Ubicación:** `app/controllers/CategoriaController.php` línea 168  
**Error:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.activo' in 'on clause'`

**Causa:** El query SQL intentaba filtrar por `p.activo = 1` pero la tabla `publicaciones` NO tiene una columna `activo`. Solo tiene la columna `estado`.

**Estructura real de la tabla:**
- ✅ Tiene: `estado` (enum: 'borrador','pendiente','aprobada','rechazada','vendida','archivada')
- ❌ NO tiene: `activo`

**Solución:** Eliminé la condición `AND p.activo = 1` del query:
```sql
-- ANTES:
LEFT JOIN publicaciones p ON p.subcategoria_id = s.id 
    AND p.estado = 'aprobada'
    AND p.activo = 1

-- DESPUÉS:
LEFT JOIN publicaciones p ON p.subcategoria_id = s.id 
    AND p.estado = 'aprobada'
```

---

## Archivos Modificados

1. ✅ `app/helpers/functions.php` - Agregadas funciones `icon()`, `layout()` y `e()`
2. ✅ `app/views/pages/home.php` - Cambiado acceso de arrays a objetos
3. ✅ `app/controllers/CategoriaController.php` - Eliminada referencia a columna inexistente `p.activo`

---

## Estado Final

✅ **http://chilechocados.local:8080/** - Funcionando correctamente (HTTP 200)  
✅ **http://chilechocados.local:8080/categorias** - Funcionando correctamente (HTTP 200)  
✅ Footer cargando correctamente  
✅ Sin errores en logs

---

## Notas Importantes

- La tabla `publicaciones` usa `estado` para controlar el estado de las publicaciones, no una columna `activo`
- Los modelos devuelven objetos `stdClass` por defecto con `PDO::FETCH_OBJ`
- Las funciones helper deben estar en `app/helpers/functions.php` para ser cargadas por `public/index.php`
