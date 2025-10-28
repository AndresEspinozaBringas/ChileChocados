# 📦 Guía de Deployment - ChileChocados

## 🎯 Recaudos y Mejores Prácticas

### ✅ Antes de Subir Archivos

1. **Verificar cambios localmente**
   - Probar en tu servidor local (Apache)
   - Verificar que no haya errores en logs
   - Probar funcionalidades modificadas

2. **Revisar archivos sensibles**
   - NO subir `.env` (contiene credenciales)
   - NO subir archivos de prueba (`test_*.php`, `debug_*.php`)
   - NO subir logs (`logs/*.log`)
   - NO subir archivos temporales (`.DS_Store`, `*.swp`)

3. **Backup del servidor**
   - Hacer backup de la BD antes de cambios importantes
   - Guardar copia de archivos críticos modificados

---

## 🚀 Métodos de Deployment

### Método 1: Sincronización Inteligente (Recomendado)

```bash
# 1. Ver qué archivos cambiarían
./sync_to_server.sh --check

# 2. Simular sin hacer cambios (recomendado)
./sync_to_server.sh --dry-run

# 3. Aplicar cambios
./sync_to_server.sh --sync

# 4. Deploy completo (todos los archivos)
./sync_to_server.sh --full
```

**Ventajas:**
- Solo sube archivos modificados
- Más rápido
- Menos riesgo de sobrescribir cambios del servidor
- Muestra preview de cambios

---

### Método 2: SFTP Manual

**Usando FileZilla o Cyberduck:**

1. **Conectar:**
   - Host: `iad1-shared-b8-24.dreamhost.com`
   - Usuario: `lgongo`
   - Password: `GONGI$2025`
   - Puerto: `22` (SFTP)

2. **Navegar a:** `/home/lgongo/chilechocados.cl`

3. **Subir archivos:**
   - Arrastra archivos desde local
   - Sobrescribe solo los modificados

**⚠️ Cuidado con:**
- No sobrescribir `.env` del servidor
- No eliminar carpeta `uploads/` con imágenes
- No sobrescribir `logs/` con datos

---

### Método 3: rsync Directo

```bash
# Sincronizar solo archivos PHP modificados
rsync -avz --update \
  --include='*.php' \
  --exclude='*' \
  -e "ssh -p 22" \
  ./app/ \
  lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/app/

# Sincronizar carpeta específica
rsync -avz --update \
  -e "ssh -p 22" \
  ./app/controllers/ \
  lgongo@iad1-shared-b8-24.dreamhost.com:~/chilechocados.cl/app/controllers/
```

---

## 🔍 Identificar Archivos Modificados

### Opción 1: Git (Si usas control de versiones)

```bash
# Ver archivos modificados desde último commit
git status

# Ver archivos modificados en las últimas 24 horas
git diff --name-only HEAD@{1.day.ago}

# Ver archivos modificados desde fecha específica
git log --since="2025-10-27" --name-only --pretty=format: | sort -u
```

### Opción 2: Por fecha de modificación

```bash
# Archivos modificados en las últimas 24 horas
find . -type f -name "*.php" -mtime -1

# Archivos modificados en los últimos 7 días
find . -type f -name "*.php" -mtime -7

# Archivos modificados después de fecha específica
find . -type f -newermt "2025-10-27"
```

### Opción 3: Comparar con servidor

```bash
# Crear script de comparación
./comparar_con_servidor.sh
```

---

## 📋 Checklist de Deployment

### Pre-Deployment

- [ ] Código probado localmente
- [ ] Sin errores en logs locales
- [ ] Backup de BD del servidor
- [ ] Backup de archivos críticos
- [ ] Revisar archivos a subir

### Durante Deployment

- [ ] Usar `--dry-run` primero
- [ ] Verificar lista de archivos
- [ ] Confirmar cambios
- [ ] Subir archivos

### Post-Deployment

- [ ] Verificar sitio funciona
- [ ] Revisar logs del servidor
- [ ] Probar funcionalidades modificadas
- [ ] Verificar permisos de archivos

---

## 🛡️ Archivos que NUNCA debes sobrescribir

### En el servidor:

1. **`.env`** - Contiene credenciales de producción
2. **`public/uploads/`** - Imágenes subidas por usuarios
3. **`logs/`** - Logs de producción (útiles para debug)
4. **`vendor/`** - Dependencias de Composer (regenerar si es necesario)

### Archivos que NO debes subir:

```
.env                    # Credenciales locales
.git/                   # Control de versiones
.vscode/                # Configuración del editor
node_modules/           # Dependencias Node
*.log                   # Logs locales
.DS_Store              # Archivos de macOS
test_*.php             # Archivos de prueba
debug_*.php            # Scripts de debug
fix_*.php              # Scripts de corrección
*.md                   # Documentación
*.sh                   # Scripts de deployment
```

---

## 🔧 Comandos Útiles

### Ver diferencias con servidor

```bash
# Comparar archivo específico
diff <(cat app/controllers/AuthController.php) \
     <(ssh lgongo@iad1-shared-b8-24.dreamhost.com 'cat ~/chilechocados.cl/app/controllers/AuthController.php')

# Listar archivos en servidor
ssh lgongo@iad1-shared-b8-24.dreamhost.com 'ls -la ~/chilechocados.cl/app/controllers/'
```

### Verificar permisos

```bash
# Ver permisos en servidor
ssh lgongo@iad1-shared-b8-24.dreamhost.com 'ls -la ~/chilechocados.cl/public/uploads/'

# Corregir permisos
ssh lgongo@iad1-shared-b8-24.dreamhost.com << 'EOF'
cd ~/chilechocados.cl
chmod -R 755 app public includes
chmod -R 777 logs public/uploads
EOF
```

### Ver logs en tiempo real

```bash
# Logs de PHP
ssh lgongo@iad1-shared-b8-24.dreamhost.com 'tail -f ~/chilechocados.cl/logs/php_errors.log'

# Logs de Apache
ssh lgongo@iad1-shared-b8-24.dreamhost.com 'tail -f ~/logs/chilechocados.cl/http/error.log'
```

---

## 🚨 Solución de Problemas

### Error 500 después de subir archivos

1. Revisar logs: `tail -f logs/php_errors.log`
2. Verificar permisos: `chmod -R 755 app/`
3. Verificar `.htaccess` no esté corrupto
4. Verificar que exista `includes/helpers.php`

### Archivos no se actualizan

1. Limpiar caché del navegador
2. Verificar que se subió correctamente: `ls -la archivo.php`
3. Verificar fecha de modificación: `stat archivo.php`
4. Forzar recarga: Ctrl+Shift+R

### Permisos incorrectos

```bash
# Corregir todos los permisos
ssh lgongo@iad1-shared-b8-24.dreamhost.com << 'EOF'
cd ~/chilechocados.cl
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 777 logs public/uploads
EOF
```

---

## 📝 Notas Importantes

1. **Siempre usa `--dry-run` primero** para ver qué cambiaría
2. **Haz backup antes de cambios importantes**
3. **Prueba en local antes de subir a producción**
4. **Revisa logs después de cada deployment**
5. **Mantén sincronizado tu repositorio Git**
6. **Documenta cambios importantes**

---

## 🔗 Enlaces Útiles

- **phpMyAdmin:** https://east1-phpmyadmin.dreamhost.com/
- **Panel DreamHost:** https://panel.dreamhost.com/
- **Sitio Web:** https://chilechocados.cl/
- **Admin Panel:** https://chilechocados.cl/admin

---

**Última actualización:** 27 de Octubre, 2025
