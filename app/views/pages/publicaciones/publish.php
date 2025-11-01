<?php
use App\Helpers\Auth;

// Determinar si estamos en modo edición
$modoEdicion = isset($modoEdicion) && $modoEdicion;
$publicacion = $publicacion ?? null;
$imagenes = $imagenes ?? [];

// Título de la página
if (!isset($pageTitle)) {
  $pageTitle = $modoEdicion ? 'Editar Publicación' : 'Publicar Vehículo';
}

// Verificar que el usuario esté logueado
if (!Auth::check()) {
  header('Location: ' . BASE_URL . '/login');
  exit;
}

// Definir constantes de precios si no existen
if (!defined('PRECIO_DESTACADO_15_DIAS')) {
  define('PRECIO_DESTACADO_15_DIAS', 15000);
}
if (!defined('PRECIO_DESTACADO_30_DIAS')) {
  define('PRECIO_DESTACADO_30_DIAS', 25000);
}

require_once APP_PATH . '/views/layouts/header.php';
?>

<style>
/* Estilos mejorados para el formulario de publicación */
.container form input[type="text"],
.container form input[type="number"],
.container form select,
.container form textarea {
  padding: 12px 16px !important;
  font-size: 15px !important;
  line-height: 1.5 !important;
  border: 1px solid #ddd !important;
  border-radius: 8px !important;
  width: 100% !important;
  box-sizing: border-box !important;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

.container form input::placeholder,
.container form textarea::placeholder {
  color: #999 !important;
  font-size: 14px !important;
}

.container form label {
  display: block !important;
  margin-bottom: 8px !important;
  font-weight: 500 !important;
  font-size: 14px !important;
  color: #333 !important;
}

.container form select {
  appearance: none !important;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
  background-repeat: no-repeat !important;
  background-position: right 12px center !important;
  padding-right: 36px !important;
}

.container form textarea {
  resize: vertical !important;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

/* Estilos para radio buttons en horizontal */
.kit {
  display: flex !important;
  gap: 16px !important;
  flex-wrap: wrap !important;
}

.kit label.tag {
  flex: 0 0 auto !important;
  padding: 12px 20px !important;
  border: 2px solid #ddd !important;
  border-radius: 8px !important;
  cursor: pointer !important;
  transition: all 0.2s !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

.kit label.tag:hover {
  border-color: #E6332A !important;
  background: #fff5f5 !important;
}

.kit label.tag input[type="radio"] {
  margin: 0 !important;
  width: auto !important;
}

/* Estilos para el paso 4 - Fotos */
.gallery {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 16px !important;
  margin-top: 16px !important;
}

.gallery label {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  padding: 24px 16px !important;
  border: 2px dashed #ddd !important;
  border-radius: 8px !important;
  cursor: pointer !important;
  transition: all 0.2s !important;
  min-height: 120px !important;
  justify-content: center !important;
}

.gallery label:hover {
  border-color: #E6332A !important;
  background: #fff5f5 !important;
}

.gallery input[type="file"] {
  display: none !important;
}

.gallery input[type="radio"] {
  margin-top: 8px !important;
  width: auto !important;
}
</style>

<main class="container">

  <div class="h1"><?php echo $modoEdicion ? 'Editar publicación' : 'Publicar vehículo'; ?></div>
  
  <?php
  // Determinar la URL de acción según el modo
  if ($modoEdicion) {
    $action_url = BASE_URL . '/publicaciones/' . $publicacionId . '/update';
  } else {
    $action_url = BASE_URL . '/publicar/procesar';
  }
  ?>
  <form id="form-publicar" method="POST" action="<?php echo $action_url; ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
    
    <div class="card">
      <div class="h3">Paso 1: Tipificación</div>
      <div class="kit">
        <label class="tag" id="label-chocado">
          <input type="radio" name="tipificacion" value="chocado" id="tip-chocado" 
            <?php echo ($modoEdicion && $publicacion->tipificacion === 'chocado') ? 'checked' : (!$modoEdicion ? 'required' : ''); ?>> Chocado
        </label>
        <label class="tag" id="label-siniestrado">
          <input type="radio" name="tipificacion" value="siniestrado" id="tip-siniestrado"
            <?php echo ($modoEdicion && $publicacion->tipificacion === 'siniestrado') ? 'checked' : ''; ?>> Siniestrado
        </label>
      </div>
      <p class="meta" style="margin-top: 8px; font-size: 13px;">
        <strong>Chocado:</strong> Venta directa con precio definido · 
        <strong>Siniestrado:</strong> Precio a convenir
      </p>
    </div>

    <div class="card" id="step2">
      <div class="h3">Paso 2: Tipo de venta</div>
      <div class="kit">
        <label class="tag" id="label-completo">
          <input type="radio" name="tipo_venta" value="completo" id="venta-completo" 
            <?php echo ($modoEdicion && $publicacion->tipo_venta === 'completo') ? 'checked' : (!$modoEdicion ? 'required' : ''); ?>> Venta Directa (con precio)
        </label>
        <label class="tag" id="label-desarme">
          <input type="radio" name="tipo_venta" value="desarme" id="venta-desarme"
            <?php echo ($modoEdicion && $publicacion->tipo_venta === 'desarme') ? 'checked' : ''; ?>> Precio a convenir
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 3: Datos del vehículo</div>
      <!-- Fila 1: Marca, Modelo, Año -->
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Marca
          <input type="text" name="marca" placeholder="Ej: Toyota" 
            value="<?php echo $modoEdicion ? htmlspecialchars($publicacion->marca ?? '') : ''; ?>" 
            <?php echo !$modoEdicion ? 'required' : ''; ?>>
        </label>
        
        <label>Modelo
          <input type="text" name="modelo" placeholder="Ej: Corolla" 
            value="<?php echo $modoEdicion ? htmlspecialchars($publicacion->modelo ?? '') : ''; ?>" 
            <?php echo !$modoEdicion ? 'required' : ''; ?>>
        </label>
        
        <label>Año
          <input type="number" name="anio" placeholder="2020" min="1900" max="2025" 
            value="<?php echo $modoEdicion ? ($publicacion->anio ?? '') : ''; ?>" 
            <?php echo !$modoEdicion ? 'required' : ''; ?>>
        </label>
      </div>
      
      <!-- Fila 2: Categoría, Subcategoría -->
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Categoría
          <select name="categoria_padre_id" id="categoria_padre" <?php echo !$modoEdicion ? 'required' : ''; ?>>
            <option value="">Seleccionar...</option>
            <?php if (!empty($categorias)): ?>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria->id; ?>" 
                  data-subcategorias='<?php echo json_encode($categoria->subcategorias ?? []); ?>'
                  <?php echo ($modoEdicion && $publicacion->categoria_padre_id == $categoria->id) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($categoria->nombre); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </label>
        
        <label>Subcategoría
          <select name="subcategoria_id" id="subcategoria">
            <option value="">Selecciona categoría...</option>
          </select>
        </label>
      </div>
      
      <!-- Fila 3: Región, Comuna -->
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Región
          <select name="region_id" id="region" <?php echo !$modoEdicion ? 'required' : ''; ?>>
            <option value="">Seleccionar...</option>
            <?php if (!empty($regiones)): ?>
              <?php foreach ($regiones as $region): ?>
                <option value="<?php echo $region->id; ?>"
                  <?php echo ($modoEdicion && $publicacion->region_id == $region->id) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($region->nombre); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </label>
        
        <label>Comuna
          <select name="comuna_id" id="comuna" <?php echo !$modoEdicion ? 'required' : ''; ?>>
            <option value="">Selecciona región...</option>
          </select>
        </label>
      </div>
      
      <!-- Fila 4: Precio -->
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
        <label id="precio-field">Precio
          <input type="text" name="precio" placeholder="Ej: $5.000.000"
            value="<?php echo $modoEdicion ? number_format($publicacion->precio ?? 0, 0, ',', '.') : ''; ?>">
        </label>
        <div></div>
      </div>
      
      <!-- Fila 4: Descripción (ancho completo) -->
      <div style="margin-bottom: 16px;">
        <label style="display: block;">Descripción detallada
          <textarea name="descripcion" rows="6" placeholder="Describe los daños principales, estado actual del vehículo, piezas disponibles, historial, etc. Sé lo más detallado posible." <?php echo !$modoEdicion ? 'required' : ''; ?> style="min-height: 120px; width: 100%; margin-top: 8px;"><?php echo $modoEdicion ? htmlspecialchars($publicacion->descripcion ?? '') : ''; ?></textarea>
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 4: Fotos (1 a 6) · Selecciona la <strong>foto principal</strong></div>
      
      <?php if ($modoEdicion && !empty($imagenes)): ?>
        <div style="margin-bottom: 20px; padding: 12px; background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px;">
          <p style="margin: 0; font-size: 14px; color: #1e40af;">
            <?php echo icon('info', 16); ?> <strong>Fotos actuales:</strong> <?php echo count($imagenes); ?> imagen(es). Puedes agregar más fotos o reemplazarlas.
          </p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
          <?php foreach ($imagenes as $img): ?>
            <?php 
            $rutaImagen = BASE_URL . '/uploads/publicaciones/' . htmlspecialchars($img->ruta);
            ?>
            <div style="position: relative; border: 2px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
              <img src="<?php echo $rutaImagen; ?>" 
                   alt="Foto" 
                   style="width: 100%; height: 150px; object-fit: cover;"
                   onerror="this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:150px;background:#f3f4f6;color:#9ca3af;\'>Imagen no disponible</div>'">
              <?php if ($img->es_principal): ?>
                <div style="position: absolute; top: 8px; right: 8px; background: #10b981; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                  PRINCIPAL
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <div class="gallery">
        <?php for ($i = 1; $i <= 6; $i++): ?>
        <label>
          <span style="font-size: 14px; color: #666; margin-bottom: 8px;">Foto <?php echo $i; ?></span>
          <input type="file" name="fotos[]" accept="image/*" id="foto_<?php echo $i; ?>">
          <button type="button" onclick="document.getElementById('foto_<?php echo $i; ?>').click()" style="padding: 8px 16px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin-top: 8px;">
            Seleccionar archivo
          </button>
          <span style="font-size: 12px; color: #999; margin-top: 8px;">Sin archivos seleccionados</span>
          <div style="margin-top: 12px; display: flex; align-items: center; gap: 6px;">
            <input type="radio" name="foto_principal" value="<?php echo $i; ?>" <?php echo $i === 1 ? 'checked' : ''; ?>>
            <span style="font-size: 13px;">Foto principal</span>
          </div>
        </label>
        <?php endfor; ?>
      </div>
      <p class="meta" style="margin-top: 16px; color: #666;">
        <?php if ($modoEdicion): ?>
          Las nuevas fotos se agregarán a las existentes. La primera foto será la principal por defecto.
        <?php else: ?>
          La primera foto será la principal por defecto
        <?php endif; ?>
      </p>
    </div>

    <div class="card">
      <div class="h3">Paso 5: Promoción</div>
      <div class="kit">
        <?php
        // Determinar qué promoción está seleccionada
        $promocionActual = 'normal';
        if ($modoEdicion && isset($publicacion->es_destacada) && $publicacion->es_destacada == 1) {
          // Calcular días de diferencia si hay fechas
          if (!empty($publicacion->fecha_destacada_fin) && !empty($publicacion->fecha_destacada_inicio)) {
            $diff_seconds = strtotime($publicacion->fecha_destacada_fin) - strtotime($publicacion->fecha_destacada_inicio);
            $diff_days = $diff_seconds / (24 * 3600);
            $promocionActual = ($diff_days <= 20) ? 'destacada15' : 'destacada30';
          } else {
            $promocionActual = 'destacada15'; // Por defecto si no hay fechas
          }
        }
        ?>
        <label class="tag">
          <input type="radio" name="promocion" value="normal" 
            <?php echo ($promocionActual === 'normal') ? 'checked' : ''; ?>> Normal (gratis)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada15"
            <?php echo ($promocionActual === 'destacada15') ? 'checked' : ''; ?>> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_15_DIAS); ?> · 15 días)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada30"
            <?php echo ($promocionActual === 'destacada30') ? 'checked' : ''; ?>> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_30_DIAS); ?> · 30 días)
        </label>
      </div>
    </div>

    <div class="sticky-actions">
      <button type="button" class="btn" onclick="guardarBorrador()">Guardar borrador</button>
      <button type="submit" class="btn primary" id="btn-enviar">
        <?php echo $modoEdicion ? 'Actualizar publicación' : 'Enviar a revisión'; ?>
      </button>
    </div>
  </form>

