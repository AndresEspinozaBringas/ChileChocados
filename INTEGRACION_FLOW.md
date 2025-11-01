# Integración con Flow - Sistema de Pagos

## Descripción General

Este documento describe la integración del sistema de pagos con Flow para publicaciones destacadas en ChileChocados.

## Credenciales Flow

- **API Key:** `4BDAF26D-2D4A-45A5-A5B5-79D5A0DL0A05`
- **Secret Key:** `0d697a08e5fa0cba649451c5b8cbca7c5bd3a736`
- **Modo:** Sandbox (desarrollo)
- **Documentación:** https://developers.flow.cl/

## Flujo de Pago

### 1. Usuario selecciona publicación destacada

Cuando el usuario crea o edita una publicación y selecciona una opción destacada:
- **Destacada 15 días:** $15.000
- **Destacada 30 días:** $25.000

### 2. Proceso de pago

```
Usuario selecciona destacada
    ↓
Guarda publicación (estado: pendiente)
    ↓
Redirige a /pago/preparar (pantalla de confirmación)
    ↓
Usuario confirma → POST /pago/iniciar
    ↓
Se crea registro en tabla pagos_flow
    ↓
Se genera orden en Flow API
    ↓
Redirige a Flow para pagar
    ↓
Usuario paga en Flow
    ↓
Flow envía callback a /pago/confirmar
    ↓
Se actualiza estado del pago
    ↓
Si aprobado: se activa el destacado
    ↓
Redirige a /pago/retorno (resultado)
```

### 3. Estados del pago

- **pendiente:** Pago iniciado pero no confirmado
- **aprobado:** Pago exitoso, destacado activado
- **rechazado:** Pago rechazado por Flow
- **expirado:** Pago no completado en tiempo límite

## Archivos Creados

### Controladores

**app/controllers/PagoController.php**
- `preparar()` - Pantalla de confirmación antes de pagar
- `iniciar()` - Inicia el proceso de pago con Flow
- `confirmar()` - Callback de Flow (webhook)
- `retorno()` - Página de resultado después del pago
- `reintentar()` - Permite reintentar un pago rechazado

### Helpers

**app/helpers/FlowHelper.php**
- `crearOrden()` - Crea una orden de pago en Flow
- `obtenerEstadoOrden()` - Consulta el estado de una orden
- `validarFirma()` - Valida la firma de Flow (seguridad)
- `generarFirma()` - Genera firma para peticiones
- `obtenerUrlPago()` - Obtiene URL de pago de Flow
- `obtenerPrecioDestacado()` - Retorna precio según tipo
- `obtenerDiasDestacado()` - Retorna días según tipo

### Vistas

**app/views/pages/pagos/preparar.php**
- Pantalla de confirmación antes de ir a Flow
- Muestra resumen de la publicación y monto a pagar
- Botón para confirmar e ir a Flow

**app/views/pages/pagos/retorno.php**
- Pantalla de resultado después del pago
- Tres estados posibles:
  - ✅ Pago exitoso
  - ❌ Pago rechazado (con opción de reintentar)
  - ⏳ Pago pendiente

## Rutas Agregadas

```php
GET  /pago/preparar      → Pantalla de confirmación
POST /pago/iniciar       → Iniciar pago con Flow
POST /pago/confirmar     → Callback de Flow (webhook)
GET  /pago/retorno       → Resultado del pago
POST /pago/reintentar    → Reintentar pago rechazado
```

## Modificaciones en Archivos Existentes

### app/controllers/PublicacionController.php

**Método `store()` modificado:**
```php
// Si es destacada y no es borrador, redirigir a pago
if ($es_destacada && !$es_borrador) {
    $_SESSION['publicacion_pendiente_pago'] = [
        'publicacion_id' => $publicacion_id,
        'tipo_destacado' => $promocion
    ];
    header('Location: ' . BASE_URL . '/pago/preparar');
}
```

**Método `update()` modificado:**
```php
// Si cambió de borrador a pendiente y es destacada, redirigir a pago
if ($cambio_de_borrador_a_pendiente) {
    if ($es_destacada) {
        $_SESSION['publicacion_pendiente_pago'] = [
            'publicacion_id' => $id,
            'tipo_destacado' => $promocion
        ];
        header('Location: ' . BASE_URL . '/pago/preparar');
    }
}
```

