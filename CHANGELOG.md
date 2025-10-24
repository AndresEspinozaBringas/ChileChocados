# CHANGELOG - ChileChocados

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

---

## [Sin versionar] - Fase 0 en progreso

### ✅ Completado [2025-10-23]

#### Agregado
- Estructura MVC completa implementada
- Sistema de enrutamiento automático
- Front Controller configurado
- 5 modelos PHP con Active Record pattern
  - Usuario.php
  - Publicacion.php
  - Categoria.php
  - Mensaje.php
  - Pago.php
- 3 layouts principales (header, footer, nav)
- Sistema de helpers (security, validation, utils)
- Base de datos completa (12 tablas)
- Datos semilla (~1,400 registros)
- Documentación del proyecto
  - PLAN_DESARROLLO_CHILECHOCADOS.md
  - Historial_Actividades_ChileChocados.md
  - CHANGELOG.md
  - README.md actualizado

#### Cambiado
- Entorno de desarrollo migrado a LAMP local
- Apache configurado en puerto 8080
- Estructura de carpetas organizada

#### Limpieza
- ✅ Carpeta duplicada `public/public/` eliminada
- ✅ Archivos HTML movidos a `_archive/wireframes/` (17 archivos)
- ✅ .gitignore actualizado
- ✅ Branches Git configurados

---

## Próximas Versiones Planificadas

### [0.1.0] - Fase 1: Rediseño de Interfaz
**Fecha estimada:** Semana 1

#### Por hacer
- [ ] Sistema de diseño CSS propio
- [ ] Paleta de colores definitiva
- [ ] Tipografía Inter integrada
- [ ] Iconos Lucide Icons
- [ ] Componentes CSS reutilizables
- [ ] Actualización de layouts
- [ ] Responsive design completo

---

### [0.2.0] - Fase 2: Autenticación
**Fecha estimada:** Semanas 2-3

#### Por hacer
- [ ] Sistema de registro
- [ ] Verificación de email
- [ ] Login/Logout
- [ ] Recuperación de contraseña
- [ ] Edición de perfil
- [ ] Gestión de sesiones

---

### [0.3.0] - Fase 3: Migración HTML → PHP
**Fecha estimada:** Semana 4

#### Por hacer
- [ ] Migrar 11 páginas públicas pendientes
- [ ] Migrar 4 páginas admin pendientes
- [ ] Integrar con controladores
- [ ] Conectar con base de datos
- [ ] Archivar HTML obsoletos

---

### [0.4.0] - Fase 4: Publicaciones
**Fecha estimada:** Semanas 5-6

#### Por hacer
- [ ] CRUD completo de publicaciones
- [ ] Sistema de categorización
- [ ] Upload de fotos (1-6 imágenes)
- [ ] Búsqueda y filtros avanzados
- [ ] Moderación admin
- [ ] Estados de publicación

---

### [0.5.0] - Fase 5: Mensajería y Pagos
**Fecha estimada:** Semanas 7-8

#### Por hacer
- [ ] Sistema de mensajería privada
- [ ] Notificaciones en tiempo real
- [ ] Integración con Flow (pasarela de pagos)
- [ ] Gestión de servicios destacados
- [ ] Historial de pagos

---

### [0.6.0] - Fase 6: Admin y RRSS
**Fecha estimada:** Semanas 9-10

#### Por hacer
- [ ] Panel administrativo completo
- [ ] Gestión de usuarios
- [ ] Gestión de publicaciones
- [ ] Estadísticas y reportes
- [ ] Integración con redes sociales
- [ ] Sistema de compartir

---

### [0.7.0] - Fase 7: Favoritos y Optimización
**Fecha estimada:** Semanas 11-12

#### Por hacer
- [ ] Sistema de favoritos
- [ ] Optimización de rendimiento
- [ ] SEO avanzado
- [ ] Tests automatizados
- [ ] Documentación API
- [ ] Preparación para deploy

---

### [1.0.0] - Lanzamiento Producción
**Fecha estimada:** Semana 13

#### Por hacer
- [ ] Testing completo QA
- [ ] Deploy a producción
- [ ] Monitoreo y analytics
- [ ] Backup automático
- [ ] Plan de mantenimiento

---

## Leyenda

- **Agregado**: Nuevas funcionalidades
- **Cambiado**: Cambios en funcionalidades existentes
- **Deprecado**: Funcionalidades que se eliminarán pronto
- **Eliminado**: Funcionalidades eliminadas
- **Corregido**: Corrección de bugs
- **Seguridad**: Cambios de seguridad

---

**Última actualización:** 23 de Octubre, 2025  
**Mantenido por:** ToroDigital - Andrés Espinoza Bringas
