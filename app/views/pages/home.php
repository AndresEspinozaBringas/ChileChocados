<?php

/**
 * Vista: Home / Landing
 * Página principal de ChileChocados
 */
$pageTitle = 'ChileChocados - Compra y Venta de Vehículos Siniestrados';
require_once APP_PATH . '/views/layouts/header.php';
require_once APP_PATH . '/views/layouts/nav.php';

// Las categorías y publicaciones vienen del controlador
// $categorias - desde la BD
// $publicacionesDestacadas - publicaciones destacadas activas desde la BD
// $publicacionesRecientes - publicaciones recientes aprobadas desde la BD
?>

<main class="container">

<!-- Hero Section -->
<section class="hero">
    <div class="card hero-banner">
        <h1 class="h1 hero-title">Encuentra vehículos siniestrados al mejor precio</h1>
        <p class="hero-text">
            Compra y vende vehículos chocados, siniestrados o en desarme de forma segura
        </p>
    </div>
</section>

<!-- Estadísticas rápidas -->
<section class="stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 32px 0;">
    <div class="card" style="text-align: center; padding: 24px;">
        <div class="h2" style="color: var(--primary);">350+</div>
        <p class="meta">Vehículos publicados</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
        <div class="h2" style="color: var(--primary);">120+</div>
        <p class="meta">Ventas realizadas</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
        <div class="h2" style="color: var(--primary);">500+</div>
        <p class="meta">Usuarios registrados</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
        <div class="h2" style="color: var(--primary);">15+</div>
        <p class="meta">Regiones con cobertura</p>
    </div>
</section>

<!-- Publicaciones destacadas -->
<?php if (!empty($publicacionesDestacadas)): ?>
<section>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <div class="h2">⭐ Publicaciones destacadas</div>
            <p class="meta">Los vehículos más destacados del momento</p>
        </div>
        <a class="btn" href="<?php echo BASE_URL; ?>/listado">
            Ver todas
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>
    </div>
    
    <div class="grid cols-4">
        <?php foreach ($publicacionesDestacadas as $pub): ?>
            <a class="card pub-card" href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" style="text-decoration: none; color: inherit;">
                <!-- Imagen del vehículo -->
                <div class="pub-img" style="width: 100%; height: 200px; background: var(--bg-secondary); border-radius: 8px; overflow: hidden; position: relative;">
                    <?php if (!empty($pub->foto_principal)): ?>
                        <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo htmlspecialchars($pub->foto_principal); ?>" 
                             alt="<?php echo htmlspecialchars($pub->titulo); ?>"
                             style="width: 100%; height: 100%; object-fit: cover; <?php echo (isset($pub->estado) && $pub->estado === 'vendida') ? 'opacity: 0.6;' : ''; ?>">
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badge de vendido en esquina superior izquierda -->
                    <?php if (isset($pub->estado) && $pub->estado === 'vendida'): ?>
                    <span style="position: absolute; top: 12px; left: 12px; background: #10B981; color: white; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 0.5px; z-index: 10; pointer-events: none;">
                        ✓ VENDIDO
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- Información -->
                <div style="padding: 16px 0;">
                    <div class="h3" style="margin-bottom: 8px;"><?php echo htmlspecialchars($pub->titulo); ?></div>
                    
                    <div class="row meta" style="gap: 8px; margin-bottom: 8px;">
                        <span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <?php echo htmlspecialchars($pub->region_nombre ?? 'Chile'); ?>
                        </span>
                        <?php if (!empty($pub->categoria_nombre)): ?>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($pub->categoria_nombre); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <span class="h2" style="color: var(--primary);">
                        <?php 
                        if ($pub->tipo_venta === 'completo' && !empty($pub->precio)) {
                            echo formatPrice($pub->precio);
                        } else {
                            echo 'A convenir';
                        }
                        ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Publicaciones recientes -->
