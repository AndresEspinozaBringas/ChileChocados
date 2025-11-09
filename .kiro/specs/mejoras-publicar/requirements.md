# Mejoras en Página de Publicar

## Fecha: 2025-11-08
## Estado: Pendiente

---

## 1. Sistema de Marca y Modelo con Autocompletado

### Problema Actual
- Los campos marca y modelo son inputs de texto libre
- No hay validación ni sugerencias
- No se aprovecha el archivo `chileautos_marcas_modelos.json`

### Solución Propuesta (UX/UI)

#### Opción A: Combobox con Autocompletado (Recomendada)
**Flujo del Usuario:**
1. Usuario hace clic en campo "Marca"
2. Aparece dropdown con marcas más populares (top 10)
3. Al escribir, filtra las marcas en tiempo real
4. Al final de la lista: opción "Otra marca (especificar)"
5. Si selecciona "Otra marca", se habilita input de texto libre
6. Mismo flujo para "Modelo" (dependiente de marca seleccionada)

**Ventajas:**
- Guía al usuario con opciones predefinidas
- Permite flexibilidad para casos no contemplados
- Reduce errores de escritura
- Mejora la consistencia de datos

#### Flujo de Aprobación Admin
1. Usuario ingresa marca/modelo personalizado
2. Se marca con flag `requiere_aprobacion_marca = 1`
3. Admin recibe notificación
4. Admin puede:
   - Aprobar tal cual
   - Modificar y aprobar
   - Rechazar (solicitar corrección)
5. Una vez aprobado, la publicación continúa su flujo normal

### Campos de BD Necesarios
```sql
ALTER TABLE publicaciones 
ADD COLUMN marca_personalizada TINYINT(1) DEFAULT 0,
ADD COLUMN modelo_personalizado TINYINT(1) DEFAULT 0,
ADD COLUMN marca_original VARCHAR(100) NULL COMMENT 'Marca ingresada por usuario antes de aprobación',
ADD COLUMN modelo_original VARCHAR(100) NULL COMMENT 'Modelo ingresado por usuario antes de aprobación';
```

---

## 2. Gestión de Fotos en Modo Edición

### Problema Actual
- Al editar, muestra fotos existentes
- Pero también muestra controles de "Seleccionar archivo"
- Validación requiere subir fotos nuevamente
- Diseño inconsistente entre crear y editar

### Solución Propuesta

#### Modo Crear (Sin cambios)
- 6 slots para subir fotos
- Mínimo 1 foto requerida
- Selección de foto principal

#### Modo Editar (Mejorado)
**Vista de Fotos Existentes:**
```
┌─────────────────────────────────────────┐
│ Fotos actuales: 4 imagen(es)            │
│                                          │
│ ┌────────┐ ┌────────┐ ┌────────┐       │
│ │ [IMG]  │ │ [IMG]  │ │ [IMG]  │       │
│ │PRINCIPAL│ │        │ │        │       │
│ │  [×]   │ │  [×]   │ │  [×]   │       │
│ │ [★]    │ │ [☆]    │ │ [☆]    │       │
│ └────────┘ └────────┘ └────────┘       │
│                                          │
│ [+ Agregar más fotos]                   │
└─────────────────────────────────────────┘
```

**Funcionalidades:**
- Mostrar miniaturas de fotos existentes
- Botón [×] para eliminar foto
- Botón [★] para marcar como principal
- Botón "+ Agregar más fotos" para subir adicionales
- Validación: Al menos 1 foto debe quedar (existente o nueva)

**Lógica de Validación:**
```javascript
const fotosExistentes = document.querySelectorAll('.foto-existente:not(.eliminada)').length;
const fotosNuevas = document.querySelectorAll('input[type="file"]').filter(f => f.files.length > 0).length;
const totalFotos = fotosExistentes + fotosNuevas;

if (totalFotos < 1) {
  error('Debe mantener al menos 1 foto');
}
```

---

## 3. Implementación Técnica

### Archivos a Modificar

1. **app/views/pages/publicaciones/publish.php**
   - Reemplazar inputs de marca/modelo por combobox
   - Mejorar sección de fotos en modo edición

