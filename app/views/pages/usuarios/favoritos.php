<?php
/**
 * Vista: Mis Favoritos
 * Lista de publicaciones guardadas como favoritas por el usuario
 * URL: /favoritos
 */

// Variables del controlador
$pageTitle = $data['title'] ?? 'Mis Favoritos - ChileChocados';
$metaDescription = $data['meta_description'] ?? 'Gestiona tus publicaciones favoritas';
$favoritos = $data['favoritos'] ?? [];
$total = $data['total'] ?? 0;
$currentPage = 'favoritos';

// MOCKUP DATA - Datos de ejemplo mientras no hay BD
if (empty($favoritos)) {
    $favoritos = [
        (object)[
            'id' => 1,
            'titulo' => 'Toyota Corolla 2018 - Daño Frontal',
            'descripcion' => 'Vehículo con daño frontal moderado, motor en perfecto estado. Ideal para repuestos o reparación.',
            'precio' => 4500000,
            'precio_formateado' => '4.500.000',
            'foto_principal' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Automóviles',
            'subcategoria_nombre' => 'Sedán',
            'comuna_nombre' => 'Santiago Centro',
            'visitas' => 245,
            'tiempo_favorito' => 'Hace 2 días'
        ],
        (object)[
            'id' => 2,
            'titulo' => 'Honda CBR 600RR 2020 - Daño Lateral',
            'descripcion' => 'Motocicleta deportiva con daño en carenado lateral derecho. Motor y transmisión funcionando.',
            'precio' => 3200000,
            'precio_formateado' => '3.200.000',
            'foto_principal' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Motocicletas',
            'subcategoria_nombre' => 'Deportiva',
            'comuna_nombre' => 'Las Condes',
            'visitas' => 189,
            'tiempo_favorito' => 'Hace 5 días'
        ],
        (object)[
            'id' => 3,
            'titulo' => 'Chevrolet Spark 2019 - Choque Trasero',
            'descripcion' => 'Auto compacto con daño en parachoques trasero. Mecánica en excelente estado.',
            'precio' => 2800000,
            'precio_formateado' => '2.800.000',
            'foto_principal' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Automóviles',
            'subcategoria_nombre' => 'Hatchback',
            'comuna_nombre' => 'Maipú',
            'visitas' => 312,
            'tiempo_favorito' => 'Hace 1 semana'
        ],
        (object)[
            'id' => 4,
            'titulo' => 'Nissan X-Trail 2017 - Volcamiento',
            'descripcion' => 'SUV con daño por volcamiento. Motor y caja de cambios operativos. Ideal para repuestos.',
            'precio' => 5500000,
            'precio_formateado' => '5.500.000',
            'foto_principal' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=800&h=600&fit=crop',
            'categoria_nombre' => 'SUV',
            'subcategoria_nombre' => 'Mediana',
            'comuna_nombre' => 'Providencia',
            'visitas' => 428,
            'tiempo_favorito' => 'Hoy'
        ]
    ];
    $total = count($favoritos);
}

// Cargar header
require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* Estilos específicos para favoritos usando variables del sistema */
.favoritos-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
}

