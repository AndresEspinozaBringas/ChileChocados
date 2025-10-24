<!-- ======================================================================
     NAVEGACIÓN PRINCIPAL
     ====================================================================== -->
<nav class="main-nav" role="navigation" aria-label="Navegación principal">
    <div class="nav-container">
        <ul class="nav-menu">
            
            <!-- Inicio -->
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/" class="nav-link <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                    <?php echo icon('home', 18); ?>
                    <span>Inicio</span>
                </a>
            </li>
            
            <!-- Explorar (con dropdown) -->
            <li class="nav-item has-dropdown">
                <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                    <?php echo icon('grid', 18); ?>
                    <span>Explorar</span>
                    <?php echo icon('chevron-down', 16, 'chevron-icon'); ?>
                </button>
                
                <div class="nav-dropdown">
                    <div class="nav-dropdown-content">
                        <div class="nav-dropdown-section">
                            <h4 class="nav-dropdown-title">Por Categoría</h4>
                            <ul class="nav-dropdown-list">
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/autos" class="nav-dropdown-link">
                                        <?php echo icon('car', 20); ?>
                                        <div>
                                            <div class="link-title">Autos</div>
                                            <div class="link-meta">Sedan • SUV • Hatchback</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/camionetas" class="nav-dropdown-link">
                                        <?php echo icon('truck', 20); ?>
                                        <div>
                                            <div class="link-title">Camionetas</div>
                                            <div class="link-meta">Pickup • Van • 4x4</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/motos" class="nav-dropdown-link">
                                        <?php echo icon('bike', 20); ?>
                                        <div>
                                            <div class="link-title">Motos</div>
                                            <div class="link-meta">Scooter • Deportiva • Touring</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/camiones" class="nav-dropdown-link">
                                        <?php echo icon('truck', 20); ?>
                                        <div>
                                            <div class="link-title">Camiones</div>
                                            <div class="link-meta">Liviano • Mediano • Pesado</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="nav-dropdown-divider"></div>
                        
                        <div class="nav-dropdown-section">
                            <h4 class="nav-dropdown-title">Más Categorías</h4>
                            <ul class="nav-dropdown-list">
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/bus" class="nav-dropdown-link">
                                        <?php echo icon('bus', 20); ?>
                                        <span>Bus</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/nauticos" class="nav-dropdown-link">
                                        <?php echo icon('boat', 20); ?>
                                        <span>Náuticos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/aereos" class="nav-dropdown-link">
                                        <?php echo icon('plane', 20); ?>
                                        <span>Aéreos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>/categoria/maquinaria" class="nav-dropdown-link">
                                        <?php echo icon('settings', 20); ?>
                                        <span>Maquinaria</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="nav-dropdown-footer">
                            <a href="<?php echo BASE_URL; ?>/categorias" class="nav-dropdown-btn">
                                Ver todas las categorías
                                <?php echo icon('arrow-right', 16); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            
            <!-- Cómo funciona -->
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/como-funciona" class="nav-link <?php echo ($currentPage === 'como-funciona') ? 'active' : ''; ?>">
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
            
            <!-- Ayuda (con dropdown) -->
            <li class="nav-item has-dropdown">
                <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                    <?php echo icon('help', 18); ?>
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
            
            <li class="mobile-menu-separator">
                <span>Información</span>
            </li>
            
            <li>
                <a href="<?php echo BASE_URL; ?>/como-funciona" class="mobile-menu-link">
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
                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                    <li class="mobile-menu-separator">
                        <span>Administración</span>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin" class="mobile-menu-link admin-link">
                            <?php echo icon('shield', 22); ?>
                            <span>Panel Admin</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="mobile-menu-separator"></li>
                
                <li>
                    <a href="<?php echo BASE_URL; ?>/logout" class="mobile-menu-link logout-link">
                        <?php echo icon('logout', 22); ?>
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