</main>

<script>
// ============================================================================
// LÓGICA DE TIPIFICACIÓN Y TIPO DE VENTA
// ============================================================================

// Controlar la relación entre Tipificación y Tipo de Venta
document.querySelectorAll('input[name="tipificacion"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const ventaCompleto = document.getElementById('venta-completo');
    const ventaDesarme = document.getElementById('venta-desarme');
    const labelCompleto = document.getElementById('label-completo');
    const labelDesarme = document.getElementById('label-desarme');
    const precioField = document.getElementById('precio-field');
    const precioInput = document.querySelector('input[name="precio"]');
    
    if (this.value === 'chocado') {
      // CHOCADO: Solo "Venta Directa (con precio)"
      ventaCompleto.checked = true;
      ventaCompleto.disabled = false;
      ventaDesarme.disabled = true;
      ventaDesarme.checked = false;
      
      labelCompleto.style.opacity = '1';
      labelCompleto.style.cursor = 'pointer';
      labelDesarme.style.opacity = '0.4';
      labelDesarme.style.cursor = 'not-allowed';
      
      // Mostrar y habilitar campo precio
      precioField.style.display = 'block';
      precioInput.disabled = false;
      precioInput.required = true;
      
    } else if (this.value === 'siniestrado') {
      // SINIESTRADO: Solo "Precio a convenir"
      ventaDesarme.checked = true;
      ventaDesarme.disabled = false;
      ventaCompleto.disabled = true;
      ventaCompleto.checked = false;
      
      labelDesarme.style.opacity = '1';
      labelDesarme.style.cursor = 'pointer';
      labelCompleto.style.opacity = '0.4';
      labelCompleto.style.cursor = 'not-allowed';
      
      // Ocultar, deshabilitar y limpiar campo precio
      precioField.style.display = 'none';
      precioInput.disabled = true;
      precioInput.required = false;
      precioInput.value = ''; // Limpiar el valor
    }
  });
});

