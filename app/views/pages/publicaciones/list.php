<?php
/**
 * Vista: Listado de Publicaciones
 * Ruta: /listado
 * Muestra publicaciones con filtros y búsqueda
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';

$filtros = $data['filtros_aplicados'];
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / 
    <a href="<?php echo BASE_URL; ?>/categorias">Categorías</a> / 
    <span>Listado</span>
  </div>

  <!-- Título y contador -->
  <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <div class="h1">Vehículos Disponibles</div>
      <p class="meta" style="margin-top: 4px;">
        Mostrando <?php echo count($data['publicaciones']); ?> de 
        <?php echo number_format($data['total'], 0, ',', '.'); ?> resultados
        <?php if (!empty($filtros['buscar'])): ?>
          para "<?php echo htmlspecialchars($filtros['buscar']); ?>"
        <?php endif; ?>
      </p>
    </div>
    
    <!-- Botón publicar -->
    <a href="<?php echo BASE_URL; ?>/publicaciones/crear" class="btn primary">
      + Publicar Vehículo
    </a>
  </div>

  <div style="display: grid; grid-template-columns: 280px 1fr; gap: 24px; margin-top: 24px;">
    
    <!-- SIDEBAR: Filtros -->
    <aside>
      <form method="GET" action="<?php echo BASE_URL; ?>/listado" id="filtros-form">
        
        <div class="card">
          <div class="h3">Filtros</div>
          
          <!-- Búsqueda por texto -->
          <div style="margin-top: 16px;">
            <label>
              Buscar
              <input 
                type="text" 
                name="q" 
                placeholder="Marca, modelo, año..." 
                value="<?php echo htmlspecialchars($filtros['buscar'] ?? ''); ?>"
              >
            </label>
          </div>

          <!-- Categoría -->
          <div style="margin-top: 12px;">
            <label>
              Categoría
              <select name="categoria" id="categoria-select">
                <option value="">Todas las categorías</option>
                <?php foreach ($data['categorias'] as $cat): ?>
                  <option 
                    value="<?php echo $cat['id']; ?>"
                    <?php echo ($filtros['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>
                  >
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <!-- Subcategoría (se carga dinámicamente) -->
          <div style="margin-top: 12px;" id="subcategoria-container" 
               <?php echo empty($filtros['categoria_id']) ? 'style="display:none"' : ''; ?>>
            <label>
              Subcategoría
              <select name="subcategoria" id="subcategoria-select">
                <option value="">Todas</option>
              </select>
            </label>
          </div>

          <!-- Región -->
          <div style="margin-top: 12px;">
            <label>
              Región
              <select name="region" id="region-select">
                <option value="">Todas las regiones</option>
                <?php foreach ($data['regiones'] as $region): ?>
                  <option 
                    value="<?php echo $region['id']; ?>"
                    <?php echo ($filtros['region_id'] == $region['id']) ? 'selected' : ''; ?>
                  >
                    <?php echo htmlspecialchars($region['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <!-- Estado del vehículo -->
          <div style="margin-top: 12px;">
            <label>
              Estado
              <select name="estado">
                <option value="">Todos</option>
                <option value="siniestrado" <?php echo ($filtros['estado'] == 'siniestrado') ? 'selected' : ''; ?>>
                  Siniestrado
                </option>
                <option value="desarme" <?php echo ($filtros['estado'] == 'desarme') ? 'selected' : ''; ?>>
                  En desarme
                </option>
              </select>
            </label>
          </div>

          <!-- Rango de precio -->
          <div style="margin-top: 12px;">
            <label>
              Precio mínimo
              <input 
                type="number" 
                name="precio_min" 
                placeholder="$ 0" 
                step="100000"
                value="<?php echo $filtros['precio_min'] ?? ''; ?>"
              >
            </label>
          </div>

          <div style="margin-top: 12px;">
            <label>
              Precio máximo
              <input 
                type="number" 
                name="precio_max" 
                placeholder="$ 50.000.000" 
                step="100000"
                value="<?php echo $filtros['precio_max'] ?? ''; ?>"
              >
            </label>
          </div>

          <!-- Botones -->
          <div style="margin-top: 16px; display: flex; gap: 8px;">
            <button type="submit" class="btn primary" style="flex: 1;">
              Aplicar
            </button>
            <a href="<?php echo BASE_URL; ?>/listado" class="btn" style="flex: 1; text-align: center;">
              Limpiar
            </a>
          </div>
        </div>

        <!-- Filtros activos -->
        <?php
        $filtros_activos = 0;
        if (!empty($filtros['categoria_id'])) $filtros_activos++;
        if (!empty($filtros['region_id'])) $filtros_activos++;
        if (!empty($filtros['estado'])) $filtros_activos++;
        if (!empty($filtros['buscar'])) $filtros_activos++;
        if (!empty($filtros['precio_min']) || !empty($filtros['precio_max'])) $filtros_activos++;
        ?>
        
        <?php if ($filtros_activos > 0): ?>
          <div class="card" style="margin-top: 12px; background: #e3f2fd;">
            <div style="font-size: 14px; font-weight: 600; color: #0066CC;">
              <?php echo $filtros_activos; ?> filtro(s) aplicado(s)
            </div>
          </div>
        <?php endif; ?>
      </form>
    </aside>

    <!-- CONTENIDO PRINCIPAL: Listado -->
    <section>
      
      <!-- Ordenamiento -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div class="meta">
          Página <?php echo $data['pagina_actual']; ?> de <?php echo $data['total_paginas']; ?>
        </div>
        
        <div style="display: flex; gap: 8px; align-items: center;">
          <span class="meta">Ordenar:</span>
          <select 
            name="orden" 
            form="filtros-form" 
            onchange="document.getElementById('filtros-form').submit()"
            style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 6px;"
          >
            <option value="recientes" <?php echo ($filtros['orden'] == 'recientes') ? 'selected' : ''; ?>>
              Más recientes
            </option>
            <option value="precio_asc" <?php echo ($filtros['orden'] == 'precio_asc') ? 'selected' : ''; ?>>
              Precio: menor a mayor
            </option>
            <option value="precio_desc" <?php echo ($filtros['orden'] == 'precio_desc') ? 'selected' : ''; ?>>
              Precio: mayor a menor
            </option>
            <option value="antiguos" <?php echo ($filtros['orden'] == 'antiguos') ? 'selected' : ''; ?>>
              Más antiguos
            </option>
          </select>
        </div>
      </div>

      <!-- Grid de publicaciones -->
      <?php if (!empty($data['publicaciones'])): ?>
        <div class="grid cols-3" style="gap: 16px;">
          <?php foreach ($data['publicaciones'] as $pub): ?>
            <a class="card" href="<?php echo BASE_URL; ?>/publicaciones/detalle/<?php echo $pub['id']; ?>">
              
              <!-- Imagen -->
              <div class="ph" style="background-image: url('<?php echo BASE_URL . ($pub['foto_principal'] ?? '/assets/placeholder.jpg'); ?>'); background-size: cover; background-position: center; height: 180px; border-radius: 8px;">
                <!-- Badge de estado -->
                <span class="badge" style="position: absolute; top: 8px; left: 8px; background: <?php echo ($pub['tipo_publicacion'] == 'siniestrado') ? '#f44336' : '#ff9800'; ?>;">
                  <?php echo ($pub['tipo_publicacion'] == 'siniestrado') ? 'Siniestrado' : 'En desarme'; ?>
                </span>
              </div>
              
              <!-- Info -->
              <div class="h3" style="margin-top: 12px;">
                <?php echo htmlspecialchars($pub['titulo']); ?>
              </div>
              
              <div class="meta" style="margin-top: 4px;">
                <?php echo htmlspecialchars($pub['categoria_nombre']); ?>
                <?php if (!empty($pub['subcategoria_nombre'])): ?>
                  • <?php echo htmlspecialchars($pub['subcategoria_nombre']); ?>
                <?php endif; ?>
              </div>
              
              <div class="meta" style="margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <?php echo htmlspecialchars($pub['region_nombre'] . ', ' . $pub['comuna_nombre']); ?>
              </div>
              
              <!-- Precio -->
              <div class="row price-row" style="justify-content: space-between; margin-top: 12px; align-items: center;">
                <?php if ($pub['precio'] > 0): ?>
                  <span class="h3" style="font-weight: 800; color: #0066CC;">
                    $ <?php echo number_format($pub['precio'], 0, ',', '.'); ?>
                  </span>
                  <?php if ($pub['precio_negociable']): ?>
                    <span class="meta" style="font-size: 12px;">Negociable</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="meta">A convenir</span>
                <?php endif; ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <?php if ($data['total_paginas'] > 1): ?>
          <div style="margin-top: 32px; display: flex; justify-content: center; gap: 8px; align-items: center;">
            
            <?php if ($data['pagina_actual'] > 1): ?>
              <a href="?<?php echo http_build_query(array_merge($filtros, ['page' => $data['pagina_actual'] - 1])); ?>" class="btn">
                ← Anterior
              </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
              <?php if ($i == $data['pagina_actual']): ?>
                <span class="btn primary" style="pointer-events: none;">
                  <?php echo $i; ?>
                </span>
              <?php elseif ($i == 1 || $i == $data['total_paginas'] || abs($i - $data['pagina_actual']) <= 2): ?>
                <a href="?<?php echo http_build_query(array_merge($filtros, ['page' => $i])); ?>" class="btn">
                  <?php echo $i; ?>
                </a>
              <?php elseif (abs($i - $data['pagina_actual']) == 3): ?>
                <span class="meta">...</span>
              <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($data['pagina_actual'] < $data['total_paginas']): ?>
              <a href="?<?php echo http_build_query(array_merge($filtros, ['page' => $data['pagina_actual'] + 1])); ?>" class="btn">
                Siguiente →
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <!-- Sin resultados -->
        <div class="card" style="text-align: center; padding: 64px 32px;">
          <div style="font-size: 48px; opacity: 0.3; margin-bottom: 16px;">🚗</div>
          <div class="h2">No se encontraron resultados</div>
          <p class="meta" style="margin-top: 8px;">
            Intenta ajustar los filtros o realiza una búsqueda diferente
          </p>
          <a href="<?php echo BASE_URL; ?>/listado" class="btn primary" style="margin-top: 16px;">
            Ver todas las publicaciones
          </a>
        </div>
      <?php endif; ?>

    </section>
  </div>
</main>

<!-- JavaScript para cargar subcategorías dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const categoriaSelect = document.getElementById('categoria-select');
  const subcategoriaContainer = document.getElementById('subcategoria-container');
  const subcategoriaSelect = document.getElementById('subcategoria-select');
  
  // Cargar subcategorías al cambiar categoría
  categoriaSelect.addEventListener('change', function() {
    const categoriaId = this.value;
    
    if (!categoriaId) {
      subcategoriaContainer.style.display = 'none';
      return;
    }
    
    // Mostrar container
    subcategoriaContainer.style.display = 'block';
    
    // Hacer petición AJAX para obtener subcategorías
    fetch('<?php echo BASE_URL; ?>/api/categorias/' + categoriaId + '/subcategorias')
      .then(response => response.json())
      .then(data => {
        // Limpiar select
        subcategoriaSelect.innerHTML = '<option value="">Todas</option>';
        
        // Agregar opciones
        if (data.success && data.data) {
          data.data.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.nombre;
            subcategoriaSelect.appendChild(option);
          });
        }
      })
      .catch(error => console.error('Error:', error));
  });
  
  // Si hay categoría seleccionada al cargar, trigger change
  <?php if (!empty($filtros['categoria_id'])): ?>
    categoriaSelect.dispatchEvent(new Event('change'));
    <?php if (!empty($filtros['subcategoria_id'])): ?>
      setTimeout(() => {
        subcategoriaSelect.value = '<?php echo $filtros['subcategoria_id']; ?>';
      }, 500);
    <?php endif; ?>
  <?php endif; ?>
});
</script>

<style>
.price-row {
  border-top: 1px solid #eee;
  padding-top: 12px;
}

aside .card {
  position: sticky;
  top: 24px;
}

@media (max-width: 768px) {
  main > div {
    grid-template-columns: 1fr !important;
  }
  
  aside {
    order: 2;
  }
}
</style>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
