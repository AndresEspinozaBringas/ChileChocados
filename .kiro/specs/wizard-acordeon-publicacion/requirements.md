# Documento de Requisitos

## Introducción

Esta funcionalidad transforma el formulario de publicación de un diseño vertical de página única a una interfaz wizard progresiva usando acordeones expandibles. El objetivo es mejorar la experiencia del usuario guiándolo a través de un proceso paso a paso, reduciendo la carga cognitiva y proporcionando mejor retroalimentación de validación en cada etapa.

## Glosario

- **Formulario de Publicación**: El formulario web donde los usuarios crean o editan listados de vehículos
- **Wizard**: Una interfaz de múltiples pasos que guía a los usuarios a través de un proceso secuencialmente
- **Acordeón**: Un componente de UI colapsable que muestra/oculta contenido
- **Paso**: Una de las cinco secciones en el proceso de publicación (Tipificación, Tipo de venta, Datos del vehículo, Fotos, Promoción)
- **Barra de Progreso**: Indicador visual que muestra el estado de completitud a través de todos los pasos
- **Resumen de Paso**: Vista condensada de los datos del paso completado mostrada en el encabezado del acordeón colapsado
- **Validación**: Proceso de verificar campos requeridos antes de permitir la progresión
- **Sistema**: El formulario de publicación con interfaz wizard

## Requisitos

### Requisito 1: Navegación Progresiva por Pasos

**Historia de Usuario:** Como usuario publicando un vehículo, quiero completar el formulario un paso a la vez, para poder enfocarme en cada sección sin sentirme abrumado.

#### Criterios de Aceptación

1. CUANDO el Formulario de Publicación carga, EL Sistema DEBERÁ mostrar solo el Paso 1 (Tipificación) en estado expandido
2. CUANDO el Formulario de Publicación carga, EL Sistema DEBERÁ mostrar los Pasos 2-5 en estado colapsado con interacción deshabilitada
3. CUANDO un usuario completa el Paso 1 y hace clic en "Continuar", EL Sistema DEBERÁ validar los campos del Paso 1
4. SI la validación del Paso 1 pasa, ENTONCES EL Sistema DEBERÁ colapsar el Paso 1, expandir el Paso 2, y hacer scroll al Paso 2
5. CUANDO un usuario hace clic en el encabezado de un paso previamente completado, EL Sistema DEBERÁ expandir ese paso y permitir edición

### Requisito 2: Indicación Visual de Progreso

**Historia de Usuario:** Como usuario, quiero ver mi progreso a través del proceso de publicación, para saber cuánto falta por completar.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ mostrar una barra de progreso en la parte superior del formulario mostrando el número de paso actual y total de pasos
2. CUANDO un paso está pendiente, EL Sistema DEBERÁ mostrar un ícono de pendiente (⏳) en el encabezado del paso
3. CUANDO un paso está activo, EL Sistema DEBERÁ mostrar un ícono activo (✏️) en el encabezado del paso
4. CUANDO un paso está completado, EL Sistema DEBERÁ mostrar un ícono de checkmark (✅) en el encabezado del paso
5. EL Sistema DEBERÁ actualizar el porcentaje de la barra de progreso conforme los pasos se completan

### Requisito 3: Visualización de Resumen de Paso

**Historia de Usuario:** Como usuario, quiero ver un resumen de lo que ingresé en los pasos completados, para poder revisar rápidamente mi información sin expandir cada sección.

#### Criterios de Aceptación

1. CUANDO un paso está colapsado y completado, EL Sistema DEBERÁ mostrar un resumen de datos clave en el encabezado del acordeón
2. DONDE el Paso 1 está completado, EL Sistema DEBERÁ mostrar el valor de tipificación seleccionado en el encabezado
3. DONDE el Paso 2 está completado, EL Sistema DEBERÁ mostrar el tipo de venta seleccionado en el encabezado
4. DONDE el Paso 3 está completado, EL Sistema DEBERÁ mostrar marca, modelo y año en el encabezado
5. DONDE el Paso 4 está completado, EL Sistema DEBERÁ mostrar el número de fotos subidas en el encabezado
6. DONDE el Paso 5 está completado, EL Sistema DEBERÁ mostrar el tipo de promoción seleccionado en el encabezado

### Requisito 4: Validación de Pasos

**Historia de Usuario:** Como usuario, quiero recibir retroalimentación inmediata cuando intento proceder con información incompleta, para poder corregir errores antes de avanzar.

#### Criterios de Aceptación

1. CUANDO un usuario hace clic en "Continuar" en cualquier paso, EL Sistema DEBERÁ validar todos los campos requeridos para ese paso
2. SI la validación falla, ENTONCES EL Sistema DEBERÁ mostrar un mensaje de error listando los campos faltantes o inválidos
3. SI la validación falla, ENTONCES EL Sistema DEBERÁ prevenir la progresión al siguiente paso
4. EL Sistema DEBERÁ resaltar campos inválidos con borde rojo y texto de error
5. CUANDO un usuario corrige un campo inválido, EL Sistema DEBERÁ remover el estilo de error inmediatamente