<section>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <div class="h2">Publicaciones recientes</div>
            <p class="meta">Los últimos vehículos publicados</p>
        </div>
        <a class="btn" href="<?php echo BASE_URL; ?>/listado">
            Ver todas
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>
    </div>
    
    <div class="grid cols-4">
        <?php if (!empty($publicacionesRecientes)): ?>
            <?php foreach ($publicacionesRecientes as $pub): ?>
            <a class="card pub-card" href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" style="text-decoration: none; color: inherit;">
                <!-- Imagen del vehículo -->
                <div class="pub-img" style="width: 100%; height: 200px; background: var(--bg-secondary); border-radius: 8px; overflow: hidden; position: relative;">
                    <?php if (!empty($pub->foto_principal)): ?>
                        <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo htmlspecialchars($pub->foto_principal); ?>" 
                             alt="<?php echo htmlspecialchars($pub->titulo); ?>"
                             style="width: 100%; height: 100%; object-fit: cover; <?php echo (isset($pub->estado) && $pub->estado === 'vendida') ? 'opacity: 0.6;' : ''; ?>">
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badge de vendido en esquina superior izquierda -->
                    <?php if (isset($pub->estado) && $pub->estado === 'vendida'): ?>
                    <span style="position: absolute; top: 12px; left: 12px; background: #10B981; color: white; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 0.5px; z-index: 10; pointer-events: none;">
                        ✓ VENDIDO
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- Información -->
                <div style="padding: 16px 0;">
                    <div class="h3" style="margin-bottom: 8px;"><?php echo htmlspecialchars($pub->titulo); ?></div>
                    
                    <div class="row meta" style="gap: 8px; margin-bottom: 8px;">
                        <span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <?php echo htmlspecialchars($pub->region_nombre ?? 'Chile'); ?>
                        </span>
                        <?php if (!empty($pub->categoria_nombre)): ?>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($pub->categoria_nombre); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <span class="h2" style="color: var(--primary);">
                        <?php 
                        if ($pub->tipo_venta === 'completo' && !empty($pub->precio)) {
                            echo formatPrice($pub->precio);
                        } else {
                            echo 'A convenir';
                        }
                        ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-secondary);">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 16px; opacity: 0.3;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p>No hay publicaciones disponibles en este momento</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categorías padre -->
<section>
    <div class="h2">Categorías principales</div>
    <div class="grid cols-4">
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): ?>
            <a class="card cat-card" href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $cat->id; ?>">
                <div class="left">
                    <span class="iconify">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                    </span>
                    <div>
                        <div class="h3"><?php echo htmlspecialchars($cat->nombre); ?></div>
                        <p class="meta"><?php echo $cat->total_subcategorias ?? 0; ?> subcategorías</p>
                    </div>
                </div>
                <span class="cat-count" style="background: var(--bg-secondary); padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                    <?php echo $cat->total_publicaciones ?? 0; ?>
                </span>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Vender -->
<section>
    <div class="card cta-banner">
        <div class="h2 cta-title">¿Tienes un vehículo siniestrado para vender?</div>
        <p class="cta-text">
            Publica tu vehículo gratis y llega a miles de compradores potenciales
        </p>
        <div class="cta-buttons">
            <a class="btn primary" href="<?php echo BASE_URL; ?>/publicar">
                Publicar ahora
            </a>
            <a class="btn cta-btn-secondary" href="<?php echo BASE_URL; ?>/vender">
                Ver más información
            </a>
        </div>
    </div>
</section>

<!-- Cómo funciona -->
<section>
    <div class="h2" style="text-align: center; margin-bottom: 32px;">¿Cómo funciona?</div>
    <div class="grid cols-3" style="gap: 24px;">
        <div class="card" style="text-align: center; padding: 32px;">
            <div style="width: 64px; height: 64px; background: var(--bg-secondary); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="h3" style="margin-bottom: 8px;">1. Regístrate</div>
            <p class="meta">Crea tu cuenta gratuita en menos de 2 minutos</p>
        </div>
        
        <div class="card" style="text-align: center; padding: 32px;">
            <div style="width: 64px; height: 64px; background: var(--bg-secondary); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
            <div class="h3" style="margin-bottom: 8px;">2. Publica o busca</div>
            <p class="meta">Sube fotos de tu vehículo o busca el que necesitas</p>
        </div>
        
        <div class="card" style="text-align: center; padding: 32px;">
            <div style="width: 64px; height: 64px; background: var(--bg-secondary); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
            </div>
            <div class="h3" style="margin-bottom: 8px;">3. Negocia y cierra</div>
            <p class="meta">Contacta directamente y acuerda los detalles de la compra</p>
        </div>
    </div>
</section>

</main>

<style>
/* Hero Section */
.hero {
    padding: 60px 0;
    text-align: center;
}

