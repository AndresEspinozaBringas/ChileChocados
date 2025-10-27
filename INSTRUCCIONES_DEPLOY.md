# Instrucciones de Deploy a DreamHost

## Datos del Servidor

- **SSH Host:** iad1-shared-b8-24.dreamhost.com
- **SSH Port:** 22
- **Usuario SSH:** lgongo
- **Password SSH:** GONGI$2025
- **Ruta del sitio:** /home/lgongo/chilechocados.cl

## Datos de Base de Datos

- **Servidor MySQL:** mysql.chilechocados.cl
- **Nombre BD:** db_chocados
- **Usuario BD:** gongo_db
- **Password BD:** GONGI$2025

---

## Opción 1: Deploy Automático (Recomendado)

### Paso 1: Ejecutar el script de deploy

```bash
chmod +x deploy_to_dreamhost.sh
./deploy_to_dreamhost.sh
```

Cuando te pida la contraseña, ingresa: `GONGI$2025`

### Paso 2: Crear archivo .env en el servidor

```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com
cd ~/chilechocados.cl
cp .env.production .env
nano .env  # Verificar y ajustar si es necesario
```

### Paso 3: Crear la base de datos

1. Ve a phpMyAdmin: https://east1-phpmyadmin.dreamhost.com
2. Selecciona la base de datos `db_chocados`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `chilechocados_database.sql`
5. Ejecuta el script

---

## Opción 2: Deploy Manual con SFTP

### Usando FileZilla o Cyberduck:

1. **Conectar:**
   - Protocolo: SFTP
   - Host: iad1-shared-b8-24.dreamhost.com
   - Puerto: 22
   - Usuario: lgongo
   - Contraseña: GONGI$2025

2. **Subir archivos:**
   - Navega a `/home/lgongo/chilechocados.cl`
   - Sube todos los archivos EXCEPTO:
     - `.git/`
     - `node_modules/`
     - `_archive/`
     - `*.html` (wireframes)
     - `*.md` (documentación)
     - `.env` (crear nuevo en servidor)

3. **Configurar permisos:**
   - `logs/` → 777
   - `public/uploads/` → 777
   - `.htaccess` → 644
   - Resto de archivos → 755

4. **Crear .env:**
   - Copia el contenido de `.env.production`
   - Crea un nuevo archivo `.env` en el servidor
   - Pega el contenido y guarda

---

## Opción 3: Deploy Manual con rsync

```bash
# Subir archivos
rsync -avz --progress \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='_archive' \
  --exclude='*.html' \
  --exclude='*.md' \
  -e "ssh -p 22" \
  ./ lgongo@iad1-shared-b8-24.dreamhost.com:/home/lgongo/chilechocados.cl/

# Configurar permisos
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com << 'EOF'
cd ~/chilechocados.cl
chmod -R 755 .
chmod -R 777 logs
chmod -R 777 public/uploads
chmod 644 .htaccess
chmod 644 public/.htaccess
EOF
```

---

## Verificación Post-Deploy

### 1. Verificar conexión a BD

```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com
cd ~/chilechocados.cl
php test_db_connection.php
```

### 2. Verificar el sitio

Visita: https://chilechocados.cl

### 3. Verificar logs

```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com
tail -f ~/chilechocados.cl/logs/php_errors.log
```

### 4. Probar login admin

- URL: https://chilechocados.cl/admin
- Usuario: admin@chilechocados.cl
- Password: (el que configuraste)

---

## Comandos Útiles

### Conectar por SSH
```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com
```

### Ver logs en tiempo real
```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com 'tail -f ~/chilechocados.cl/logs/php_errors.log'
```

### Limpiar caché
```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com 'cd ~/chilechocados.cl && php public/clear-cache.php'
```

### Backup de base de datos
```bash
ssh -p 22 lgongo@iad1-shared-b8-24.dreamhost.com
mysqldump -h mysql.chilechocados.cl -u gongo_db -p db_chocados > backup_$(date +%Y%m%d).sql
```

---

## Solución de Problemas

### Error 500
- Verificar permisos de archivos
- Revisar logs: `tail -f logs/php_errors.log`
- Verificar que existe el archivo `.env`

### No conecta a BD
- Verificar credenciales en `.env`
- Verificar que el servidor MySQL es accesible
- Probar con: `php test_db_connection.php`

### Archivos no se suben
- Verificar permisos de escritura
- Verificar espacio en disco
- Verificar que la ruta es correcta

---

## Notas Importantes

1. **Nunca subir el archivo `.env` local** - Crear uno nuevo en el servidor
2. **Verificar permisos** - Los directorios `logs/` y `uploads/` deben ser escribibles
3. **Backup regular** - Hacer backup de la BD antes de cambios importantes
4. **Logs** - Revisar logs regularmente para detectar errores
5. **Seguridad** - Cambiar contraseñas por defecto después del primer deploy

---

## Contacto y Soporte

- DreamHost Panel: https://panel.dreamhost.com
- phpMyAdmin: https://east1-phpmyadmin.dreamhost.com
- Documentación DreamHost: https://help.dreamhost.com
