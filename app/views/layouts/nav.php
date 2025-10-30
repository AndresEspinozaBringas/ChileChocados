<!-- ======================================================================
     NAVEGACIÓN PRINCIPAL - CONTEXTUAL POR ROL
     ====================================================================== -->
<nav class="main-nav" role="navigation" aria-label="Navegación principal">
    <div class="nav-container">
        <ul class="nav-menu">
            
            <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
                <!-- ============================================
                     NAVEGACIÓN PARA ADMINISTRADOR
                     ============================================ -->
                <?php $adminNotifications = getAdminNotifications(); ?>
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin" class="nav-link <?php echo ($currentPage === 'admin' || $currentPage === 'admin-dashboard') ? 'active' : ''; ?>">
                        <?php echo icon('layout-dashboard', 18); ?>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <!-- Publicaciones con badge -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="nav-link <?php echo ($currentPage === 'admin-publicaciones') ? 'active' : ''; ?>">
                        <?php echo icon('file-text', 18); ?>
                        <span>Publicaciones</span>
                        <?php if ($adminNotifications['publicaciones_pendientes'] > 0): ?>
                            <span class="nav-badge"><?php echo $adminNotifications['publicaciones_pendientes']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Usuarios -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="nav-link <?php echo ($currentPage === 'admin-usuarios') ? 'active' : ''; ?>">
                        <?php echo icon('users', 18); ?>
                        <span>Usuarios</span>
                    </a>
                </li>
                
                <!-- Mensajes con badge -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin/mensajes" class="nav-link <?php echo ($currentPage === 'admin-mensajes') ? 'active' : ''; ?>">
                        <?php echo icon('message-square', 18); ?>
                        <span>Mensajes</span>
                        <?php if ($adminNotifications['mensajes_sin_leer'] > 0): ?>
                            <span class="nav-badge"><?php echo $adminNotifications['mensajes_sin_leer']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Reportes -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin/reportes" class="nav-link <?php echo ($currentPage === 'admin-reportes') ? 'active' : ''; ?>">
                        <?php echo icon('bar-chart-2', 18); ?>
                        <span>Reportes</span>
                    </a>
                </li>
                
                <!-- Separador visual -->
                <li class="nav-item nav-separator"></li>
                
                <!-- Ver sitio público -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/?view=public" class="nav-link nav-link-secondary">
                        <?php echo icon('eye', 18); ?>
                        <span>Ver Sitio</span>
                    </a>
                </li>
                
            <?php else: ?>
                <!-- ============================================
                     NAVEGACIÓN PARA VENDEDOR/USUARIO
                     ============================================ -->
                
                <!-- Inicio -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/" class="nav-link <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                        <?php echo icon('home', 18); ?>
                        <span>Inicio</span>
                    </a>
                </li>
                
                <!-- Explorar -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/categorias" class="nav-link <?php echo ($currentPage === 'categorias') ? 'active' : ''; ?>">
                        <?php echo icon('grid', 18); ?>
                        <span>Explorar</span>
                    </a>
                </li>
                
                <!-- Cómo funciona -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/vender" class="nav-link <?php echo ($currentPage === 'vender') ? 'active' : ''; ?>">
                        <?php echo icon('info', 18); ?>
                        <span>Cómo funciona</span>
                    </a>
                </li>
                
                <!-- Destacados -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/destacados" class="nav-link <?php echo ($currentPage === 'destacados') ? 'active' : ''; ?>">
                        <?php echo icon('star', 18); ?>
                        <span>Destacados</span>
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Favoritos (solo usuarios autenticados) -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/favoritos" class="nav-link <?php echo ($currentPage === 'favoritos') ? 'active' : ''; ?>">
                        <?php echo icon('heart', 18); ?>
                        <span>Favoritos</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Ayuda (con dropdown) -->
                <li class="nav-item has-dropdown">
                    <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                        <?php echo icon('help-circle', 18); ?>
                        <span>Ayuda</span>
                        <?php echo icon('chevron-down', 16, 'chevron-icon'); ?>
                    </button>
                    
                    <div class="nav-dropdown">
                        <div class="nav-dropdown-content">
                            <ul class="nav-dropdown-list">
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/preguntas-frecuentes" class="nav-dropdown-link">
                                        <?php echo icon('help-circle', 20); ?>
                                        <span>Preguntas Frecuentes</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/guia-comprador" class="nav-dropdown-link">
                                        <?php echo icon('bookmark', 20); ?>
                                        <span>Guía del Comprador</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/guia-vendedor" class="nav-dropdown-link">
                                        <?php echo icon('tag', 20); ?>
                                        <span>Guía del Vendedor</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/contacto" class="nav-dropdown-link">
                                        <?php echo icon('mail', 20); ?>
                                        <span>Contacto</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                
            <?php endif; ?>
            
        </ul>
    </div>
