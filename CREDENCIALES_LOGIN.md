# 🔐 Credenciales de Acceso - ChileChocados

## 🌐 URL de la Aplicación
**http://chilechocados.local:8080/login**

---

## 👤 Usuarios de Prueba

### 1️⃣ Usuario Administrador
- **Email:** admin@chilechocados.cl
- **Contraseña:** admin123
- **Rol:** Administrador
- **Permisos:** Acceso completo al sistema, panel de administración

### 2️⃣ Usuario Vendedor
- **Email:** vendedor@test.cl
- **Contraseña:** vendedor123
- **Rol:** Vendedor
- **Permisos:** Crear y gestionar publicaciones, ver mensajes

---

## 🗄️ Acceso a phpMyAdmin

### URL
**http://localhost:8080/phpmyadmin**

### Credenciales Base de Datos
- **Usuario:** chilechocados_user
- **Contraseña:** a1936@2065A!
- **Base de datos:** chilechocados

---

## 📝 Notas Importantes

### Seguridad
⚠️ **IMPORTANTE:** Estas son credenciales de desarrollo. En producción:
1. Cambia todas las contraseñas
2. Usa contraseñas más seguras (mínimo 12 caracteres)
3. Habilita autenticación de dos factores
4. Limita los intentos de login

### Roles del Sistema
- **admin:** Acceso completo, gestión de usuarios, moderación
- **vendedor:** Puede publicar vehículos, gestionar sus publicaciones
- **comprador:** Puede ver publicaciones, contactar vendedores, guardar favoritos

### Crear Nuevos Usuarios
Puedes crear nuevos usuarios desde:
1. La página de registro: http://chilechocados.local:8080/registro
2. El panel de administración (como admin)
3. Directamente en phpMyAdmin

### Verificación de Email
Los usuarios creados arriba ya están verificados (`verificado = 1`).
Los nuevos usuarios que se registren necesitarán verificar su email.

---

## 🔧 Comandos Útiles

### Ver todos los usuarios
```bash
mysql -u chilechocados_user -p'a1936@2065A!' chilechocados -e "SELECT id, nombre, email, rol, estado FROM usuarios;"
```

### Crear un nuevo usuario admin desde terminal
```bash
php -r "echo password_hash('tu_password', PASSWORD_ARGON2ID);"
# Luego inserta en la base de datos con el hash generado
```

### Resetear contraseña de un usuario
```sql
UPDATE usuarios 
SET password = '$argon2id$v=19$m=65536,t=4,p=1$...' 
WHERE email = 'usuario@email.com';
```

---

## 🐛 Solución de Problemas

### No puedo iniciar sesión
1. Verifica que estés usando el email correcto (no el nombre de usuario)
2. Asegúrate de que el usuario esté activo: `estado = 'activo'`
3. Verifica que el usuario esté verificado: `verificado = 1`
4. Revisa los logs: `/opt/homebrew/var/log/httpd/chilechocados_error.log`

### Error "Too many login attempts"
El sistema bloquea después de 5 intentos fallidos por 15 minutos.
Para resetear manualmente, limpia la sesión o espera 15 minutos.

### La página no carga
1. Verifica que Apache esté corriendo: `brew services list | grep httpd`
2. Verifica que MySQL esté corriendo: `brew services list | grep mysql`
3. Revisa los logs de error de Apache

---

## 📚 Recursos Adicionales

- **Documentación del proyecto:** README.md
- **Configuración de base de datos:** app/config/database.php
- **Variables de entorno:** .env
- **Logs de Apache:** /opt/homebrew/var/log/httpd/
- **Logs de MySQL:** /opt/homebrew/var/mysql/

---

**Última actualización:** 25 de Octubre, 2025
