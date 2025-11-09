# Instrucciones para Migración en Servidor de Producción

## 📋 Resumen
Estos scripts migran tu sistema para soportar marcas y modelos con autocompletado y aprobación de valores personalizados.

## 🔧 Archivos Generados

1. **PRODUCCION_1_crear_tablas.sql** - Crea tablas de marcas, modelos y pendientes
2. **PRODUCCION_2_modificar_publicaciones.sql** - Agrega campos a tabla publicaciones
3. **PRODUCCION_3_migrar_datos_existentes.sql** - Actualiza publicaciones existentes
4. **PRODUCCION_4_importar_marcas.php** - Importa catálogo desde JSON (requiere PHP)
5. **PRODUCCION_5_inserts_marcas_modelos.sql** - INSERT directo de marcas/modelos (alternativa SQL pura)

## 📝 Pasos de Ejecución

### Paso 1: Backup de la Base de Datos
```bash
# Crear backup completo antes de cualquier cambio
mysqldump -u tu_usuario -p tu_base_datos > backup_antes_migracion_$(date +%Y%m%d_%H%M%S).sql
```

### Paso 2: Subir Archivos al Servidor
Sube estos archivos a tu servidor:
- `PRODUCCION_1_crear_tablas.sql`
- `PRODUCCION_2_modificar_publicaciones.sql` (o `PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php`)
- `PRODUCCION_3_migrar_datos_existentes.sql`
- `PRODUCCION_5_inserts_marcas_modelos.sql` (recomendado)
- `PRODUCCION_4_importar_marcas.php` (opcional - si prefieres usar PHP)
- `chileautos_marcas_modelos.json` (solo si usas la opción PHP)

### Paso 3: Ejecutar Scripts SQL

#### Opción A: Desde línea de comandos
```bash
# Script 1: Crear tablas
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_1_crear_tablas.sql

# Script 2: Modificar publicaciones (IMPORTANTE: Ignora errores de columnas duplicadas)
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_2_modificar_publicaciones.sql

# Script 3: Migrar datos existentes
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_3_migrar_datos_existentes.sql
```

#### Opción B: Desde phpMyAdmin
1. Accede a phpMyAdmin
2. Selecciona tu base de datos
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de cada archivo .sql
5. Ejecuta uno por uno en orden
6. **IMPORTANTE:** Si ves errores como "#1060 Duplicate column name", es normal - significa que la columna ya existe

#### Opción C: Script PHP (Más seguro - verifica antes de agregar)
```bash
# Edita credenciales en PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php
php PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php
```
Esta opción verifica si las columnas existen antes de agregarlas, evitando errores.

### Paso 4: Importar Marcas y Modelos

Tienes 2 opciones para importar el catálogo:

#### Opción A: SQL Puro (Recomendado - Más simple)
```bash
# Ejecutar desde línea de comandos
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_5_inserts_marcas_modelos.sql
```

O desde phpMyAdmin:
1. Ve a la pestaña "Importar"
2. Selecciona `PRODUCCION_5_inserts_marcas_modelos.sql`
3. Ejecuta

**Ventajas:** No requiere PHP, más rápido, más compatible

#### Opción B: Script PHP
1. Edita `PRODUCCION_4_importar_marcas.php`:
```php
// Líneas 9-12: Ajusta estos valores
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos');  // ← Cambiar
define('DB_USER', 'tu_usuario');         // ← Cambiar
define('DB_PASS', 'tu_password');        // ← Cambiar
```

2. Ejecuta:
```bash
php PRODUCCION_4_importar_marcas.php
```

**Ventajas:** Muestra progreso detallado, mejor manejo de errores

### Paso 5: Verificar Resultados

Ejecuta estas consultas para verificar:

```sql
-- Verificar tablas creadas
SHOW TABLES LIKE 'marcas%';
SHOW TABLES LIKE 'modelos';

-- Verificar datos importados
SELECT COUNT(*) FROM marcas;
SELECT COUNT(*) FROM modelos;

-- Verificar campos en publicaciones
SHOW COLUMNS FROM publicaciones LIKE '%marca%';
SHOW COLUMNS FROM publicaciones LIKE '%modelo%';

-- Ver ejemplos de marcas
SELECT m.nombre, COUNT(mo.id) as total_modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
GROUP BY m.id
ORDER BY total_modelos DESC
LIMIT 10;
```

## ✅ Resultados Esperados

Después de ejecutar todos los scripts:

- ✅ 3 nuevas tablas: `marcas`, `modelos`, `marcas_modelos_pendientes`
- ✅ 5 nuevos campos en `publicaciones`: 
  - `marca_personalizada`
  - `modelo_personalizado`
  - `marca_original`
  - `modelo_original`
  - `marca_modelo_aprobado`
- ✅ ~100+ marcas importadas
- ✅ ~2000+ modelos importados
- ✅ Todas las publicaciones existentes marcadas como aprobadas

## 🚨 Solución de Problemas

### Error: "Column already exists"
**Solución:** Es normal si ejecutas los scripts más de una vez. Los scripts usan `IF NOT EXISTS` para evitar errores.

### Error: "Cannot add foreign key constraint"
**Solución:** Verifica que la tabla `publicaciones` y `usuarios` existan antes de ejecutar el Script 1.

### Error en Script PHP: "File not found"
**Solución:** Asegúrate de que `chileautos_marcas_modelos.json` esté en la ubicación correcta (raíz del proyecto).

### Error: "Access denied"
**Solución:** Verifica las credenciales de base de datos en `PRODUCCION_4_importar_marcas.php`.

## 🔄 Rollback (Si algo sale mal)

Si necesitas revertir los cambios:

```bash
# Restaurar desde backup
mysql -u tu_usuario -p tu_base_datos < backup_antes_migracion_YYYYMMDD_HHMMSS.sql
```

## 📞 Soporte

Si encuentras problemas:
1. Revisa los logs de MySQL
2. Verifica que todas las tablas necesarias existan
3. Confirma que tienes permisos suficientes en la base de datos
4. Revisa que el archivo JSON esté en la ubicación correcta

## 🎯 Próximos Pasos

Una vez completada la migración:
1. Sube los archivos PHP actualizados del sistema
2. Limpia la caché de OPcache si está activo
3. Prueba crear una nueva publicación
4. Verifica el autocompletado de marcas/modelos
5. Prueba ingresar una marca/modelo personalizado
