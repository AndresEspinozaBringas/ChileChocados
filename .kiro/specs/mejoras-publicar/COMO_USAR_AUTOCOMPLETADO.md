# 🎯 Cómo Usar el Autocompletado de Marca y Modelo

## Fecha: 2025-11-08

---

## 📝 Descripción

El autocompletado usa la tecnología **HTML5 datalist**, que es nativa del navegador. No es un dropdown tradicional, sino un campo de texto con sugerencias.

---

## 🔍 Cómo Funciona

### 1. Campo de Marca

**Paso a paso:**

1. **Click en el campo "Marca"**
   - Verás un campo de texto normal
   - Placeholder: "Ej: Toyota"

2. **Empieza a escribir**
   - Escribe las primeras letras, por ejemplo: "toy"
   - Aparecerá una lista desplegable con sugerencias
   - Verás: "Toyota", "Toyota Corolla", etc.

3. **Selecciona de la lista**
   - Puedes hacer click en una sugerencia
   - O usar las flechas ↑↓ del teclado y presionar Enter
   - O seguir escribiendo el nombre completo

4. **Resultado**
   - El campo se llena con la marca seleccionada
   - El campo "Modelo" se habilita automáticamente
   - Los modelos de esa marca se cargan

### 2. Campo de Modelo

**Paso a paso:**

1. **Primero debes seleccionar una marca**
   - El campo "Modelo" está deshabilitado hasta que selecciones una marca

2. **Click en el campo "Modelo"**
   - Ahora está habilitado
   - Placeholder: "Ej: Corolla"

3. **Empieza a escribir**
   - Escribe las primeras letras, por ejemplo: "cor"
   - Aparecerá una lista con los modelos de la marca seleccionada
   - Verás: "Corolla", "Corona", etc.

4. **Selecciona de la lista**
   - Click en una sugerencia
   - O usa las flechas ↑↓ y Enter
   - O escribe el nombre completo

---

## 🎨 Apariencia Visual

### En Chrome/Edge:
```
┌─────────────────────────────────┐
│ Toyota                      [▼] │ ← Pequeña flecha indica datalist
└─────────────────────────────────┘
     ↓ Al hacer click o escribir
┌─────────────────────────────────┐
│ Toyota                      [▼] │
├─────────────────────────────────┤
│ Toyota                          │ ← Sugerencias
│ Toyota Corolla                  │
│ Toyota Yaris                    │
└─────────────────────────────────┘
```

### En Firefox:
```
┌─────────────────────────────────┐
│ toy                         [▼] │
└─────────────────────────────────┘
     ↓ Al escribir
┌─────────────────────────────────┐
│ Toyota                          │ ← Sugerencias
│ Toyota Corolla                  │
└─────────────────────────────────┘
```

### En Safari:
```
┌─────────────────────────────────┐
│ toy                             │
└─────────────────────────────────┘
     ↓ Al escribir
┌─────────────────────────────────┐
│ Toyota                          │ ← Sugerencias
└─────────────────────────────────┘
```

---

## ✅ Verificar que Funciona

### 1. Abrir DevTools (F12)

**Consola debe mostrar:**
```
🚀 Inicializando MarcaModeloSelector...
Marca input: <input id="marca" ...>
Modelo input: <input id="modelo" ...>
✅ Inputs encontrados, creando selector...
Datos de marcas/modelos cargados desde servidor
```

**Si ves errores:**
- ❌ "No se encontraron los inputs" → Los campos no tienen ID
- ❌ "Error cargando datos" → El JSON no se puede cargar

### 2. Inspeccionar el HTML

**En DevTools, pestaña Elements:**

Busca el campo de marca, debe verse así:
```html
<input type="text" 
       id="marca" 
       name="marca" 
       list="marcas-list"    ← Esto conecta con el datalist
       placeholder="Ej: Toyota">

<datalist id="marcas-list">   ← Aquí están las sugerencias
  <option value="Chevrolet">
  <option value="Toyota">
  <option value="Nissan">
  ...
</datalist>
```

### 3. Verificar localStorage

**En DevTools, pestaña Application > Local Storage:**

Debe haber dos entradas:
- `marcas_modelos_data` → JSON completo con todas las marcas
- `marcas_modelos_cache_time` → Timestamp de cuando se cargó