// Ocultar/mostrar campo precio según tipo de venta
document.querySelectorAll('input[name="tipo_venta"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const precioField = document.getElementById('precio-field');
    const precioInput = document.querySelector('input[name="precio"]');
    
    if (this.value === 'desarme') {
      // Precio a convenir: ocultar, deshabilitar y limpiar
      precioField.style.display = 'none';
      precioInput.disabled = true;
      precioInput.required = false;
      precioInput.value = '';
    } else {
      // Venta directa: mostrar y habilitar
      precioField.style.display = 'block';
      precioInput.disabled = false;
      precioInput.required = true;
    }
  });
});

// Cargar subcategorías cuando se selecciona una categoría
document.getElementById('categoria_padre').addEventListener('change', function() {
  const subcategoriaSelect = document.getElementById('subcategoria');
  const selectedOption = this.options[this.selectedIndex];
  
  // Limpiar subcategorías
  subcategoriaSelect.innerHTML = '<option value="">Seleccionar subcategoría...</option>';
  
  if (this.value) {
    try {
      const subcategorias = JSON.parse(selectedOption.getAttribute('data-subcategorias') || '[]');
      
      if (subcategorias.length > 0) {
        subcategorias.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.nombre;
          subcategoriaSelect.appendChild(option);
        });
      } else {
        subcategoriaSelect.innerHTML = '<option value="">No hay subcategorías disponibles</option>';
      }
    } catch (e) {
      console.error('Error al cargar subcategorías:', e);
    }
  }
});

