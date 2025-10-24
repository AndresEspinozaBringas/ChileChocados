# ChileChocados - Marketplace de Bienes Siniestrados

Plataforma web para compra y venta de vehículos y bienes siniestrados en Chile.

## 🚀 Tecnologías

- **Backend:** PHP 8+ con arquitectura MVC simplificada
- **Base de Datos:** MySQL con PDO
- **Frontend:** HTML5, CSS3, JavaScript vanilla
- **Servidor:** Apache (LAMP)
- **Pasarela de Pagos:** Flow

## 📁 Estructura del Proyecto

```
chilechocados/
├── .htaccess                 # Configuración Apache raíz
├── .env.example              # Variables de entorno (copiar a .env)
├── public/                   # Document root
│   ├── index.php            # Front controller
│   ├── .htaccess            # Rewrite rules
│   ├── assets/              # Archivos estáticos
│   │   ├── css/style.css
│   │   ├── js/app.js
│   │   ├── images/
│   │   └── icons/
│   └── uploads/             # Archivos subidos por usuarios
├── app/
│   ├── config/
│   │   ├── config.php       # Configuración principal
│   │   └── database.php     # Conexión PDO
│   ├── controllers/         # Lógica de negocio
│   ├── models/              # Modelos de datos
│   └── views/
│       ├── layouts/         # Header, footer
│       └── pages/           # Vistas de páginas
├── admin/                   # Panel administrativo
├── includes/                # Funciones auxiliares
│   └── helpers.php
└── logs/                    # Logs de errores

```

## ⚙️ Instalación Local (LAMP)

### 1. Requisitos Previos
- PHP >= 8.0
- MySQL >= 5.7
- Apache >= 2.4
- Composer (opcional)

### 2. Configuración

```bash
# Clonar proyecto
git clone https://github.com/ToroDigital/chilechocados.git
cd chilechocados

# Copiar archivo de entorno
cp .env.example .env

# Editar .env con tus credenciales
nano .env
```

### 3. Configurar Apache

Agregar VirtualHost en `/etc/apache2/sites-available/chilechocados.conf`:

```apache
<VirtualHost *:80>
    ServerName chilechocados.local
    DocumentRoot /ruta/a/chilechocados/public

    <Directory /ruta/a/chilechocados/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/chilechocados_error.log
    CustomLog ${APACHE_LOG_DIR}/chilechocados_access.log combined
</VirtualHost>
```

```bash
# Habilitar sitio y mod_rewrite
sudo a2ensite chilechocados.conf
sudo a2enmod rewrite
sudo systemctl restart apache2

# Agregar dominio local
echo "127.0.0.1 chilechocados.local" | sudo tee -a /etc/hosts
```

### 4. Permisos

```bash
# Dar permisos a carpetas de escritura
chmod -R 775 logs/
chmod -R 775 public/uploads/
chown -R www-data:www-data logs/ public/uploads/
```

### 5. Base de Datos (próximo paso)

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE chilechocados CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON chilechocados.* TO 'usuario'@'localhost' IDENTIFIED BY 'contraseña';
FLUSH PRIVILEGES;
EXIT;

# Importar esquema SQL (cuando esté disponible)
mysql -u root -p chilechocados < database/schema.sql
```

## 🌐 Acceso

- **Frontend:** http://chilechocados.local
- **Admin:** http://chilechocados.local/admin (próximamente)

## 📝 Rutas Principales

- `/` - Home
- `/categorias` - Categorías
- `/listado` - Listado de publicaciones
- `/detalle/{id}` - Detalle de publicación
- `/publicar` - Nueva publicación
- `/login` - Iniciar sesión
- `/registro` - Registro de usuario
- `/perfil` - Perfil de usuario

## 🔐 Variables de Entorno (.env)

```env
# Base de datos
DB_HOST=localhost
DB_PORT=3306
DB_NAME=chilechocados
DB_USER=usuario
DB_PASS=contraseña

# Aplicación
APP_NAME=ChileChocados
APP_ENV=development
APP_URL=http://chilechocados.local
APP_DEBUG=true

# Flow
FLOW_API_KEY=tu_api_key
FLOW_SECRET_KEY=tu_secret_key
FLOW_SANDBOX=true
```

## 📋 Próximos Pasos

1. ✅ Estructura base del proyecto creada
2. ✅ Sistema de rutas implementado
3. ✅ Layouts y vistas principales
4. ⏳ Crear esquema de base de datos
5. ⏳ Implementar sistema de autenticación
6. ⏳ CRUD de publicaciones
7. ⏳ Sistema de mensajería
8. ⏳ Integración con Flow
9. ⏳ Panel administrativo

## 🛠️ Desarrollo

### Agregar nueva página

1. Crear controlador en `app/controllers/`
2. Crear vista en `app/views/pages/`
3. La ruta se genera automáticamente por el front controller

Ejemplo:
```php
// app/controllers/MiControlador.php
class MiControlador {
    public function index() {
        require_once APP_PATH . '/views/pages/mi-pagina.php';
    }
}

// Acceso: http://chilechocados.local/mi
```

## 📚 Documentación

Ver carpeta `/mnt/project/` para:
- Master_Chilechocados.docx
- Requerimiento_Programacion_ChileChocados.docx
- Propuesta_Tecnica_ChileChocados.docx

## 👥 Equipo

- **ToroDigital** - Desarrollo
- **ChileChocados SpA** - Cliente

## 📄 Licencia

Propietario - ChileChocados SpA

---

**Versión:** 1.0.0  
**Última actualización:** Octubre 2025
