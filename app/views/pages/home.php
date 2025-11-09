<?php

/**
 * Vista: Home / Landing
 * Página principal de ChileChocados
 */
$pageTitle = 'ChileChocados - Compra y Venta de Vehículos Siniestrados';
$additionalCSS = ['/assets/css/home.css?v=' . time()];
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
    <div class="card hero-banner" style="border: 0 !important; border-width: 0 !important; box-shadow: none !important;">
        <h1 class="h1 hero-title">Encuentra vehículos siniestrados al mejor precio</h1>
        <p class="hero-text">
            Compra y vende vehículos chocados, siniestrados o en desarme de forma segura
        </p>
    </div>
</section>

<!-- Categorías principales -->
<section>
    <div class="h2">Categorías principales</div>
    <div class="grid cols-4">
        <?php if (!empty($categorias)): ?>
            <?php 
            // Mapeo de iconos por categoría (SVG completos)
            $iconos = [
                'Auto' => '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>',
                'Moto' => '<path d="M19.44 9.03L15.41 5H11v2h3.59l2 2H5c-2.8 0-5 2.2-5 5s2.2 5 5 5c2.46 0 4.45-1.69 4.9-4h1.65l2.77-2.77c-.21.54-.32 1.14-.32 1.77 0 2.8 2.2 5 5 5s5-2.2 5-5c0-2.65-1.97-4.77-4.56-4.97zM7.82 15C7.4 16.15 6.28 17 5 17c-1.63 0-3-1.37-3-3s1.37-3 3-3c1.28 0 2.4.85 2.82 2H5v2h2.82zM19 17c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>',
                'Camión' => '<path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><rect x="3" y="10" width="12" height="2"/>',
                'Casa Rodante' => '<path d="M17 2H7c-1.1 0-2 .9-2 2v13c0 1.1.9 2 2 2h1.33l1.34 2.67c.2.4.6.66 1.05.66.45 0 .85-.26 1.05-.66L13.67 19H17c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 15H7V4h10v13z"/><rect x="9" y="6" width="6" height="4"/><rect x="9" y="11" width="2" height="2"/><rect x="13" y="11" width="2" height="2"/><circle cx="8" cy="17" r="1"/><circle cx="16" cy="17" r="1"/>',
                'Camioneta' => '<path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>',
                'SUV' => '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/><rect x="7" y="7" width="10" height="3" rx="1"/>',
                'Bus' => '<path d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>',
                'Maquinaria' => '<path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/><circle cx="7" cy="15" r="1.5"/><circle cx="12" cy="15" r="1.5"/>',
                'Repuesto' => '<path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>',
                'Náutica' => '<path d="M4 18l8-13 8 13H4zm8-11.5L6.5 16h11L12 6.5z"/><path d="M2 20h20v2H2z"/>',
                'Aéreos' => '<path d="M22 16v-2l-8.5-5V3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5V9L2 14v2l8.5-2.5V19L8 20.5V22l4-1 4 1v-1.5L13.5 19v-5.5L22 16z"/>'
            ];
            
            foreach ($categorias as $cat): 
                $nombreCat = htmlspecialchars($cat->nombre);
                $iconPath = $iconos[$nombreCat] ?? '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>';
            ?>
            <a class="card cat-card-new" href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $cat->id; ?>">
                <div class="cat-icon-wrapper">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="cat-icon">
                        <?php echo $iconPath; ?>
                    </svg>
                </div>
                <div class="cat-info">
                    <div class="h3 cat-title"><?php echo $nombreCat; ?></div>
                    <p class="meta cat-meta"><?php echo $cat->total_subcategorias ?? 0; ?> subcategorías</p>
                </div>
                <div class="cat-count-badge">
                    <span class="cat-count-number"><?php echo $cat->total_publicaciones ?? 0; ?></span>
                    <span class="cat-count-label">publicaciones</span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
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

<!-- CTA Vender -->
<section>
    <div class="card cta-banner" style="position: relative; overflow: hidden; padding: 0 !important;">
        <!-- Imagen de fondo -->
        <img src="<?php echo BASE_URL; ?>/assets/Fondo.jpeg?v=<?php echo time(); ?>" 
             alt="Fondo" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; z-index: 0;">
        
        <!-- Overlay oscuro para mejorar legibilidad -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1;"></div>
        
        <!-- Contenido -->
        <div style="position: relative; z-index: 2; padding: 60px 32px; min-height: 250px; display: flex; align-items: center; justify-content: center;">
            <div style="width: 100%; max-width: 800px; margin: 0 auto; text-align: center;">
                <div class="h2 cta-title" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">¿Tienes un vehículo siniestrado para vender?</div>
                <p class="cta-text" style="color: rgba(255, 255, 255, 0.95) !important; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                    Publica tu vehículo gratis y llega a miles de compradores potenciales
                </p>
                <div class="cta-buttons">
                    <a class="btn primary" href="<?php echo BASE_URL; ?>/publicar">
                        Publicar ahora
                    </a>
                    <a class="btn cta-btn-secondary" href="<?php echo BASE_URL; ?>/vender" style="background: rgba(255, 255, 255, 0.2) !important; color: white !important; border-color: white !important;">
                        Ver más información
                    </a>
                </div>
            </div>
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

</main>

<!-- Los estilos están en public/assets/css/home.css -->

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
