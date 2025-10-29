<?php
use App\Helpers\Auth;

$pageTitle = 'Publicar Vehículo';

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

  <div class="h1">Publicar vehículo</div>
  
  <?php
  // DEBUG: Mostrar la URL de acción
  $action_url = BASE_URL . '/publicar/procesar';
  error_log("Form action URL: " . $action_url);
  ?>
  <form method="POST" action="<?php echo $action_url; ?>" enctype="multipart/form-data">
    <!-- DEBUG: Mostrar URL en comentario HTML -->
    <!-- Action URL: <?php echo $action_url; ?> -->
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
    
    <div class="card">
      <div class="h3">Paso 1: Tipificación</div>
      <div class="kit">
        <label class="tag" id="label-chocado">
          <input type="radio" name="tipificacion" value="chocado" id="tip-chocado" required> Chocado
        </label>
        <label class="tag" id="label-siniestrado">
          <input type="radio" name="tipificacion" value="siniestrado" id="tip-siniestrado"> Siniestrado
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
          <input type="radio" name="tipo_venta" value="completo" id="venta-completo" required> Venta Directa (con precio)
        </label>
        <label class="tag" id="label-desarme">
          <input type="radio" name="tipo_venta" value="desarme" id="venta-desarme"> Precio a convenir
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 3: Datos del vehículo</div>
      <!-- Fila 1: Marca, Modelo, Año -->
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Marca
          <input type="text" name="marca" placeholder="Ej: Toyota" required>
        </label>
        
        <label>Modelo
          <input type="text" name="modelo" placeholder="Ej: Corolla" required>
        </label>
        
        <label>Año
          <input type="number" name="anio" placeholder="2020" min="1900" max="2025" required>
        </label>
      </div>
      
      <!-- Fila 2: Categoría, Subcategoría -->
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Categoría
          <select name="categoria_padre_id" id="categoria_padre" required>
            <option value="">Seleccionar...</option>
            <?php if (!empty($categorias)): ?>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria->id; ?>" data-subcategorias='<?php echo json_encode($categoria->subcategorias ?? []); ?>'>
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
          <select name="region_id" id="region" required>
            <option value="">Seleccionar...</option>
            <?php if (!empty($regiones)): ?>
              <?php foreach ($regiones as $region): ?>
                <option value="<?php echo $region->id; ?>">
                  <?php echo htmlspecialchars($region->nombre); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </label>
        
        <label>Comuna
          <select name="comuna_id" id="comuna" required>
            <option value="">Selecciona región...</option>
          </select>
        </label>
      </div>
      
      <!-- Fila 4: Precio -->
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
        <label id="precio-field">Precio
          <input type="text" name="precio" placeholder="Ej: $5.000.000">
        </label>
        <div></div>
      </div>
      
      <!-- Fila 4: Descripción (ancho completo) -->
      <div style="margin-bottom: 16px;">
        <label style="display: block;">Descripción detallada
          <textarea name="descripcion" rows="6" placeholder="Describe los daños principales, estado actual del vehículo, piezas disponibles, historial, etc. Sé lo más detallado posible." required style="min-height: 120px; width: 100%; margin-top: 8px;"></textarea>
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 4: Fotos (1 a 6) · Selecciona la <strong>foto principal</strong></div>
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
      <p class="meta" style="margin-top: 16px; color: #666;">La primera foto será la principal por defecto</p>
    </div>

    <div class="card">
      <div class="h3">Paso 5: Promoción</div>
      <div class="kit">
        <label class="tag">
          <input type="radio" name="promocion" value="normal" checked> Normal (gratis)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada15"> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_15_DIAS); ?> · 15 días)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada30"> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_30_DIAS); ?> · 30 días)
        </label>
      </div>
    </div>

    <div class="sticky-actions">
      <button type="button" class="btn" onclick="guardarBorrador()">Guardar borrador</button>
      <button type="submit" class="btn primary">Enviar a revisión</button>
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
  // Agregar campo oculto para indicar que es borrador
  const form = document.querySelector('form');
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'guardar_borrador';
  input.value = '1';
  form.appendChild(input);
  
  // Enviar formulario
  form.submit();
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
