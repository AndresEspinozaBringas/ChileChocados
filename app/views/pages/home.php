<?php

/**
 * Vista: Home / Landing
 * Página principal de ChileChocados
 */
$pageTitle = 'ChileChocados - Compra y Venta de Vehículos Siniestrados';
require_once APP_PATH . '/views/layouts/header.php';
require_once APP_PATH . '/views/layouts/nav.php';

// Datos de ejemplo de categorías (esto luego vendrá de la BD)
$categorias = [
    ['id' => 1, 'nombre' => 'Autos', 'icon' => 'car', 'subcategorias' => '12 subcategorías', 'count' => 156],
    ['id' => 2, 'nombre' => 'Camionetas', 'icon' => 'truck', 'subcategorias' => '8 subcategorías', 'count' => 89],
    ['id' => 3, 'nombre' => 'SUV', 'icon' => 'car', 'subcategorias' => '6 subcategorías', 'count' => 67],
    ['id' => 4, 'nombre' => 'Motos', 'icon' => 'bike', 'subcategorias' => '5 subcategorías', 'count' => 43],
];

// Publicaciones destacadas con imágenes reales
$publicaciones_destacadas = [
    [
        'id' => 1,
        'titulo' => 'Ford Territory',
        'imagen' => 'ford-territory.jpg',
        'categoria' => 'SUV',
        'estado' => 'Chocado',
        'precio' => 8500000,
        'ubicacion' => 'Santiago, RM'
    ],
    [
        'id' => 2,
        'titulo' => 'Kia Cerato',
        'imagen' => 'kia-cerato.jpg',
        'categoria' => 'Sedán',
        'estado' => 'Chocado',
        'precio' => 6200000,
        'ubicacion' => 'Valparaíso, V'
    ],
    [
        'id' => 3,
        'titulo' => 'Kia Rio 5',
        'imagen' => 'kia-rio-5.jpg',
        'categoria' => 'Hatchback',
        'estado' => 'Chocado',
        'precio' => 4800000,
        'ubicacion' => 'Concepción, VIII'
    ],
    [
        'id' => 4,
        'titulo' => 'Dodge Journey',
        'imagen' => 'dodge.jpg',
        'categoria' => 'SUV',
        'estado' => 'En Desarme',
        'precio' => null,  // A convenir
        'ubicacion' => 'La Serena, IV'
    ],
];
?>

<main class="container">

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="h1">Encuentra vehículos siniestrados al mejor precio</h1>
        <p class="h3" style="margin-top: 12px; font-weight: 400; color: var(--text-secondary);">
            Compra y vende vehículos chocados, siniestrados o en desarme de forma segura
        </p>
        
        <!-- Buscador -->
        <form class="search-bar" style="margin-top: 24px;" action="<?php echo BASE_URL; ?>/listado" method="GET">
            <div class="row" style="gap: 8px;">
                <input 
                    type="text" 
                    name="q"
                    placeholder="Buscar por marca, modelo o categoría..." 
                    class="form-control" 
                    style="flex: 1;"
                >
                <select name="categoria" class="form-control" style="min-width: 180px;">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    Buscar
                </button>
            </div>
        </form>
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
<section>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <div class="h2">Publicaciones destacadas</div>
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
        <?php foreach ($publicaciones_destacadas as $pub): ?>
        <a class="card pub-card" href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>">
            <!-- Imagen del vehículo -->
            <div class="pub-img" style="width: 100%; height: 200px; background: var(--bg-secondary); border-radius: 8px; overflow: hidden; position: relative;">
                <?php if ($pub['imagen']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo $pub['imagen']; ?>" 
                         alt="<?php echo $pub['titulo']; ?>"
                         style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                        Sin imagen
                    </div>
                <?php endif; ?>
                
                <!-- Badge de estado -->
                <span class="badge" style="position: absolute; top: 12px; left: 12px; background: var(--primary); color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                    <?php echo strtoupper($pub['estado']); ?>
                </span>
            </div>
            
            <!-- Información -->
            <div style="padding: 16px 0;">
                <div class="h3" style="margin-bottom: 8px;"><?php echo $pub['titulo']; ?></div>
                
                <div class="row meta" style="gap: 8px; margin-bottom: 8px;">
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <?php echo $pub['ubicacion']; ?>
                    </span>
                    <span>•</span>
                    <span><?php echo $pub['categoria']; ?></span>
                </div>
                
                <span class="h2" style="color: var(--primary);">
                    <?php echo $pub['precio'] ? formatPrice($pub['precio']) : 'A convenir'; ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Categorías padre -->
<section>
    <div class="h2">Categorías principales</div>
    <div class="grid cols-4">
        <?php foreach ($categorias as $cat): ?>
        <a class="card cat-card" href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $cat['id']; ?>">
            <div class="left">
                <span class="iconify">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                    </svg>
                </span>
                <div>
                    <div class="h3"><?php echo $cat['nombre']; ?></div>
                    <p class="meta"><?php echo $cat['subcategorias']; ?></p>
                </div>
            </div>
            <span class="cat-count" style="background: var(--bg-secondary); padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                <?php echo $cat['count']; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA Vender -->
<section>
    <div class="card" style="background: linear-gradient(135deg, #E6332A 0%, #C02A23 100%); color: white; padding: 48px; text-align: center;">
        <div class="h2" style="color: white; margin-bottom: 12px;">¿Tienes un vehículo siniestrado para vender?</div>
        <p style="font-size: 18px; opacity: 0.9; margin-bottom: 24px;">
            Publica tu vehículo gratis y llega a miles de compradores potenciales
        </p>
        <div class="row" style="gap: 12px; justify-content: center;">
            <a class="btn" style="background: white; color: var(--primary); border: none;" href="<?php echo BASE_URL; ?>/publicar">
                Publicar ahora
            </a>
            <a class="btn" style="background: transparent; color: white; border: 2px solid white;" href="<?php echo BASE_URL; ?>/vender">
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
</style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>