---

## 🐛 Problemas Comunes

### Problema 1: No aparecen sugerencias

**Posibles causas:**
1. El script no se cargó
   - Verificar en Network que `marca-modelo-selector.js` se cargó (Status 200)
   
2. Los campos no tienen ID
   - Verificar en Elements que `<input id="marca">` existe
   
3. El JSON no se cargó
   - Verificar en Network que `chileautos_marcas_modelos.json` se cargó
   - Verificar en Console que no hay errores

**Solución:**
```bash
# Limpiar caché del navegador
Ctrl+Shift+R (Windows/Linux)
Cmd+Shift+R (Mac)

# O en DevTools:
Application > Clear storage > Clear site data
```

### Problema 2: Campo "Modelo" no se habilita

**Causa:**
- No se seleccionó una marca válida del catálogo

**Solución:**
- Asegúrate de seleccionar una marca de la lista de sugerencias
- No escribas una marca que no existe (eso es para marcas personalizadas)

### Problema 3: Sugerencias no se ven bien en dark mode

**Causa:**
- El datalist usa estilos del navegador

**Solución:**
- Esto es normal, el datalist HTML5 usa estilos nativos del navegador
- No se puede personalizar completamente con CSS

---

## 🎯 Ejemplo Completo

### Caso 1: Marca del Catálogo

1. Ir a: `http://chilechocados.local:8080/publicar`
2. Scroll hasta "Paso 3: Datos del vehículo"
3. Click en campo "Marca"
4. Escribir: "toy"
5. Ver sugerencias: "Toyota"
6. Click en "Toyota" o presionar Enter
7. Campo "Modelo" se habilita
8. Click en campo "Modelo"
9. Escribir: "cor"
10. Ver sugerencias: "Corolla", "Corona", etc.
11. Seleccionar "Corolla"
12. ✅ Listo, continuar con el formulario

### Caso 2: Marca Personalizada

1. Ir a: `http://chilechocados.local:8080/publicar`
2. Scroll hasta "Paso 3: Datos del vehículo"
3. Click en campo "Marca"
4. Escribir: "BYD" (marca no en catálogo)
5. Presionar Tab o click fuera del campo
6. ⚠️ Aparece warning amarillo: "Marca personalizada"
7. Campo "Modelo" se habilita (sin sugerencias)
8. Escribir: "Seal"
9. Presionar Tab
10. ⚠️ Warning se mantiene
11. Continuar con formulario
12. Al publicar, quedará como borrador para aprobación

---

## 📱 Compatibilidad

### Navegadores Soportados:
- ✅ Chrome 20+
- ✅ Firefox 4+
- ✅ Safari 12.1+
- ✅ Edge 12+
- ✅ Opera 9.5+

### Navegadores con Soporte Limitado:
- ⚠️ Safari < 12.1: Funciona pero sin flecha visual
- ⚠️ IE 10-11: Funciona pero con estilos básicos

### Móviles:
- ✅ Chrome Android
- ✅ Safari iOS 12.2+
- ✅ Firefox Android

---

## 🔧 Debugging

### Ver datos cargados en consola:

```javascript
// En la consola del navegador:
console.log(window.marcaModeloSelector);
console.log(window.marcaModeloSelector.data);
console.log(localStorage.getItem('marcas_modelos_data'));
```

### Forzar recarga del JSON:

```javascript
// En la consola del navegador:
localStorage.removeItem('marcas_modelos_data');
localStorage.removeItem('marcas_modelos_cache_time');
location.reload();
```

### Ver si una marca existe:

```javascript
// En la consola del navegador:
window.marcaModeloSelector.findMarca('Toyota');
// Debe retornar objeto con la marca
```

---

## 📞 Soporte

Si el autocompletado no funciona después de seguir estos pasos:

1. **Verificar consola del navegador** (F12 > Console)
2. **Verificar que los archivos existen:**
   - `/assets/js/marca-modelo-selector.js`
   - `/chileautos_marcas_modelos.json`
3. **Limpiar caché del navegador**
4. **Probar en modo incógnito**
5. **Probar en otro navegador**

---

**Última actualización:** 2025-11-08  
**Versión:** 1.0