.favoritos-header svg {
    color: var(--cc-primary, #E6332A);
}

.favoritos-count {
    background: var(--cc-primary-pale, #FFF1F0);
    color: var(--cc-primary, #E6332A);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.favoritos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.favorito-card {
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    position: relative;
    display: grid;
    grid-template-rows: auto 1fr auto;
    height: 100%;
}

.favorito-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.favorito-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 66.67%;
    background: var(--cc-bg-muted, #F5F5F5);
    overflow: hidden;
}

.favorito-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.favorito-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--cc-white, #FFFFFF);
    color: var(--cc-primary, #E6332A);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.favorito-remove {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 32px;
    height: 32px;
    background: var(--cc-white, #FFFFFF);
    color: var(--cc-danger, #EF4444);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
    opacity: 1;
}

.favorito-remove:hover {
    background: var(--cc-danger, #EF4444);
    color: var(--cc-white, #FFFFFF);
    transform: scale(1.1);
}

@media (min-width: 769px) {
    .favorito-remove {
        opacity: 0;
    }
    
    .favorito-card:hover .favorito-remove {
        opacity: 1;
    }
}

.favorito-content {
    padding: 16px;
    display: flex;
    flex-direction: column;
}

.favorito-categoria {
    font-size: 11px;
    font-weight: 600;
    color: var(--cc-primary, #E6332A);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.favorito-titulo {
    font-size: 16px;
    font-weight: 700;
    color: var(--cc-text-primary, #1A1A1A);
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

.favorito-descripcion {
    font-size: 13px;
    color: var(--cc-text-secondary, #666);
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.5;
}

.favorito-precio {
    font-size: 22px;
    font-weight: 700;
    color: var(--cc-primary, #E6332A);
    margin-bottom: 12px;
}

.favorito-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--cc-border-light, #E8E8E8);
    font-size: 12px;
    color: var(--cc-text-secondary, #666);
}

.favorito-meta svg {
    flex-shrink: 0;
}

.favorito-ubicacion {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
}

.favorito-ubicacion span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.favorito-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.favorito-footer {
    padding: 12px 16px;
    background: var(--cc-bg-muted, #F5F5F5);
    display: flex;
    align-items: stretch;
}

.btn-ver-detalle {
    flex: 1;
    padding: 10px;
    background: var(--cc-primary, #E6332A);
    color: var(--cc-white, #FFFFFF);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-ver-detalle:hover {
    background: var(--cc-primary-dark, #B82920);
}

.favoritos-empty {
    text-align: center;
    padding: 60px 24px;
    max-width: 500px;
    margin: 0 auto;
}

.favoritos-empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    background: var(--cc-bg-muted, #F5F5F5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cc-text-tertiary, #999);
}

.favoritos-empty h2 {
    font-size: 22px;
    font-weight: 700;
    color: var(--cc-text-primary, #1A1A1A);
    margin-bottom: 10px;
}

.favoritos-empty p {
    font-size: 15px;
    color: var(--cc-text-secondary, #666);
    line-height: 1.6;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .favoritos-header h1 {
        font-size: 24px;
    }
    
    .favoritos-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .favorito-remove {
        opacity: 1;
    }
}
</style>

<main class="container" style="margin-top: 24px; margin-bottom: 60px;">
    
    <!-- Header -->
    <div class="favoritos-header">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <h1 class="h2" style="margin: 0;">Mis Favoritos</h1>
        <?php if ($total > 0): ?>
            <span class="favoritos-count"><?php echo $total; ?></span>
        <?php endif; ?>
    </div>
    
    <?php if (empty($favoritos)): ?>
        <!-- Empty State -->
        <div class="favoritos-empty">
            <div class="favoritos-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <h2>No tienes favoritos aún</h2>
            <p>Comienza a guardar las publicaciones que te interesen para encontrarlas fácilmente más tarde.</p>
            <a href="<?php echo BASE_URL; ?>/publicaciones" class="btn primary">
                Explorar Publicaciones
            </a>
        </div>
        
    <?php else: ?>
        <!-- Grid de Favoritos -->
        <div class="favoritos-grid">
            <?php foreach ($favoritos as $favorito): ?>
                <article class="favorito-card" data-publicacion-id="<?php echo $favorito->id; ?>">
                    <!-- Imagen -->
                    <div class="favorito-image-wrapper">
                        <img 
                            src="<?php echo htmlspecialchars($favorito->foto_principal); ?>" 
                            alt="<?php echo htmlspecialchars($favorito->titulo); ?>"
                            class="favorito-image"
                            loading="lazy"
                        >
                        
                        <!-- Badge fecha agregado -->
                        <div class="favorito-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <?php echo htmlspecialchars($favorito->tiempo_favorito); ?>
                        </div>
                        
                        <!-- Botón eliminar -->
                        <button 
                            class="favorito-remove" 
                            onclick="eliminarFavorito(<?php echo $favorito->id; ?>)"
                            title="Eliminar de favoritos"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="favorito-content">
                        <div class="favorito-categoria">
                            <?php echo htmlspecialchars($favorito->categoria_nombre); ?>
                            <?php if ($favorito->subcategoria_nombre): ?>
                                · <?php echo htmlspecialchars($favorito->subcategoria_nombre); ?>
                            <?php endif; ?>
                        </div>
                        
                        <h2 class="favorito-titulo">
                            <?php echo htmlspecialchars($favorito->titulo); ?>
                        </h2>
                        
                        <?php if ($favorito->descripcion): ?>
                            <p class="favorito-descripcion">
                                <?php echo htmlspecialchars($favorito->descripcion); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="favorito-precio">
                            $<?php echo $favorito->precio_formateado; ?>
                        </div>
                        
                        <div class="favorito-meta">
                            <div class="favorito-ubicacion">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span><?php echo htmlspecialchars($favorito->comuna_nombre); ?></span>
                            </div>
                            
                            <div class="favorito-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span><?php echo number_format($favorito->visitas); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="favorito-footer">
                        <a href="<?php echo BASE_URL; ?>/detalle/<?php echo $favorito->id; ?>" class="btn-ver-detalle">
                            Ver Detalle
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
    <?php endif; ?>
</main>

<script>
/**
 * Eliminar publicación de favoritos
 */
function eliminarFavorito(publicacionId) {
    if (!confirm('¿Estás seguro de eliminar esta publicación de favoritos?')) {
        return;
    }
    
    // Encontrar la card
    const card = document.querySelector(`[data-publicacion-id="${publicacionId}"]`);
    
    // Animación de salida
    if (card) {
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
    }
    
    // Hacer petición AJAX
    fetch('/favoritos/eliminar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `publicacion_id=${publicacionId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover elemento del DOM
            if (card) {
                card.remove();
            }
            
            // Actualizar contador
            const countBadge = document.querySelector('.favoritos-count');
            if (countBadge) {
                countBadge.textContent = data.total_favoritos;
                
                // Si no quedan favoritos, recargar para mostrar empty state
                if (data.total_favoritos === 0) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            }
            
            // Mostrar notificación (opcional)
            console.log('Eliminado de favoritos');
        } else {
            alert(data.message || 'Error al eliminar de favoritos');
            if (card) {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar de favoritos');
        if (card) {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
        }
    });
}

// Animación de entrada para las cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.favorito-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>

<?php
// Cargar footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
