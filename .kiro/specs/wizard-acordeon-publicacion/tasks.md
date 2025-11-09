# Plan de Implementación - Wizard con Acordeones

## Objetivo

Transformar el formulario de publicación en una interfaz wizard con acordeones expandibles, manteniendo TODOS los campos existentes, la integración actual con la API y las validaciones sin modificar la funcionalidad backend.

---

## Tareas de Implementación

- [x] 1. Crear estructura HTML de wizard con acordeones
  - Envolver cada sección `.card` existente en estructura de acordeón
  - Agregar barra de progreso en la parte superior del formulario
  - Agregar encabezados de acordeón con íconos de estado
  - Agregar área de resumen en cada encabezado
  - Agregar botones de navegación (Anterior/Continuar) en cada paso
  - Mantener TODOS los campos del formulario existente sin modificar
  - Mantener atributos `name`, `id`, `required` de todos los campos
  - Mantener estructura de `<form>` y `action` URL existente
  - _Requisitos: 1.1, 1.2, 2.2, 2.3, 2.4, 5.1, 5.2, 5.3_

- [x] 2. Implementar estilos CSS del wizard
  - Crear estilos para barra de progreso con animación
  - Crear estilos para acordeones (colapsado/expandido)
  - Crear estilos para encabezados con íconos de estado
  - Crear estilos para área de resumen
  - Crear estilos para botones de navegación
  - Implementar animaciones de transición (300ms)
  - Agregar estilos responsive para móvil (< 768px)
  - Implementar estilos para dark mode
  - Asegurar que los estilos NO sobrescriban los existentes del formulario
  - _Requisitos: 2.1, 2.5, 6.1, 6.2, 6.4, 6.5, 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 3. Implementar clase WizardManager
  - Crear clase JavaScript `WizardManager` con constructor
  - Implementar método `init()` para inicializar wizard
  - Implementar método `goToStep(stepNumber, skipValidation)` para navegación
  - Implementar método `expandStep(stepNumber)` para expandir/colapsar acordeones
  - Implementar método `completeStep(stepNumber)` para marcar pasos completados
  - Implementar método `updateProgressBar()` para actualizar barra de progreso
  - Implementar detección automática de modo edición (`$modoEdicion`)
  - Implementar scroll automático al expandir paso
  - Agregar event listeners para botones Anterior/Continuar
  - Agregar event listeners para encabezados de acordeón (click para expandir)
  - NO modificar las funciones existentes `guardarBorrador()` y `enviarFormulario()`
  - _Requisitos: 1.1, 1.2, 1.4, 1.5, 5.4, 5.5, 6.3, 7.1, 7.3_

- [x] 4. Implementar validación por pasos
  - Crear objeto `stepValidationRules` con reglas para cada paso
  - Implementar validación para Paso 1 (tipificación)
  - Implementar validación para Paso 2 (tipo_venta)
  - Implementar validación para Paso 3 (marca, modelo, año, categoría, subcategoría, región, comuna, descripción, precio condicional)
  - Implementar validación para Paso 4 (fotos - considerar fotos existentes en modo edición)
  - Implementar validación para Paso 5 (promoción)
  - Integrar con función existente `validarFormulario()` para validación final
  - Integrar con modal existente `mostrarModalValidacion()` para mostrar errores
  - Implementar resaltado de campos inválidos con borde rojo
  - Implementar remoción automática de errores al corregir campos
  - Mantener validación de lógica existente (tipificación → tipo_venta → precio)
  - _Requisitos: 4.1, 4.2, 4.3, 4.4, 4.5, 7.5_

- [x] 5. Implementar generador de resúmenes
  - Crear objeto `summaryGenerators` con función para cada paso
  - Implementar generador de resumen para Paso 1 (mostrar tipificación seleccionada)
  - Implementar generador de resumen para Paso 2 (mostrar tipo de venta)
  - Implementar generador de resumen para Paso 3 (mostrar "Marca Modelo Año")
  - Implementar generador de resumen para Paso 4 (mostrar "X fotos")
  - Implementar generador de resumen para Paso 5 (mostrar tipo de promoción)
  - Implementar método `generateSummary(stepNumber)` en WizardManager
  - Actualizar resumen automáticamente al completar paso
  - Actualizar resumen al editar paso completado
  - _Requisitos: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [x] 6. Implementar modo edición
  - Detectar variable PHP `$modoEdicion` al inicializar wizard
  - Si modo edición: marcar todos los pasos como completados
  - Si modo edición: generar resúmenes basados en datos existentes (`$publicacion`)
  - Si modo edición: permitir expandir cualquier paso sin restricciones
  - Si modo edición: expandir Paso 1 por defecto
  - Mantener pre-población de campos existente (ya implementada en PHP)
  - Mantener carga de subcategorías y comunas existente
  - Mantener visualización de fotos existentes
  - _Requisitos: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 7. Implementar persistencia de datos
  - Verificar que datos persisten al navegar entre pasos (sin recargar página)
  - Verificar que selecciones de archivos se mantienen
  - Verificar que estados dinámicos se preservan (visibilidad de campo precio)
  - Mantener funcionalidad existente de "Guardar borrador"
  - Implementar auto-guardado en localStorage como respaldo (opcional)
  - _Requisitos: 9.1, 9.2, 9.3, 9.4, 9.5_

