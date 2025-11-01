# Problema con API de Flow

## Error Detectado

Al intentar crear una orden de pago en Flow, la API responde con:

```json
{
    "code": 501,
    "message": "Internal Server Error - apiKey not found"
}
```

**HTTP Status:** 401 Unauthorized

## Causa

Las credenciales proporcionadas no son válidas o no están configuradas correctamente en Flow:

- **API Key:** `4BDAF26D-2D4A-45A5-A5B5-79D5A0DL0A05`
- **Secret Key:** `0d697a08e5fa0cba649451c5b8cbca7c5bd3a736`

## Posibles Soluciones

### 1. Verificar Credenciales en Flow

1. Inicia sesión en tu cuenta de Flow: https://www.flow.cl/app/
2. Ve a **Configuración → API**
3. Verifica que las credenciales sean correctas
4. Asegúrate de que la cuenta esté activa en modo **Sandbox**

### 2. Generar Nuevas Credenciales

Si las credenciales son incorrectas:

1. Ve a https://www.flow.cl/app/
2. Navega a **Configuración → API**
3. Genera nuevas credenciales para Sandbox
4. Actualiza el archivo `.env`:

```env
FLOW_API_KEY=tu_nueva_api_key
FLOW_SECRET_KEY=tu_nuevo_secret_key
FLOW_SANDBOX=true
```

### 3. Activar Cuenta Sandbox

Si la cuenta no está activa:

1. Contacta a soporte de Flow: soporte@flow.cl
2. Solicita activación de cuenta Sandbox
3. Espera confirmación

### 4. Usar Modo de Prueba Local (Temporal)

He creado un modo de prueba local que simula el comportamiento de Flow sin hacer peticiones reales. Esto te permite probar todo el flujo de la aplicación mientras resuelves el problema con Flow.

Ver archivo: `MODO_PRUEBA_LOCAL.md`

## Verificación

Para verificar que las credenciales funcionan, ejecuta:

```bash
php test_flow_api.php
```

O visita:
```
http://chilechocados.local:8080/test_flow_api.php
```

## Logs

Los logs de Flow se encuentran en:
```
logs/php_errors.log
```

Busca líneas que contengan "FLOW API" para ver los detalles de las peticiones.

## Contacto Flow

- **Email:** soporte@flow.cl
- **Teléfono:** +56 2 2570 8000
- **Documentación:** https://developers.flow.cl/
- **Panel:** https://www.flow.cl/app/

## Próximos Pasos

1. ✅ Verificar credenciales en panel de Flow
2. ✅ Generar nuevas credenciales si es necesario
3. ✅ Actualizar `.env` con credenciales correctas
4. ✅ Ejecutar `test_flow_api.php` para verificar
5. ✅ Probar flujo completo de pago

---

**Nota:** Mientras tanto, puedes usar el modo de prueba local para continuar desarrollando y probando la aplicación.
