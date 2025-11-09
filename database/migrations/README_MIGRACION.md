# 📦 Scripts de Migración - Marcas y Modelos

## 🎯 Resumen

Esta carpeta contiene todos los scripts necesarios para migrar tu sistema y agregar soporte para marcas/modelos con autocompletado.

---

## 📁 Archivos Disponibles

### Scripts SQL (Ejecutar en orden)
1. **PRODUCCION_1_crear_tablas.sql** (2 KB)
   - Crea tablas: `marcas`, `modelos`, `marcas_modelos_pendientes`
   
2. **PRODUCCION_2_modificar_publicaciones.sql** (1 KB)
   - Agrega 5 campos nuevos a tabla `publicaciones`
   - Puede dar errores de "columna duplicada" si se ejecuta más de una vez (es normal)
   
3. **PRODUCCION_3_migrar_datos_existentes.sql** (1 KB)
   - Actualiza publicaciones existentes
   - Marca todas como aprobadas
   
4. **PRODUCCION_5_inserts_marcas_modelos.sql** (25 KB) ⭐ **RECOMENDADO**
   - INSERT de 27 marcas y 544 modelos
   - SQL puro, compatible con cualquier servidor

### Scripts PHP (Alternativos)
5. **PRODUCCION_2_ALTERNATIVO_modificar_publicaciones.php**
   - Versión PHP del Script 2
   - Verifica si columnas existen antes de agregar
   - Más seguro, sin errores
   
6. **PRODUCCION_4_importar_marcas.php**
   - Importa marcas/modelos desde JSON
   - Requiere editar credenciales
   - Muestra progreso detallado

### Scripts de Utilidad
7. **generar_inserts_sql.php**
   - Genera el archivo SQL desde JSON
   - Ya ejecutado, no necesitas usarlo

8. **ejecutar_migracion_completa.sh** ⭐ **TODO-EN-UNO**
   - Script bash que ejecuta todo automáticamente
   - Solicita credenciales
   - Crea backup automático
   - Verifica resultados

### Documentación
9. **PRODUCCION_INSTRUCCIONES.md**
   - Guía completa y detallada
   - Solución de problemas
   - Verificación paso a paso

10. **EJECUTAR_EN_PRODUCCION.md** ⭐ **GUÍA RÁPIDA**
    - Comandos listos para copiar/pegar
    - Verificación rápida
    - Problemas comunes

11. **README_MIGRACION.md** (este archivo)
    - Índice de todos los archivos

---

## 🚀 Métodos de Ejecución

### Método 1: Script Automático (Más Fácil) ⭐
```bash
bash ejecutar_migracion_completa.sh
```
- Ejecuta todo automáticamente
- Crea backup
- Verifica resultados
- **Recomendado para usuarios con acceso SSH**

### Método 2: Comandos Manuales (Más Control)
```bash
# 1. Backup
mysqldump -u usuario -p base_datos > backup.sql

# 2. Ejecutar scripts
mysql -u usuario -p base_datos < PRODUCCION_1_crear_tablas.sql
mysql -u usuario -p base_datos < PRODUCCION_2_modificar_publicaciones.sql
mysql -u usuario -p base_datos < PRODUCCION_3_migrar_datos_existentes.sql
mysql -u usuario -p base_datos < PRODUCCION_5_inserts_marcas_modelos.sql
```
- **Recomendado para usuarios avanzados**

### Método 3: phpMyAdmin (Sin SSH)
1. Crear backup desde phpMyAdmin (Exportar)
2. Ir a pestaña "SQL"
3. Copiar y pegar cada script en orden
4. Para el Script 5, usar "Importar" (es muy grande)
- **Recomendado para hosting compartido**

---

## 📊 Datos que se Importarán

```
✓ 27 marcas de vehículos
✓ 544 modelos distribuidos entre las marcas
✓ 3 tablas nuevas
✓ 5 campos nuevos en tabla publicaciones
```

### Top 5 Marcas con Más Modelos:
1. Toyota - 72 modelos
2. Chevrolet - 54 modelos
3. Nissan - 45 modelos
4. Hyundai - 42 modelos
5. Kia - 38 modelos

---

## ⏱️ Tiempo Estimado

- **Método 1 (Script automático):** 2-3 minutos
- **Método 2 (Manual):** 3-5 minutos
- **Método 3 (phpMyAdmin):** 5-10 minutos

---

## ✅ Checklist de Ejecución

- [ ] Hacer backup de la base de datos
- [ ] Ejecutar PRODUCCION_1_crear_tablas.sql
- [ ] Ejecutar PRODUCCION_2_modificar_publicaciones.sql
- [ ] Ejecutar PRODUCCION_3_migrar_datos_existentes.sql
- [ ] Ejecutar PRODUCCION_5_inserts_marcas_modelos.sql
- [ ] Verificar que se crearon las tablas
- [ ] Verificar que se importaron las marcas (27)
- [ ] Verificar que se importaron los modelos (544)
- [ ] Subir archivos PHP actualizados
- [ ] Limpiar caché del servidor
- [ ] Probar crear una publicación nueva
- [ ] Verificar autocompletado de marcas/modelos

---

## 🔍 Verificación Rápida

```sql
-- Debe retornar 27
SELECT COUNT(*) FROM marcas;

-- Debe retornar 544
SELECT COUNT(*) FROM modelos;

-- Debe mostrar 5 columnas nuevas
SHOW COLUMNS FROM publicaciones LIKE '%marca%';
SHOW COLUMNS FROM publicaciones LIKE '%modelo%';
```

---

## 🚨 Problemas Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Duplicate column name" | Columna ya existe | Normal, ignora el error |
| "Cannot add foreign key" | Tablas no existen | Ejecuta Script 1 primero |
| "Access denied" | Credenciales incorrectas | Verifica usuario/contraseña |
| Importación lenta | Muchos datos | Normal, espera 10-30 seg |

---

## 🔄 Rollback

Si algo sale mal:
```bash
mysql -u usuario -p base_datos < backup.sql
```

---

## 📞 Soporte

Si tienes problemas:
1. Lee **EJECUTAR_EN_PRODUCCION.md** para soluciones rápidas
2. Lee **PRODUCCION_INSTRUCCIONES.md** para guía detallada
3. Verifica los logs de MySQL
4. Asegúrate de tener permisos suficientes

---

## 🎯 Después de la Migración

1. **Subir archivos PHP actualizados:**
   - `app/models/MarcaModelo.php`
   - `app/controllers/MarcaModeloController.php`
   - `app/views/pages/publicaciones/publish.php`
   - `public/assets/js/marca-modelo-selector.js`
   - Y todos los demás archivos del sistema

2. **Limpiar caché:**
   ```bash
   # Si usas OPcache
   php -r "opcache_reset();"
   
   # O reinicia PHP-FPM
   sudo service php-fpm restart
   ```

3. **Probar funcionalidad:**
   - Crear nueva publicación
   - Verificar autocompletado
   - Probar marca/modelo personalizado
   - Verificar panel admin

---

## 📈 Resultados Esperados

Después de ejecutar todos los scripts:

```
✅ Base de datos actualizada
✅ 3 tablas nuevas creadas
✅ 5 campos agregados a publicaciones
✅ 27 marcas importadas
✅ 544 modelos importados
✅ Publicaciones existentes migradas
✅ Sistema listo para usar
```

---

## 🎉 ¡Listo!

Tu sistema ahora tiene:
- ✅ Autocompletado de marcas y modelos
- ✅ Opción para ingresar marcas/modelos personalizados
- ✅ Panel de administración para aprobar personalizados
- ✅ Catálogo completo de vehículos chilenos