### public/index.php

Agregadas rutas de pago en el sistema de routing.

### .env

Agregadas credenciales de Flow:
```env
FLOW_API_KEY=4BDAF26D-2D4A-45A5-A5B5-79D5A0DL0A05
FLOW_SECRET_KEY=0d697a08e5fa0cba649451c5b8cbca7c5bd3a736
FLOW_SANDBOX=true
```

## Tabla pagos_flow

La tabla ya existe en la base de datos con la siguiente estructura:

```sql
CREATE TABLE pagos_flow (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    tipo ENUM('destacada15', 'destacada30', 'banner') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    flow_token VARCHAR(255),
    flow_orden VARCHAR(100),
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'expirado') NOT NULL DEFAULT 'pendiente',
    respuesta_flow JSON,
    fecha_pago DATETIME,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

## Comportamiento del Sistema

### Guardar como Borrador
- ✅ NO redirige a pago
- ✅ Guarda la publicación con estado "borrador"
- ✅ Usuario puede editarla después

### Enviar a Revisión (Normal)
- ✅ NO redirige a pago
- ✅ Guarda con estado "pendiente"
- ✅ Espera aprobación del admin

### Enviar a Revisión (Destacada)
- ✅ Redirige a pantalla de pago
- ✅ Usuario debe pagar antes de que se envíe a revisión
- ✅ Una vez pagado, se activa el destacado automáticamente
- ✅ El destacado se activa por el número de días pagados

## Seguridad

### Validación de Firma
Flow envía una firma en cada callback que debe ser validada:

```php
$firma = $_POST['s'] ?? $_GET['s'] ?? null;
if (!$this->flowHelper->validarFirma($response, $firma)) {
    error_log('Pago Flow: Firma inválida');
    http_response_code(400);
    exit;
}
```

### CSRF Token
Todos los formularios de pago incluyen token CSRF para prevenir ataques.

### Verificación de Propiedad
Se verifica que el usuario sea dueño de la publicación antes de procesar el pago.

## Testing

### Modo Sandbox
El sistema está configurado en modo sandbox para pruebas:
- No se realizan cargos reales
- Se puede probar todo el flujo de pago
- Flow proporciona tarjetas de prueba

### Tarjetas de Prueba Flow
Consultar documentación de Flow para tarjetas de prueba en sandbox.

## Producción

Para pasar a producción:

1. Cambiar en `.env`:
```env
FLOW_SANDBOX=false
FLOW_API_KEY=tu_api_key_produccion
FLOW_SECRET_KEY=tu_secret_key_produccion
```

2. Actualizar URLs de callback en Flow:
```
URL Confirmación: https://chilechocados.cl/pago/confirmar
URL Retorno: https://chilechocados.cl/pago/retorno
```

3. Verificar que el servidor pueda recibir callbacks de Flow (no bloqueado por firewall)

## Logs

Los logs de Flow se guardan en:
- `logs/php_errors.log` - Errores generales
- Logs de Flow API en cada petición

## Soporte

- Documentación Flow: https://developers.flow.cl/
- Soporte Flow: soporte@flow.cl
- Panel Flow: https://www.flow.cl/app/

## Notas Importantes

1. **Callback de Flow:** El endpoint `/pago/confirmar` debe ser accesible públicamente para que Flow pueda enviar la confirmación del pago.

2. **Timeout:** Flow tiene un timeout de 30 minutos para completar el pago. Después de ese tiempo, el pago expira.

3. **Reintentos:** Si un pago es rechazado, el usuario puede reintentarlo desde la pantalla de resultado.

4. **Estado de Publicación:** La publicación se guarda con estado "pendiente" independientemente del pago. El destacado se activa solo si el pago es aprobado.

5. **Activación del Destacado:** El destacado se activa automáticamente cuando Flow confirma el pago exitoso, sin necesidad de intervención del admin.

## Próximas Mejoras

- [ ] Notificaciones por email cuando se aprueba/rechaza un pago
- [ ] Panel de administración para ver todos los pagos
- [ ] Reportes de ingresos por pagos
- [ ] Renovación automática de destacados
- [ ] Descuentos y cupones
- [ ] Pagos recurrentes para banners

---

**Fecha de implementación:** 1 de Noviembre 2025  
**Versión:** 1.0  
**Estado:** Implementado y funcional
