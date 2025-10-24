# ChileChocados 🚗💥

> Marketplace líder en Chile para la compraventa de vehículos siniestrados

[![Estado](https://img.shields.io/badge/estado-desarrollo-yellow.svg)]()
[![Progreso](https://img.shields.io/badge/progreso-54%25-orange.svg)]()
[![PHP](https://img.shields.io/badge/PHP-8.x-blue.svg)]()
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue.svg)]()

---

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Tecnologías](#tecnologías)
- [Instalación](#instalación)
- [Uso](#uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Roadmap](#roadmap)
- [Contribuir](#contribuir)
- [Licencia](#licencia)
- [Contacto](#contacto)

---

## 📖 Descripción

**ChileChocados** es una plataforma web innovadora que facilita la compraventa de vehículos siniestrados en Chile. Conectamos vendedores que tienen vehículos accidentados con compradores interesados en repararlos o adquirir piezas de desarme.

### 🎯 Objetivos del Proyecto

- Crear el marketplace líder en Chile para vehículos siniestrados
- Facilitar la conexión entre vendedores y compradores
- Monetizar mediante servicios de destacado
- Generar tráfico desde redes sociales
- Ofrecer una plataforma segura y confiable

---

## ✨ Características

### Funcionalidades Actuales (Fase 0 - 54% completado)

- ✅ **Arquitectura MVC robusta**
- ✅ **Base de datos completa** (12 tablas, ~1,400 registros)
- ✅ **Sistema de enrutamiento automático**
- ✅ **Modelos PHP con Active Record pattern**
- ✅ **Layouts responsivos** (header, footer, nav)
- ✅ **Helpers de seguridad y validación**

### Próximamente (En desarrollo)

- 🔜 Sistema de autenticación y registro
- 🔜 CRUD completo de publicaciones
- 🔜 Sistema de mensajería privada
- 🔜 Integración con Flow (pagos)
- 🔜 Panel administrativo
- 🔜 Sistema de favoritos
- 🔜 Búsqueda avanzada con filtros

---

## 🛠 Tecnologías

### Backend
- **PHP 8.x** - Lenguaje principal
- **MySQL 8.0** - Base de datos
- **Apache 2.4** - Servidor web
- **PDO** - Capa de abstracción de datos

### Frontend
- **HTML5** / **CSS3**
- **JavaScript ES6+**
- **jQuery 3.x**
- **Lucide Icons** - Iconografía
- **Inter Font** - Tipografía

### Herramientas
- **Homebrew** - Gestor de paquetes (macOS)
- **phpMyAdmin** - Administración de BD
- **Git** - Control de versiones
- **GitHub** - Repositorio

---

## 🚀 Instalación

### Requisitos Previos

- PHP >= 8.0
- MySQL >= 8.0
- Apache >= 2.4
- Composer (opcional)
- macOS con Homebrew (recomendado)

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/AndresEspinozaBringas/ChileChocados.git
cd ChileChocados
```

### Paso 2: Configurar Base de Datos

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE chilechocados CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Importar esquema
mysql -u root -p chilechocados < database/schema.sql

# Importar datos semilla (opcional)
mysql -u root -p chilechocados < database/seeds.sql
```

### Paso 3: Configurar Entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Editar credenciales
nano .env
```

Configurar `.env`:

```env
DB_HOST=localhost
DB_NAME=chilechocados
DB_USER=root
DB_PASS=tu_contraseña
DB_CHARSET=utf8mb4

BASE_URL=http://localhost:8080/chilechocados
APP_ENV=development
```

### Paso 4: Configurar Apache

**Archivo:** `/opt/homebrew/etc/httpd/httpd.conf`

```apache
# Habilitar módulos
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so
LoadModule php_module /opt/homebrew/opt/php/lib/httpd/modules/libphp.so

# Configurar PHP
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

# Agregar index.php
DirectoryIndex index.php index.html
```

### Paso 5: Configurar Permisos

```bash
chmod -R 755 public/
chmod -R 777 public/uploads/
chmod -R 777 logs/
```

### Paso 6: Iniciar Servicios

```bash
# Iniciar Apache
brew services start httpd

# Iniciar MySQL
brew services start mysql

# Verificar servicios
brew services list
```

### Paso 7: Acceder a la Aplicación

Abrir navegador en:
- **Frontend:** http://localhost:8080/chilechocados
- **phpMyAdmin:** http://localhost:8080/phpmyadmin

---

## 📘 Uso

### Estructura de URLs

```
/ o /home              → Página de inicio
/publicaciones         → Listado de publicaciones
/publicacion/{id}      → Detalle de publicación
/categorias            → Categorías
/usuarios/login        → Iniciar sesión
/usuarios/register     → Registrarse
/usuarios/perfil       → Perfil de usuario
/admin                 → Panel administrativo
```

### Comandos Útiles

```bash
# Ver logs de Apache
tail -f /opt/homebrew/var/log/httpd/error_log

# Backup de base de datos
mysqldump -u root -p chilechocados > backup_$(date +%Y%m%d).sql

# Reiniciar servicios
brew services restart httpd
brew services restart mysql
```

---

## 📁 Estructura del Proyecto

```
chilechocados/
├── app/
│   ├── Controllers/        # Controladores MVC
│   ├── Models/            # Modelos de datos
│   └── Views/             # Vistas PHP
│       ├── layouts/       # Layouts compartidos
│       └── pages/         # Páginas de la aplicación
├── config/
│   └── database.php       # Configuración de BD
├── database/
│   ├── schema.sql         # Esquema de BD
│   └── seeds.sql          # Datos semilla
├── includes/
│   ├── helpers.php        # Funciones auxiliares
│   └── routes.php         # Sistema de rutas
├── public/
│   ├── assets/           # CSS, JS, imágenes
│   ├── uploads/          # Archivos de usuario
│   └── index.php         # Front Controller
├── logs/                 # Logs de aplicación
├── _archive/            # Archivos archivados
│   └── wireframes/      # HTML de referencia
├── .env                 # Configuración de entorno
├── .gitignore          # Archivos ignorados por Git
├── CHANGELOG.md        # Historial de cambios
├── README.md           # Este archivo
└── PLAN_DESARROLLO.md  # Plan de desarrollo

```

---

## 🗺 Roadmap

### Fase Actual: Fase 0 - Limpieza (54% completado)

- [x] Configuración de entorno LAMP
- [x] Estructura MVC
- [x] Base de datos con datos semilla
- [x] Modelos PHP implementados
- [x] Limpieza de carpetas duplicadas
- [x] Organización de archivos HTML
- [ ] Configuración de branches Git

### Próximas Fases (3-4 meses)

| Fase | Nombre | Duración | Estado |
|------|--------|----------|--------|
| **1** | Rediseño de Interfaz | 5-7 días | ⏳ Pendiente |
| **2** | Autenticación | 2 semanas | ⏳ Pendiente |
| **3** | Migración HTML → PHP | 1 semana | ⏳ Pendiente |
| **4** | Publicaciones | 2 semanas | ⏳ Pendiente |
| **5** | Mensajería y Pagos | 2 semanas | ⏳ Pendiente |
| **6** | Admin y RRSS | 2 semanas | ⏳ Pendiente |
| **7** | Favoritos y Optimización | 2 semanas | ⏳ Pendiente |
| **QA** | Testing y Deploy | 1 semana | ⏳ Pendiente |

**Ver roadmap completo:** [PLAN_DESARROLLO_CHILECHOCADOS.md](PLAN_DESARROLLO_CHILECHOCADOS.md)

---

## 🤝 Contribuir

Este es un proyecto privado en desarrollo. No se aceptan contribuciones externas en este momento.

### Para el equipo de desarrollo:

1. Crear branch desde `develop`:
   ```bash
   git checkout develop
   git checkout -b feature/nombre-feature
   ```

2. Hacer cambios y commit:
   ```bash
   git add .
   git commit -m "Descripción del cambio"
   ```

3. Push y crear Pull Request:
   ```bash
   git push origin feature/nombre-feature
   ```

---

## 📊 Estado del Proyecto

### Progreso por Categoría

| Categoría | Completado | Pendiente | % Avance |
|-----------|------------|-----------|----------|
| Modelos | 5/5 | 0 | 100% ✅ |
| Vistas Públicas | 8/15 | 7 | 53% ⚠️ |
| Vistas Admin | 2/6 | 4 | 33% ⚠️ |
| Controladores | 1/6 | 5 | 17% ❌ |
| Layouts | 3/3 | 0 | 100% ✅ |

**Progreso Total:** 19/35 archivos = **54% completado**

### Métricas

- **Líneas de código PHP:** ~3,000+
- **Tablas en BD:** 12
- **Registros de prueba:** ~1,400
- **Tiempo invertido:** ~7 horas
- **Días de desarrollo:** 1

---

## 📄 Licencia

**Propietario:** ChileChocados SpA  
**Desarrollador:** ToroDigital  
**Todos los derechos reservados**

Este es un proyecto propietario. El código y contenido no pueden ser utilizados, copiados, modificados o distribuidos sin autorización expresa del propietario.

---

## 📞 Contacto

### Desarrollador

**ToroDigital**
- Desarrollador: Andrés Espinoza Bringas
- Email: desarrollo@torodigital.cl
- GitHub: [@AndresEspinozaBringas](https://github.com/AndresEspinozaBringas)

### Cliente

**ChileChocados SpA**
- Web: www.chilechocados.cl
- Email: contacto@chilechocados.cl

---

## 📚 Documentación Adicional

- [Plan de Desarrollo](PLAN_DESARROLLO_CHILECHOCADOS.md) - Roadmap completo por fases
- [Historial de Actividades](Historial_Actividades_ChileChocados.md) - Registro de sesiones
- [Especificaciones Funcionales](Especificaciones_Funcionales_ChileChocados.md) - Requerimientos detallados
- [CHANGELOG](CHANGELOG.md) - Historial de cambios

---

## 🙏 Agradecimientos

Gracias a la comunidad open source y a todas las herramientas que hacen posible este proyecto.

---

**Última actualización:** 23 de Octubre, 2025  
**Versión:** 0.0.1 (Fase 0 en desarrollo)

---

<div align="center">
  <strong>Hecho con ❤️ en Chile 🇨🇱</strong>
</div>