// Cargar comunas cuando se selecciona una región
document.getElementById('region').addEventListener('change', function() {
  const comunaSelect = document.querySelector('select[name="comuna_id"]');
  const regionId = this.value;
  
  if (!comunaSelect) return;
  
  // Limpiar comunas
  comunaSelect.innerHTML = '<option value="">Cargando comunas...</option>';
  
  if (regionId) {
    // Hacer petición AJAX para obtener comunas
    fetch(`<?php echo BASE_URL; ?>/api/comunas?region_id=${regionId}`)
      .then(response => response.json())
      .then(data => {
        comunaSelect.innerHTML = '<option value="">Seleccionar comuna...</option>';
        if (data.comunas && data.comunas.length > 0) {
          data.comunas.forEach(comuna => {
            const option = document.createElement('option');
            option.value = comuna.id;
            option.textContent = comuna.nombre;
            comunaSelect.appendChild(option);
          });
        } else {
          comunaSelect.innerHTML = '<option value="">No hay comunas disponibles</option>';
        }
      })
      .catch(error => {
        console.error('Error al cargar comunas:', error);
        comunaSelect.innerHTML = '<option value="">Error al cargar comunas</option>';
      });
  } else {
    comunaSelect.innerHTML = '<option value="">Primero selecciona una región...</option>';
  }
});

