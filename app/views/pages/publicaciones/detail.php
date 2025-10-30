<?php  // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterTagBlock, PSR12.Files.FileHeader.SpacingAfterTagBlock

/**
 * Vista: Detalle de Publicación
 * Muestra toda la información de una publicación específica
 */
$pageTitle = $publicacion->titulo ?? 'Detalle de Publicación';
layout('header');
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
          <img id="imagen-principal" 
               src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($publicacion->foto_principal); ?>" 
               alt="<?php echo e($publicacion->titulo); ?>"
               style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.3s ease;">
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
            <div class="thumbnail-item" 
                 data-image="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($imagen->ruta ?? $imagen->ruta_archivo ?? ''); ?>"
                 style="aspect-ratio: 1; background: var(--bg-secondary); border-radius: 4px; overflow: hidden; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
              <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($imagen->ruta ?? $imagen->ruta_archivo ?? ''); ?>" 
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
<!-- Botón Favorito (agregar después del título) -->
<button 
    id="btnFavorito" 
    class="btn" 
    onclick="toggleFavorito(<?php echo $publicacion->id; ?>)"
    data-publicacion-id="<?php echo $publicacion->id; ?>"
>
    <svg id="iconoFavorito" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
    </svg>
    <span id="textoFavorito">Guardar favoritos</span>
</button>

<style>
.btn-favorito {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--cc-white);
    border: 2px solid var(--cc-border-default);
    border-radius: var(--cc-radius-lg);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-favorito:hover {
    border-color: var(--cc-primary);
    color: var(--cc-primary);
}

.btn-favorito.activo {
    background: var(--cc-primary-pale);
    border-color: var(--cc-primary);
    color: var(--cc-primary);
}

.btn-favorito.activo svg {
    fill: var(--cc-primary);
}
</style>

<script>
// Sistema de favoritos con localStorage (mock - sin BD)
document.addEventListener('DOMContentLoaded', function() {
    const publicacionId = <?php echo $publicacion->id; ?>;
    verificarEstadoFavorito(publicacionId);
});

function verificarEstadoFavorito(publicacionId) {
    const favoritos = JSON.parse(localStorage.getItem('favoritos') || '[]');
    const enFavoritos = favoritos.includes(publicacionId);
    actualizarBotonFavorito(enFavoritos);
}

function toggleFavorito(publicacionId) {
    const btn = document.getElementById('btnFavorito');
    const esActivo = btn.classList.contains('activo');
    
    // Obtener favoritos del localStorage
    let favoritos = JSON.parse(localStorage.getItem('favoritos') || '[]');
    
    if (esActivo) {
        // Eliminar de favoritos
        favoritos = favoritos.filter(id => id !== publicacionId);
        localStorage.setItem('favoritos', JSON.stringify(favoritos));
        actualizarBotonFavorito(false);
        
        // Mostrar notificación
        mostrarNotificacion('Eliminado de favoritos', 'info');
    } else {
        // Agregar a favoritos
        if (!favoritos.includes(publicacionId)) {
            favoritos.push(publicacionId);
            localStorage.setItem('favoritos', JSON.stringify(favoritos));
        }
        actualizarBotonFavorito(true);
        
        // Mostrar notificación
        mostrarNotificacion('Agregado a favoritos ❤️', 'success');
    }
}

function actualizarBotonFavorito(esActivo) {
    const btn = document.getElementById('btnFavorito');
    const icono = document.getElementById('iconoFavorito');
    const texto = document.getElementById('textoFavorito');
    
    if (esActivo) {
        btn.classList.add('activo');
        icono.setAttribute('fill', 'currentColor');
        texto.textContent = 'Guardado';
    } else {
        btn.classList.remove('activo');
        icono.setAttribute('fill', 'none');
        texto.textContent = 'Guardar';
    }
}

function mostrarNotificacion(mensaje, tipo) {
    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${tipo === 'success' ? '#d4edda' : '#d1ecf1'};
        color: ${tipo === 'success' ? '#155724' : '#0c5460'};
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    notif.textContent = mensaje;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notif.remove(), 300);
    }, 2000);
}
</script>
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