.hero-banner {
    background: white;
    border: 2px solid #E5E5E5;
    text-align: center;
    padding: 48px 32px;
}

.hero-title {
    color: #2E2E2E;
    margin-bottom: 12px;
}

.hero-text {
    font-size: 18px;
    color: #666666;
    margin: 0;
}

/* CTA Banner */
.cta-banner {
    background: white;
    border: 2px solid #E5E5E5;
    text-align: center;
    padding: 48px 32px;
}

.cta-title {
    color: #2E2E2E;
    margin-bottom: 12px;
}

.cta-text {
    font-size: 18px;
    color: #666666;
    margin: 0 0 24px 0;
}

.cta-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-btn-secondary {
    background: white;
    color: #2E2E2E;
    border: 2px solid #E5E5E5;
}

.cta-btn-secondary:hover {
    background: #F5F5F5;
    border-color: var(--cc-primary);
}

/* Categorías */
.cat-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    transition: all 0.2s ease;
}

.cat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.cat-card .left {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Publicaciones */
.pub-card {
    transition: all 0.2s ease;
}

.pub-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] main {
    background: #111827 !important;
}

:root[data-theme="dark"] .container {
    background: #111827 !important;
}

:root[data-theme="dark"] main.container {
    background: #111827 !important;
}

:root[data-theme="dark"] .hero {
    background: #111827 !important;
}