// Preview de imágenes cuando se seleccionan
document.querySelectorAll('input[type="file"][name="fotos[]"]').forEach((input, index) => {
  input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const label = this.closest('label');
    const statusSpan = label.querySelector('span[style*="font-size: 12px"]');
    
    if (file) {
      // Validar que sea una imagen
      if (!file.type.startsWith('image/')) {
        alert('Por favor selecciona solo archivos de imagen');
        this.value = '';
        return;
      }
      
      // Validar tamaño (máximo 5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('La imagen no debe superar 5MB');
        this.value = '';
        return;
      }
      
      // Actualizar texto de estado
      statusSpan.textContent = file.name;
      statusSpan.style.color = '#4CAF50';
      
      // Crear preview
      const reader = new FileReader();
      reader.onload = function(e) {
        // Buscar si ya existe un preview
        let preview = label.querySelector('.image-preview');
        if (!preview) {
          preview = document.createElement('img');
          preview.className = 'image-preview';
          preview.style.cssText = 'width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-top: 12px;';
          label.insertBefore(preview, label.querySelector('button'));
        }
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      statusSpan.textContent = 'Sin archivos seleccionados';
      statusSpan.style.color = '#999';
      
      // Eliminar preview si existe
      const preview = label.querySelector('.image-preview');
      if (preview) {
        preview.remove();
      }
    }
  });
});