### Requisito 5: Controles de Navegación

**Historia de Usuario:** Como usuario, quiero navegar entre pasos fácilmente, para poder avanzar o retroceder para editar información previa.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ mostrar un botón "Continuar" al final de cada paso (Pasos 1-4)
2. EL Sistema DEBERÁ mostrar botones "Anterior" y "Continuar" para los Pasos 2-4
3. EL Sistema DEBERÁ mostrar botones "Anterior" y de envío final para el Paso 5
4. CUANDO un usuario hace clic en "Anterior", EL Sistema DEBERÁ colapsar el paso actual y expandir el paso anterior sin validación
5. CUANDO un usuario hace clic en "Continuar", EL Sistema DEBERÁ validar el paso actual antes de proceder

### Requisito 6: Comportamiento Responsive del Acordeón

**Historia de Usuario:** Como usuario móvil, quiero que la interfaz de acordeón funcione suavemente en mi dispositivo, para poder publicar vehículos desde mi teléfono.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ mostrar acordeones en diseño de ancho completo en dispositivos móviles (< 768px)
2. EL Sistema DEBERÁ usar transiciones CSS suaves para animaciones de expandir/colapsar (duración 300ms)
3. CUANDO un acordeón se expande, EL Sistema DEBERÁ hacer scroll del viewport para mostrar el encabezado del paso en la parte superior
4. EL Sistema DEBERÁ mantener tamaños de botón amigables al tacto (altura mínima 44px) en móvil
5. EL Sistema DEBERÁ apilar botones de navegación verticalmente en dispositivos móviles

### Requisito 7: Compatibilidad con Modo Edición

**Historia de Usuario:** Como usuario editando una publicación existente, quiero que todos los pasos sean accesibles inmediatamente, para poder actualizar rápidamente información específica.

#### Criterios de Aceptación

1. DONDE el formulario está en modo edición, EL Sistema DEBERÁ marcar todos los pasos como completados inicialmente
2. DONDE el formulario está en modo edición, EL Sistema DEBERÁ mostrar resúmenes para todos los pasos basados en datos existentes
3. DONDE el formulario está en modo edición, EL Sistema DEBERÁ permitir a los usuarios expandir cualquier paso sin restricciones
4. DONDE el formulario está en modo edición, EL Sistema DEBERÁ pre-poblar todos los campos con datos de publicación existentes
5. DONDE el formulario está en modo edición, EL Sistema DEBERÁ mantener validación cuando el usuario modifica cualquier paso

### Requisito 8: Soporte para Modo Oscuro

**Historia de Usuario:** Como usuario con modo oscuro habilitado, quiero que el wizard de acordeón se muestre correctamente en tema oscuro, para que mis ojos no se cansen.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ aplicar colores de tema oscuro a los encabezados de acordeón en modo oscuro
2. EL Sistema DEBERÁ aplicar colores de tema oscuro a las áreas de contenido del acordeón en modo oscuro
3. EL Sistema DEBERÁ asegurar que la barra de progreso sea visible en modo oscuro con contraste apropiado
4. EL Sistema DEBERÁ aplicar colores de tema oscuro a los íconos de estado de paso en modo oscuro
5. EL Sistema DEBERÁ mantener todos los estados hover y activo con colores compatibles con modo oscuro

### Requisito 9: Persistencia de Datos

**Historia de Usuario:** Como usuario, quiero que mis datos ingresados persistan cuando navego entre pasos, para no perder mi trabajo.

#### Criterios de Aceptación

1. CUANDO un usuario navega a un paso diferente, EL Sistema DEBERÁ retener todos los datos ingresados en pasos previos
2. CUANDO un usuario regresa a un paso previo, EL Sistema DEBERÁ mostrar todos los valores previamente ingresados
3. CUANDO un usuario hace clic en "Guardar borrador", EL Sistema DEBERÁ guardar todos los datos de todos los pasos sin importar el estado de completitud
4. EL Sistema DEBERÁ mantener las selecciones de archivos cuando se navega entre pasos
5. EL Sistema DEBERÁ preservar estados dinámicos de campos (ej: visibilidad del campo precio basado en tipificación)

### Requisito 10: Accesibilidad

**Historia de Usuario:** Como usuario con necesidades de accesibilidad, quiero navegar el wizard usando teclado y lectores de pantalla, para poder publicar vehículos independientemente.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ permitir navegación por teclado entre encabezados de acordeón usando la tecla Tab
2. EL Sistema DEBERÁ permitir expandir/colapsar acordeones usando las teclas Enter o Espacio
3. EL Sistema DEBERÁ anunciar cambios de estado de paso a lectores de pantalla usando atributos ARIA
4. EL Sistema DEBERÁ mantener orden lógico de foco a través de todos los elementos interactivos
5. EL Sistema DEBERÁ proporcionar etiquetas ARIA para todos los íconos e indicadores de estado
