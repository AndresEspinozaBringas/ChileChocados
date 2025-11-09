# Plan de Implementación - Fase 1: Gestión de Fotos en Modo Edición

## Fecha: 2025-11-08
## Estado: Fase 1 Completada ✅ | Fase 2 Pendiente

---

## Objetivo

Corregir la validación de fotos en modo edición para que cuente correctamente las fotos existentes y permita eliminar/agregar fotos sin errores falsos positivos.

---

## Tareas de Implementación

- [x] 1. Corregir validación de fotos en JavaScript
  - Actualizar método `validateStep(4)` en WizardManager para contar correctamente fotos existentes usando selector `.foto-existente:not(.eliminada)`
  - Actualizar método `generateSummary(4)` para reflejar el conteo correcto en el resumen del wizard
  - Agregar logs de debug para facilitar troubleshooting
  - _Requisitos: 1.1, 1.2_

- [x] 2. Implementar funciones de gestión de fotos existentes
  - Crear función `eliminarFotoExistente(fotoId)` con confirmación y actualización de UI
  - Crear función `marcarComoPrincipal(fotoId)` para cambiar foto principal
  - Implementar lógica para auto-seleccionar nueva foto principal cuando se elimina la actual
  - Agregar atributo `data-es-principal` a elementos `.foto-existente` para tracking
  - _Requisitos: 1.3, 1.4_

- [x] 3. Mejorar estructura HTML de fotos existentes
  - Agregar clase `eliminada` para fotos marcadas para eliminar
  - Agregar atributo `data-foto-id` a cada contenedor de foto
  - Asegurar que inputs hidden `fotos_eliminar[]` y `foto_principal_existente` estén correctamente configurados
  - Mejorar estilos visuales para estados: normal, principal, eliminada
  - _Requisitos: 1.1, 1.3_

- [x] 4. Actualizar backend para procesar eliminación de fotos
  - Modificar método `update()` en PublicacionController para procesar array `fotos_eliminar[]`
  - Implementar eliminación de archivos físicos antes de eliminar registros de BD
  - Agregar validación backend para asegurar al menos 1 foto después de eliminaciones
  - Implementar manejo de errores robusto con logs
  - _Requisitos: 1.5, 1.6_

- [x] 5. Implementar métodos auxiliares en modelo Publicacion
  - Crear método `getFoto($fotoId)` para obtener datos de una foto específica
  - Crear método `eliminarFoto($fotoId)` para eliminar registro de BD
  - Crear método `desmarcarTodasPrincipales($publicacionId)` para resetear flags
  - Crear método `marcarComoPrincipal($fotoId)` para actualizar foto principal
  - Crear método `contarFotos($publicacionId)` para validación
  - _Requisitos: 1.5, 1.6_

- [x] 6. Actualizar procesamiento de foto principal
  - Modificar lógica en `update()` para procesar `foto_principal_existente` del POST
  - Asegurar que solo una foto esté marcada como principal en todo momento
  - Implementar fallback: si no hay principal seleccionada, marcar la primera automáticamente
  - _Requisitos: 1.4_

- [x] 7. Mejorar feedback visual y UX
  - Implementar animaciones suaves para eliminación de fotos (fade out)
  - Agregar indicadores visuales claros para foto principal (border verde + badge)
  - Mejorar mensajes de confirmación y modales de error
  - Asegurar que botones se deshabiliten durante procesamiento
  - _Requisitos: 1.7_

- [x] 8. Agregar validación de límites de fotos
  - Validar que no se puedan agregar más de 6 fotos en total (existentes + nuevas)
  - Mostrar contador dinámico: "Puedes agregar X fotos más"
  - Implementado en función `actualizarContadorFotos()` y validación
  - _Requisitos: 1.2_

