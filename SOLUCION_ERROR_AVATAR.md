# 🚨 Solución Error: Column 'avatar' not found

## Error
```
Error al procesar la imagen: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'avatar' in 'field list'
```

## Causa
La columna `avatar` no existe en la tabla `usuarios` en tu base de datos de producción.

## ✅ Solución Rápida

### Opción 1: Desde phpMyAdmin (Más Fácil)

1. **Accede a phpMyAdmin**
2. **Selecciona tu base de datos**
3. **Ve a la pestaña "SQL"**
4. **Copia y pega este código:**

```sql
ALTER TABLE usuarios 
ADD COLUMN avatar VARCHAR(255) NULL 
COMMENT 'Nombre del archivo de avatar (thumbnail 200x200)';
```

5. **Click en "Ejecutar"**
6. **Verifica que aparezca:** "1 fila afectada"

### Opción 2: Desde Navegador (Script PHP)

1. **Sube el archivo `agregar_avatar_urgente.php` a la raíz de tu sitio**
2. **Accede desde tu navegador:**
   ```
   https://tudominio.com/agregar_avatar_urgente.php
   ```
3. **Verás un mensaje de éxito**
4. **IMPORTANTE: Elimina el archivo después de ejecutarlo**

### Opción 3: Desde Línea de Comandos (SSH)

```bash
# Opción A: SQL directo
mysql -u tu_usuario -p tu_base_datos < database/migrations/PRODUCCION_AGREGAR_AVATAR.sql

# Opción B: Script PHP
php database/migrations/add_avatar_field.php
```

## 📋 Verificación

Después de ejecutar cualquiera de las opciones, verifica que funcionó:

### Desde phpMyAdmin:
1. Ve a la tabla `usuarios`
2. Click en "Estructura"
3. Busca el campo `avatar`
4. Debe aparecer como: `avatar` VARCHAR(255) NULL

### Desde SQL:
```sql
SHOW COLUMNS FROM usuarios LIKE 'avatar';
```

Debe retornar:
```
Field: avatar
Type: varchar(255)
Null: YES
Default: NULL
```

## 🎯 Después de la Migración

Una vez agregado el campo:

1. ✅ Los usuarios podrán subir avatares
2. ✅ El error desaparecerá
3. ✅ Los avatares aparecerán en:
   - Perfil de usuario
   - Header del sitio
   - Mensajes (conversaciones)

## 🔒 Seguridad

Si usaste el script PHP (`agregar_avatar_urgente.php`):
- **ELIMÍNALO inmediatamente después de usarlo**
- No lo dejes en el servidor
- Comando para eliminarlo:
  ```bash
  rm agregar_avatar_urgente.php
  ```

## 📁 Carpeta de Avatares

Asegúrate de que existe la carpeta:
```
public/uploads/avatars/
```

Si no existe, créala:
```bash
mkdir -p public/uploads/avatars
chmod 777 public/uploads/avatars
```

O desde PHP (ya incluido en el script):
```php
mkdir(__DIR__ . '/public/uploads/avatars', 0777, true);
```

## 🐛 Si el Error Persiste

1. **Verifica que el campo se agregó:**
   ```sql
   DESCRIBE usuarios;
   ```

2. **Limpia caché de OPcache:**
   ```bash
   # Si tienes acceso SSH
   php -r "opcache_reset();"
   
   # O reinicia PHP-FPM
   sudo service php-fpm restart
   ```

3. **Verifica permisos de la carpeta:**
   ```bash
   ls -la public/uploads/
   chmod 777 public/uploads/avatars
   ```

4. **Revisa logs de PHP:**
   ```bash
   tail -f /var/log/php-fpm/error.log
   # o
   tail -f /var/log/apache2/error.log
   ```

## 📞 Soporte Adicional

Si después de ejecutar la migración el error persiste:

1. Verifica que estás conectado a la base de datos correcta
2. Revisa el archivo `.env` o configuración de base de datos
3. Asegúrate de que el usuario de BD tiene permisos para ALTER TABLE
4. Verifica que no hay múltiples bases de datos (dev/prod)

## ✨ Resumen

**Comando más rápido (phpMyAdmin):**
```sql
ALTER TABLE usuarios ADD COLUMN avatar VARCHAR(255) NULL;
```

**Verificación:**
```sql
SHOW COLUMNS FROM usuarios LIKE 'avatar';
```

**Resultado esperado:**
```
✓ Campo agregado
✓ Error desaparece
✓ Avatares funcionando
```