// Cambiar imagen principal al hacer clic en miniaturas
document.querySelectorAll('.thumbnail-item').forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        const imagenPrincipal = document.getElementById('imagen-principal');
        const nuevaImagen = this.dataset.image;
        
        if (imagenPrincipal && nuevaImagen) {
            // Efecto de transición suave
            imagenPrincipal.style.opacity = '0.5';
            
            setTimeout(() => {
                imagenPrincipal.src = nuevaImagen;
                imagenPrincipal.style.opacity = '1';
            }, 150);
        }
        
        // Resaltar miniatura seleccionada
        document.querySelectorAll('.thumbnail-item').forEach(t => {
            t.style.transform = 'scale(1)';
            t.style.boxShadow = 'none';
        });
        this.style.transform = 'scale(1.05)';
        this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    });
    
    // Efecto hover
    thumbnail.addEventListener('mouseenter', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1.02)';
        }
    });
    
    thumbnail.addEventListener('mouseleave', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1)';
        }
    });
});

// Cambiar imagen principal al hacer clic en miniaturas
document.querySelectorAll('.thumbnail-item').forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        const imagenPrincipal = document.getElementById('imagen-principal');
        const nuevaImagen = this.dataset.image;
        
        if (imagenPrincipal && nuevaImagen) {
            // Efecto de transición suave
            imagenPrincipal.style.opacity = '0.5';
            
            setTimeout(() => {
                imagenPrincipal.src = nuevaImagen;
                imagenPrincipal.style.opacity = '1';
            }, 150);
        }
        
        // Resaltar miniatura seleccionada
        document.querySelectorAll('.thumbnail-item').forEach(t => {
            t.style.transform = 'scale(1)';
            t.style.boxShadow = 'none';
        });
        this.style.transform = 'scale(1.05)';
        this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    });
    
    // Efecto hover
    thumbnail.addEventListener('mouseenter', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1.02)';
        }
    });
    
    thumbnail.addEventListener('mouseleave', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1)';
        }
    });
});

// Cambiar imagen principal al hacer clic en miniaturas
document.querySelectorAll('.thumbnail-item').forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        const imagenPrincipal = document.getElementById('imagen-principal');
        const nuevaImagen = this.dataset.image;
        
        if (imagenPrincipal && nuevaImagen) {
            imagenPrincipal.style.opacity = '0.5';
            setTimeout(() => {
                imagenPrincipal.src = nuevaImagen;
                imagenPrincipal.style.opacity = '1';
            }, 150);
        }
        
        document.querySelectorAll('.thumbnail-item').forEach(t => {
            t.style.transform = 'scale(1)';
            t.style.boxShadow = 'none';
        });
        this.style.transform = 'scale(1.05)';
        this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    });
    
    thumbnail.addEventListener('mouseenter', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1.02)';
        }
    });
    
    thumbnail.addEventListener('mouseleave', function() {
        if (this.style.transform !== 'scale(1.05)') {
            this.style.transform = 'scale(1)';
        }
    });
});
</script>

<?php layout('footer'); ?>

<script>
// Galería de imágenes - Cambiar imagen principal
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.thumbnail-item').forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const imagenPrincipal = document.getElementById('imagen-principal');
            const nuevaImagen = this.dataset.image;
            
            if (imagenPrincipal && nuevaImagen) {
                imagenPrincipal.style.opacity = '0.5';
                setTimeout(() => {
                    imagenPrincipal.src = nuevaImagen;
                    imagenPrincipal.style.opacity = '1';
                }, 150);
            }
            
            document.querySelectorAll('.thumbnail-item').forEach(t => {
                t.style.transform = 'scale(1)';
                t.style.boxShadow = 'none';
            });
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        });
        
        thumbnail.addEventListener('mouseenter', function() {
            if (this.style.transform !== 'scale(1.05)') {
                this.style.transform = 'scale(1.02)';
            }
        });
        
        thumbnail.addEventListener('mouseleave', function() {
            if (this.style.transform !== 'scale(1.05)') {
                this.style.transform = 'scale(1)';
            }
        });
    });
});
</script>