function guardarBorrador() {
  // Validar campos requeridos
  const errores = validarFormulario(true);
  
  if (errores.length > 0) {
    mostrarModalValidacion(errores);
    return;
  }
  
  const form = document.getElementById('form-publicar');
  
  // Verificar que el formulario existe
  if (!form) {
    alert('Error: No se encontró el formulario de publicación');
    console.error('Formulario #form-publicar no encontrado');
    return;
  }
  
  // Bloquear botón y mostrar loader
  const btnBorrador = event.target;
  const textoOriginal = btnBorrador.innerHTML;
  btnBorrador.disabled = true;
  btnBorrador.innerHTML = '<span style="display: inline-flex; align-items: center; gap: 8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg> Guardando...</span>';
  
  // Remover validación HTML5 temporalmente
  const requiredFields = form.querySelectorAll('[required]');
  requiredFields.forEach(field => {
    field.removeAttribute('required');
    field.dataset.wasRequired = 'true';
  });
  
  // Agregar campo oculto para indicar que es borrador
  let input = form.querySelector('input[name="guardar_borrador"]');
  if (!input) {
    input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'guardar_borrador';
    input.value = '1';
    form.appendChild(input);
  }
  
  // Debug
  console.log('Guardando borrador...');
  console.log('Form action:', form.action);
  console.log('Form method:', form.method);
  
  // Enviar formulario
  form.submit();
}

// Agregar event listener al formulario para limpiar campo borrador en submit normal
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('form-publicar');
  const btnEnviar = document.getElementById('btn-enviar');
  
  if (form && btnEnviar) {
    // Cuando se hace click en el botón de enviar (no borrador)
    btnEnviar.addEventListener('click', function(e) {
      const borradorInput = form.querySelector('input[name="guardar_borrador"]');
      if (borradorInput) {
        borradorInput.remove();
        console.log('Campo guardar_borrador eliminado antes de enviar');
      }
    });
  }
});

// ============================================================================
// MODO EDICIÓN: Cargar subcategoría y comuna seleccionadas
// ============================================================================
<?php if ($modoEdicion): ?>
document.addEventListener('DOMContentLoaded', function() {
  // Cargar subcategoría seleccionada
  const categoriaSelect = document.getElementById('categoria_padre');
  const subcategoriaSelect = document.getElementById('subcategoria');
  const subcategoriaId = <?php echo $publicacion->subcategoria_id ?? 'null'; ?>;
  
  if (categoriaSelect && categoriaSelect.value && subcategoriaId) {
    const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
    const subcategorias = JSON.parse(selectedOption.getAttribute('data-subcategorias') || '[]');
    
    subcategoriaSelect.innerHTML = '<option value="">Seleccionar subcategoría...</option>';
    subcategorias.forEach(sub => {
      const option = document.createElement('option');
      option.value = sub.id;
      option.textContent = sub.nombre;
      if (sub.id == subcategoriaId) {
        option.selected = true;
      }
      subcategoriaSelect.appendChild(option);
    });
  }
  
  // Cargar comuna seleccionada
  const regionSelect = document.getElementById('region');
  const comunaSelect = document.getElementById('comuna');
  const comunaId = <?php echo $publicacion->comuna_id ?? 'null'; ?>;
  
  if (regionSelect && regionSelect.value && comunaId) {
    fetch(`<?php echo BASE_URL; ?>/api/comunas?region_id=${regionSelect.value}`)
      .then(response => response.json())
      .then(data => {
        comunaSelect.innerHTML = '<option value="">Seleccionar comuna...</option>';
        if (data.comunas && data.comunas.length > 0) {
          data.comunas.forEach(comuna => {
            const option = document.createElement('option');
            option.value = comuna.id;
            option.textContent = comuna.nombre;
            if (comuna.id == comunaId) {
              option.selected = true;
            }
            comunaSelect.appendChild(option);
          });
        }
      })
      .catch(error => console.error('Error al cargar comunas:', error));
  }
});
<?php endif; ?>

// ============================================================================
// VALIDACIÓN DEL FORMULARIO
// ============================================================================

