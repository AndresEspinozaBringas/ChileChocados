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

// Cargar header
require_once __DIR__ . '/../../../layouts/header.php';
?>

<style>
/* ============================================================================
 * ESTILOS ESPECÍFICOS PARA PÁGINA DE FAVORITOS
 * ============================================================================ */
.favoritos-header {
    background: linear-gradient(135deg, var(--cc-primary) 0%, var(--cc-primary-dark) 100%);
    color: var(--cc-white);
    padding: 48px 0;
    margin-bottom: 48px;
}

.favoritos-header-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.favoritos-header h1 {
    font-size: 36px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 16px;
}

.favoritos-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: var(--cc-radius-full);
    font-size: 18px;
    font-weight: 600;
}

.favoritos-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px 80px;
}

/* Grid de publicaciones */
.favoritos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-top: 32px;
}

/* Card de publicación */
.favorito-card {
    background: var(--cc-bg-surface);
    border-radius: var(--cc-radius-xl);
    overflow: hidden;
    box-shadow: var(--cc-shadow);
    border: 1px solid var(--cc-border-light);
    transition: all 0.3s ease;
    position: relative;
}

.favorito-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--cc-shadow-md);
}

.favorito-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 66.67%; /* Ratio 3:2 */
    background: var(--cc-gray-200);
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
    background: var(--cc-white);
    color: var(--cc-primary);
    padding: 6px 12px;
    border-radius: var(--cc-radius-lg);
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: var(--cc-shadow-sm);
}

.favorito-remove {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    background: var(--cc-white);
    color: var(--cc-danger);
    border: none;
    border-radius: var(--cc-radius-full);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--cc-shadow);
    transition: all 0.2s ease;
    opacity: 0;
}

.favorito-card:hover .favorito-remove {
    opacity: 1;
}

.favorito-remove:hover {
    background: var(--cc-danger);
    color: var(--cc-white);
    transform: scale(1.1);
}

.favorito-content {
    padding: 20px;
}

.favorito-categoria {
    font-size: 12px;
    font-weight: 600;
    color: var(--cc-primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.favorito-titulo {
    font-size: 18px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

.favorito-descripcion {
    font-size: 14px;
    color: var(--cc-text-secondary);
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.5;
}

.favorito-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--cc-border-light);
    font-size: 13px;
    color: var(--cc-text-tertiary);
}

.favorito-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.favorito-ubicacion {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 4px;
}

.favorito-precio {
    font-size: 24px;
    font-weight: 700;
    color: var(--cc-primary);
    margin-bottom: 16px;
}

.favorito-footer {
    padding: 16px 20px;
    background: var(--cc-bg-muted);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.btn-ver-detalle {
    flex: 1;
    padding: 10px 16px;
    background: var(--cc-primary);
    color: var(--cc-white);
    border: none;
    border-radius: var(--cc-radius-lg);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    text-align: center;
}

.btn-ver-detalle:hover {
    background: var(--cc-primary-dark);
    transform: translateY(-1px);
}

/* Empty state */
.favoritos-empty {
    text-align: center;
    padding: 80px 24px;
    max-width: 500px;
    margin: 0 auto;
}

.favoritos-empty-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 24px;
    background: var(--cc-gray-100);
    border-radius: var(--cc-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cc-gray-400);
}

.favoritos-empty h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin-bottom: 12px;
}

.favoritos-empty p {
    font-size: 16px;
    color: var(--cc-text-secondary);
    line-height: 1.6;
    margin-bottom: 24px;
}

.btn-explorar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--cc-primary);
    color: var(--cc-white);
    text-decoration: none;
    border-radius: var(--cc-radius-lg);
    font-size: 16px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-explorar:hover {
    background: var(--cc-primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--cc-shadow-md);
}

/* Responsive */
@media (max-width: 968px) {
    .favoritos-header h1 {
        font-size: 28px;
    }
    
    .favoritos-header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .favoritos-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 640px) {
    .favoritos-header {
        padding: 32px 0;
        margin-bottom: 32px;
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

<!-- Header -->
<section class="favoritos-header">
    <div class="favoritos-header-content">
        <h1>
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            Mis Favoritos
            <?php if ($total > 0): ?>
                <span class="favoritos-count"><?php echo $total; ?></span>
            <?php endif; ?>
        </h1>
    </div>
</section>

<!-- Main Content -->
<main class="favoritos-container">
    
    <?php if (empty($favoritos)): ?>
        <!-- Empty State -->
        <div class="favoritos-empty">
            <div class="favoritos-empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <h2>No tienes favoritos aún</h2>
            <p>Comienza a guardar las publicaciones que te interesen para encontrarlas fácilmente más tarde.</p>
            <a href="/publicaciones" class="btn-explorar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
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
                        <a href="/detalle/<?php echo $favorito->id; ?>" class="btn-ver-detalle">
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
require_once __DIR__ . '/../../../layouts/footer.php';
?>