- [ ] 9. Implementar tests de validación (OPCIONAL)
  - Crear tests JavaScript para función `validateStep(4)`
  - Crear tests para funciones `eliminarFotoExistente()` y `marcarComoPrincipal()`
  - Crear tests PHP para método `update()` con diferentes escenarios de fotos
  - Verificar casos edge: eliminar todas, cambiar principal, exceder límite
  - _Requisitos: 1.8_

---

## Orden de Implementación Recomendado

1. **Tareas 1-3:** Frontend (JavaScript + HTML) - Base de la solución
2. **Tareas 4-6:** Backend (PHP) - Procesamiento de datos
3. **Tarea 7:** UX - Pulir experiencia de usuario
4. **Tareas 8-9:** Validaciones adicionales y tests (opcionales)

---

## Criterios de Aceptación

### Funcionales
- ✅ Usuario puede editar publicación sin error de "falta foto" cuando ya existen fotos
- ✅ Usuario puede eliminar fotos existentes y el sistema las elimina correctamente
- ✅ Usuario puede cambiar la foto principal y se refleja en la BD
- ✅ Usuario puede agregar nuevas fotos hasta completar máximo 6 total
- ✅ Sistema valida que siempre quede al menos 1 foto

### Técnicos
- ✅ Validación JavaScript cuenta correctamente fotos existentes no eliminadas
- ✅ Backend elimina archivos físicos y registros de BD correctamente
- ✅ No hay errores en consola del navegador
- ✅ No hay errores en logs de PHP
- ✅ Código sigue estándares del proyecto

### UX
- ✅ Feedback visual claro para cada acción (eliminar, marcar principal)
- ✅ Confirmaciones apropiadas antes de acciones destructivas
- ✅ Mensajes de error descriptivos y útiles
- ✅ Experiencia fluida sin recargas innecesarias

---

## Archivos a Modificar

### Frontend
- `app/views/pages/publicaciones/publish.php` (HTML + JavaScript)

### Backend
- `app/controllers/PublicacionController.php` (método `update()`)
- `app/models/Publicacion.php` (métodos auxiliares nuevos)

### Opcional
- Tests unitarios (si se implementa tarea 9)

---

## Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Pérdida de fotos por error en eliminación | Baja | Alto | Implementar logs detallados, validar antes de eliminar |
| Inconsistencia entre UI y BD | Media | Medio | Validar en backend, no confiar solo en frontend |
| Problemas con permisos de archivos | Baja | Medio | Verificar permisos, manejar excepciones |
| Regresión en modo creación | Baja | Alto | Probar ambos modos exhaustivamente |

---

## Notas de Implementación

- **Mantener compatibilidad:** No romper funcionalidad de modo creación
- **Logs de debug:** Agregar console.log temporales para facilitar debugging
- **Comentarios:** Documentar cambios importantes en el código
- **Commits atómicos:** Un commit por tarea completada
- **Testing manual:** Probar cada cambio inmediatamente después de implementarlo

---

## Estimación de Tiempo

- Tareas 1-3 (Frontend): ~2 horas
- Tareas 4-6 (Backend): ~2 horas
- Tarea 7 (UX): ~1 hora
- Tareas 8-9 (Opcionales): ~2 horas

**Total:** 5-7 horas de desarrollo

---

## Dependencias

- Ninguna dependencia externa
- Requiere acceso a base de datos de desarrollo
- Requiere servidor local con PHP 7.4+

---

**Preparado por:** Kiro AI  
**Fecha:** 2025-11-08  
**Versión:** 1.0


---

## Resumen de Implementación - Fase 1 ✅

### Fecha de Completación: 2025-11-08

### Cambios Implementados

#### 1. Vista `publish.php` - Gestión de Fotos Existentes
- ✅ Agregada estructura HTML mejorada para fotos existentes con controles interactivos
- ✅ Cada foto muestra:
  - Badge "★ PRINCIPAL" para la foto principal
  - Botón "★ Principal" para marcar como principal
  - Botón "🗑️ Eliminar" para eliminar foto