</nav>

<!-- ======================================================================
     MENÚ MÓVIL
     ====================================================================== -->
<div class="mobile-menu" role="navigation" aria-label="Menú móvil">
    <div class="mobile-menu-content">
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Usuario autenticado - Perfil móvil -->
            <div class="mobile-menu-user">
                <div class="mobile-user-info">
                    <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                        <img 
                            src="<?php echo BASE_URL . '/uploads/avatars/' . $_SESSION['user_avatar']; ?>" 
                            alt="<?php echo htmlspecialchars($_SESSION['user_nombre']); ?>"
                            class="mobile-user-avatar"
                        >
                    <?php else: ?>
                        <div class="mobile-user-avatar-placeholder">
                            <?php echo substr($_SESSION['user_nombre'], 0, 1); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="mobile-user-name"><?php echo htmlspecialchars($_SESSION['user_nombre']); ?></div>
                        <div class="mobile-user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                            <div class="mobile-user-role-badge">
                                <?php echo icon('shield', 12); ?>
                                <span>Admin</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Enlaces del menú móvil -->
        <ul class="mobile-menu-list">
            <li>
                <a href="<?php echo BASE_URL; ?>/" class="mobile-menu-link">
                    <?php echo icon('home', 22); ?>
                    <span>Inicio</span>
                </a>
            </li>
            
            <li class="mobile-menu-separator">
                <span>Categorías</span>
            </li>
            
            <li>
                <a href="<?php echo BASE_URL; ?>/categoria/autos" class="mobile-menu-link">
                    <?php echo icon('car', 22); ?>
                    <span>Autos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/categoria/camionetas" class="mobile-menu-link">
                    <?php echo icon('truck', 22); ?>
                    <span>Camionetas</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/categoria/motos" class="mobile-menu-link">
                    <?php echo icon('bike', 22); ?>
                    <span>Motos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/categorias" class="mobile-menu-link">
                    <?php echo icon('grid', 22); ?>
                    <span>Ver todas</span>
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                    <!-- MENÚ MÓVIL PARA ADMINISTRADOR -->
                    <?php $adminNotifications = getAdminNotifications(); ?>
                    <li class="mobile-menu-separator">
                        <span>Panel de Administración</span>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin" class="mobile-menu-link admin-link">
                            <?php echo icon('layout-dashboard', 22); ?>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="mobile-menu-link admin-link">
                            <?php echo icon('file-text', 22); ?>
                            <span>Publicaciones</span>
                            <?php if ($adminNotifications['publicaciones_pendientes'] > 0): ?>
                                <span class="menu-badge"><?php echo $adminNotifications['publicaciones_pendientes']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="mobile-menu-link admin-link">
                            <?php echo icon('users', 22); ?>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/mensajes" class="mobile-menu-link admin-link">
                            <?php echo icon('message-square', 22); ?>
                            <span>Mensajes</span>
                            <?php if ($adminNotifications['mensajes_sin_leer'] > 0): ?>
                                <span class="menu-badge"><?php echo $adminNotifications['mensajes_sin_leer']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="mobile-menu-separator">
                        <span>Mi Cuenta</span>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/perfil" class="mobile-menu-link">
                            <?php echo icon('user', 22); ?>
                            <span>Mi Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="mobile-menu-link">
                            <?php echo icon('list', 22); ?>
                            <span>Mis Publicaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/mensajes" class="mobile-menu-link">
                            <?php echo icon('message', 22); ?>
                            <span>Mensajes Personales</span>
                            <?php if (isset($messageCount) && $messageCount > 0): ?>
                                <span class="mobile-menu-badge"><?php echo $messageCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/configuracion" class="mobile-menu-link">
                            <?php echo icon('settings', 22); ?>
                            <span>Configuración</span>
                        </a>
                    </li>
                    
                    <li class="mobile-menu-separator"></li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/?view=public" class="mobile-menu-link">
                            <?php echo icon('eye', 22); ?>
                            <span>Ver Sitio Público</span>
                        </a>
                    </li>
                <?php else: ?>
                    <!-- MENÚ MÓVIL PARA VENDEDOR/USUARIO -->
                    <li class="mobile-menu-separator">
                        <span>Mi Cuenta</span>
                    </li>
                    
                    <li>
                        <a href="<?php echo BASE_URL; ?>/perfil" class="mobile-menu-link">
                            <?php echo icon('user', 22); ?>
                            <span>Mi Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="mobile-menu-link">
                            <?php echo icon('list', 22); ?>
                            <span>Mis Publicaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/mensajes" class="mobile-menu-link">
                            <?php echo icon('message', 22); ?>
                            <span>Mensajes</span>
                            <?php if (isset($messageCount) && $messageCount > 0): ?>
                                <span class="mobile-menu-badge"><?php echo $messageCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/favoritos" class="mobile-menu-link">
                            <?php echo icon('heart', 22); ?>
                            <span>Favoritos</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            
            <li class="mobile-menu-separator">
                <span>Información</span>
            </li>
            
            <li>
                <a href="<?php echo BASE_URL; ?>/vender" class="mobile-menu-link">
                    <?php echo icon('info', 22); ?>
                    <span>Cómo funciona</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/preguntas-frecuentes" class="mobile-menu-link">
                    <?php echo icon('help-circle', 22); ?>
                    <span>Preguntas Frecuentes</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/contacto" class="mobile-menu-link">
                    <?php echo icon('mail', 22); ?>
                    <span>Contacto</span>
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="mobile-menu-separator"></li>
                
                <li>
                    <a href="<?php echo BASE_URL; ?>/logout" class="mobile-menu-link logout-link">
                        <?php echo icon('log-out', 22); ?>
                        <span>Cerrar Sesión</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="mobile-menu-separator"></li>
                
                <li>
                    <a href="<?php echo BASE_URL; ?>/login" class="mobile-menu-link">
                        <?php echo icon('login', 22); ?>
                        <span>Ingresar</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/registro" class="mobile-menu-link primary-link">
                        <?php echo icon('user-circle', 22); ?>
                        <span>Registrarse</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="mobile-menu-overlay"></div>

