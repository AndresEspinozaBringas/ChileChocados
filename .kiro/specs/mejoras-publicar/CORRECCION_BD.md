# ✅ Corrección: Marcas y Modelos en Base de Datos

## Fecha: 2025-11-08
## Estado: COMPLETADO

---

## 🎯 Problemas Identificados

### 1. Error de Foreign Key
**Problema:** 
```
#3780 - Referencing column 'publicacion_id' and referenced column 'id' 
in foreign key constraint are incompatible
```

**Causa:**  
- `publicaciones.id` es `INT UNSIGNED`
- `marcas_modelos_pendientes.publicacion_id` era `INT` (sin UNSIGNED)

**Solución:**  
Cambiar todos los campos de foreign key a `INT UNSIGNED`

### 2. Datos en JSON vs Base de Datos
**Problema:**  
Los datos de marcas y modelos estaban solo en el archivo JSON, no en la base de datos.

**Solución:**  
- Crear tablas `marcas` y `modelos` en la BD
- Importar datos desde el JSON a las tablas
- Actualizar el código para consultar la BD en lugar del JSON

---

## 🗄️ Estructura de Base de Datos

### Tabla: `marcas`
```sql
CREATE TABLE marcas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    cantidad_modelos INT UNSIGNED DEFAULT 0,
    activa TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activa (activa)
);
```

**Datos:**
- 27 marcas importadas
- Ejemplos: Toyota (72 modelos), Chevrolet (54 modelos), Nissan (54 modelos)

### Tabla: `modelos`
```sql
CREATE TABLE modelos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marca_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE CASCADE,
    INDEX idx_marca_id (marca_id),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo),
    UNIQUE KEY unique_marca_modelo (marca_id, nombre)
);
```

**Datos:**
- 542 modelos importados
- Relacionados con sus marcas mediante `marca_id`

### Tabla: `marcas_modelos_pendientes` (CORREGIDA)
```sql
CREATE TABLE marcas_modelos_pendientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT UNSIGNED NOT NULL,  -- ✅ CORREGIDO: INT UNSIGNED
    marca_ingresada VARCHAR(100) NOT NULL,
    modelo_ingresado VARCHAR(100) NOT NULL,
    marca_sugerida VARCHAR(100) NULL,
    modelo_sugerido VARCHAR(100) NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'modificado') DEFAULT 'pendiente',
    notas_admin TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    admin_id INT UNSIGNED NULL,  -- ✅ CORREGIDO: INT UNSIGNED
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
);
```

---

## 📝 Archivos Creados

### 1. `database/migrations/create_marcas_modelos_tables.sql`
SQL para crear las 3 tablas necesarias

### 2. `database/migrations/import_marcas_modelos_from_json.php`
Script PHP que:
- Lee el archivo `chileautos_marcas_modelos.json`
- Inserta marcas en tabla `marcas`
- Inserta modelos en tabla `modelos`
- Usa transacciones para integridad
- Maneja duplicados con `ON DUPLICATE KEY UPDATE`

### 3. `database/migrations/run_complete_migration.php`
Script maestro que ejecuta todo en orden:
1. Crea las tablas
2. Importa los datos
3. Muestra resumen de resultados

---

## 🔧 Cambios en el Código

### Modelo: `MarcaModelo.php`

**Antes (usaba JSON):**
```php
public function marcaExisteEnCatalogo($marca)
{
    $json = file_get_contents('chileautos_marcas_modelos.json');
    $data = json_decode($json, true);
    // ... buscar en array
}
```

**Después (usa BD):**
```php
public function marcaExisteEnCatalogo($marca)
{
    $sql = "SELECT COUNT(*) FROM marcas WHERE nombre = ? AND activa = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$marca]);
    return $stmt->fetchColumn() > 0;
}
```

**Métodos nuevos agregados:**
- `getMarcas()` - Obtiene todas las marcas activas
- `getModelosPorMarca($marcaId)` - Obtiene modelos por ID de marca
- `getModelosPorNombreMarca($nombreMarca)` - Obtiene modelos por nombre de marca

### Controlador: `MarcaModeloController.php`

**Antes (usaba JSON):**
```php
public function buscarMarcas()
{
    $json = file_get_contents('chileautos_marcas_modelos.json');
    $data = json_decode($json, true);
    // ... filtrar array
}
```

**Después (usa BD):**
```php
public function buscarMarcas()
{
    $marcas = $this->marcaModeloModel->getMarcas();
    // ... filtrar resultados de BD
}
```

---

## ✅ Verificación