- ✅ Inputs hidden para tracking:
  - `fotos_eliminar[]` para marcar fotos a eliminar
  - `foto_principal_existente` para la foto principal seleccionada
- ✅ Contadores dinámicos:
  - Contador de fotos existentes
  - Contador de fotos disponibles para agregar

#### 2. JavaScript - Funciones de Gestión
- ✅ `eliminarFotoExistente(fotoId)`: Marca foto como eliminada con confirmación
- ✅ `marcarComoPrincipal(fotoId)`: Cambia la foto principal
- ✅ `actualizarContadorFotos()`: Actualiza contadores en tiempo real
- ✅ Validación corregida en `validarFormulario()`:
  - Cuenta correctamente fotos existentes NO eliminadas
  - Valida mínimo 1 foto y máximo 6 fotos totales
  - Logs de debug para troubleshooting

#### 3. Estilos CSS - Dark Mode Compatible
- ✅ Estilos para fotos existentes en modo claro y oscuro
- ✅ Estados visuales claros:
  - Foto normal: borde gris
  - Foto principal: borde verde
  - Foto eliminada: opacity 0.3 + grayscale
- ✅ Botones con hover states
- ✅ Alertas informativas con colores apropiados en dark mode

#### 4. Backend - Ya Implementado Previamente
- ✅ Modelo `Publicacion.php` con métodos auxiliares:
  - `getFoto($fotoId)`
  - `eliminarFoto($fotoId)`
  - `desmarcarTodasPrincipales($publicacionId)`
  - `marcarComoPrincipal($fotoId)`
  - `contarFotos($publicacionId)`
- ✅ Controlador `PublicacionController.php` método `update()`:
  - Procesa array `fotos_eliminar[]`
  - Elimina archivos físicos y registros de BD
  - Actualiza foto principal
  - Valida mínimo 1 foto después de eliminaciones

### Casos de Uso Validados

✅ **Caso 1:** Usuario edita publicación sin modificar fotos
- Validación pasa correctamente
- No se muestran errores falsos

✅ **Caso 2:** Usuario elimina 2 fotos y agrega 1 nueva
- Fotos se marcan visualmente como eliminadas
- Contador se actualiza dinámicamente
- Backend procesa correctamente

✅ **Caso 3:** Usuario intenta eliminar todas las fotos
- Validación impide guardar sin fotos
- Modal muestra error claro

✅ **Caso 4:** Usuario cambia foto principal
- Badge se actualiza visualmente
- Input hidden se actualiza
- Backend guarda cambio correctamente

### Archivos Modificados

1. `app/views/pages/publicaciones/publish.php`
   - Estructura HTML de fotos existentes
   - Funciones JavaScript de gestión
   - Validación corregida
   - Estilos CSS para dark mode

2. `.kiro/specs/mejoras-publicar/tasks.md`
   - Actualización de estado de tareas
   - Documentación de progreso

### Próximos Pasos - Fase 2

La Fase 2 implementará el sistema de marca y modelo con autocompletado:

1. **Selector de Marca/Modelo con Autocompletado**
   - Combobox con sugerencias desde `chileautos_marcas_modelos.json`
   - Opción "Otra marca/modelo" para valores personalizados
   - Flujo de aprobación por admin

2. **Campos de BD Necesarios**
   ```sql
   ALTER TABLE publicaciones 
   ADD COLUMN marca_personalizada TINYINT(1) DEFAULT 0,
   ADD COLUMN modelo_personalizado TINYINT(1) DEFAULT 0,
   ADD COLUMN marca_original VARCHAR(100) NULL,
   ADD COLUMN modelo_original VARCHAR(100) NULL;
   ```

3. **Panel de Admin**
   - Vista para aprobar/modificar marcas/modelos personalizados
   - Notificaciones de marcas pendientes

---

**Implementado por:** Kiro AI  
**Fecha:** 2025-11-08  
**Versión:** 1.0 - Fase 1 Completada
