# 🧹 Limpiar Caché del Navegador

## Problema
Si el navegador tiene datos antiguos del JSON en localStorage, puede mostrar menos marcas de las que hay en la base de datos.

## Solución

### Opción 1: Limpiar localStorage desde la Consola del Navegador

1. Abre DevTools (F12)
2. Ve a la pestaña **Console**
3. Ejecuta estos comandos:

```javascript
// Ver qué hay en caché
console.log('Datos en caché:', localStorage.getItem('marcas_modelos_data'));
console.log('Tiempo de caché:', localStorage.getItem('marcas_modelos_cache_time'));

// Limpiar caché
localStorage.removeItem('marcas_modelos_data');
localStorage.removeItem('marcas_modelos_cache_time');

// Recargar página
location.reload();
```

### Opción 2: Limpiar desde DevTools

1. Abre DevTools (F12)
2. Ve a la pestaña **Application**
3. En el menú izquierdo, expande **Local Storage**
4. Click en `http://chilechocados.local:8080`
5. Busca las keys:
   - `marcas_modelos_data`
   - `marcas_modelos_cache_time`
6. Click derecho → **Delete**
7. Refresca la página (Ctrl+Shift+R)

### Opción 3: Limpiar todo el almacenamiento

1. Abre DevTools (F12)
2. Ve a la pestaña **Application**
3. En el menú izquierdo, click en **Clear storage**
4. Click en el botón **Clear site data**
5. Refresca la página (Ctrl+Shift+R)

## Verificar que funciona

Después de limpiar el caché, en la consola deberías ver:

```
🚀 Inicializando MarcaModeloSelector...
✅ Inputs encontrados, creando selector...
Datos de marcas/modelos cargados desde API (BD)
Total marcas: 27
📋 Configurando autocompletado con 27 marcas
```

Si ves **27 marcas**, está funcionando correctamente desde la base de datos.

Si ves menos de 27, el caché antiguo sigue activo.

## Verificar en Network

1. Abre DevTools (F12)
2. Ve a la pestaña **Network**
3. Refresca la página
4. Busca la petición a `/api/marcas`
5. Click en ella
6. Ve a la pestaña **Response**
7. Deberías ver un JSON con 27 marcas

## Forzar recarga sin caché

Usa **Ctrl+Shift+R** (Windows/Linux) o **Cmd+Shift+R** (Mac) para forzar una recarga completa sin caché.