<!-- ======================================================================
     ESTILOS ADICIONALES PARA MENÚ MEJORADO
     ====================================================================== -->
<style>
    /* Badge de rol en menú móvil */
    .mobile-user-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        padding: 3px 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 10px;
        font-weight: 600;
        border-radius: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .mobile-user-role-badge svg {
        width: 12px;
        height: 12px;
    }
    
    /* Estilo para enlaces admin en móvil */
    .mobile-menu-link.admin-link {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.08) 0%, transparent 100%);
        border-left: 3px solid #667eea;
        padding-left: 13px;
    }
    
    /* Badge de notificaciones en navegación principal */
    .nav-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        background: #E6332A;
        color: white;
        font-size: 10px;
        font-weight: 700;
        border-radius: 9px;
        margin-left: 6px;
        line-height: 1;
        animation: pulse-badge 2s ease-in-out infinite;
    }
    
    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    /* Separador visual en navegación */
    .nav-separator {
        width: 1px;
        height: 24px;
        background: rgba(0, 0, 0, 0.1);
        margin: 0 8px;
        pointer-events: none;
    }
    
    [data-theme="dark"] .nav-separator {
        background: rgba(255, 255, 255, 0.1);
    }
    
    /* Estilo secundario para "Ver Sitio" */
    .nav-link-secondary {
        color: #667eea !important;
        font-weight: 500;
    }
    
    .nav-link-secondary:hover {
        background: rgba(102, 126, 234, 0.1) !important;
    }
</style>

<!-- ======================================================================
     SCRIPTS DE NAVEGACIÓN
     ====================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle mobile menu
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const body = document.body;
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            const isOpen = body.classList.toggle('mobile-menu-open');
            mobileMenuToggle.setAttribute('aria-expanded', isOpen);
        });
        
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', function() {
                body.classList.remove('mobile-menu-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            });
        }
    }
    
    // Dropdown navigation
    const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
    
    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const navItem = this.closest('.nav-item');
            const isOpen = navItem.classList.toggle('dropdown-open');
            this.setAttribute('aria-expanded', isOpen);
            
            // Cerrar otros dropdowns
            dropdownTriggers.forEach(otherTrigger => {
                if (otherTrigger !== trigger) {
                    const otherNavItem = otherTrigger.closest('.nav-item');
                    otherNavItem.classList.remove('dropdown-open');
                    otherTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
    
    // Cerrar dropdowns al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-item')) {
            document.querySelectorAll('.nav-item.dropdown-open').forEach(item => {
                item.classList.remove('dropdown-open');
                const trigger = item.querySelector('.dropdown-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            });
        }
    });
    
    // User menu dropdown
    const userMenuTrigger = document.querySelector('.user-menu-trigger');
    const userMenuWrapper = document.querySelector('.user-menu-wrapper');
    
    if (userMenuTrigger && userMenuWrapper) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            userMenuWrapper.classList.toggle('menu-open');
        });
        
        // Cerrar user menu al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu-wrapper')) {
                userMenuWrapper.classList.remove('menu-open');
            }
        });
    }
    
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
