# Esquema de Base de Datos - ChileChocados
**Base de datos:** `chilechocados`  
**Motor:** MySQL 8.x  
**Charset:** utf8mb4_unicode_ci  
**Fecha:** 26 de Octubre 2025

---

## Índice de Tablas

1. [usuarios](#1-usuarios) - Gestión de usuarios del sistema
2. [categorias_padre](#2-categorias_padre) - Categorías principales de vehículos
3. [subcategorias](#3-subcategorias) - Subcategorías de vehículos
4. [regiones](#4-regiones) - Regiones de Chile
5. [comunas](#5-comunas) - Comunas de Chile
6. [publicaciones](#6-publicaciones) - Publicaciones de vehículos
7. [publicacion_fotos](#7-publicacion_fotos) - Fotos de publicaciones
8. [mensajes](#8-mensajes) - Sistema de mensajería
9. [favoritos](#9-favoritos) - Favoritos de usuarios
10. [pagos_flow](#10-pagos_flow) - Pagos con Flow
11. [auditoria](#11-auditoria) - Registro de auditoría
12. [configuraciones](#12-configuraciones) - Configuraciones del sistema

---

## 1. usuarios

**Descripción:** Gestión de usuarios del sistema (compradores, vendedores y administradores).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único del usuario |
| `nombre` | VARCHAR(100) | NO | - | Nombre del usuario |
| `apellido` | VARCHAR(100) | NO | - | Apellido del usuario |
| `email` | VARCHAR(150) | NO | - | Email único del usuario |
| `password` | VARCHAR(255) | NO | - | Contraseña hasheada |
| `telefono` | VARCHAR(20) | YES | NULL | Teléfono de contacto |
| `rut` | VARCHAR(12) | YES | NULL | RUT sin puntos, con guión |
| `rol` | ENUM | NO | 'comprador' | admin, vendedor, comprador |
| `estado` | ENUM | NO | 'activo' | activo, suspendido, eliminado |
| `verificado` | TINYINT(1) | NO | 0 | 1 = vendedor verificado |
| `foto_perfil` | VARCHAR(255) | YES | NULL | Ruta de la foto de perfil |
| `redes_sociales` | JSON | YES | NULL | Facebook, Instagram, etc. |
| `token_recuperacion` | VARCHAR(100) | YES | NULL | Token para recuperar contraseña |
| `token_expira` | DATETIME | YES | NULL | Fecha de expiración del token |
| `ultima_conexion` | DATETIME | YES | NULL | Última vez que se conectó |
| `fecha_registro` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de registro |
| `fecha_actualizacion` | DATETIME | YES | ON UPDATE | Última actualización |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `email`
- **INDEX:** `idx_rol` (rol)
- **INDEX:** `idx_estado` (estado)

### Notas

- El campo `redes_sociales` es JSON para almacenar múltiples redes
- El `verificado` indica si es un vendedor confiable
- Estados: activo (puede operar), suspendido (bloqueado temporalmente), eliminado (soft delete)

---

## 2. categorias_padre

**Descripción:** Categorías principales de vehículos (Auto, Moto, Camión, etc.).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la categoría |
| `nombre` | VARCHAR(100) | NO | - | Nombre de la categoría |
| `slug` | VARCHAR(100) | NO | - | Slug único para URLs |
| `icono` | VARCHAR(50) | YES | NULL | Nombre del icono (car, bike, truck) |
| `descripcion` | TEXT | YES | NULL | Descripción de la categoría |
| `activo` | TINYINT(1) | NO | 1 | 1 = activa, 0 = inactiva |
| `orden` | INT | NO | 0 | Orden de visualización |
| `fecha_creacion` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de creación |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `slug`
- **INDEX:** `idx_activo` (activo)

### Datos de ejemplo

- Auto (car)
- Moto (bike)
- Camión (truck)
- Casa Rodante (rv)
- Náutica (boat)
- Bus (bus)
- Maquinaria (gear)
- Aéreos (plane)

---

## 3. subcategorias

**Descripción:** Subcategorías de vehículos (Sedán, SUV, Pickup, etc.).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la subcategoría |
| `categoria_padre_id` | INT UNSIGNED | NO | - | ID de la categoría padre |
| `nombre` | VARCHAR(100) | NO | - | Nombre de la subcategoría |
| `slug` | VARCHAR(100) | NO | - | Slug para URLs |
| `activo` | TINYINT(1) | NO | 1 | 1 = activa, 0 = inactiva |
| `orden` | INT | NO | 0 | Orden de visualización |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `categoria_slug` (categoria_padre_id, slug)
- **INDEX:** `idx_categoria` (categoria_padre_id)
- **FOREIGN KEY:** `fk_subcat_categoria` → categorias_padre(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** categorias_padre
- **Tiene muchas:** publicaciones

---

## 4. regiones

**Descripción:** Regiones de Chile (16 regiones).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la región |
| `nombre` | VARCHAR(100) | NO | - | Nombre de la región |
| `codigo` | VARCHAR(10) | NO | - | Código único (RM, V, VIII, etc.) |
| `orden` | INT | NO | 0 | Orden de visualización |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `codigo`

### Relaciones

- **Tiene muchas:** comunas
- **Tiene muchas:** publicaciones

---

## 5. comunas

**Descripción:** Comunas de Chile (346 comunas).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la comuna |
| `region_id` | INT UNSIGNED | NO | - | ID de la región |
| `nombre` | VARCHAR(100) | NO | - | Nombre de la comuna |

### Índices

- **PRIMARY KEY:** `id`
- **INDEX:** `idx_region` (region_id)
- **FOREIGN KEY:** `fk_comuna_region` → regiones(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** regiones
- **Tiene muchas:** publicaciones

---

## 6. publicaciones

**Descripción:** Publicaciones de vehículos siniestrados o en desarme.

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la publicación |
| `usuario_id` | INT UNSIGNED | NO | - | ID del usuario que publica |
| `categoria_padre_id` | INT UNSIGNED | NO | - | ID de la categoría principal |
| `subcategoria_id` | INT UNSIGNED | YES | NULL | ID de la subcategoría |
| `titulo` | VARCHAR(200) | NO | - | Título de la publicación |
| `tipificacion` | VARCHAR(50) | YES | NULL | chocado o mecanico |
| `marca` | VARCHAR(100) | YES | NULL | Marca del vehículo |
| `modelo` | VARCHAR(100) | YES | NULL | Modelo del vehículo |
| `anio` | YEAR | YES | NULL | Año del vehículo |
| `descripcion` | TEXT | NO | - | Descripción detallada |
| `tipo_venta` | ENUM | NO | 'completo' | completo, desarme |
| `precio` | DECIMAL(12,2) | YES | NULL | Precio (NULL si es desarme) |
| `region_id` | INT UNSIGNED | NO | - | ID de la región |
| `comuna_id` | INT UNSIGNED | YES | NULL | ID de la comuna |
| `estado` | ENUM | NO | 'pendiente' | Ver estados abajo |
| `es_destacada` | TINYINT(1) | NO | 0 | 1 = publicación destacada |
| `fecha_destacada_inicio` | DATETIME | YES | NULL | Inicio del destacado |
| `fecha_destacada_fin` | DATETIME | YES | NULL | Fin del destacado |
| `visitas` | INT UNSIGNED | NO | 0 | Contador de visitas |
| `foto_principal` | VARCHAR(255) | YES | NULL | Ruta de la foto principal |
| `motivo_rechazo` | TEXT | YES | NULL | Razón del rechazo |
| `fecha_publicacion` | DATETIME | YES | NULL | Fecha de aprobación |
| `fecha_venta` | DATETIME | YES | NULL | Fecha de venta |
| `fecha_creacion` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de creación |
| `fecha_actualizacion` | DATETIME | YES | ON UPDATE | Última actualización |

### Estados posibles

- **borrador:** Guardado pero no enviado
- **pendiente:** Enviado, esperando aprobación
- **aprobada:** Aprobada y visible públicamente
- **rechazada:** Rechazada por el admin
- **vendida:** Marcada como vendida
- **archivada:** Archivada por el usuario

### Índices

- **PRIMARY KEY:** `id`
- **INDEX:** `idx_usuario` (usuario_id)
- **INDEX:** `idx_categoria` (categoria_padre_id)
- **INDEX:** `idx_subcategoria` (subcategoria_id)
- **INDEX:** `idx_region` (region_id)
- **INDEX:** `idx_estado` (estado)
- **INDEX:** `idx_destacada` (es_destacada)
- **INDEX:** `idx_fecha_pub` (fecha_publicacion)
- **FOREIGN KEY:** `fk_pub_usuario` → usuarios(id) ON DELETE CASCADE
- **FOREIGN KEY:** `fk_pub_categoria` → categorias_padre(id)
- **FOREIGN KEY:** `fk_pub_subcategoria` → subcategorias(id)
- **FOREIGN KEY:** `fk_pub_region` → regiones(id)

### Relaciones

- **Pertenece a:** usuarios
- **Pertenece a:** categorias_padre
- **Pertenece a:** subcategorias (opcional)
- **Pertenece a:** regiones
- **Pertenece a:** comunas (opcional)
- **Tiene muchas:** publicacion_fotos
- **Tiene muchas:** mensajes
- **Tiene muchas:** favoritos
- **Tiene muchas:** pagos_flow

### Notas importantes

- ⚠️ **NO tiene columna `activo`** - Se usa `estado` para controlar visibilidad
- Solo publicaciones con `estado = 'aprobada'` son visibles públicamente
- Si `tipo_venta = 'desarme'`, el `precio` debe ser NULL
- El campo `tipificacion` indica si el daño es por choque o mecánico

---

## 7. publicacion_fotos

**Descripción:** Fotos asociadas a las publicaciones (hasta 6 fotos por publicación).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la foto |
| `publicacion_id` | INT UNSIGNED | NO | - | ID de la publicación |
| `ruta` | VARCHAR(255) | NO | - | Ruta del archivo |
| `es_principal` | TINYINT(1) | NO | 0 | 1 = foto principal |
| `orden` | INT | NO | 0 | Orden de visualización |
| `fecha_subida` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de subida |

### Índices

- **PRIMARY KEY:** `id`
- **INDEX:** `idx_publicacion` (publicacion_id)
- **FOREIGN KEY:** `fk_foto_publicacion` → publicaciones(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** publicaciones

### Notas

- Solo una foto puede tener `es_principal = 1` por publicación
- Las fotos se guardan en `/uploads/publicaciones/YYYY/MM/`

---

## 8. mensajes

**Descripción:** Sistema de mensajería entre usuarios sobre publicaciones.

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único del mensaje |
| `publicacion_id` | INT UNSIGNED | NO | - | ID de la publicación |
| `remitente_id` | INT UNSIGNED | NO | - | ID del remitente |
| `destinatario_id` | INT UNSIGNED | NO | - | ID del destinatario |
| `mensaje` | TEXT | NO | - | Contenido del mensaje |
| `archivo_adjunto` | VARCHAR(255) | YES | NULL | Ruta del archivo adjunto |
| `leido` | TINYINT(1) | NO | 0 | 1 = mensaje leído |
| `fecha_lectura` | DATETIME | YES | NULL | Fecha de lectura |
| `fecha_envio` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de envío |

### Índices

- **PRIMARY KEY:** `id`
- **INDEX:** `idx_publicacion` (publicacion_id)
- **INDEX:** `idx_remitente` (remitente_id)
- **INDEX:** `idx_destinatario` (destinatario_id)
- **INDEX:** `idx_leido` (leido)
- **FOREIGN KEY:** `fk_mensaje_publicacion` → publicaciones(id) ON DELETE CASCADE
- **FOREIGN KEY:** `fk_mensaje_remitente` → usuarios(id) ON DELETE CASCADE
- **FOREIGN KEY:** `fk_mensaje_destinatario` → usuarios(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** publicaciones
- **Pertenece a:** usuarios (remitente)
- **Pertenece a:** usuarios (destinatario)

---

## 9. favoritos

**Descripción:** Publicaciones marcadas como favoritas por los usuarios.

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único del favorito |
| `usuario_id` | INT UNSIGNED | NO | - | ID del usuario |
| `publicacion_id` | INT UNSIGNED | NO | - | ID de la publicación |
| `fecha_agregado` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de agregado |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `usuario_publicacion` (usuario_id, publicacion_id)
- **INDEX:** `idx_usuario` (usuario_id)
- **INDEX:** `idx_publicacion` (publicacion_id)
- **FOREIGN KEY:** `fk_fav_usuario` → usuarios(id) ON DELETE CASCADE
- **FOREIGN KEY:** `fk_fav_publicacion` → publicaciones(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** usuarios
- **Pertenece a:** publicaciones

### Notas

- Un usuario no puede agregar la misma publicación dos veces (UNIQUE constraint)

---

## 10. pagos_flow

**Descripción:** Registro de pagos realizados con Flow (destacados y banners).

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único del pago |
| `publicacion_id` | INT UNSIGNED | NO | - | ID de la publicación |
| `usuario_id` | INT UNSIGNED | NO | - | ID del usuario |
| `tipo` | ENUM | NO | - | destacado_15, destacado_30, banner |
| `monto` | DECIMAL(10,2) | NO | - | Monto del pago |
| `flow_token` | VARCHAR(255) | YES | NULL | Token de Flow |
| `flow_orden` | VARCHAR(100) | YES | NULL | Número de orden de Flow |
| `estado` | ENUM | NO | 'pendiente' | pendiente, aprobado, rechazado, expirado |
| `respuesta_flow` | JSON | YES | NULL | Respuesta completa de Flow |
| `fecha_pago` | DATETIME | YES | NULL | Fecha del pago |
| `fecha_creacion` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de creación |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `flow_orden`
- **INDEX:** `idx_publicacion` (publicacion_id)
- **INDEX:** `idx_usuario` (usuario_id)
- **INDEX:** `idx_estado` (estado)
- **FOREIGN KEY:** `fk_pago_publicacion` → publicaciones(id) ON DELETE CASCADE
- **FOREIGN KEY:** `fk_pago_usuario` → usuarios(id) ON DELETE CASCADE

### Relaciones

- **Pertenece a:** publicaciones
- **Pertenece a:** usuarios

### Tipos de pago

- **destacado_15:** Publicación destacada por 15 días
- **destacado_30:** Publicación destacada por 30 días
- **banner:** Espacio publicitario banner

---

## 11. auditoria

**Descripción:** Registro de auditoría de todas las acciones importantes del sistema.

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único del registro |
| `usuario_id` | INT UNSIGNED | YES | NULL | ID del usuario (NULL si es sistema) |
| `tabla` | VARCHAR(50) | NO | - | Nombre de la tabla afectada |
| `registro_id` | INT UNSIGNED | NO | - | ID del registro afectado |
| `accion` | ENUM | NO | - | crear, actualizar, eliminar |
| `datos_anteriores` | JSON | YES | NULL | Datos antes del cambio |
| `datos_nuevos` | JSON | YES | NULL | Datos después del cambio |
| `ip` | VARCHAR(45) | YES | NULL | IP del usuario |
| `user_agent` | VARCHAR(255) | YES | NULL | User agent del navegador |
| `fecha` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha de la acción |

### Índices

- **PRIMARY KEY:** `id`
- **INDEX:** `idx_usuario` (usuario_id)
- **INDEX:** `idx_tabla_registro` (tabla, registro_id)
- **INDEX:** `idx_fecha` (fecha)

### Notas

- Los campos JSON permiten almacenar el estado completo antes y después
- Útil para rastrear cambios y recuperar datos

---

## 12. configuraciones

**Descripción:** Configuraciones generales del sistema.

### Estructura

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | ID único de la configuración |
| `clave` | VARCHAR(100) | NO | - | Clave única de la configuración |
| `valor` | TEXT | NO | - | Valor de la configuración |
| `tipo` | ENUM | NO | 'string' | string, int, float, boolean, json |
| `descripcion` | TEXT | YES | NULL | Descripción de la configuración |
| `fecha_actualizacion` | DATETIME | YES | ON UPDATE | Última actualización |

### Índices

- **PRIMARY KEY:** `id`
- **UNIQUE:** `clave`

### Ejemplos de configuraciones

- `site_name`: "ChileChocados"
- `items_per_page`: 20
- `destacado_15_precio`: 15000
- `destacado_30_precio`: 25000
- `max_fotos_publicacion`: 6
- `email_admin`: "admin@chilechocados.cl"

---

## Diagrama de Relaciones

```
usuarios (1) ──────< (N) publicaciones
                          │
                          ├──< (N) publicacion_fotos
                          ├──< (N) mensajes
                          ├──< (N) favoritos
                          └──< (N) pagos_flow

categorias_padre (1) ──< (N) subcategorias
                    └────< (N) publicaciones

regiones (1) ──< (N) comunas
         └──────< (N) publicaciones
```

---

## Notas Importantes

### Sobre el campo `activo` vs `estado`

- ⚠️ **publicaciones NO tiene columna `activo`**
- Se usa el campo `estado` (ENUM) para controlar la visibilidad
- Solo publicaciones con `estado = 'aprobada'` son visibles públicamente

### Sobre los índices

- Todos los campos de relación (FK) tienen índices
- Los campos frecuentemente usados en WHERE tienen índices
- Los campos ENUM usados para filtrar tienen índices

### Sobre CASCADE

- La mayoría de las relaciones usan `ON DELETE CASCADE`
- Esto significa que al eliminar un registro padre, se eliminan los hijos
- Ejemplo: Al eliminar un usuario, se eliminan sus publicaciones, mensajes, etc.

### Sobre JSON

- Se usa JSON para datos flexibles: `redes_sociales`, `respuesta_flow`, `datos_anteriores`, `datos_nuevos`
- Permite almacenar estructuras complejas sin crear tablas adicionales

---

## Estadísticas Actuales

- **Total de tablas:** 12
- **Total de relaciones (FK):** 15
- **Total de índices:** ~40
- **Usuarios registrados:** 2
- **Publicaciones:** 4 (1 aprobada, 3 pendientes)
- **Categorías principales:** 8
- **Subcategorías:** 53
- **Regiones:** 16
- **Comunas:** 346

---

**Documento generado:** 26 de Octubre 2025  
**Versión de MySQL:** 8.x  
**Charset:** utf8mb4_unicode_ci  
**Engine:** InnoDB
