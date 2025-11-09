# 🧪 Instrucciones de Prueba - Fase 2: Sistema de Marca y Modelo

## Fecha: 2025-11-08
## Estado: LISTO PARA PRUEBAS

---

## 📋 Pre-requisitos

Antes de comenzar las pruebas, asegúrate de:

1. ✅ Migración de BD ejecutada correctamente
2. ✅ Servidor local corriendo (Apache/Nginx + PHP + MySQL)
3. ✅ Navegador con DevTools disponible
4. ✅ Usuario normal y usuario admin creados

---

## 🔍 Prueba 1: Autocompletado con Marca del Catálogo

### Objetivo
Verificar que el autocompletado funciona correctamente con marcas del catálogo.

### Pasos

1. **Abrir página de publicar**
   ```
   http://chilechocados.local:8080/publicar
   ```

2. **Verificar carga del JSON**
   - Abrir DevTools (F12)
   - Ir a pestaña "Network"
   - Buscar petición a `chileautos_marcas_modelos.json`
   - Verificar que se carga correctamente (Status 200)

3. **Probar autocompletado de Marca**
   - Click en campo "Marca"
   - Escribir "toy"
   - Verificar que aparece "Toyota" en sugerencias
   - Seleccionar "Toyota"

4. **Probar autocompletado de Modelo**
   - Verificar que campo "Modelo" se habilita
   - Click en campo "Modelo"
   - Escribir "cor"
   - Verificar que aparece "Corolla" en sugerencias
   - Seleccionar "Corolla"

5. **Completar formulario**
   - Llenar todos los campos requeridos
   - Subir al menos 1 foto
   - Click "Publicar"

6. **Verificar resultado**
   - Publicación debe guardarse como "pendiente" (no borrador)
   - No debe aparecer warning de marca personalizada
   - Verificar en BD:
     ```sql
     SELECT marca, modelo, marca_personalizada, modelo_personalizado, estado 
     FROM publicaciones 
     ORDER BY id DESC LIMIT 1;
     ```
   - Debe mostrar: `marca_personalizada = 0`, `modelo_personalizado = 0`, `estado = 'pendiente'`

### Resultado Esperado
✅ Autocompletado funciona  
✅ Publicación guardada como "pendiente"  
✅ No se crea solicitud de aprobación  

---

## 🔍 Prueba 2: Marca Personalizada

### Objetivo
Verificar que el sistema detecta y maneja marcas personalizadas correctamente.

### Pasos

1. **Abrir página de publicar**
   ```
   http://chilechocados.local:8080/publicar
   ```

2. **Ingresar marca personalizada**
   - Click en campo "Marca"
   - Escribir "BYD" (marca no en catálogo)
   - Presionar Tab o click fuera del campo

3. **Verificar warning**
   - Debe aparecer un alert amarillo debajo del campo Modelo
   - Texto: "Marca personalizada - Has ingresado una marca que no está en nuestro catálogo..."

4. **Ingresar modelo personalizado**
   - Campo "Modelo" debe estar habilitado
   - Escribir "Seal"
   - Presionar Tab o click fuera del campo

5. **Completar formulario**
   - Llenar todos los campos requeridos
   - Subir al menos 1 foto
   - Click "Publicar"

6. **Verificar resultado**
   - Publicación debe guardarse como "borrador"
   - Verificar en BD:
     ```sql
     SELECT id, marca, modelo, marca_personalizada, modelo_personalizado, 
            marca_original, modelo_original, estado 
     FROM publicaciones 
     ORDER BY id DESC LIMIT 1;
     ```
   - Debe mostrar:
     - `marca_personalizada = 1`
     - `modelo_personalizado = 1`
     - `marca_original = 'BYD'`
     - `modelo_original = 'Seal'`
     - `estado = 'borrador'`

7. **Verificar solicitud creada**
   ```sql
   SELECT * FROM marcas_modelos_pendientes 
   ORDER BY id DESC LIMIT 1;
   ```
   - Debe existir registro con:
     - `marca_ingresada = 'BYD'`
     - `modelo_ingresado = 'Seal'`
     - `estado = 'pendiente'`

