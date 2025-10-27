<?php
/**
 * Vista: Detalle de Publicación para Moderación (Admin)
 * Permite revisar y tomar decisiones sobre una publicación
 */
$pageTitle = 'Revisar Publicación #' . $publicacion->id . ' - Admin';
require_once __DIR__ . '/../../layouts/header.php';
?>

<main class="container" style="padding: 32px 0;">
  
  <!-- Breadcrumb y acciones -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="meta" style="color: var(--cc-primary); text-decoration: none; display: flex; align-items: center; gap: 4px;">
        ← Volver a lista de publicaciones
      </a>
      <h1 class="h2" style="margin: 8px 0 0 0;">
        Revisar Publicación #<?php echo $publicacion->id; ?>
      </h1>
    </div>
    
    <!-- Estado actual -->
    <div>
      <?php
        $estado_colors = [
          'pendiente' => ['bg' => '#FFF3CD', 'color' => '#856404', 'text' => '⏳ Pendiente'],
          'aprobada' => ['bg' => '#D4EDDA', 'color' => '#155724', 'text' => '✓ Aprobada'],
          'rechazada' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'text' => '✗ Rechazada'],
          'borrador' => ['bg' => '#E2E3E5', 'color' => '#383D41', 'text' => '📝 Borrador'],
        ];
        $estado = $estado_colors[$publicacion->estado] ?? ['bg' => '#E2E3E5', 'color' => '#383D41', 'text' => $publicacion->estado];
      ?>
      <span style="
        display: inline-block;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        background-color: <?php echo $estado['bg']; ?>;
        color: <?php echo $estado['color']; ?>;
      ">
        <?php echo $estado['text']; ?>
      </span>
    </div>
  </div>

  <!-- Mensajes flash -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert success" style="margin-bottom: 24px;">
      <?php 
        echo htmlspecialchars($_SESSION['success']); 
        unset($_SESSION['success']);
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert error" style="margin-bottom: 24px;">
      <?php 
        echo htmlspecialchars($_SESSION['error']); 
        unset($_SESSION['error']);
      ?>
    </div>
  <?php endif; ?>

  <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    
    <!-- Columna Principal: Información de la Publicación -->
    <div>
      
      <!-- Galería de Fotos -->
      <?php if (!empty($fotos)): ?>
        <div class="card" style="margin-bottom: 24px; padding: 0; overflow: hidden;">
          <div style="position: relative; width: 100%; padding-top: 56.25%; background: var(--cc-gray-100);">
            <img 
              id="mainPhoto"
              src="<?php echo BASE_URL . $fotos[0]->ruta; ?>" 
              alt="Foto principal"
              style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
            >
          </div>
          
          <!-- Miniaturas -->
          <?php if (count($fotos) > 1): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 8px; padding: 16px; background: var(--cc-gray-50);">
              <?php foreach ($fotos as $index => $foto): ?>
                <img 
                  src="<?php echo BASE_URL . $foto->ruta; ?>" 
                  alt="Miniatura <?php echo $index + 1; ?>"
                  style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s;"
                  onclick="document.getElementById('mainPhoto').src = '<?php echo BASE_URL . $foto->ruta; ?>'; document.querySelectorAll('.thumb').forEach(t => t.style.borderColor = 'transparent'); this.style.borderColor = 'var(--cc-primary)';"
                  class="thumb"
                >
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="card" style="margin-bottom: 24px; padding: 64px; text-align: center; background: var(--cc-gray-50);">
          <div style="font-size: 48px; margin-bottom: 16px;">📷</div>
          <p class="meta">Sin fotos</p>
        </div>
      <?php endif; ?>

      <!-- Detalles de la Publicación -->
      <div class="card" style="margin-bottom: 24px;">
        <div class="h3" style="margin-bottom: 16px;">Información del Vehículo</div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
          
          <div>
            <div class="label">Título</div>
            <div style="font-weight: 500;">
              <?php echo htmlspecialchars($publicacion->titulo); ?>
            </div>
          </div>

          <div>
            <div class="label">Categoría</div>
            <div>
              <?php echo htmlspecialchars($publicacion->categoria_nombre); ?>
              <?php if ($publicacion->subcategoria_nombre): ?>
                <span class="meta"> / <?php echo htmlspecialchars($publicacion->subcategoria_nombre); ?></span>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($publicacion->marca): ?>
            <div>
              <div class="label">Marca</div>
              <div><?php echo htmlspecialchars($publicacion->marca); ?></div>
            </div>
          <?php endif; ?>

          <?php if ($publicacion->modelo): ?>
            <div>
              <div class="label">Modelo</div>
              <div><?php echo htmlspecialchars($publicacion->modelo); ?></div>
            </div>
          <?php endif; ?>

          <?php if ($publicacion->anio): ?>
            <div>
              <div class="label">Año</div>
              <div><?php echo htmlspecialchars($publicacion->anio); ?></div>
            </div>
          <?php endif; ?>

          <div>
            <div class="label">Tipo de Venta</div>
            <div style="text-transform: capitalize;">
              <?php echo htmlspecialchars($publicacion->tipo_venta); ?>
            </div>
          </div>

          <?php if ($publicacion->precio): ?>
            <div>
              <div class="label">Precio</div>
              <div style="font-size: 20px; font-weight: 600; color: var(--cc-primary);">
                $<?php echo number_format($publicacion->precio, 0, ',', '.'); ?>
              </div>
            </div>
          <?php endif; ?>

          <div>
            <div class="label">Ubicación</div>
            <div>
              <?php echo htmlspecialchars($publicacion->region_nombre); ?>
              <?php if ($publicacion->comuna_nombre): ?>
                <span class="meta"> / <?php echo htmlspecialchars($publicacion->comuna_nombre); ?></span>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <?php if ($publicacion->descripcion): ?>
          <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--cc-border-light);">
            <div class="label" style="margin-bottom: 8px;">Descripción</div>
            <div style="line-height: 1.6; white-space: pre-line;">
              <?php echo nl2br(htmlspecialchars($publicacion->descripcion)); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Motivo de Rechazo (si existe) -->
      <?php if ($publicacion->estado === 'rechazada' && $publicacion->motivo_rechazo): ?>
        <div class="card" style="background: #FFF3CD; border-left: 4px solid #FFC107; margin-bottom: 24px;">
          <div class="label" style="color: #856404; margin-bottom: 8px;">
            ⚠️ Motivo de Rechazo Anterior
          </div>
          <div style="color: #856404;">
            <?php echo nl2br(htmlspecialchars($publicacion->motivo_rechazo)); ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- Columna Lateral: Información del Usuario y Acciones -->
    <div>
      
      <!-- Información del Usuario -->
      <div class="card" style="margin-bottom: 24px;">
        <div class="h4" style="margin-bottom: 16px;">Información del Usuario</div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
          
          <div>
            <div class="label">Nombre Completo</div>
            <div style="font-weight: 500;">
              <?php echo htmlspecialchars($publicacion->usuario_nombre . ' ' . $publicacion->usuario_apellido); ?>
            </div>
          </div>

          <div>
            <div class="label">Email</div>
            <div>
              <a href="mailto:<?php echo htmlspecialchars($publicacion->usuario_email); ?>" style="color: var(--cc-primary); text-decoration: none;">
                <?php echo htmlspecialchars($publicacion->usuario_email); ?>
              </a>
            </div>
          </div>

          <?php if ($publicacion->usuario_telefono): ?>
            <div>
              <div class="label">Teléfono</div>
              <div>
                <a href="tel:<?php echo htmlspecialchars($publicacion->usuario_telefono); ?>" style="color: var(--cc-primary); text-decoration: none;">
                  <?php echo htmlspecialchars($publicacion->usuario_telefono); ?>
                </a>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($publicacion->usuario_rut): ?>
            <div>
              <div class="label">RUT</div>
              <div class="meta" style="font-family: 'JetBrains Mono', monospace;">
                <?php echo htmlspecialchars($publicacion->usuario_rut); ?>
              </div>
            </div>
          <?php endif; ?>

          <div>
            <div class="label">Registrado desde</div>
            <div class="meta">
              <?php echo date('d/m/Y', strtotime($publicacion->usuario_fecha_registro)); ?>
            </div>
          </div>

        </div>
      </div>

      <!-- Metadatos de la Publicación -->
      <div class="card" style="margin-bottom: 24px; background: var(--cc-gray-50);">
        <div class="label" style="margin-bottom: 12px;">Metadatos</div>
        
        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px;">
          <div style="display: flex; justify-content: space-between;">
            <span class="meta">ID:</span>
            <span style="font-family: 'JetBrains Mono', monospace; font-weight: 500;">#<?php echo $publicacion->id; ?></span>
          </div>
          
          <div style="display: flex; justify-content: space-between;">
            <span class="meta">Creada:</span>
            <span><?php echo date('d/m/Y H:i', strtotime($publicacion->fecha_creacion)); ?></span>
          </div>

          <?php if ($publicacion->fecha_publicacion): ?>
            <div style="display: flex; justify-content: space-between;">
              <span class="meta">Publicada:</span>
              <span><?php echo date('d/m/Y H:i', strtotime($publicacion->fecha_publicacion)); ?></span>
            </div>
          <?php endif; ?>

          <div style="display: flex; justify-content: space-between;">
            <span class="meta">Visitas:</span>
            <span style="font-weight: 500;"><?php echo number_format($publicacion->visitas); ?></span>
          </div>

          <?php if ($publicacion->es_destacada): ?>
            <div style="display: flex; justify-content: space-between; color: #FFC107;">
              <span>⭐ Destacada</span>
              <span style="font-size: 11px;">
                Hasta: <?php echo date('d/m', strtotime($publicacion->fecha_destacada_fin)); ?>
              </span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Acciones de Moderación -->
      <div class="card" style="border: 2px solid var(--cc-primary);">
        <div class="h4" style="margin-bottom: 16px; color: var(--cc-primary);">
          ⚡ Acciones de Moderación
        </div>

        <!-- Botón Aprobar -->
        <?php if ($publicacion->estado !== 'aprobada'): ?>
          <form method="POST" action="<?php echo BASE_URL; ?>/admin/publicaciones/<?php echo $publicacion->id; ?>/aprobar" style="margin-bottom: 12px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <button 
              type="submit" 
              class="btn primary"
              style="width: 100%; background: #34C759; border-color: #34C759; padding: 12px; font-weight: 600;"
              onclick="return confirm('¿Estás seguro de aprobar esta publicación?\n\nSe enviará una notificación al usuario.');"
            >
              ✓ Aprobar Publicación
            </button>
          </form>
        <?php endif; ?>

        <!-- Botón Rechazar (con modal) -->
        <?php if ($publicacion->estado !== 'rechazada'): ?>
          <button 
            type="button"
            class="btn"
            style="width: 100%; background: #FF3B30; color: white; border-color: #FF3B30; padding: 12px; font-weight: 600; margin-bottom: 12px;"
            onclick="document.getElementById('modalRechazo').style.display = 'flex';"
          >
            ✗ Rechazar Publicación
          </button>
        <?php endif; ?>

        <!-- Botón Ver Publicación Pública -->
        <?php if ($publicacion->estado === 'aprobada'): ?>
          <a 
            href="<?php echo BASE_URL; ?>/detalle/<?php echo $publicacion->id; ?>" 
            target="_blank"
            class="btn outline"
            style="width: 100%; padding: 12px; text-align: center; display: block; text-decoration: none;"
          >
            👁️ Ver Publicación Pública
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>