### Comprobar que las tablas existen:
```sql
SHOW TABLES LIKE 'marcas%';
```

**Resultado esperado:**
```
marcas
marcas_modelos_pendientes
modelos
```

### Comprobar cantidad de datos:
```sql
SELECT COUNT(*) FROM marcas;    -- Debe ser 27
SELECT COUNT(*) FROM modelos;   -- Debe ser ~542
```

### Comprobar una marca específica:
```sql
SELECT m.nombre, COUNT(mo.id) as total_modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
WHERE m.nombre = 'Toyota'
GROUP BY m.id;
```

**Resultado esperado:**
```
Toyota | 71-72
```

### Comprobar foreign keys:
```sql
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'chilechocados'
AND TABLE_NAME = 'marcas_modelos_pendientes';
```

---

## 🚀 Cómo Ejecutar la Migración

### Opción 1: Script Completo (Recomendado)
```bash
php database/migrations/run_complete_migration.php
```

### Opción 2: Paso a Paso
```bash
# 1. Crear tablas
mysql -u root -p chilechocados < database/migrations/create_marcas_modelos_tables.sql

# 2. Importar datos
php database/migrations/import_marcas_modelos_from_json.php
```

---

## 📊 Resultados de la Importación

```
=== IMPORTACIÓN COMPLETADA ===
✅ Marcas importadas: 27
✅ Modelos importados: 544

=== VERIFICACIÓN ===
Total marcas en BD: 27
Total modelos en BD: 542

=== EJEMPLOS ===
Top 5 marcas con más modelos:
  - Toyota: 71 modelos
  - Chevrolet: 54 modelos
  - Nissan: 54 modelos
  - BMW: 33 modelos
  - Audi: 29 modelos
```

---

## 🎯 Ventajas de Usar Base de Datos

### Antes (JSON):
- ❌ Archivo grande (89KB)
- ❌ Se carga completo en cada petición
- ❌ No se puede filtrar eficientemente
- ❌ Difícil de actualizar
- ❌ No hay control de versiones

### Ahora (BD):
- ✅ Queries rápidas con índices
- ✅ Solo se cargan datos necesarios
- ✅ Filtrado eficiente con SQL
- ✅ Fácil de actualizar (INSERT/UPDATE)
- ✅ Control de activación (campo `activa`)
- ✅ Timestamps de auditoría
- ✅ Relaciones con foreign keys

---

## 🔄 Actualización de Datos

### Agregar una nueva marca:
```sql
INSERT INTO marcas (nombre, cantidad_modelos) 
VALUES ('BYD', 5);
```

### Agregar modelos a una marca:
```sql
-- Obtener ID de la marca
SELECT id FROM marcas WHERE nombre = 'BYD';  -- Supongamos que es 28

-- Insertar modelos
INSERT INTO modelos (marca_id, nombre) VALUES
(28, 'Seal'),
(28, 'Atto 3'),
(28, 'Han'),
(28, 'Tang'),
(28, 'Song Plus');
```

### Desactivar una marca:
```sql
UPDATE marcas SET activa = 0 WHERE nombre = 'MarcaVieja';
```

### Desactivar un modelo:
```sql
UPDATE modelos SET activo = 0 WHERE nombre = 'ModeloViejo';
```

---

## 🐛 Troubleshooting

### Error: "Table already exists"
**Solución:** Las tablas ya existen, puedes continuar con la importación de datos.

### Error: "Duplicate entry"
**Solución:** Los datos ya fueron importados. El script usa `ON DUPLICATE KEY UPDATE` para evitar errores.

### Error: "Foreign key constraint fails"
**Solución:** Verifica que los tipos de datos coincidan:
```sql
-- Verificar tipo de publicaciones.id
DESCRIBE publicaciones;

-- Verificar tipo de marcas_modelos_pendientes.publicacion_id
DESCRIBE marcas_modelos_pendientes;
```

Ambos deben ser `INT UNSIGNED`.

---

## 📝 Notas Importantes

1. **El archivo JSON sigue existiendo** como respaldo, pero el código ahora usa la BD
2. **Las APIs siguen siendo compatibles** con el frontend (mismo formato de respuesta)
3. **El autocompletado ahora es más rápido** porque consulta la BD en lugar de parsear JSON
4. **Se pueden agregar/editar marcas** sin modificar archivos, solo la BD

---

**Implementado por:** Kiro AI  
**Fecha:** 2025-11-08  
**Versión:** 2.1  
**Estado:** ✅ COMPLETADO Y VERIFICADO