:root[data-theme="dark"] .hero-banner {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .hero-title {
    color: white !important;
}

:root[data-theme="dark"] .hero-text {
    color: rgba(255,255,255,0.9) !important;
}

/* Cards de estadísticas */
:root[data-theme="dark"] .card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .card .h2,
:root[data-theme="dark"] .card .h3,
:root[data-theme="dark"] .card div[class*="h2"],
:root[data-theme="dark"] .card div[class*="h3"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .card .meta,
:root[data-theme="dark"] .card p[class*="meta"] {
    color: #9CA3AF !important;
}

/* Estadísticas específicas */
:root[data-theme="dark"] .stats .card {
    background: #1F2937 !important;
}

:root[data-theme="dark"] .stats .card .h2,
:root[data-theme="dark"] .stats .card div[class*="h2"] {
    color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .stats .card .meta,
:root[data-theme="dark"] .stats .card p {
    color: #9CA3AF !important;
}

/* Títulos de secciones */
:root[data-theme="dark"] .h2 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .meta {
    color: #9CA3AF;
}

/* Publicaciones cards */
:root[data-theme="dark"] .pub-card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .pub-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    border-color: var(--primary);
}

:root[data-theme="dark"] .pub-card .h3,
:root[data-theme="dark"] .pub-card div[class*="h3"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .pub-card .meta,
:root[data-theme="dark"] .pub-card div[class*="meta"],
:root[data-theme="dark"] .pub-card .row {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .pub-card .h2,
:root[data-theme="dark"] .pub-card span[class*="h2"] {
    color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .pub-img {
    background: #374151 !important;
}

:root[data-theme="dark"] .pub-card span,
:root[data-theme="dark"] .pub-card div {
    color: inherit;
}

/* Categorías cards */
:root[data-theme="dark"] .cat-card {
    background: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .cat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    border-color: var(--primary);
}

:root[data-theme="dark"] .cat-card .h3 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .cat-card .meta {
    color: #9CA3AF;
}

:root[data-theme="dark"] .cat-count {
    background: #374151 !important;
    color: #F3F4F6;
}

:root[data-theme="dark"] .iconify svg {
    stroke: var(--primary);
}

/* CTA Vender - dark mode */
:root[data-theme="dark"] .cta-banner {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .cta-title {
    color: white !important;
}

:root[data-theme="dark"] .cta-text {
    color: rgba(255,255,255,0.9) !important;
}

:root[data-theme="dark"] .cta-btn-secondary {
    background: transparent !important;
    color: white !important;
    border-color: white !important;
}

:root[data-theme="dark"] .cta-btn-secondary:hover {
    background: rgba(255,255,255,0.1) !important;
    border-color: white !important;
}

/* Sección "Cómo funciona" */
:root[data-theme="dark"] .grid .card[style*="text-align: center"] {
    background: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .grid .card[style*="text-align: center"] .h3 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .grid .card[style*="text-align: center"] .meta {
    color: #9CA3AF;
}

:root[data-theme="dark"] .grid .card[style*="text-align: center"] > div[style*="background: var(--bg-secondary)"] {
    background: #374151 !important;
}

/* SVG icons */
:root[data-theme="dark"] svg {
    color: inherit;
}

/* Botones generales */
:root[data-theme="dark"] .btn {
    background: #374151;
    color: #F3F4F6;
    border-color: #4B5563;
}

:root[data-theme="dark"] .btn:hover {
    background: #4B5563;
    border-color: #6B7280;
}

:root[data-theme="dark"] .btn.primary {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* Estado vacío */
:root[data-theme="dark"] div[style*="grid-column: 1 / -1"] {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="grid-column: 1 / -1"] p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="grid-column: 1 / -1"] svg {
    opacity: 0.2;
}

/* Asegurar que todos los textos en cards tengan buen contraste */
:root[data-theme="dark"] section .card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] section .card * {
    border-color: #374151;
}

/* Forzar colores en elementos con estilos inline */
:root[data-theme="dark"] main div[style*="padding: 24px"] {
    background: #1F2937 !important;
}

:root[data-theme="dark"] main div[style*="text-align: center"] .h2,
:root[data-theme="dark"] main div[style*="text-align: center"] div[class*="h2"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] main div[style*="text-align: center"] .meta,
:root[data-theme="dark"] main div[style*="text-align: center"] p {
    color: #9CA3AF !important;
}

/* Grid de publicaciones y categorías */
:root[data-theme="dark"] .grid a.card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .grid .card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

/* Sobrescribir estilos inline específicos */
:root[data-theme="dark"] section div[style*="padding: 16px 0"] .h3,
:root[data-theme="dark"] section div[style*="padding: 16px 0"] div[class*="h3"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] section div[style*="padding: 16px 0"] .row,
:root[data-theme="dark"] section div[style*="padding: 16px 0"] div[class*="row"] {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] section div[style*="padding: 16px 0"] span[class*="h2"] {
    color: var(--cc-primary) !important;
}

/* Asegurar que las imágenes de publicaciones tengan el fondo correcto */
:root[data-theme="dark"] div[style*="height: 200px"] {
    background: #374151 !important;
}

/* Sobrescribir todos los divs con estilos inline de padding */
:root[data-theme="dark"] .card > div {
    color: inherit;
}

:root[data-theme="dark"] .card > div * {
    color: inherit;
}

/* Títulos de secciones con display flex */
:root[data-theme="dark"] section > div[style*="display: flex"] .h2 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] section > div[style*="display: flex"] .meta {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] section > div[style*="display: flex"] p {
    color: #9CA3AF !important;
}

/* Enlaces "Ver todas" */
:root[data-theme="dark"] section > div[style*="display: flex"] a.btn {
    background: #374151;
    color: #F3F4F6;
    border-color: #4B5563;
}

:root[data-theme="dark"] section > div[style*="display: flex"] a.btn:hover {
    background: #4B5563;
    border-color: #6B7280;
}

/* Asegurar que los badges de "VENDIDO" sean visibles */
:root[data-theme="dark"] span[style*="background: #10B981"] {
    background: #10B981 !important;
    color: white !important;
}

/* Forzar que las cards NO tengan el fondo del banner especial */
:root[data-theme="dark"] .grid .card:not([style*="background: #2C3E50"]) {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

/* Excluir los banners especiales de las reglas generales de cards */
:root[data-theme="dark"] .card:not([style*="background: #2C3E50"]):not(.hero-banner):not(.cta-banner) {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

/* Asegurar que no haya fondos blancos en secciones */
:root[data-theme="dark"] section {
    background: #111827 !important;
}

/* Forzar que el body tenga el fondo correcto */
:root[data-theme="dark"] body {
    background: #111827 !important;
}

/* Asegurar que el área alrededor del hero tenga el fondo correcto */
:root[data-theme="dark"] .hero,
:root[data-theme="dark"] .hero * {
    background-color: transparent;
}

:root[data-theme="dark"] .hero .card.hero-banner {
    background: #1F2937 !important;
}
:root[data-theme="dark"] div[style*="height: 200px"] {
    background: #374151 !important;
}

/* Sobrescribir todos los divs con estilos inline de padding */
:root[data-theme="dark"] .card > div {
    color: inherit;
}

:root[data-theme="dark"] .card > div * {
    color: inherit;
}
</style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>