### Resultado Esperado
✅ Warning aparece correctamente  
✅ Publicación guardada como "borrador"  
✅ Solicitud creada en tabla `marcas_modelos_pendientes`  
✅ Campos `marca_personalizada` y `modelo_personalizado` = 1  

---

## 🔍 Prueba 3: Panel de Admin - Listar Pendientes

### Objetivo
Verificar que el panel de admin muestra correctamente las solicitudes pendientes.

### Pasos

1. **Login como admin**
   ```
   http://chilechocados.local:8080/login
   ```
   - Usuario: admin@chilechocados.cl (o tu usuario admin)
   - Contraseña: tu contraseña admin

2. **Verificar badge en menú**
   - En el menú lateral admin, buscar "Marcas/Modelos"
   - Debe aparecer un badge amarillo con el número de pendientes
   - Ejemplo: "Marcas/Modelos (1)"

3. **Acceder al panel**
   ```
   http://chilechocados.local:8080/admin/marcas-modelos-pendientes
   ```

4. **Verificar tabla de pendientes**
   - Debe mostrar la solicitud de "BYD Seal"
   - Columnas visibles:
     - Fecha de solicitud
     - Título de publicación (con link)
     - Nombre y email del usuario
     - Marca ingresada: "BYD"
     - Modelo ingresado: "Seal"
     - Botones: "Aprobar" y "Rechazar"

5. **Verificar historial**
   - Scroll hacia abajo
   - Debe haber una sección "Historial Reciente"
   - Si hay solicitudes procesadas anteriormente, deben aparecer aquí

### Resultado Esperado
✅ Badge aparece en menú con número correcto  
✅ Panel carga correctamente  
✅ Solicitud pendiente visible en tabla  
✅ Todos los datos se muestran correctamente  

---

## 🔍 Prueba 4: Aprobar Marca Personalizada

### Objetivo
Verificar que el proceso de aprobación funciona correctamente.

### Pasos

1. **Estar en panel de admin**
   ```
   http://chilechocados.local:8080/admin/marcas-modelos-pendientes
   ```

2. **Click en "Aprobar"**
   - Click en botón verde "Aprobar" de la solicitud "BYD Seal"
   - Debe abrirse un modal

3. **Verificar modal de aprobación**
   - Título: "Aprobar Marca/Modelo"
   - Alert azul con instrucciones
   - Campos:
     - Marca Sugerida (opcional)
     - Modelo Sugerido (opcional)
     - Notas (opcional)
   - Botones: "Cancelar" y "Aprobar"

4. **Aprobar tal cual (sin modificar)**
   - Dejar campos vacíos
   - Click "Aprobar"

5. **Verificar resultado**
   - Debe aparecer mensaje de éxito: "Marca/modelo aprobado exitosamente"
   - Solicitud debe desaparecer de tabla de pendientes
   - Badge en menú debe actualizarse (decrementar o desaparecer)

6. **Verificar en BD**
   ```sql
   -- Verificar solicitud
   SELECT estado, fecha_revision, admin_id 
   FROM marcas_modelos_pendientes 
   WHERE marca_ingresada = 'BYD';
   
   -- Verificar publicación
   SELECT estado, marca_modelo_aprobado 
   FROM publicaciones 
   WHERE marca = 'BYD';
   ```
   - Solicitud: `estado = 'aprobado'`, `fecha_revision` no NULL
   - Publicación: `estado = 'pendiente'`, `marca_modelo_aprobado = 1`

### Resultado Esperado
✅ Modal se abre correctamente  
✅ Aprobación se procesa sin errores  
✅ Publicación cambia de "borrador" a "pendiente"  
✅ Solicitud cambia a "aprobado"  
✅ Badge se actualiza  

---

## 🔍 Prueba 5: Aprobar con Modificación

### Objetivo
Verificar que el admin puede modificar la marca/modelo al aprobar.

### Pasos

1. **Crear otra publicación con marca personalizada**
   - Seguir pasos de Prueba 2
   - Usar marca: "Geely", modelo: "Coolray"

2. **Ir al panel de admin**
   ```
   http://chilechocados.local:8080/admin/marcas-modelos-pendientes
   ```

