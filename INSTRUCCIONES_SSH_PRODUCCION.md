# 🚀 Instrucciones SSH para Producción

## 📋 Pasos para Configurar Avatares

### 1. Conectar por SSH

```bash
ssh tu_usuario@chilechocados.cl
# O si tienes un puerto específico:
ssh -p 22 tu_usuario@chilechocados.cl
```

### 2. Ir al Directorio del Proyecto

```bash
# Ubicaciones comunes según el hosting:

# Opción A: Hosting compartido típico
cd ~/public_html

# Opción B: VPS con dominio
cd /var/www/chilechocados.cl

# Opción C: VPS con html
cd /var/www/html

# Opción D: Usuario específico
cd /home/tu_usuario/public_html

# Verificar que estás en el lugar correcto
ls -la
# Debes ver carpetas: app, public, database, etc.
```

### 3. Descargar el Script de Configuración

```bash
# Opción A: Si tienes git configurado
git pull origin main

# Opción B: Descargar directamente
wget https://raw.githubusercontent.com/AndresEspinozaBringas/ChileChocados/main/setup_avatars_produccion.sh

# Opción C: Crear el archivo manualmente (ver contenido abajo)
nano setup_avatars_produccion.sh
# Pega el contenido del script y guarda (Ctrl+X, Y, Enter)
```

### 4. Dar Permisos de Ejecución

```bash
chmod +x setup_avatars_produccion.sh
```

### 5. Ejecutar el Script

```bash
./setup_avatars_produccion.sh
```

O directamente:

```bash
bash setup_avatars_produccion.sh
```

### 6. Verificar Resultados

El script mostrará:
- ✅ Carpetas creadas
- ✅ Permisos configurados
- ✅ Archivos de seguridad creados
- ✅ Prueba de escritura exitosa

---

## 🔧 Comandos Manuales (Si prefieres hacerlo paso a paso)

### Crear Carpetas

```bash
# Desde la raíz del proyecto
mkdir -p public/uploads/avatars
mkdir -p public/uploads/publicaciones
```

### Configurar Permisos

```bash
chmod 777 public/uploads
chmod 777 public/uploads/avatars
chmod 777 public/uploads/publicaciones
```

### Crear .htaccess

```bash
cat > public/uploads/avatars/.htaccess << 'EOF'
# Permitir acceso a imágenes
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Denegar acceso a PHP
<FilesMatch "\.php$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
EOF
```

### Verificar

```bash
# Ver estructura
ls -la public/uploads/

# Ver permisos
ls -la public/uploads/avatars/

# Probar escritura
echo "test" > public/uploads/avatars/test.txt && rm public/uploads/avatars/test.txt && echo "✓ Escritura OK"
```

---

## 🔍 Encontrar la Ruta Correcta

Si no sabes dónde está el proyecto:

```bash
# Buscar por nombre de carpeta
find ~ -name "chilechocados" -type d 2>/dev/null

# Buscar por archivo específico
find ~ -name "config.php" -path "*/app/config/*" 2>/dev/null

# Ver tu directorio home
pwd

# Listar contenido
ls -la
```

---

## 🐛 Solución de Problemas

### Problema: "Permission denied"

```bash
# Verificar usuario actual
whoami

# Cambiar propietario (si tienes sudo)
sudo chown -R www-data:www-data public/uploads
# O
sudo chown -R apache:apache public/uploads
# O con tu usuario
sudo chown -R $(whoami):$(whoami) public/uploads

# Dar permisos
chmod -R 777 public/uploads
```

### Problema: "No such file or directory"

```bash
# Verificar que estás en el directorio correcto
pwd
ls -la

# Buscar el proyecto
find ~ -name "public" -type d | grep chilechocados
```

### Problema: Script no se ejecuta

```bash
# Verificar permisos del script
ls -la setup_avatars_produccion.sh

# Dar permisos
chmod +x setup_avatars_produccion.sh

# Ejecutar con bash explícitamente
bash setup_avatars_produccion.sh
```

---

## 📊 Verificación Final

Después de ejecutar todo:

```bash
# 1. Verificar estructura
tree public/uploads/ -L 2
# O si no tienes tree:
find public/uploads/ -type d

# 2. Verificar permisos
ls -la public/uploads/avatars/

# 3. Verificar archivos
ls -la public/uploads/avatars/.htaccess

# 4. Probar desde navegador
curl -I https://chilechocados.cl/uploads/avatars/
# Debe retornar 200 o 403, NO 404
```

---

## 🎯 Resultado Esperado

```
public/
└── uploads/
    ├── avatars/
    │   ├── .htaccess
    │   └── .gitkeep
    └── publicaciones/
        └── .gitkeep

Permisos:
drwxrwxrwx  2 usuario grupo 4096 Nov  8 20:00 avatars
```

---

## 📞 Información Útil

### Comandos de Diagnóstico

```bash
# Ver usuario de PHP
ps aux | grep php

# Ver configuración de PHP
php -i | grep upload

# Ver logs de Apache
tail -f /var/log/apache2/error.log

# Ver logs de Nginx
tail -f /var/log/nginx/error.log

# Ver espacio en disco
df -h

# Ver permisos recursivos
find public/uploads -type d -exec ls -ld {} \;
```

### Información del Servidor

```bash
# Sistema operativo
cat /etc/os-release

# Versión de PHP
php -v

# Usuario actual
whoami

# Grupos del usuario
groups

# Directorio home
echo $HOME
```

---

## ✅ Checklist Final

- [ ] Conectado por SSH
- [ ] En el directorio correcto del proyecto
- [ ] Script descargado o creado
- [ ] Script ejecutado exitosamente
- [ ] Carpetas creadas (avatars, publicaciones)
- [ ] Permisos configurados (777)
- [ ] .htaccess creado
- [ ] Prueba de escritura exitosa
- [ ] Verificación desde navegador OK
- [ ] Avatar se puede subir desde el perfil

---

## 🚨 Si Nada Funciona

Contacta a tu proveedor de hosting y pídeles que:

1. Creen la carpeta: `public/uploads/avatars`
2. Den permisos 777 a la carpeta
3. Verifiquen que el usuario de PHP puede escribir
4. Verifiquen que no hay restricciones de `open_basedir`

O comparte el error específico que ves para ayudarte mejor.
