# Acceso a phpMyAdmin

## 🔗 URL de Acceso

Puedes acceder a phpMyAdmin en cualquiera de estas URLs:

- **http://localhost:8080/phpmyadmin**
- **http://chilechocados.local:8080/phpmyadmin**
- **http://127.0.0.1:8080/phpmyadmin**

## 🔐 Credenciales de Acceso

Según tu archivo `.env`, las credenciales de la base de datos son:

- **Servidor:** localhost
- **Usuario:** chilechocados_user
- **Contraseña:** a1936@2065A!
- **Base de datos:** chilechocados

## ✅ Configuración Aplicada

He configurado Apache para servir phpMyAdmin con las siguientes características:

1. **Alias creado:** `/phpmyadmin` apunta a `/opt/homebrew/share/phpmyadmin`
2. **Archivo de configuración:** `/opt/homebrew/etc/httpd/extra/phpmyadmin.conf`
3. **Permisos:** Acceso permitido desde cualquier origen
4. **Límites PHP aumentados:**
   - Upload max filesize: 128M
   - Post max size: 128M
   - Max execution time: 600 segundos
   - Max input time: 600 segundos

## 🔧 Comandos Útiles

### Reiniciar Apache
```bash
sudo brew services restart httpd
```

### Ver logs de Apache
```bash
tail -f /opt/homebrew/var/log/httpd/error_log
```

### Verificar configuración de Apache
```bash
httpd -t
```

### Ver estado de Apache
```bash
brew services list | grep httpd
```

## 🐛 Solución de Problemas

### Si phpMyAdmin no carga:

1. **Verificar que Apache esté corriendo:**
   ```bash
   brew services list | grep httpd
   ```

2. **Verificar logs de error:**
   ```bash
   tail -50 /opt/homebrew/var/log/httpd/error_log
   ```

3. **Verificar que PHP esté habilitado en Apache:**
   ```bash
   httpd -M | grep php
   ```

4. **Reiniciar Apache:**
   ```bash
   sudo brew services restart httpd
   ```

### Si no puedes iniciar sesión:

1. Verifica que MySQL esté corriendo:
   ```bash
   brew services list | grep mysql
   ```

2. Prueba la conexión desde terminal:
   ```bash
   mysql -u chilechocados_user -p chilechocados
   ```

3. Si el usuario no existe, créalo:
   ```bash
   mysql -u root -p
   ```
   
   Luego ejecuta:
   ```sql
   CREATE USER 'chilechocados_user'@'localhost' IDENTIFIED BY 'a1936@2065A!';
   GRANT ALL PRIVILEGES ON chilechocados.* TO 'chilechocados_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

## 📁 Ubicaciones Importantes

- **phpMyAdmin:** `/opt/homebrew/share/phpmyadmin`
- **Configuración phpMyAdmin:** `/opt/homebrew/etc/phpmyadmin.config.inc.php`
- **Configuración Apache:** `/opt/homebrew/etc/httpd/httpd.conf`
- **Virtual Hosts:** `/opt/homebrew/etc/httpd/extra/httpd-vhosts.conf`
- **Logs Apache:** `/opt/homebrew/var/log/httpd/`

## 🔒 Seguridad

⚠️ **IMPORTANTE:** Esta configuración es para desarrollo local. En producción:

1. Restringe el acceso por IP
2. Usa HTTPS
3. Cambia las credenciales por defecto
4. Considera usar autenticación adicional
5. Limita los permisos del usuario de base de datos

## 📝 Notas

- phpMyAdmin está instalado vía Homebrew en la versión 5.2.3
- La configuración permite acceso desde cualquier origen (solo para desarrollo)
- Los límites de PHP están aumentados para facilitar importación de bases de datos grandes
