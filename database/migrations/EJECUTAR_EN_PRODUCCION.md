# 🚀 Guía Rápida - Ejecutar en Producción

## ⚡ Comandos Rápidos (Copiar y Pegar)

### 1️⃣ Hacer Backup
```bash
mysqldump -u tu_usuario -p tu_base_datos > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2️⃣ Ejecutar Migraciones SQL
```bash
# Script 1: Crear tablas
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_1_crear_tablas.sql

# Script 2: Modificar publicaciones
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_2_modificar_publicaciones.sql

# Script 3: Migrar datos existentes
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_3_migrar_datos_existentes.sql

# Script 5: Importar marcas y modelos
mysql -u tu_usuario -p tu_base_datos < PRODUCCION_5_inserts_marcas_modelos.sql
```

### 3️⃣ Verificar Resultados
```bash
mysql -u tu_usuario -p tu_base_datos -e "
SELECT 'Marcas:' as tabla, COUNT(*) as total FROM marcas
UNION ALL
SELECT 'Modelos:', COUNT(*) FROM modelos
UNION ALL
SELECT 'Publicaciones actualizadas:', COUNT(*) FROM publicaciones WHERE marca_modelo_aprobado = 1;
"
```

---

## 📋 Alternativa: Desde phpMyAdmin

### Paso 1: Crear Tablas
1. Abre phpMyAdmin
2. Selecciona tu base de datos
3. Ve a "SQL"
4. Copia y pega el contenido de `PRODUCCION_1_crear_tablas.sql`
5. Ejecuta

### Paso 2: Modificar Publicaciones
**Opción A - SQL (puede dar errores de columnas duplicadas, ignóralos):**
- Copia y pega `PRODUCCION_2_modificar_publicaciones.sql`

**Opción B - PHP (más seguro):**
```bash
# Edita credenciales en el archivo primero
php PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php
```

### Paso 3: Migrar Datos
- Copia y pega `PRODUCCION_3_migrar_datos_existentes.sql`

### Paso 4: Importar Marcas/Modelos
1. Ve a "Importar"
2. Selecciona `PRODUCCION_5_inserts_marcas_modelos.sql`
3. Ejecuta (puede tardar 10-30 segundos)

---

## ✅ Resultados Esperados

Después de ejecutar todos los scripts:

```
✓ Tabla 'marcas' creada con 27 marcas
✓ Tabla 'modelos' creada con 544 modelos
✓ Tabla 'marcas_modelos_pendientes' creada
✓ Tabla 'publicaciones' con 5 campos nuevos
✓ Todas las publicaciones existentes marcadas como aprobadas
```

---

## 🔍 Verificación Rápida

Ejecuta estas consultas para verificar:

```sql
-- Ver total de marcas
SELECT COUNT(*) FROM marcas;
-- Resultado esperado: 27

-- Ver total de modelos
SELECT COUNT(*) FROM modelos;
-- Resultado esperado: 544

-- Ver top 5 marcas
SELECT m.nombre, COUNT(mo.id) as modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
GROUP BY m.id
ORDER BY modelos DESC
LIMIT 5;

-- Ver campos nuevos en publicaciones
SHOW COLUMNS FROM publicaciones LIKE '%marca%';
SHOW COLUMNS FROM publicaciones LIKE '%modelo%';

-- Ver publicaciones actualizadas
SELECT 
    COUNT(*) as total,
    SUM(marca_modelo_aprobado = 1) as aprobadas
FROM publicaciones;
```

---

## 🚨 Problemas Comunes

### Error: "Duplicate column name"
**Solución:** Es normal si ejecutas el Script 2 más de una vez. Ignora el error o usa la versión PHP alternativa.

### Error: "Cannot add foreign key constraint"
**Solución:** Asegúrate de ejecutar el Script 1 primero para crear las tablas.

### Error: "Access denied"
**Solución:** Verifica usuario y contraseña de MySQL.

### Importación muy lenta
**Solución:** Normal para 544 modelos. Puede tardar 10-30 segundos.

---

## 🔄 Rollback (Si algo sale mal)

```bash
# Restaurar desde backup
mysql -u tu_usuario -p tu_base_datos < backup_YYYYMMDD_HHMMSS.sql
```

---

## 📞 Orden de Ejecución (Resumen)

1. ✅ Backup
2. ✅ PRODUCCION_1_crear_tablas.sql
3. ✅ PRODUCCION_2_modificar_publicaciones.sql
4. ✅ PRODUCCION_3_migrar_datos_existentes.sql
5. ✅ PRODUCCION_5_inserts_marcas_modelos.sql
6. ✅ Verificar resultados

**Tiempo estimado:** 2-5 minutos

---

## 🎯 Después de la Migración

1. Sube los archivos PHP actualizados del sistema
2. Limpia caché si usas OPcache:
   ```bash
   # Si tienes acceso SSH
   php -r "opcache_reset();"
   ```
3. Prueba crear una nueva publicación
4. Verifica el autocompletado de marcas/modelos
