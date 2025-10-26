<?php // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterTagBlock

/**
 * Vista: Categorías
 * Ruta: /categorias
 * Muestra todas las categorías de vehículos con sus subcategorías
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Categorías</span>
  </div>

  <!-- Banner de categorías -->
  <div class="card" style="margin-top: 16px; background: linear-gradient(135deg, #0066CC 0%, #004C99 100%); color: white;">
    <div class="h1" style="color: white;">Explora por Categoría</div>
    <p class="meta" style="margin-top: 8px; color: rgba(255,255,255,0.9);">
      Encuentra vehículos siniestrados y en desarme organizados por tipo. 
      <?php echo count($categorias); ?> categorías disponibles con 
      <?php echo number_format($total_publicaciones, 0, ',', '.'); ?> publicaciones activas.
    </p>
  </div>

  <!-- Grid de categorías padre -->
  <section style="margin-top: 24px;">
    <div class="h2">Categorías Principales</div>
    <p class="meta" style="margin-top: 8px;">
      Selecciona el tipo de vehículo que buscas. Cada categoría incluye diferentes subcategorías específicas.
    </p>

    <div class="grid cols-4" style="margin-top: 16px;">
      <?php if (!empty($categorias)): ?>
        <?php foreach ($categorias as $categoria): ?>
          <a class="card cat-card" href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $categoria->id; ?>">
            <div class="left">
              <!-- Icono según categoría -->
              <span class="iconify" style="font-size: 32px; color: #0066CC;">
                <?php
                // SVG icons según tipo de categoría
                $iconos = [
                  'auto' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-2z"></path><path d="M5 17H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h1"></path><path d="M19 17h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-1"></path><path d="M5 14l1-4h12l1 4"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>',
                  'moto' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M16 8h3l2 5"></path><path d="M5 19L14 9h-3L8 5"></path><path d="M14 9l3 10"></path></svg>',
                  'default' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="18" rx="2" ry="2"></rect><line x1="1" y1="9" x2="23" y2="9"></line></svg>'
                ];

                $slug = strtolower($categoria->slug ?? '');
                $icono_key = 'default';
                foreach (array_keys($iconos) as $key) {
                  if (strpos($slug, $key) !== false) {
                    $icono_key = $key;
                    break;
                  }
                }
                echo $iconos[$icono_key];
                ?>
              </span>
              
              <div>
                <div class="h3"><?php echo htmlspecialchars($categoria->nombre); ?></div>
                
                <!-- Mostrar hasta 3 subcategorías -->
                <?php if (!empty($categoria->subcategorias)): ?>
                  <p class="meta">
                    <?php
                    $subs = array_slice($categoria->subcategorias, 0, 3);
                    $nombres = array_map(function ($s) {
                      return htmlspecialchars($s->nombre);
                    }, $subs);
                    echo implode(' • ', $nombres);

                    $total_subs = count($categoria->subcategorias);
                    if ($total_subs > 3) {
                      echo ' (+' . ($total_subs - 3) . ' más)';
                    }
                    ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Contador de publicaciones -->
            <span class="cat-count" style="background: #0066CC; color: white; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;">
              <?php echo number_format($categoria->total_publicaciones, 0, ',', '.'); ?>
            </span>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
          <p class="meta">No hay categorías disponibles en este momento.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Sección de subcategorías destacadas -->
  <?php if (!empty($categorias)): ?>
    <section style="margin-top: 48px;">
      <div class="h2">Explora por Subcategoría</div>
      <p class="meta" style="margin-top: 8px;">
        Accede directamente a tipos específicos de vehículos
      </p>

      <?php foreach ($categorias as $categoria): ?>
        <?php if (!empty($categoria->subcategorias)): ?>
          <div class="card" style="margin-top: 16px;">
            <div class="h3"><?php echo htmlspecialchars($categoria->nombre); ?></div>
            
            <div class="grid cols-4" style="margin-top: 12px; gap: 8px;">
              <?php foreach ($categoria->subcategorias as $subcategoria): ?>
                <a 
                  href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $categoria->id; ?>&subcategoria=<?php echo $subcategoria->id; ?>" 
                  class="btn" 
                  style="justify-content: flex-start; text-align: left;"
                >
                  <?php echo htmlspecialchars($subcategoria->nombre); ?>
                  <span style="margin-left: auto; opacity: 0.6; font-size: 12px;">
                    (<?php echo $subcategoria->total_publicaciones ?? 0; ?>)
                  </span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>

  <!-- Call to action -->
  <div class="card" style="margin-top: 48px; background: #f8f9fa; text-align: center; padding: 32px;">
    <div class="h2">¿Tienes un vehículo para vender?</div>
    <p class="meta" style="margin-top: 8px;">
      Publica tu vehículo siniestrado o en desarme de forma gratuita
    </p>
    <div style="margin-top: 16px;">
      <a href="<?php echo BASE_URL; ?>/publicaciones/crear" class="btn primary" style="padding: 12px 24px;">
        Publicar mi vehículo
      </a>
      <a href="<?php echo BASE_URL; ?>/listado" class="btn" style="margin-left: 12px; padding: 12px 24px;">
        Ver todas las publicaciones
      </a>
    </div>
  </div>

  <div class="breadcrumbs" style="margin-top: 24px; margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>">← Volver al inicio</a>
  </div>
</main>

<!-- Estilos adicionales para tarjetas de categorías -->
<style>
.cat-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  transition: all 0.2s ease;
  border: 2px solid transparent;
}

.cat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  border-color: #0066CC;
}

.cat-card .left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.cat-card .h3 {
  margin: 0;
  font-size: 18px;
}

.cat-card .meta {
  margin: 4px 0 0 0;
  font-size: 13px;
}

.cat-count {
  flex-shrink: 0;
}
</style>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