3. **Click en "Aprobar"**
   - Click en botón "Aprobar" de la solicitud "Geely Coolray"

4. **Modificar marca/modelo**
   - En campo "Marca Sugerida": escribir "Geely"
   - En campo "Modelo Sugerido": escribir "Coolray"
   - En campo "Notas": escribir "Marca china, aprobada"
   - Click "Aprobar"

5. **Verificar resultado**
   - Mensaje de éxito
   - Verificar en BD:
     ```sql
     SELECT estado, marca_sugerida, modelo_sugerido, notas_admin 
     FROM marcas_modelos_pendientes 
     WHERE marca_ingresada = 'Geely';
     ```
   - Debe mostrar:
     - `estado = 'modificado'`
     - `marca_sugerida = 'Geely'`
     - `modelo_sugerido = 'Coolray'`
     - `notas_admin = 'Marca china, aprobada'`

### Resultado Esperado
✅ Modificación se guarda correctamente  
✅ Estado cambia a "modificado"  
✅ Notas se guardan  
✅ Publicación se aprueba con valores modificados  

---

## 🔍 Prueba 6: Rechazar Marca Personalizada

### Objetivo
Verificar que el proceso de rechazo funciona correctamente.

### Pasos

1. **Crear otra publicación con marca personalizada**
   - Seguir pasos de Prueba 2
   - Usar marca: "MarcaInvalida", modelo: "ModeloInvalido"

2. **Ir al panel de admin**
   ```
   http://chilechocados.local:8080/admin/marcas-modelos-pendientes
   ```

3. **Click en "Rechazar"**
   - Click en botón rojo "Rechazar" de la solicitud
   - Debe abrirse un modal

4. **Verificar modal de rechazo**
   - Título: "Rechazar Marca/Modelo"
   - Alert amarillo con advertencia
   - Campo: "Motivo del Rechazo" (requerido)
   - Botones: "Cancelar" y "Rechazar"

5. **Rechazar con motivo**
   - En campo "Motivo": escribir "Marca no válida para el catálogo"
   - Click "Rechazar"

6. **Verificar resultado**
   - Mensaje de éxito: "Marca/modelo rechazado"
   - Solicitud desaparece de pendientes
   - Verificar en BD:
     ```sql
     SELECT estado, notas_admin 
     FROM marcas_modelos_pendientes 
     WHERE marca_ingresada = 'MarcaInvalida';
     
     SELECT estado, motivo_rechazo 
     FROM publicaciones 
     WHERE marca = 'MarcaInvalida';
     ```
   - Solicitud: `estado = 'rechazado'`, `notas_admin` con motivo
   - Publicación: `estado = 'borrador'`, `motivo_rechazo` con motivo

### Resultado Esperado
✅ Modal se abre correctamente  
✅ Rechazo se procesa sin errores  
✅ Publicación permanece como "borrador"  
✅ Motivo se guarda correctamente  
✅ Solicitud cambia a "rechazado"  

---

## 🔍 Prueba 7: Caché de JSON

### Objetivo
Verificar que el caché en localStorage funciona correctamente.

### Pasos

1. **Primera carga**
   - Abrir DevTools (F12)
   - Ir a pestaña "Application" > "Local Storage"
   - Limpiar localStorage
   - Ir a `/publicar`
   - En pestaña "Network", verificar petición a `chileautos_marcas_modelos.json`
   - En "Local Storage", verificar que aparecen:
     - `marcas_modelos_data` (con JSON completo)
     - `marcas_modelos_cache_time` (timestamp)

2. **Segunda carga (con caché)**
   - Recargar página (F5)
   - En pestaña "Network", verificar que NO hay petición a JSON
   - En consola, debe aparecer: "Datos de marcas/modelos cargados desde caché"

3. **Expiración de caché**
   - En "Local Storage", editar `marcas_modelos_cache_time`
   - Cambiar timestamp a hace 25 horas (restar 90000000 al valor actual)
   - Recargar página
   - Verificar que se hace nueva petición al JSON
   - Verificar que timestamp se actualiza