- [x] 8. Implementar accesibilidad
  - Agregar atributos ARIA a encabezados de acordeón (`role="button"`, `aria-expanded`)
  - Agregar `tabindex="0"` a encabezados para navegación por teclado
  - Implementar navegación con Enter/Space para expandir acordeones
  - Agregar `aria-label` a íconos de estado
  - Agregar `aria-live` para anunciar cambios de paso
  - Verificar orden lógico de foco (Tab)
  - _Requisitos: 10.1, 10.2, 10.3, 10.4, 10.5_

- [x] 9. Testing y ajustes finales
  - Probar flujo completo: crear publicación nueva desde paso 1 hasta envío
  - Probar navegación: avanzar, retroceder, saltar a pasos completados
  - Probar validación: intentar avanzar sin completar campos en cada paso
  - Probar resúmenes: verificar que se generan correctamente para cada paso
  - Probar modo edición: cargar publicación existente y verificar todos los pasos
  - Probar "Guardar borrador" desde diferentes pasos
  - Probar responsive: móvil (< 768px), tablet, desktop
  - Probar dark mode: verificar contraste y legibilidad
  - Probar integración con API: verificar que datos se envían correctamente al backend
  - Verificar que TODOS los campos se envían (usar DevTools Network tab)
  - Probar lógica existente: tipificación → tipo_venta, región → comunas, categoría → subcategorías
  - Probar carga de imágenes y preview
  - _Requisitos: Todos_

---

## Notas Importantes

### ⚠️ Restricciones Críticas

1. **NO modificar campos del formulario:**
   - Mantener todos los atributos `name` exactamente iguales
   - Mantener todos los `id` para JavaScript existente
   - Mantener estructura de `<form>` y `action` URL
   - No eliminar campos ocultos (csrf_token, guardar_borrador)

2. **NO modificar integración con API:**
   - El formulario debe seguir enviándose al mismo endpoint
   - Los datos deben enviarse en el mismo formato
   - Mantener `enctype="multipart/form-data"` para fotos

3. **NO modificar validaciones existentes:**
   - Mantener función `validarFormulario()`
   - Mantener función `mostrarModalValidacion()`
   - Mantener lógica de tipificación → tipo_venta → precio
   - Agregar validación por paso SIN eliminar validación final

4. **NO modificar funciones existentes:**
   - `guardarBorrador()` debe seguir funcionando igual
   - `enviarFormulario()` debe seguir funcionando igual
   - Event listeners de tipificación, tipo_venta, región, categoría deben mantenerse

5. **Mantener compatibilidad:**
   - Modo edición debe seguir funcionando
   - Carga de subcategorías y comunas vía AJAX debe mantenerse
   - Preview de imágenes debe mantenerse
   - Dark mode debe seguir funcionando

### ✅ Estrategia de Implementación

- **Enfoque aditivo:** Agregar funcionalidad de wizard SIN eliminar código existente
- **Wrapper approach:** Envolver elementos existentes en lugar de reemplazarlos
- **Progressive enhancement:** El formulario debe funcionar incluso si JavaScript falla
- **Testing continuo:** Probar después de cada tarea para detectar problemas temprano

### 🎯 Criterios de Éxito

- ✅ Usuario puede completar publicación paso a paso
- ✅ Validación funciona en cada paso
- ✅ Resúmenes se muestran correctamente
- ✅ Modo edición permite acceder a todos los pasos
- ✅ Responsive funciona en móvil
- ✅ Dark mode se ve correctamente
- ✅ Datos se envían correctamente al backend
- ✅ "Guardar borrador" funciona desde cualquier paso
- ✅ Todas las funcionalidades existentes siguen funcionando
