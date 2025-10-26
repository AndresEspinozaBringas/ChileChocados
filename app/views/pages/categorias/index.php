<?php
/**
 * Vista: Categorías
 * Ruta: /categorias
 * Descripción: Listado completo de categorías de vehículos con subcategorías
 * 
 * Variables disponibles desde CategoriaController:
 * - $pageTitle: Título de la página
 * - $pageDescription: Meta descripción
 * - $categorias: Array de categorías con subcategorías y conteo
 * - $total_categorias: Número total de categorías
 * - $total_publicaciones: Suma total de publicaciones
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container" style="padding-top: 32px; padding-bottom: 64px;">
  
  <!-- Breadcrumbs -->
  <div class="breadcrumbs" style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.875rem; color: var(--cc-gray-600);">
    <a href="<?php echo BASE_URL; ?>/" style="color: var(--cc-gray-600); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--cc-primary)'" onmouseout="this.style.color='var(--cc-gray-600)'">
      Inicio
    </a>
    <span>/</span>
    <span style="color: var(--cc-gray-900); font-weight: 600;">Categorías</span>
  </div>

  <!-- ============================================================================
       BANNER DE CATEGORÍAS
       ============================================================================ -->
  <div class="card" style="background: linear-gradient(135deg, var(--cc-primary) 0%, var(--cc-primary-dark) 100%); color: white; padding: 48px; margin-bottom: 48px; border: none; position: relative; overflow: hidden;">
    
    <!-- Patrón decorativo -->
    <div style="position: absolute; top: 0; right: 0; bottom: 0; width: 50%; opacity: 0.1; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, white 10px, white 20px);"></div>
    
    <div style="position: relative; z-index: 1;">
      <h1 class="h1" style="color: white; margin-bottom: 16px; font-size: 3rem;">
        Explora por Categoría
      </h1>
      <p class="meta" style="color: rgba(255,255,255,0.95); font-size: 1.125rem; margin-bottom: 24px; max-width: 700px;">
        Encuentra vehículos siniestrados y en desarme organizados por tipo.
        <?php if (isset($total_categorias) && isset($total_publicaciones)): ?>
          <br><strong style="color: var(--cc-secondary);"><?php echo $total_categorias; ?> categorías disponibles</strong> con 
          <strong style="color: var(--cc-secondary);"><?php echo number_format($total_publicaciones, 0, ',', '.'); ?> publicaciones activas</strong>.
        <?php endif; ?>
      </p>
      
      <!-- Botón de búsqueda -->
      <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?php echo BASE_URL; ?>/listado" class="btn" style="background: white; color: var(--cc-primary); border-color: white;">
          Ver Todas las Publicaciones
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
        <a href="<?php echo BASE_URL; ?>/publicar" class="btn" style="background: var(--cc-secondary); color: var(--cc-gray-900); border-color: var(--cc-secondary);">
          Publicar Vehículo
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- ============================================================================
       GRID DE CATEGORÍAS PRINCIPALES
       ============================================================================ -->
  <section style="margin-bottom: 64px;">
    <div style="margin-bottom: 32px;">
      <h2 class="h2" style="color: var(--cc-primary); margin-bottom: 8px;">
        Categorías Principales
      </h2>
      <p class="meta" style="font-size: 1rem;">
        Selecciona el tipo de vehículo que buscas. Cada categoría incluye diferentes subcategorías específicas.
      </p>
    </div>

    <div class="grid grid-cols-4" style="gap: 24px;">
      <?php if (!empty($categorias)): ?>
        <?php foreach ($categorias as $categoria): ?>
          <a class="card cat-card" href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $categoria->id; ?>" style="text-decoration: none; color: inherit; padding: 28px; transition: all 0.3s ease; border: 2px solid var(--cc-border-light); position: relative; overflow: hidden;" onmouseover="this.style.borderColor='var(--cc-primary)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--cc-shadow-lg)'" onmouseout="this.style.borderColor='var(--cc-border-light)'; this.style.transform='translateY(0)'; this.style.boxShadow='var(--cc-shadow-sm)'">
            
            <!-- Gradiente decorativo de fondo -->
            <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, var(--cc-primary-pale) 0%, transparent 100%); opacity: 0.5; border-radius: 0 0 0 100%;"></div>
            
            <div style="position: relative; z-index: 1;">
              <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                <!-- Icono según categoría -->
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, var(--cc-primary) 0%, var(--cc-primary-dark) 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(230, 51, 42, 0.25);">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <?php
                    // Iconos SVG según tipo de categoría
                    $iconos = [
                        'auto' => '<path d="M5 17h14v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-2z"/><path d="M5 17H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h1"/><path d="M19 17h2a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-1"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>',
                        'moto' => '<circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M16 8h3l2 5"/><path d="M5 19L14 9h-3L8 5"/><path d="M14 9l3 10"/>',
                        'camion' => '<rect x="1" y="3" width="22" height="18" rx="2"/><path d="M10 3v18"/><path d="M1 8h22"/><path d="M1 14h22"/>',
                        'bus' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/><circle cx="7" cy="17" r="1"/><circle cx="17" cy="17" r="1"/>',
                        'default' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>'
                    ];
                    
                    $slug = strtolower($categoria->slug ?? '');
                    $icon = $iconos['default'];
                    
                    if (strpos($slug, 'auto') !== false || strpos($slug, 'sedan') !== false) {
                        $icon = $iconos['auto'];
                    } elseif (strpos($slug, 'moto') !== false) {
                        $icon = $iconos['moto'];
                    } elseif (strpos($slug, 'camion') !== false || strpos($slug, 'truck') !== false) {
                        $icon = $iconos['camion'];
                    } elseif (strpos($slug, 'bus') !== false) {
                        $icon = $iconos['bus'];
                    }
                    
                    echo $icon;
                    ?>
                  </svg>
                </div>
                
                <!-- Contenido -->
                <div style="flex: 1;">
                  <h3 class="h3" style="font-size: 1.25rem; margin-bottom: 8px; color: var(--cc-gray-900);">
                    <?php echo htmlspecialchars($categoria->nombre); ?>
                  </h3>
                  
                  <?php if (!empty($categoria->subcategorias)): ?>
                    <?php 
                      $subcategorias_nombres = array_map(function($sub) {
                        return htmlspecialchars($sub->nombre);
                      }, array_slice($categoria->subcategorias, 0, 3));
                      
                      $total_subs = count($categoria->subcategorias);
                    ?>
                    <p class="meta" style="font-size: 0.875rem; color: var(--cc-gray-600); line-height: 1.5;">
                      <?php 
                        echo implode(' • ', $subcategorias_nombres);
                        if ($total_subs > 3) {
                          echo ' (+' . ($total_subs - 3) . ' más)';
                        }
                      ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Contador de publicaciones -->
              <div style="display: flex; justify-content: flex-end;">
                <span style="background: var(--cc-secondary); color: var(--cc-gray-900); padding: 6px 14px; border-radius: 10px; font-size: 0.875rem; font-weight: 700; box-shadow: 0 2px 4px rgba(245, 197, 66, 0.3);">
                  <?php echo number_format($categoria->total_publicaciones ?? 0, 0, ',', '.'); ?> publicaciones
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 64px; background: var(--cc-gray-50);">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--cc-gray-300)" stroke-width="2" style="margin: 0 auto 16px;">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M3 9h18"/>
            <path d="M9 21V9"/>
          </svg>
          <p class="meta" style="font-size: 1rem; color: var(--cc-gray-600);">
            No hay categorías disponibles en este momento.
          </p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============================================================================
       SECCIÓN DE SUBCATEGORÍAS DESTACADAS
       ============================================================================ -->
  <?php if (!empty($categorias)): ?>
    <section style="margin-bottom: 64px;">
      <div style="margin-bottom: 32px;">
        <h2 class="h2" style="color: var(--cc-primary); margin-bottom: 8px;">
          Explora por Subcategoría
        </h2>
        <p class="meta" style="font-size: 1rem;">
          Accede directamente a tipos específicos de vehículos para resultados más precisos
        </p>
      </div>

      <?php foreach ($categorias as $categoria): ?>
        <?php if (!empty($categoria->subcategorias)): ?>
          <div class="card" style="margin-bottom: 24px; padding: 32px; border: 1px solid var(--cc-border-light);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
              <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--cc-primary-light) 0%, var(--cc-primary) 100%); display: flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <path d="M3 9h18"/>
                </svg>
              </div>
              <h3 class="h3" style="font-size: 1.375rem; margin: 0; color: var(--cc-gray-900);">
                <?php echo htmlspecialchars($categoria->nombre); ?>
              </h3>
            </div>
            
            <div class="grid grid-cols-4" style="gap: 12px;">
              <?php foreach ($categoria->subcategorias as $subcategoria): ?>
                <a 
                  href="<?php echo BASE_URL; ?>/listado?categoria=<?php echo $categoria->id; ?>&subcategoria=<?php echo $subcategoria->id; ?>" 
                  class="btn outline" 
                  style="justify-content: space-between; text-align: left; padding: 14px 18px; background: white; transition: all 0.2s ease;"
                  onmouseover="this.style.background='var(--cc-primary-pale)'; this.style.borderColor='var(--cc-primary)'; this.style.color='var(--cc-primary)'"
                  onmouseout="this.style.background='white'; this.style.borderColor='var(--cc-border-default)'; this.style.color='var(--cc-text-primary)'"
                >
                  <span style="font-weight: 600;"><?php echo htmlspecialchars($subcategoria->nombre); ?></span>
                  <span style="font-size: 0.75rem; color: var(--cc-gray-600); background: var(--cc-gray-100); padding: 2px 8px; border-radius: 6px;">
                    <?php echo number_format($subcategoria->total_publicaciones ?? 0, 0, ',', '.'); ?>
                  </span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>

  <!-- ============================================================================
       CTA - LLAMADO A LA ACCIÓN
       ============================================================================ -->
  <section>
    <div class="card" style="background: linear-gradient(135deg, var(--cc-secondary) 0%, var(--cc-secondary-dark) 100%); padding: 64px 48px; text-align: center; border: none; position: relative; overflow: hidden;">
      
      <!-- Patrón decorativo -->
      <div style="position: absolute; inset: 0; opacity: 0.1; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, var(--cc-gray-900) 10px, var(--cc-gray-900) 20px);"></div>
      
      <div style="position: relative; z-index: 1;">
        <h2 class="h2" style="color: var(--cc-gray-900); margin-bottom: 16px; font-size: 2.25rem;">
          ¿No encuentras lo que buscas?
        </h2>
        <p class="meta" style="color: var(--cc-gray-800); font-size: 1.125rem; margin-bottom: 32px; max-width: 600px; margin-left: auto; margin-right: auto;">
          Crea una alerta personalizada y te notificaremos cuando aparezca el vehículo que necesitas
        </p>
        
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <a href="<?php echo BASE_URL; ?>/alertas/crear" class="btn" style="background: var(--cc-gray-900); color: white; border-color: var(--cc-gray-900); padding: 16px 32px; font-size: 1.125rem;">
            Crear Alerta
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </a>
          <a href="<?php echo BASE_URL; ?>/contacto" class="btn" style="background: white; color: var(--cc-gray-900); border-color: white; padding: 16px 32px; font-size: 1.125rem;">
            Contactar Soporte
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
