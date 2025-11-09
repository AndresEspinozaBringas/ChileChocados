<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO -->
    <title><?php echo $pageTitle ?? 'ChileChocados - Marketplace de Bienes Siniestrados'; ?></title>
    <meta name="description" content="<?php echo $pageDescription ?? 'Compra y vende vehículos siniestrados en Chile. Miles de oportunidades en autos, motos, camiones y más.'; ?>">
    <meta name="keywords" content="<?php echo $pageKeywords ?? 'vehiculos siniestrados, autos chocados, compra venta, chile, marketplace'; ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo $pageTitle ?? 'ChileChocados'; ?>">
    <meta property="og:description" content="<?php echo $pageDescription ?? 'Marketplace de bienes siniestrados'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Preconnect a dominios externos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- CSS - Sistema de Diseño ChileChocados -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/design-system.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/typography.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/fixes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/override.css?v=<?php echo time(); ?>">
    
    <!-- CRITICAL FIXES - Se carga al final con máxima prioridad -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/critical-fixes.css?v=<?php echo time(); ?>">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/iso.png">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Sistema de notificaciones en tiempo real (solo para admins) -->
    <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
        <script src="<?php echo BASE_URL; ?>/assets/js/admin-notifications.js"></script>
    <?php endif; ?>
    
    <!-- Estilos para badges de notificación y menú mejorado -->
    <style>
        /* Badge de notificaciones y mensajes en el header */
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: #E6332A;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9px;
            line-height: 1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .header-action-btn {
            position: relative;
        }
        
        .menu-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: #E6332A;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 10px;
            margin-left: auto;
            line-height: 1;
        }
        
        .user-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        .user-menu-item .menu-badge {
            position: absolute;
            right: 12px;
        }
        
        .mobile-menu-link .menu-badge {
            margin-left: auto;
        }
        
        /* Badge de rol en el header del menú */
        .user-menu-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            padding: 4px 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .user-menu-role-badge svg {
            width: 14px;
            height: 14px;
        }
        
        /* Títulos de sección en el menú */
        .user-menu-section-title {
            padding: 8px 16px 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            opacity: 0.7;
        }
        
        /* Estilo especial para enlaces admin */
        .user-menu-item.admin-link {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
        }
        
        .user-menu-item.admin-link:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
        }
        
        /* Estilo para "Ver Sitio Público" */
        .user-menu-item:has(svg[data-lucide="eye"]) {
            color: #667eea;
            font-weight: 500;
        }
        
        /* Indicador de rol en el header (junto al logo) */
        .header-role-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 12px;
            padding: 4px 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            animation: subtle-glow 3s ease-in-out infinite;
        }
        
        .header-role-indicator svg {
            width: 14px;
            height: 14px;
        }
        
        @keyframes subtle-glow {
            0%, 100% { box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); }
            50% { box-shadow: 0 2px 12px rgba(102, 126, 234, 0.5); }
        }
        
        /* Responsive: ocultar en móvil */
        @media (max-width: 768px) {
            .header-role-indicator {
                display: none;
            }
        }
        
        /* Estilos del dropdown de notificaciones */
        .notificaciones-dropdown {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 400px;
            max-height: 500px;
            background: var(--cc-bg-surface, #fff);
            border: 1px solid var(--cc-border-default, #E5E7EB);
            border-radius: var(--cc-radius-xl, 12px);
            box-shadow: var(--cc-shadow-2xl, 0 20px 25px -5px rgba(0, 0, 0, 0.1));
            z-index: 10000;
            display: flex;
            flex-direction: column;
        }
        
        .notificaciones-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--cc-border-light, #E5E7EB);
        }
        
        .notificaciones-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--cc-text-primary, #111827);
        }
        
        .btn-text {
            background: none;
            border: none;
            color: var(--cc-primary, #E6332A);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .btn-text:hover {
            background: var(--cc-primary-pale, #FEF2F2);
        }
        
        .notificaciones-body {
            overflow-y: auto;
            max-height: 400px;
        }
        
        .notificacion-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--cc-border-light, #E5E7EB);
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .notificacion-item:hover {
            background: var(--cc-bg-muted, #F9FAFB);
        }
        
        .notificacion-item.no-leida {
            background: var(--cc-primary-pale, #FEF2F2);
        }
        
        .notificacion-titulo {
            font-weight: 600;
            font-size: 14px;
            color: var(--cc-text-primary, #111827);
            margin-bottom: 4px;
        }
        
        .notificacion-mensaje {
            font-size: 13px;
            color: var(--cc-text-secondary, #6B7280);
            margin-bottom: 4px;
        }
        
        .notificacion-fecha {
            font-size: 12px;
            color: var(--cc-text-tertiary, #9CA3AF);
        }
        
        .notificaciones-loading,
        .notificaciones-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--cc-text-secondary, #6B7280);
        }
        
        /* Dark mode */
        :root[data-theme="dark"] .notificaciones-dropdown {
            background: var(--cc-gray-800, #1F2937);
            border-color: var(--cc-gray-700, #374151);
        }
        
        :root[data-theme="dark"] .notificaciones-header {
            border-bottom-color: var(--cc-gray-700, #374151);
        }
        
        :root[data-theme="dark"] .notificaciones-header h3 {
            color: var(--cc-gray-100, #F3F4F6);
        }
        
        :root[data-theme="dark"] .notificacion-item {
            border-bottom-color: var(--cc-gray-700, #374151);
        }
        
        :root[data-theme="dark"] .notificacion-item:hover {
            background: var(--cc-gray-700, #374151);
        }
        
        :root[data-theme="dark"] .notificacion-item.no-leida {
            background: rgba(230, 51, 42, 0.15);
        }
        
        :root[data-theme="dark"] .notificacion-titulo {
            color: var(--cc-gray-100, #F3F4F6);
        }
        
        :root[data-theme="dark"] .notificacion-mensaje {
            color: var(--cc-gray-400, #9CA3AF);
        }
        
        @media (max-width: 768px) {
            .notificaciones-dropdown {
                right: 10px;
                left: 10px;
                width: auto;
            }
        }
    </style>
    
    <script>
        // Sistema de notificaciones
        function toggleNotificaciones() {
            <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
                // Si es admin, redirigir a publicaciones pendientes
                window.location.href = '<?php echo BASE_URL; ?>/admin/publicaciones?estado=pendiente';
                return;
            <?php endif; ?>
            
            const dropdown = document.getElementById('notificaciones-dropdown');
            const isVisible = dropdown.style.display !== 'none';
            
            if (isVisible) {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
                cargarNotificaciones();
            }
        }
        
        function cargarNotificaciones() {
            const lista = document.getElementById('notificaciones-lista');
            lista.innerHTML = '<div class="notificaciones-loading">Cargando...</div>';
            
            fetch('<?php echo BASE_URL; ?>/api/notificaciones')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notificaciones.length > 0) {
                        lista.innerHTML = '';
                        data.notificaciones.forEach(notif => {
                            const item = document.createElement('div');
                            item.className = 'notificacion-item' + (notif.leida == 0 ? ' no-leida' : '');
                            item.onclick = () => abrirNotificacion(notif.id, notif.enlace);
                            
                            item.innerHTML = `
                                <div class="notificacion-titulo">${notif.titulo}</div>
                                <div class="notificacion-mensaje">${notif.mensaje}</div>
                                <div class="notificacion-fecha">${formatearFecha(notif.fecha_creacion)}</div>
                            `;
                            
                            lista.appendChild(item);
                        });
                    } else {
                        lista.innerHTML = '<div class="notificaciones-empty">No tienes notificaciones</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar notificaciones:', error);
                    lista.innerHTML = '<div class="notificaciones-empty">Error al cargar notificaciones</div>';
                });
        }
        
        function abrirNotificacion(id, enlace) {
            // Marcar como leída
            fetch('<?php echo BASE_URL; ?>/api/notificaciones/marcar-leida', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: id})
            }).then(() => {
                actualizarContador();
                if (enlace) {
                    window.location.href = '<?php echo BASE_URL; ?>' + enlace;
                }
            });
        }
        
        function marcarTodasLeidas() {
            fetch('<?php echo BASE_URL; ?>/api/notificaciones/marcar-todas-leidas', {
                method: 'POST'
            }).then(() => {
                actualizarContador();
                cargarNotificaciones();
            });
        }
        
        function actualizarContador() {
            // Actualizar contador de notificaciones
            fetch('<?php echo BASE_URL; ?>/api/notificaciones/contar')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-count');
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count;
                        } else {
                            const btn = document.getElementById('btn-notificaciones');
                            if (btn) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'notification-badge';
                                newBadge.id = 'notification-count';
                                newBadge.textContent = data.count;
                                btn.appendChild(newBadge);
                            }
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                })
                .catch(error => {
                    console.error('Error al actualizar contador de notificaciones:', error);
                });
            
            // Actualizar contador de mensajes
            actualizarContadorMensajes();
        }
        
        // Versión silenciosa que no interfiere con formularios
        function actualizarContadorSilencioso() {
            // No actualizar si las actualizaciones están pausadas
            if (window.pauseAutoUpdates || window.isPublishPage) {
                return;
            }
            
            // Solo actualizar si no hay elementos de formulario activos
            const activeElement = document.activeElement;
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'SELECT')) {
                return;
            }
            actualizarContador();
        }
        
        function actualizarContadorMensajes() {
            // No actualizar si el usuario está interactuando con un formulario
            const activeElement = document.activeElement;
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'SELECT')) {
                return;
            }
            
            fetch('<?php echo BASE_URL; ?>/api/mensajes/contar')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('message-count');
                    const link = document.querySelector('a[href*="/mensajes"]');
                    
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count;
                        } else if (link) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.id = 'message-count';
                            newBadge.textContent = data.count;
                            link.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                })
                .catch(error => {
                    console.error('Error al actualizar contador de mensajes:', error);
                });
        }
        
        function formatearFecha(fecha) {
            const ahora = new Date();
            const fechaNotif = new Date(fecha);
            const diff = Math.floor((ahora - fechaNotif) / 1000);
            
            if (diff < 60) return 'Hace un momento';
            if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
            if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
            if (diff < 604800) return `Hace ${Math.floor(diff / 86400)} días`;
            
            return fechaNotif.toLocaleDateString('es-CL');
        }
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificaciones-dropdown');
            const btn = document.getElementById('btn-notificaciones');
            
            if (dropdown && btn && !dropdown.contains(event.target) && !btn.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
        
        // Actualizar contadores cada 30 segundos (menos intrusivo)
        setInterval(actualizarContadorSilencioso, 30000);
        
        // Cargar contadores al inicio
        document.addEventListener('DOMContentLoaded', function() {
            actualizarContadorMensajes();
            actualizarContador();
        });
        
        // Hacer las funciones globales para que puedan ser llamadas desde otras páginas
        window.actualizarContadorMensajes = actualizarContadorMensajes;
        window.actualizarContadorNotificaciones = actualizarContador;
        
        // Escuchar eventos personalizados de mensajes
        window.addEventListener('mensajeEnviado', function() {
            // Actualizar contador cuando se envía un mensaje
            setTimeout(actualizarContadorMensajes, 500);
        });
        
        window.addEventListener('mensajeRecibido', function() {
            // Actualizar contador cuando se recibe un mensaje
            actualizarContadorMensajes();
        });
        
        // ============================================
        // SISTEMA DE NOTIFICACIONES TOAST EN TIEMPO REAL
        // ============================================
        <?php if (isset($_SESSION['user_id']) && (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin')): ?>
        let ultimaNotificacionId = 0;
        
        // Función para verificar nuevas notificaciones
        function verificarNuevasNotificaciones() {
            // No ejecutar si las actualizaciones están pausadas
            if (window.pauseAutoUpdates || window.isPublishPage) {
                return;
            }
            
            // No ejecutar si el usuario está escribiendo o interactuando con un formulario
            const activeElement = document.activeElement;
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'SELECT')) {
                return; // Salir sin hacer nada si hay un campo activo
            }
            
            fetch('<?php echo BASE_URL; ?>/api/notificaciones?desde=' + ultimaNotificacionId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notificaciones && data.notificaciones.length > 0) {
                        data.notificaciones.forEach(notif => {
                            // Solo mostrar notificaciones no leídas
                            if (notif.leida == 0) {
                                mostrarToastNotificacion(notif);
                            }
                            // Actualizar último ID
                            if (notif.id > ultimaNotificacionId) {
                                ultimaNotificacionId = notif.id;
                            }
                        });
                        // Actualizar contador solo si no hay formularios activos
                        actualizarContadorSilencioso();
                    }
                })
                .catch(error => {
                    console.error('Error al verificar notificaciones:', error);
                });
        }
        
        // Función para mostrar toast de notificación
        function mostrarToastNotificacion(notif) {
            // Crear contenedor de toasts si no existe
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 12px; max-width: 400px;';
                document.body.appendChild(container);
            }
            
            // Crear toast
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.style.cssText = `
                background: white;
                border-left: 4px solid ${notif.tipo === 'publicacion_aprobada' ? '#10B981' : '#EF4444'};
                border-radius: 8px;
                padding: 16px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                cursor: pointer;
                animation: slideInRight 0.3s ease-out;
                min-width: 350px;
            `;
            
            const iconColor = notif.tipo === 'publicacion_aprobada' ? '#10B981' : '#EF4444';
            const icon = notif.tipo === 'publicacion_aprobada' ? 
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>' :
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
            
            toast.innerHTML = `
                <div style="display: flex; gap: 12px; align-items: start;">
                    <div style="color: ${iconColor}; flex-shrink: 0;">
                        ${icon}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 14px; color: #111827; margin-bottom: 4px;">
                            ${notif.titulo}
                        </div>
                        <div style="font-size: 13px; color: #6B7280;">
                            ${notif.mensaje}
                        </div>
                    </div>
                    <button onclick="cerrarToast(this)" style="background: none; border: none; color: #9CA3AF; cursor: pointer; padding: 0; font-size: 20px; line-height: 1;">×</button>
                </div>
            `;
            
            // Click para ir al enlace
            toast.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    if (notif.enlace) {
                        window.location.href = '<?php echo BASE_URL; ?>' + notif.enlace;
                    }
                }
            });
            
            container.appendChild(toast);
            
            // Auto-cerrar después de 8 segundos
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 8000);
        }
        
        // Función para cerrar toast
        window.cerrarToast = function(btn) {
            const toast = btn.closest('.notification-toast');
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        };
        
        // Agregar animaciones CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
            
            .notification-toast:hover {
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }
            
            @media (max-width: 640px) {
                #toast-container {
                    right: 10px;
                    left: 10px;
                    max-width: none;
                }
                
                .notification-toast {
                    min-width: auto !important;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Verificar nuevas notificaciones cada 15 segundos (menos intrusivo)
        setInterval(verificarNuevasNotificaciones, 15000);
        
        // Verificar al cargar la página (solo si no estamos en formulario de publicar)
        setTimeout(function() {
            const isPublishPage = window.location.pathname.includes('/publicar');
            if (!isPublishPage) {
                verificarNuevasNotificaciones();
            }
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>
    <!-- Skip to content link (accesibilidad) -->
    <a href="#main-content" class="skip-link">Saltar al contenido</a>

    <!-- ======================================================================
         HEADER - Navegación Principal
         ====================================================================== -->
    <?php if (!isset($hideNav) || !$hideNav): ?>
    <header class="site-header">
        <div class="header-container">
            
            <!-- Logo -->
            <div class="header-logo">
                <a href="<?php echo BASE_URL; ?>/" class="logo-link">
                    <img src="<?php echo BASE_URL; ?>/assets/logo-chch.svg" alt="ChileChocados" class="logo-image" style="height: 40px; width: auto;">
                </a>
                <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
                    <span class="header-role-indicator">
                        <?php echo icon('shield', 14); ?>
                        <span>Admin</span>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Buscador (desktop) -->
            <div class="header-search">
                <form action="<?php echo BASE_URL; ?>/listado" method="GET" class="search-form">
                    <div class="search-input-group">
                        <?php echo icon('search', 20, 'search-icon'); ?>
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="Buscar vehículos, marcas, modelos..."
                            value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                            style="border: none; outline: none;"
                        >
                        <button type="submit" class="search-filter-btn" aria-label="Buscar">
                            <?php echo icon('search', 18); ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Acciones del header -->
            <div class="header-actions">
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Usuario autenticado -->
                    
                    <!-- Notificaciones -->
                    <?php 
                    $notificationCount = getNotificationCount();
                    $messageCount = getMessageCount();
                    ?>
                    <button class="header-action-btn" aria-label="Notificaciones" id="btn-notificaciones" onclick="toggleNotificaciones()">
                        <?php echo icon('bell', 22); ?>
                        <?php if ($notificationCount > 0): ?>
                            <span class="notification-badge" id="notification-count"><?php echo $notificationCount; ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Mensajes -->
                    <a href="<?php echo BASE_URL; ?>/mensajes" class="header-action-btn" aria-label="Mensajes">
                        <?php echo icon('message', 22); ?>
                        <?php if ($messageCount > 0): ?>
                            <span class="notification-badge" id="message-count"><?php echo $messageCount; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Botón Publicar -->
                    <a href="<?php echo BASE_URL; ?>/publicar" class="btn btn-primary">
                        <?php echo icon('plus', 18); ?>
                        <span class="btn-text">Publicar</span>
                    </a>
                    
                    <!-- Menú de usuario -->
                    <div class="user-menu-wrapper">
                        <button class="user-menu-trigger" aria-label="Menú de usuario">
                            <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                                <img 
                                    src="<?php echo BASE_URL . '/uploads/avatars/' . $_SESSION['user_avatar']; ?>" 
                                    alt="<?php echo htmlspecialchars($_SESSION['user_nombre']); ?>"
                                    class="user-avatar"
                                >
                            <?php else: ?>
                                <div class="user-avatar-placeholder">
                                    <?php echo substr($_SESSION['user_nombre'], 0, 1); ?>
                                </div>
                            <?php endif; ?>
                            <?php echo icon('chevron-down', 16, 'chevron-icon'); ?>
                        </button>
                        
                        <div class="user-menu-dropdown">
                            <div class="user-menu-header">
                                <div class="user-menu-name"><?php echo htmlspecialchars($_SESSION['user_nombre']); ?></div>
                                <div class="user-menu-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                                    <div class="user-menu-role-badge">
                                        <?php echo icon('shield', 14); ?>
                                        <span>Administrador</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                                <!-- MENÚ PARA ADMINISTRADOR -->
                                <?php $adminNotifications = getAdminNotifications(); ?>
                                <div class="user-menu-divider"></div>
                                <div class="user-menu-section-title">Panel de Administración</div>
                                <a href="<?php echo BASE_URL; ?>/admin" class="user-menu-item admin-link">
                                    <?php echo icon('layout-dashboard', 18); ?>
                                    <span>Dashboard</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="user-menu-item admin-link">
                                    <?php echo icon('file-text', 18); ?>
                                    <span>Publicaciones</span>
                                    <?php if ($adminNotifications['publicaciones_pendientes'] > 0): ?>
                                        <span class="menu-badge"><?php echo $adminNotifications['publicaciones_pendientes']; ?></span>
                                    <?php endif; ?>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="user-menu-item admin-link">
                                    <?php echo icon('users', 18); ?>
                                    <span>Usuarios</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/mensajes" class="user-menu-item admin-link">
                                    <?php echo icon('message-square', 18); ?>
                                    <span>Mensajes</span>
                                    <?php if ($adminNotifications['mensajes_sin_leer'] > 0): ?>
                                        <span class="menu-badge"><?php echo $adminNotifications['mensajes_sin_leer']; ?></span>
                                    <?php endif; ?>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/reportes" class="user-menu-item admin-link">
                                    <?php echo icon('bar-chart', 18); ?>
                                    <span>Reportes</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/configuracion" class="user-menu-item admin-link">
                                    <?php echo icon('settings', 18); ?>
                                    <span>Configuración</span>
                                </a>
                                
                                <div class="user-menu-divider"></div>
                                <div class="user-menu-section-title">Mi Cuenta</div>
                                <a href="<?php echo BASE_URL; ?>/perfil" class="user-menu-item">
                                    <?php echo icon('user', 18); ?>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="user-menu-item">
                                    <?php echo icon('list', 18); ?>
                                    <span>Mis Publicaciones</span>
                                </a>
                                
                                <div class="user-menu-divider"></div>
                                <a href="<?php echo BASE_URL; ?>/?view=public" class="user-menu-item">
                                    <?php echo icon('eye', 18); ?>
                                    <span>Ver Sitio Público</span>
                                </a>
                            <?php else: ?>
                                <!-- MENÚ PARA VENDEDOR/USUARIO -->
                                <div class="user-menu-divider"></div>
                                <a href="<?php echo BASE_URL; ?>/perfil" class="user-menu-item">
                                    <?php echo icon('user', 18); ?>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="user-menu-item">
                                    <?php echo icon('list', 18); ?>
                                    <span>Mis Publicaciones</span>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/favoritos" class="user-menu-item">
                                    <?php echo icon('heart', 18); ?>
                                    <span>Favoritos</span>
                                </a>
                            <?php endif; ?>
                            
                            <div class="user-menu-divider"></div>
                            <a href="<?php echo BASE_URL; ?>/logout" class="user-menu-item logout-link">
                                <?php echo icon('log-out', 18); ?>
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Usuario no autenticado -->
                    <a href="<?php echo BASE_URL; ?>/login" class="btn btn-ghost">
                        <?php echo icon('login', 18); ?>
                        <span class="btn-text">Ingresar</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/registro" class="btn btn-primary">
                        <span class="btn-text">Registrarse</span>
                    </a>
                <?php endif; ?>
                
                <!-- Toggle modo oscuro -->
                <button class="header-action-btn theme-toggle" aria-label="Cambiar tema">
                    <span class="theme-icon-light"><?php echo icon('sun', 22); ?></span>
                    <span class="theme-icon-dark"><?php echo icon('moon', 22); ?></span>
                </button>
                
                <!-- Menú móvil toggle -->
                <button class="mobile-menu-toggle" aria-label="Menú" aria-expanded="false">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Buscador móvil -->
        <div class="mobile-search">
            <form action="<?php echo BASE_URL; ?>/listado" method="GET" class="search-form">
                <div class="search-input-group">
                    <?php echo icon('search', 18, 'search-icon'); ?>
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="Buscar..."
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                        style="border: none; outline: none;"
                    >
                </div>
            </form>
        </div>
        
        <!-- Navegación -->
        <?php require_once APP_PATH . '/views/layouts/nav.php'; ?>
        
        <!-- Dropdown de Notificaciones -->
        <div id="notificaciones-dropdown" class="notificaciones-dropdown" style="display: none;">
            <div class="notificaciones-header">
                <h3>Notificaciones</h3>
                <button onclick="marcarTodasLeidas()" class="btn-text">Marcar todas como leídas</button>
            </div>
            <div class="notificaciones-body" id="notificaciones-lista">
                <div class="notificaciones-loading">Cargando...</div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <!-- ======================================================================
         MAIN CONTENT
         ====================================================================== -->
    <main id="main-content" class="site-main">