</main>

<!-- Modal de Rechazo -->
<div 
  id="modalRechazo" 
  style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
  "
  onclick="if(event.target === this) this.style.display = 'none';"
>
  <div class="card" style="max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <div class="h3" style="color: #FF3B30;">Rechazar Publicación</div>
      <button 
        type="button"
        onclick="document.getElementById('modalRechazo').style.display = 'none';"
        style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--cc-text-secondary);"
      >
        ×
      </button>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/admin/publicaciones/<?php echo $publicacion->id; ?>/rechazar">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      
      <div style="margin-bottom: 16px;">
        <label class="label">Motivo del Rechazo *</label>
        <textarea 
          name="motivo_rechazo" 
          class="input" 
          rows="6"
          placeholder="Explica claramente por qué se rechaza esta publicación. Este mensaje se enviará al usuario."
          required
          style="resize: vertical; min-height: 120px;"
        ></textarea>
        <div class="meta" style="margin-top: 4px; font-size: 12px;">
          Sé específico y constructivo. El usuario recibirá este mensaje por email.
        </div>
      </div>

      <div style="display: flex; gap: 12px;">
        <button 
          type="submit" 
          class="btn primary"
          style="flex: 1; background: #FF3B30; border-color: #FF3B30;"
        >
          Confirmar Rechazo
        </button>
        <button 
          type="button" 
          class="btn outline"
          style="flex: 1;"
          onclick="document.getElementById('modalRechazo').style.display = 'none';"
        >
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* Estilos adicionales para la galería */
.thumb:first-child {
  border-color: var(--cc-primary);
}

.thumb:hover {
  opacity: 0.8;
}

/* Estilos para el modal */
.card {
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 1024px) {
  .grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
