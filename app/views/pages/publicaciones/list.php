<?php
/**
 * Vista: Listado de Publicaciones
 * Ruta: /listado
 * Muestra publicaciones con filtros y búsqueda
 */

$pageTitle = 'Listado de Vehículos - ChileChocados';
$currentPage = 'listado';

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';

$filtros = $data['filtros_aplicados'] ?? [];

// DATOS DE PRUEBA - Mientras no hay BD
if (empty($data['publicaciones'])) {
    $data['publicaciones'] = [
        [
            'id' => 1,
            'titulo' => 'Toyota Corolla 2018 - Daño Frontal',
            'foto_principal' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Automóviles',
            'subcategoria_nombre' => 'Sedán',
            'tipo_publicacion' => 'siniestrado',
            'precio' => 4500000,
            'precio_negociable' => true,
            'region_nombre' => 'Metropolitana',
            'comuna_nombre' => 'Santiago Centro',
            'visitas' => 245,
            'fecha_publicacion' => '2025-10-25'
        ],
        [
            'id' => 2,
            'titulo' => 'Honda CBR 600RR 2020 - Daño Lateral',
            'foto_principal' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Motocicletas',
            'subcategoria_nombre' => 'Deportiva',
            'tipo_publicacion' => 'siniestrado',
            'precio' => 3200000,
            'precio_negociable' => false,
            'region_nombre' => 'Metropolitana',
            'comuna_nombre' => 'Las Condes',
            'visitas' => 189,
            'fecha_publicacion' => '2025-10-24'
        ],
        [
            'id' => 3,
            'titulo' => 'Chevrolet Spark 2019 - Choque Trasero',
            'foto_principal' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Automóviles',
            'subcategoria_nombre' => 'Hatchback',
            'tipo_publicacion' => 'siniestrado',
            'precio' => 2800000,
            'precio_negociable' => true,
            'region_nombre' => 'Metropolitana',
            'comuna_nombre' => 'Maipú',
            'visitas' => 312,
            'fecha_publicacion' => '2025-10-22'
        ],
        [
            'id' => 4,
            'titulo' => 'Nissan X-Trail 2017 - Volcamiento',
            'foto_principal' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=800&h=600&fit=crop',
            'categoria_nombre' => 'SUV',
            'subcategoria_nombre' => 'Mediana',
            'tipo_publicacion' => 'siniestrado',
            'precio' => 5500000,
            'precio_negociable' => false,
            'region_nombre' => 'Metropolitana',
            'comuna_nombre' => 'Providencia',
            'visitas' => 428,
            'fecha_publicacion' => '2025-10-29'
        ],
        [
            'id' => 5,
            'titulo' => 'Ford Ranger 2016 - En Desarme',
            'foto_principal' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Camionetas',
            'subcategoria_nombre' => 'Pickup',
            'tipo_publicacion' => 'desarme',
            'precio' => 0,
            'precio_negociable' => false,
            'region_nombre' => 'Valparaíso',
            'comuna_nombre' => 'Viña del Mar',
            'visitas' => 156,
            'fecha_publicacion' => '2025-10-20'
        ],
        [
            'id' => 6,
            'titulo' => 'Mazda 3 2019 - Daño Lateral Derecho',
            'foto_principal' => 'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=800&h=600&fit=crop',
            'categoria_nombre' => 'Automóviles',
            'subcategoria_nombre' => 'Sedán',
            'tipo_publicacion' => 'siniestrado',
            'precio' => 6200000,
            'precio_negociable' => true,
            'region_nombre' => 'Biobío',
            'comuna_nombre' => 'Concepción',
            'visitas' => 278,
            'fecha_publicacion' => '2025-10-28'
        ]
    ];
    $data['total'] = count($data['publicaciones']);
    $data['pagina_actual'] = 1;
    $data['total_paginas'] = 1;
}

$data['categorias'] = $data['categorias'] ?? [
    ['id' => 1, 'nombre' => 'Automóviles'],
    ['id' => 2, 'nombre' => 'Camionetas'],
    ['id' => 3, 'nombre' => 'SUV'],
    ['id' => 4, 'nombre' => 'Motocicletas'],
    ['id' => 5, 'nombre' => 'Camiones'],
];

$data['regiones'] = $data['regiones'] ?? [
    ['id' => 1, 'nombre' => 'Metropolitana'],
    ['id' => 2, 'nombre' => 'Valparaíso'],
    ['id' => 3, 'nombre' => 'Biobío'],
    ['id' => 4, 'nombre' => 'La Araucanía'],
];
?>