function validarFormulario(esBorrador = false) {
  const errores = [];
  
  // Obtener valores
  const marca = document.querySelector('input[name="marca"]')?.value.trim();
  const modelo = document.querySelector('input[name="modelo"]')?.value.trim();
  const anio = document.querySelector('input[name="anio"]')?.value;
  const categoriaPadre = document.querySelector('select[name="categoria_padre_id"]')?.value;
  const subcategoria = document.querySelector('select[name="subcategoria_id"]')?.value;
  const region = document.querySelector('select[name="region_id"]')?.value;
  const comuna = document.querySelector('select[name="comuna_id"]')?.value;
  const descripcion = document.querySelector('textarea[name="descripcion"]')?.value.trim();
  
  // Validar fotos (solo si no es modo edición o si no hay fotos existentes)
  const fotosExistentes = document.querySelectorAll('.gallery-item').length;
  const fotosNuevas = document.querySelector('input[name="fotos[]"]')?.files.length || 0;
  const totalFotos = fotosExistentes + fotosNuevas;
  
  // Validaciones básicas (siempre requeridas)
  if (!marca) errores.push('Marca del vehículo');
  if (!modelo) errores.push('Modelo del vehículo');
  if (!anio) errores.push('Año del vehículo');
  if (!categoriaPadre) errores.push('Categoría');
  if (!subcategoria) errores.push('Subcategoría');
  if (!region) errores.push('Región');
  if (!comuna) errores.push('Comuna');
  if (!descripcion || descripcion.length < 20) {
    errores.push('Descripción (mínimo 20 caracteres)');
  }
  if (totalFotos === 0) {
    errores.push('Al menos 1 foto del vehículo');
  }
  
  return errores;
}

function mostrarModalValidacion(errores) {
  const modal = document.getElementById('modalValidacion');
  const listaErrores = document.getElementById('listaErrores');
  
  // Limpiar lista
  listaErrores.innerHTML = '';
  
  // Agregar errores
  errores.forEach(error => {
    const li = document.createElement('li');
    li.innerHTML = `<span style="color: #DC2626;">✗</span> ${error}`;
    li.style.padding = '4px 0';
    li.style.display = 'flex';
    li.style.alignItems = 'center';
    li.style.gap = '8px';
    listaErrores.appendChild(li);
  });
  
  modal.style.display = 'flex';
}

function cerrarModalValidacion() {
  document.getElementById('modalValidacion').style.display = 'none';
}

// Validar antes de enviar el formulario - Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('form-publicar');
  
  if (form) {
    form.addEventListener('submit', function(e) {
      const errores = validarFormulario(false);
      
      if (errores.length > 0) {
        e.preventDefault();
        e.stopPropagation();
        mostrarModalValidacion(errores);
        return false;
      }
      
      // Bloquear botón y mostrar loader
      const btnEnviar = document.getElementById('btn-enviar');
      if (btnEnviar) {
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<span style="display: inline-flex; align-items: center; gap: 8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg> Enviando...</span>';
      }
      
      // El formulario se enviará normalmente
    });
  }
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
  const modal = document.getElementById('modalValidacion');
  if (event.target === modal) {
    cerrarModalValidacion();
  }
});

</script>

<!-- Modal de Validación -->
<div id="modalValidacion" class="admin-modal" style="display: none;">
  <div class="admin-modal-content admin-modal-small">
    <div class="admin-modal-header">
      <h2 class="h2" style="margin: 0; display: flex; align-items: center; gap: 12px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        Campos Requeridos
      </h2>
    </div>
    <div class="admin-modal-body">
      <p class="meta" style="margin-bottom: 16px; color: #6B7280;">
        Por favor, completa los siguientes campos antes de continuar:
      </p>
      
      <ul id="listaErrores" style="list-style: none; padding: 0; margin: 0 0 20px 0; font-size: 14px; line-height: 1.4;">
        <!-- Los errores se agregarán aquí dinámicamente -->
      </ul>
      
      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModalValidacion()" class="btn primary">
          Entendido
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Estilos para el modal -->
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

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
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

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
