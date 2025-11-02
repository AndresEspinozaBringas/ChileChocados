    </main>

    <!-- ======================================================================
         FOOTER
         ====================================================================== -->
    <?php
    // Cargar configuraciones para el footer
    require_once APP_PATH . '/models/Configuracion.php';
    $footerConfig = \App\Models\Configuracion::getAllWithDefaults();
    ?>
    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-container">
                
                <!-- Columna 1: Marca -->
                <div class="footer-col footer-col-brand">
                    <div class="footer-logo">
                        <a href="<?php echo BASE_URL; ?>/">
                            <?php echo icon('car', 32, 'footer-logo-icon'); ?>
                            <span class="footer-logo-text">
                                <span class="logo-primary">Chile</span><span class="logo-accent">Chocados</span>
                            </span>
                        </a>
                    </div>
                    <p class="footer-description">
                        El marketplace líder en Chile para la compra y venta de vehículos siniestrados. 
                        Conectamos compradores y vendedores de manera segura y transparente.
                    </p>
                    <div class="footer-social">
                        <?php if (!empty($footerConfig['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($footerConfig['facebook_url']); ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
                            <?php echo icon('facebook', 24); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($footerConfig['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($footerConfig['instagram_url']); ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
                            <?php echo icon('instagram', 24); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($footerConfig['twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($footerConfig['twitter_url']); ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Twitter">
                            <?php echo icon('twitter', 24); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($footerConfig['youtube_url'])): ?>
                        <a href="<?php echo htmlspecialchars($footerConfig['youtube_url']); ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="YouTube">
                            <?php echo icon('youtube', 24); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Columna 2: Enlaces rápidos -->
                <div class="footer-col">
                    <h3 class="footer-title">Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="<?php echo BASE_URL; ?>/como-funciona">
                                <?php echo icon('info', 16); ?>
                                <span>Cómo funciona</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/categorias">
                                <?php echo icon('grid', 16); ?>
                                <span>Categorías</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/destacados">
                                <?php echo icon('star', 16); ?>
                                <span>Destacados</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/preguntas-frecuentes">
                                <?php echo icon('help-circle', 16); ?>
                                <span>Preguntas Frecuentes</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Columna 3: Soporte -->
                <div class="footer-col">
                    <h3 class="footer-title">Soporte</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="<?php echo BASE_URL; ?>/contacto">
                                <?php echo icon('mail', 16); ?>
                                <span>Contacto</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/guia-comprador">
                                <?php echo icon('bookmark', 16); ?>
                                <span>Guía del Comprador</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/guia-vendedor">
                                <?php echo icon('tag', 16); ?>
                                <span>Guía del Vendedor</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/seguridad">
                                <?php echo icon('shield', 16); ?>
                                <span>Consejos de Seguridad</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Columna 4: Legal -->
                <div class="footer-col">
                    <h3 class="footer-title">Legal</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="<?php echo BASE_URL; ?>/terminos">
                                <?php echo icon('file', 16); ?>
                                <span>Términos y Condiciones</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/privacidad">
                                <?php echo icon('lock', 16); ?>
                                <span>Política de Privacidad</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/cookies">
                                <?php echo icon('settings', 16); ?>
                                <span>Política de Cookies</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/denuncias">
                                <?php echo icon('alert-circle', 16); ?>
                                <span>Canal de Denuncias</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Columna 5: Contacto -->
                <div class="footer-col">
                    <h3 class="footer-title">Contacto</h3>
                    <ul class="footer-contact">
                        <li>
                            <?php echo icon('mail', 18); ?>
                            <div>
                                <div class="contact-label">Email</div>
                                <a href="mailto:<?php echo htmlspecialchars($footerConfig['sitio_email']); ?>" class="contact-value">
                                    <?php echo htmlspecialchars($footerConfig['sitio_email']); ?>
                                </a>
                            </div>
                        </li>
                        <li>
                            <?php echo icon('phone', 18); ?>
                            <div>
                                <div class="contact-label">Teléfono</div>
                                <a href="tel:<?php echo str_replace(' ', '', $footerConfig['sitio_telefono']); ?>" class="contact-value">
                                    <?php echo htmlspecialchars($footerConfig['sitio_telefono']); ?>
                                </a>
                            </div>
                        </li>
                        <?php if (!empty($footerConfig['whatsapp_numero'])): ?>
                        <li>
                            <?php echo icon('message-circle', 18); ?>
                            <div>
                                <div class="contact-label">WhatsApp</div>
                                <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', $footerConfig['whatsapp_numero']); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="contact-value">
                                    <?php echo htmlspecialchars($footerConfig['whatsapp_numero']); ?>
                                </a>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Newsletter -->
                    <div class="footer-newsletter">
                        <h4 class="newsletter-title">Suscríbete</h4>
                        <form action="<?php echo BASE_URL; ?>/newsletter/subscribe" method="POST" class="newsletter-form">
                            <input 
                                type="email" 
                                name="email" 
                                placeholder="Tu email" 
                                required 
                                class="newsletter-input"
                            >
                            <button type="submit" class="newsletter-btn" aria-label="Suscribirse">
                                <?php echo icon('arrow-right', 18); ?>
                            </button>
                        </form>
                        <p class="newsletter-note">Recibe ofertas y novedades</p>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($footerConfig['sitio_nombre']); ?>. Todos los derechos reservados.</p>
                    <p class="footer-made-by">
                        Desarrollado con <?php echo icon('heart', 14, 'heart-icon'); ?> por 
                        <a href="https://torodigital.cl" target="_blank" rel="noopener noreferrer">ToroDigital</a>
                    </p>
                </div>
                
                <div class="footer-meta">
                    <div class="footer-language">
                        <?php echo icon('globe', 16); ?>
                        <select class="language-selector" aria-label="Idioma">
                            <option value="es">Español</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    
                    <div class="footer-stats">
                        <span class="stat-item">
                            <?php echo icon('users', 16); ?>
                            <span><?php echo number_format($totalUsers ?? 0); ?> usuarios</span>
                        </span>
                        <span class="stat-item">
                            <?php echo icon('list', 16); ?>
                            <span><?php echo number_format($totalPublications ?? 0); ?> publicaciones</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- ======================================================================
         SCRIPTS
         ====================================================================== -->
    
    <!-- Scripts principales -->
    <!-- <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script> -->
    
    <!-- Scripts adicionales de página -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo BASE_URL . $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script de tema (modo claro/oscuro) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const html = document.documentElement;
        
        // Cargar tema guardado
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
        
        // Inicializar iconos de Lucide
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
    </script>
    
    <!-- Cookie consent (opcional) -->
    <?php if (!isset($_COOKIE['cookie_consent'])): ?>
    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-content">
            <div class="cookie-text">
                <p>
                    <?php echo icon('info', 20); ?>
                    Usamos cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestra 
                    <a href="<?php echo BASE_URL; ?>/cookies">Política de Cookies</a>.
                </p>
            </div>
            <div class="cookie-actions">
                <button class="btn btn-ghost btn-sm" onclick="rejectCookies()">Rechazar</button>
                <button class="btn btn-primary btn-sm" onclick="acceptCookies()">Aceptar</button>
            </div>
        </div>
    </div>
    
    <script>
    function acceptCookies() {
        document.cookie = "cookie_consent=accepted; max-age=" + (60*60*24*365) + "; path=/";
        document.getElementById('cookieBanner').style.display = 'none';
    }
    
    function rejectCookies() {
        document.cookie = "cookie_consent=rejected; max-age=" + (60*60*24*365) + "; path=/";
        document.getElementById('cookieBanner').style.display = 'none';
    }
    </script>
    <?php endif; ?>
    
</body>
</html>
