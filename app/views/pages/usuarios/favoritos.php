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
                            src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo htmlspecialchars($favorito->foto_principal); ?>" 
                            alt="<?php echo htmlspecialchars($favorito->titulo); ?>"
                            class="favorito-image"
                            loading="lazy"
                            onerror="this.src='<?php echo BASE_URL; ?>/assets/images/no-image.jpg'"
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
                        <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $favorito->id; ?>" class="btn-ver-detalle">
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
 * Mostrar modal de confirmación para eliminar favorito
 */
function eliminarFavorito(publicacionId) {
    // Obtener el título de la publicación
    const card = document.querySelector(`[data-publicacion-id="${publicacionId}"]`);
    const titulo = card ? card.querySelector('.favorito-titulo').textContent : 'esta publicación';
    
    // Actualizar el modal con la información
    document.getElementById('publicacion_id_eliminar').value = publicacionId;
    document.getElementById('titulo_publicacion_eliminar').textContent = titulo;
    
    // Mostrar el modal
    document.getElementById('modalEliminarFavorito').style.display = 'flex';
}

/**
 * Cerrar modal de confirmación
 */
function cerrarModalEliminar() {
    document.getElementById('modalEliminarFavorito').style.display = 'none';
}

/**
 * Confirmar eliminación de favorito
 */
function confirmarEliminarFavorito() {
    const publicacionId = document.getElementById('publicacion_id_eliminar').value;
    const card = document.querySelector(`[data-publicacion-id="${publicacionId}"]`);
    
    // Cerrar modal
    cerrarModalEliminar();
    
    // Animación de salida
    if (card) {
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
    }
    
    // Hacer petición AJAX
    fetch('<?php echo BASE_URL; ?>/favoritos/eliminar', {
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

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalEliminarFavorito');
    if (event.target === modal) {
        cerrarModalEliminar();
    }
});
</script>

<!-- Modal: Confirmar eliminación de favorito -->
<div id="modalEliminarFavorito" class="admin-modal" style="display: none;">
  <div class="admin-modal-content admin-modal-small">
    <div class="admin-modal-header">
      <h2 class="h2" style="margin: 0; display: flex; align-items: center; gap: 12px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        Eliminar de Favoritos
      </h2>
    </div>
    <div class="admin-modal-body">
      <input type="hidden" id="publicacion_id_eliminar" value="">
      
      <p class="meta" style="margin-bottom: 16px; color: #6B7280; line-height: 1.6;">
        ¿Estás seguro de que deseas eliminar esta publicación de tus favoritos?
      </p>
      
      <div style="background: #FEF2F2; border-left: 3px solid #EF4444; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
        <p style="margin: 0; font-weight: 600; color: #991B1B; font-size: 14px;" id="titulo_publicacion_eliminar"></p>
      </div>
      
      <p class="meta" style="margin-bottom: 20px; color: #6B7280; font-size: 13px;">
        Esta acción no se puede deshacer, pero siempre puedes volver a agregarla a favoritos más tarde.
      </p>

      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModalEliminar()" class="btn outline">
          Cancelar
        </button>
        <button type="button" onclick="confirmarEliminarFavorito()" class="btn" style="background: #EF4444; color: white; border-color: #EF4444;">
          Sí, Eliminar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Estilos para el modal (reutilizando estilos de admin) -->
<style>
/* Modal overlay */
.admin-modal {
  display: none;
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.75);
  align-items: center;
  justify-content: center;
  padding: 20px;
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

/* Modal content container */
.admin-modal-content {
  background-color: #FFFFFF;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  animation: adminModalFadeIn 0.3s ease-out;
  border: 1px solid rgba(0, 0, 0, 0.1);
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.admin-modal-small {
  max-width: 500px;
  width: 95%;
}

/* Modal header */
.admin-modal-header {
  padding: 24px 32px;
  border-bottom: 2px solid #E5E7EB;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

/* Modal body */
.admin-modal-body {
  padding: 32px;
  overflow-y: auto;
  flex: 1;
}

@keyframes adminModalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Scrollbar personalizado para el modal */
.admin-modal-body::-webkit-scrollbar {
  width: 10px;
}

.admin-modal-body::-webkit-scrollbar-track {
  background: #F3F4F6;
  border-radius: 10px;
}

.admin-modal-body::-webkit-scrollbar-thumb {
  background: #9CA3AF;
  border-radius: 10px;
}
</style>

<?php
// Cargar footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