### Resultado Esperado
✅ Primera carga: fetch al JSON  
✅ Recargas: datos desde localStorage  
✅ Caché expira después de 24 horas  
✅ Timestamp se actualiza correctamente  

---

## 🔍 Prueba 8: Modo Edición

### Objetivo
Verificar que el autocompletado funciona en modo edición.

### Pasos

1. **Editar publicación existente**
   - Ir a "Mis Publicaciones"
   - Click "Editar" en una publicación con marca del catálogo (ej: Toyota Corolla)

2. **Verificar pre-carga de valores**
   - Campo "Marca" debe mostrar "Toyota"
   - Campo "Modelo" debe mostrar "Corolla"
   - Autocompletado debe estar activo

3. **Cambiar marca**
   - Cambiar marca a "Chevrolet"
   - Verificar que modelos se actualizan
   - Seleccionar "Spark"

4. **Guardar cambios**
   - Click "Actualizar publicación"
   - Verificar que se guarda correctamente

5. **Editar con marca personalizada**
   - Editar una publicación con marca personalizada (ej: BYD Seal)
   - Cambiar modelo a "Atto 3"
   - Verificar que aparece warning
   - Guardar
   - Verificar que se crea/actualiza solicitud

### Resultado Esperado
✅ Valores pre-cargan correctamente  
✅ Autocompletado funciona en edición  
✅ Cambios se guardan correctamente  
✅ Marca personalizada se detecta en edición  

---

## 🔍 Prueba 9: Dark Mode

### Objetivo
Verificar que todos los elementos se ven correctamente en modo oscuro.

### Pasos

1. **Activar dark mode**
   - Click en toggle de dark mode en header

2. **Verificar página de publicar**
   - Ir a `/publicar`
   - Verificar que warning amarillo se ve bien
   - Verificar que campos de autocompletado se ven bien

3. **Verificar panel de admin**
   - Ir a `/admin/marcas-modelos-pendientes`
   - Verificar que tabla se ve bien
   - Verificar que badges se ven bien
   - Verificar que modales se ven bien

4. **Verificar contraste**
   - Todos los textos deben ser legibles
   - Botones deben tener buen contraste
   - Alerts deben ser visibles

### Resultado Esperado
✅ Todos los elementos visibles en dark mode  
✅ Buen contraste en todos los componentes  
✅ Warnings legibles  
✅ Modales con colores apropiados  

---

## 📊 Checklist Final

Antes de considerar la Fase 2 como completamente probada, verifica:

- [ ] ✅ Autocompletado funciona con marcas del catálogo
- [ ] ✅ Autocompletado funciona con modelos
- [ ] ✅ Detección de marcas personalizadas funciona
- [ ] ✅ Warning aparece correctamente
- [ ] ✅ Publicación se guarda como borrador si es personalizada
- [ ] ✅ Solicitud se crea en BD
- [ ] ✅ Panel de admin muestra pendientes
- [ ] ✅ Badge en menú funciona
- [ ] ✅ Aprobación funciona (tal cual)
- [ ] ✅ Aprobación funciona (con modificación)
- [ ] ✅ Rechazo funciona
- [ ] ✅ Caché de JSON funciona
- [ ] ✅ Modo edición funciona
- [ ] ✅ Dark mode se ve bien
- [ ] ✅ No hay errores en consola
- [ ] ✅ No hay errores en logs PHP

---

## 🐛 Reporte de Bugs

Si encuentras algún bug durante las pruebas, documéntalo aquí:

### Bug #1
**Descripción:**  
**Pasos para reproducir:**  
**Resultado esperado:**  
**Resultado actual:**  
**Prioridad:** Alta / Media / Baja  

---

## 📝 Notas Adicionales

- Todas las pruebas deben realizarse en entorno de desarrollo
- Verificar logs de PHP en `logs/php_errors.log`
- Verificar logs de BD en `logs/database_errors.log`
- Usar DevTools para verificar errores JavaScript

---

**Preparado por:** Kiro AI  
**Fecha:** 2025-11-08  
**Versión:** 1.0  
**Estado:** 📋 LISTO PARA PRUEBAS