2. **app/controllers/PublicacionController.php**
   - Agregar lógica para marcas/modelos personalizados
   - Mejorar manejo de fotos en actualización

3. **public/assets/js/marca-modelo-selector.js** (NUEVO)
   - Componente de autocompletado
   - Carga de datos desde JSON
   - Manejo de opciones personalizadas

4. **database/migrations/add_marca_modelo_personalizado.sql** (NUEVO)
   - Agregar campos para marcas/modelos personalizados

---

## 4. Prioridad de Implementación

### Fase 1: Gestión de Fotos (URGENTE)
- Corregir validación en modo edición
- Mostrar fotos existentes correctamente
- Permitir eliminar/agregar fotos

### Fase 2: Marca/Modelo con Autocompletado
- Crear componente de autocompletado
- Integrar JSON de marcas/modelos
- Implementar flujo de aprobación admin

---

## 5. Casos de Uso

### Caso 1: Usuario Edita Publicación con 4 Fotos
1. Ve las 4 fotos existentes
2. Elimina 2 fotos
3. Agrega 1 foto nueva
4. Total: 3 fotos (válido ✓)

### Caso 2: Usuario Ingresa Marca No Listada
1. Escribe "BYD" en marca
2. No aparece en lista
3. Selecciona "Otra marca"
4. Ingresa "BYD"
5. Publicación queda pendiente de aprobación de marca
6. Admin revisa y aprueba

### Caso 3: Usuario Crea Publicación Nueva
1. Selecciona marca "Toyota" de lista
2. Aparecen modelos de Toyota
3. Selecciona "Corolla"
4. Sube 3 fotos
5. Publica normalmente

---

## 6. Mockups de UI

### Selector de Marca (Combobox)
```
┌─────────────────────────────────────┐
│ Marca *                              │
│ ┌─────────────────────────────────┐ │
│ │ Toyota                      [▼] │ │
│ └─────────────────────────────────┘ │
│                                      │
│ Sugerencias:                         │
│ ☑ Toyota                             │
│ ☐ Chevrolet                          │
│ ☐ Nissan                             │
│ ☐ Hyundai                            │
│ ...                                  │
│ ☐ Otra marca (especificar)           │
└─────────────────────────────────────┘
```

### Gestión de Fotos en Edición
```
┌──────────────────────────────────────────────┐
│ Paso 4: Fotos (1 a 6)                        │
│                                               │
│ ℹ Fotos actuales: 4 imagen(es)               │
│                                               │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐         │
│ │  [IMG]  │ │  [IMG]  │ │  [IMG]  │         │
│ │ ★ PRIN  │ │         │ │         │         │
│ │  [🗑️]   │ │  [🗑️]   │ │  [🗑️]   │         │
│ │  [☆]    │ │  [★]    │ │  [★]    │         │
│ └─────────┘ └─────────┘ └─────────┘         │
│                                               │
│ ┌─────────────────────────────────────────┐  │
│ │  [+] Agregar más fotos (máx 6 total)   │  │
│ └─────────────────────────────────────────┘  │
│                                               │
│ Puedes agregar hasta 2 fotos más             │
└──────────────────────────────────────────────┘
```

---

## 7. Notas de Implementación

- Usar biblioteca de autocompletado ligera (ej: Choices.js o nativa con datalist)
- Mantener compatibilidad con modo oscuro
- Asegurar accesibilidad (ARIA labels)
- Optimizar carga del JSON (lazy loading)
- Implementar caché en localStorage para marcas/modelos

---

## 8. Testing

### Casos de Prueba
1. ✓ Crear publicación con marca de lista
2. ✓ Crear publicación con marca personalizada
3. ✓ Editar publicación manteniendo fotos
4. ✓ Editar publicación eliminando fotos
5. ✓ Editar publicación agregando fotos
6. ✓ Validación: intentar guardar sin fotos
7. ✓ Admin aprueba marca personalizada
8. ✓ Admin modifica marca personalizada

---

## Siguiente Paso

¿Deseas que implemente primero la **Fase 1 (Gestión de Fotos)** que es urgente, o prefieres que trabaje en ambas fases simultáneamente?
