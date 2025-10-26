<?php
/**
 * Vista: Detalle de Publicación
 * Muestra toda la información de una publicación específica
 */
$pageTitle = $publicacion->titulo ?? 'Detalle de Publicación';
layout('header');
layout('nav');
?>

<main class="container">

<?php layout('icons'); ?>

  <!-- Breadcrumbs -->
  <div class="breadcrumbs" style="margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>/listado" style="color: var(--text-secondary); text-decoration: none;">
      ← Volver al listado
    </a>
  </div>
  
  <!-- Grid principal: Galería + Info -->
  <div class="grid cols-3" style="gap: 20px;">
    
    <!-- Galería de imágenes (2 columnas) -->
    <div class="card" style="grid-column: 1/3;">
      <!-- Imagen principal -->
      <div style="width: 100%; height: 400px; background: var(--bg-secondary); border-radius: 8px; overflow: hidden; margin-bottom: 16px;">
        <?php if (!empty($publicacion->foto_principal)): ?>
          <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($publicacion->foto_principal); ?>" 
               alt="<?php echo e($publicacion->titulo); ?>"
               style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
          <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
            Sin imagen
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Miniaturas -->
      <div class="gallery" style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px;">
        <?php if (!empty($imagenes)): ?>
          <?php foreach ($imagenes as $index => $imagen): ?>
            <div style="aspect-ratio: 1; background: var(--bg-secondary); border-radius: 4px; overflow: hidden; cursor: pointer;">
              <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($imagen->ruta_archivo); ?>" 
                   alt="Imagen <?php echo $index + 1; ?>"
                   style="width: 100%; height: 100%; object-fit: cover;">
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <?php for ($i = 1; $i <= 6; $i++): ?>
            <div style="aspect-ratio: 1; background: var(--bg-secondary); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); font-size: 12px;">
              <?php echo $i; ?>
            </div>
          <?php endfor; ?>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Sidebar: Información principal -->
    <aside class="card">
      <div class="h2"><?php echo e($publicacion->titulo); ?></div>
      
      <div class="meta" style="margin-top: 8px;">
        <?php echo icon('tag', 16); ?>
        <?php echo e($publicacion->categoria_nombre ?? 'Categoría'); ?> · 
        <?php echo e($publicacion->marca ?? ''); ?> 
        <?php echo e($publicacion->modelo ?? ''); ?>
        <?php if ($publicacion->anio): ?>
          <?php echo e($publicacion->anio); ?>
        <?php endif; ?>
      </div>
      
      <div class="meta" style="margin-top: 4px;">
        <?php echo icon('map-pin', 16); ?>
        <?php echo e($publicacion->region_nombre ?? 'Ubicación'); ?>
        <?php if (!empty($publicacion->comuna_nombre)): ?>
          , <?php echo e($publicacion->comuna_nombre); ?>
        <?php endif; ?>
      </div>
      
      <!-- Precio y tipo -->
      <div class="row" style="align-items: center; gap: 10px; margin-top: 16px;">
        <span class="badge"><?php echo $publicacion->tipo_venta === 'desarme' ? 'En desarme' : 'Siniestrado'; ?></span>
        <div class="h3" style="margin: 0;">
          <?php echo $publicacion->tipo_venta === 'desarme' ? 'A convenir' : formatPrice($publicacion->precio); ?>
        </div>
      </div>
      
      <!-- Botones de acción -->
      <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 20px;">
        <a class="btn primary" href="<?php echo BASE_URL; ?>/mensajes?publicacion=<?php echo $publicacion->id; ?>">
          <?php echo icon('message-circle', 18); ?>
          Contactar vendedor
        </a>
        <button id="fav-toggle" data-pid="<?php echo $publicacion->id; ?>" class="btn" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <?php echo icon('heart', 18); ?>
          Favorito
        </button>
      </div>
      
      <button class="btn" style="margin-top: 8px; width: 100%;" onclick="compartir()">
        <?php echo icon('share-2', 18); ?>
        Compartir
      </button>
    </aside>
  </div>

  <!-- Grid secundario: Descripción + Vendedor -->
  <div class="grid cols-3" style="margin-top: 20px; gap: 20px;">
    
    <!-- Descripción (2 columnas) -->
    <div class="card" style="grid-column: 1/3;">
      <div class="h3">Descripción</div>
      <div style="margin-top: 12px; line-height: 1.6; white-space: pre-wrap;">
        <?php echo e($publicacion->descripcion ?? 'Sin descripción disponible.'); ?>
      </div>
      
      <?php if (!empty($publicacion->tipificacion)): ?>
        <div style="margin-top: 16px;">
          <strong>Tipificación:</strong> <?php echo e($publicacion->tipificacion); ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Vendedor -->
    <div class="card">
      <div class="h3">Vendedor</div>
      <div style="margin-top: 12px;">
        <div style="font-weight: 600; margin-bottom: 8px;">
          <?php echo e($publicacion->usuario_nombre ?? 'Usuario'); ?>
        </div>
        <div class="meta">
          <?php echo icon('mail', 16); ?>
          <?php echo e($publicacion->usuario_email ?? 'Email no disponible'); ?>
        </div>
        <?php if (!empty($publicacion->usuario_telefono)): ?>
          <div class="meta" style="margin-top: 4px;">
            <?php echo icon('phone', 16); ?>
            <?php echo e($publicacion->usuario_telefono); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>


  <!-- Publicaciones similares -->
  <?php if (!empty($similares)): ?>
  <section style="margin-top: 40px;">
    <div class="h2">Publicaciones similares</div>
    <div class="grid cols-4" style="margin-top: 16px;">
      <?php foreach ($similares as $similar): ?>
        <a class="card" href="<?php echo BASE_URL; ?>/detalle/<?php echo $similar->id; ?>">
          <div style="width: 100%; aspect-ratio: 4/3; background: var(--bg-secondary); border-radius: 8px; overflow: hidden; margin-bottom: 12px;">
            <?php if (!empty($similar->foto_principal)): ?>
              <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($similar->foto_principal); ?>" 
                   alt="<?php echo e($similar->titulo); ?>"
                   style="width: 100%; height: 100%; object-fit: cover;">
            <?php endif; ?>
          </div>
          <div class="h3"><?php echo e($similar->titulo); ?></div>
          <div class="row price-row" style="justify-content: space-between; margin-top: 8px;">
            <span class="badge"><?php echo $similar->tipo_venta === 'desarme' ? 'En desarme' : 'Siniestrado'; ?></span>
            <span class="<?php echo $similar->tipo_venta === 'desarme' ? 'meta' : 'h3'; ?>">
              <?php echo $similar->tipo_venta === 'desarme' ? 'A convenir' : formatPrice($similar->precio); ?>
            </span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</main>

<script>
// Función para compartir
function compartir() {
    const url = window.location.href;
    const titulo = <?php echo json_encode($publicacion->titulo); ?>;
    
    if (navigator.share) {
        navigator.share({
            title: titulo,
            text: 'Mira esta publicación en ChileChocados',
            url: url
        }).catch(err => console.log('Error al compartir:', err));
    } else {
        // Copiar al portapapeles
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado al portapapeles');
        });
    }
}

// Toggle favorito
document.getElementById('fav-toggle')?.addEventListener('click', function() {
    const pid = this.dataset.pid;
    // Aquí iría la lógica para agregar/quitar de favoritos
    this.classList.toggle('active');
});
</script>

<?php layout('footer'); ?>