<main class="container" style="margin-top: 24px; margin-bottom: 60px;">
  
  <!-- Breadcrumbs -->
  <div style="margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>/" style="color: var(--cc-text-secondary, #666); text-decoration: none; font-size: 14px;">
      ← Volver al inicio
    </a>
  </div>

  <!-- Header con título y acciones -->
  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="h2" style="margin: 0 0 8px 0;">
        <?php if (!empty($filtros['categoria'])): ?>
          <?php echo htmlspecialchars($filtros['categoria']); ?>
        <?php else: ?>
          Vehículos Disponibles
        <?php endif; ?>
      </h1>
      <p class="meta" style="margin: 0;">
        <?php echo number_format($data['total'], 0, ',', '.'); ?> vehículos encontrados
        <?php if (!empty($filtros['q'])): ?>
          para "<strong><?php echo htmlspecialchars($filtros['q']); ?></strong>"
        <?php endif; ?>
      </p>
    </div>
    
    <a href="<?php echo BASE_URL; ?>/publicar" class="btn primary">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Publicar Vehículo
    </a>
  </div>

  <div style="display: grid; grid-template-columns: 280px 1fr; gap: 24px;">
    
    <!-- SIDEBAR: Filtros -->
    <aside>
      <form method="GET" action="<?php echo BASE_URL; ?>/listado" id="filtros-form">
        
        <div class="card" style="position: sticky; top: 24px;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h3 class="h3" style="margin: 0;">Filtros</h3>
            <?php if (!empty(array_filter($filtros))): ?>
              <a href="<?php echo BASE_URL; ?>/listado" style="font-size: 13px; color: var(--cc-primary, #E6332A); text-decoration: none;">
                Limpiar
              </a>
            <?php endif; ?>
          </div>
          
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
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
          <?php foreach ($data['publicaciones'] as $pub): ?>
            <article class="card publicacion-card" style="padding: 0; overflow: hidden; transition: all 0.2s ease;">
              <a href="<?php echo BASE_URL; ?>/detalle/<?php echo $pub['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                
                <!-- Imagen -->
                <div style="position: relative; width: 100%; padding-top: 66.67%; background: var(--cc-bg-muted, #F5F5F5); overflow: hidden;">
                  <img 
                    src="<?php echo htmlspecialchars($pub['foto_principal']); ?>" 
                    alt="<?php echo htmlspecialchars($pub['titulo']); ?>"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                    loading="lazy"
                  >
                  
                  <!-- Badge de estado -->
                  <span class="badge" style="position: absolute; top: 12px; left: 12px; background: <?php echo ($pub['tipo_publicacion'] == 'siniestrado') ? 'var(--cc-danger, #EF4444)' : 'var(--cc-warning, #F59E0B)'; ?>; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                    <?php echo ($pub['tipo_publicacion'] == 'siniestrado') ? 'SINIESTRADO' : 'EN DESARME'; ?>
                  </span>
                  
                  <!-- Botón favorito -->
                  <button 
                    class="btn-favorito" 
                    onclick="event.preventDefault(); toggleFavorito(<?php echo $pub['id']; ?>)"
                    style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: all 0.2s ease;"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--cc-danger, #EF4444)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                  </button>
                </div>
                
                <!-- Contenido -->
                <div style="padding: 16px;">
                  <!-- Categoría -->
                  <div style="font-size: 11px; font-weight: 600; color: var(--cc-primary, #E6332A); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                    <?php echo htmlspecialchars($pub['categoria_nombre']); ?>
                    <?php if (!empty($pub['subcategoria_nombre'])): ?>
                      · <?php echo htmlspecialchars($pub['subcategoria_nombre']); ?>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Título -->
                  <h3 style="font-size: 16px; font-weight: 700; color: var(--cc-text-primary, #1A1A1A); margin: 0 0 8px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo htmlspecialchars($pub['titulo']); ?>
                  </h3>
                  
                  <!-- Ubicación -->
                  <div style="display: flex; align-items: center; gap: 4px; font-size: 13px; color: var(--cc-text-secondary, #666); margin-bottom: 12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                      <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span><?php echo htmlspecialchars($pub['comuna_nombre']); ?></span>
                  </div>
                  
                  <!-- Precio -->
                  <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--cc-border-light, #E8E8E8);">
                    <?php if ($pub['precio'] > 0): ?>
                      <span style="font-size: 20px; font-weight: 700; color: var(--cc-primary, #E6332A);">
                        $<?php echo number_format($pub['precio'], 0, ',', '.'); ?>
                      </span>
                      <?php if ($pub['precio_negociable']): ?>
                        <span style="font-size: 11px; color: var(--cc-text-tertiary, #999); background: var(--cc-bg-muted, #F5F5F5); padding: 2px 8px; border-radius: 4px;">
                          Negociable
                        </span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span style="font-size: 14px; font-weight: 600; color: var(--cc-text-secondary, #666);">
                        A convenir
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              </a>
            </article>
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
/* Cards de publicaciones */
.publicacion-card {
  background: var(--cc-white, #FFFFFF);
  border: 1px solid var(--cc-border-default, #E5E5E5);
  border-radius: 12px;
}

.publicacion-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.btn-favorito:hover {
  transform: scale(1.1);
  background: var(--cc-danger-light, #FEE2E2) !important;
}

/* Filtros */
aside .card {
  position: sticky;
  top: 24px;
}

aside label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--cc-text-primary, #1A1A1A);
  margin-bottom: 6px;
}

aside input,
aside select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--cc-border-default, #E5E5E5);
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
}

aside input:focus,
aside select:focus {
  outline: none;
  border-color: var(--cc-primary, #E6332A);
  box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.1);
}

/* Responsive */
@media (max-width: 968px) {
  main > div {
    grid-template-columns: 1fr !important;
  }
  
  aside {
    order: 2;
  }
  
  aside .card {
    position: static;
  }
}

@media (max-width: 640px) {
  .publicacion-card {
    margin-bottom: 16px;
  }
}
</style>

<script>
// Toggle favorito
function toggleFavorito(publicacionId) {
  // Aquí iría la lógica para agregar/quitar de favoritos
  console.log('Toggle favorito:', publicacionId);
  alert('Funcionalidad de favoritos próximamente');
}
</script>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
