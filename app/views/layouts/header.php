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
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/favicon.ico">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Skip to content link (accesibilidad) -->
    <a href="#main-content" class="skip-link">Saltar al contenido</a>

    <!-- ======================================================================
         HEADER - Navegación Principal
         ====================================================================== -->
    <header class="site-header">
        <div class="header-container">
            
            <!-- Logo -->
            <div class="header-logo">
                <a href="<?php echo BASE_URL; ?>/" class="logo-link">
                    <span class="logo-icon"><?php echo icon('car', 32); ?></span>
                    <span class="logo-text">
                        <span class="logo-primary">Chile</span><span class="logo-accent">Chocados</span>
                    </span>
                </a>
            </div>
            
            <!-- Buscador (desktop) -->
            <div class="header-search">
                <form action="<?php echo BASE_URL; ?>/buscar" method="GET" class="search-form">
                    <div class="search-input-group">
                        <?php echo icon('search', 20, 'search-icon'); ?>
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="Buscar vehículos, marcas, modelos..."
                            value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                        >
                        <button type="button" class="search-filter-btn" aria-label="Filtros">
                            <?php echo icon('filter', 18); ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Acciones del header -->
            <div class="header-actions">
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Usuario autenticado -->
                    
                    <!-- Notificaciones -->
                    <button class="header-action-btn" aria-label="Notificaciones">
                        <?php echo icon('bell', 22); ?>
                        <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                            <span class="notification-badge"><?php echo $notificationCount; ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Mensajes -->
                    <a href="<?php echo BASE_URL; ?>/mensajes" class="header-action-btn" aria-label="Mensajes">
                        <?php echo icon('message', 22); ?>
                        <?php if (isset($messageCount) && $messageCount > 0): ?>
                            <span class="notification-badge"><?php echo $messageCount; ?></span>
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
                            </div>
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
                            <a href="<?php echo BASE_URL; ?>/configuracion" class="user-menu-item">
                                <?php echo icon('settings', 18); ?>
                                <span>Configuración</span>
                            </a>
                            <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                                <div class="user-menu-divider"></div>
                                <a href="<?php echo BASE_URL; ?>/admin" class="user-menu-item admin-link">
                                    <?php echo icon('shield', 18); ?>
                                    <span>Panel Admin</span>
                                </a>
                            <?php endif; ?>
                            <div class="user-menu-divider"></div>
                            <a href="<?php echo BASE_URL; ?>/logout" class="user-menu-item logout-link">
                                <?php echo icon('logout', 18); ?>
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
            <form action="<?php echo BASE_URL; ?>/buscar" method="GET" class="search-form">
                <div class="search-input-group">
                    <?php echo icon('search', 18, 'search-icon'); ?>
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="Buscar..."
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                    >
                </div>
            </form>
        </div>
        
        <!-- Navegación -->
        <?php require_once APP_PATH . '/views/layouts/nav.php'; ?>
    </header>

    <!-- ======================================================================
         MAIN CONTENT
         ====================================================================== -->
    <main id="main-content" class="site-main">
