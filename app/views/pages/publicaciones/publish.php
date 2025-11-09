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

// Cargar CSS específico de esta página
$additionalCSS = ['/assets/css/publicar.css?v=' . time()];

require_once APP_PATH . '/views/layouts/header.php';
?>



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
    
    <!-- Wizard con Acordeón -->
    <div class="wizard-container">
      
      <!-- Paso 1: Tipificación -->
      <div class="wizard-step" id="step1" data-step="1" data-status="active">
        <div class="wizard-step-header" onclick="toggleStep(1)" role="button" tabindex="0" aria-expanded="true">
          <div class="wizard-step-number">
            <span class="step-number">1</span>
            <span class="step-check-icon" style="display: none;">✓</span>
          </div>
          <div class="wizard-step-info">
            <h3 class="wizard-step-title">Tipificación</h3>
            <p class="wizard-step-description">Selecciona el tipo de vehículo</p>
          </div>
          <div class="wizard-step-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="wizard-step-content" style="display: block;">
          <div class="wizard-step-body">
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
        </div>
      </div>

      <!-- Paso 2: Tipo de venta -->
      <div class="wizard-step" id="step2" data-step="2" data-status="pending">
        <div class="wizard-step-header" onclick="toggleStep(2)" role="button" tabindex="0" aria-expanded="false">
          <div class="wizard-step-number">
            <span class="step-number">2</span>
            <span class="step-check-icon" style="display: none;">✓</span>
          </div>
          <div class="wizard-step-info">
            <h3 class="wizard-step-title">Tipo de venta</h3>
            <p class="wizard-step-description">Define cómo venderás el vehículo</p>
          </div>
          <div class="wizard-step-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="wizard-step-content" style="display: none;">
          <div class="wizard-step-body">
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
        </div>
      </div>

      <!-- Paso 3: Datos del vehículo -->
      <div class="wizard-step" id="step3" data-step="3" data-status="pending">
        <div class="wizard-step-header" onclick="toggleStep(3)" role="button" tabindex="0" aria-expanded="false">
          <div class="wizard-step-number">
            <span class="step-number">3</span>
            <span class="step-check-icon" style="display: none;">✓</span>
          </div>
          <div class="wizard-step-info">
            <h3 class="wizard-step-title">Datos del vehículo</h3>
            <p class="wizard-step-description">Información básica del vehículo</p>
          </div>
          <div class="wizard-step-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="wizard-step-content" style="display: none;">
          <div class="wizard-step-body">
            <!-- Fila 1: Marca, Modelo, Año -->
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
        <label>Marca
          <input type="text" id="marca" name="marca" placeholder="Ej: Toyota" 
            value="<?php echo $modoEdicion ? htmlspecialchars($publicacion->marca ?? '') : ''; ?>" 
            <?php echo !$modoEdicion ? 'required' : ''; ?>>
        </label>
        
        <label>Modelo
          <input type="text" id="modelo" name="modelo" placeholder="Ej: Corolla" 
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
        </div>
      </div>

      <!-- Paso 4: Fotos -->
      <div class="wizard-step" id="step4" data-step="4" data-status="pending">
        <div class="wizard-step-header" onclick="toggleStep(4)" role="button" tabindex="0" aria-expanded="false">
          <div class="wizard-step-number">
            <span class="step-number">4</span>
            <span class="step-check-icon" style="display: none;">✓</span>
          </div>
          <div class="wizard-step-info">
            <h3 class="wizard-step-title">Fotos del vehículo</h3>
            <p class="wizard-step-description">Sube entre 1 y 6 fotos</p>
          </div>
          <div class="wizard-step-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="wizard-step-content" style="display: none;">
          <div class="wizard-step-body">
      
      <?php if ($modoEdicion && !empty($imagenes)): ?>
        <div style="margin-bottom: 20px; padding: 12px; background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px;">
          <p style="margin: 0; font-size: 14px; color: #1e40af;">
            <?php echo icon('info', 16); ?> <strong>Fotos actuales:</strong> <span id="contador-fotos-existentes"><?php echo count($imagenes); ?></span> imagen(es). Puedes eliminar fotos o agregar más (máximo 6 en total).
          </p>
        </div>
        
        <div id="fotos-existentes-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
          <?php foreach ($imagenes as $img): ?>
            <?php 
            $rutaImagen = BASE_URL . '/uploads/publicaciones/' . htmlspecialchars($img->ruta);
            ?>
            <div class="foto-existente" 
                 data-foto-id="<?php echo $img->id; ?>" 
                 data-es-principal="<?php echo $img->es_principal ? '1' : '0'; ?>"
                 data-foto-url="<?php echo $rutaImagen; ?>"
                 style="position: relative; border: 2px solid <?php echo $img->es_principal ? '#E6332A' : '#e5e7eb'; ?>; border-radius: 8px; overflow: hidden; transition: all 0.3s; cursor: pointer;">
              
              <img src="<?php echo $rutaImagen; ?>" 
                   alt="Foto" 
                   style="width: 100%; height: 150px; object-fit: cover;"
                   onclick="abrirModalVistaPrevia('<?php echo $rutaImagen; ?>')"
                   onerror="this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:150px;background:#f3f4f6;color:#9ca3af;\'>Imagen no disponible</div>'">
              
              <?php if ($img->es_principal): ?>
                <div class="badge-principal" style="position: absolute; top: 8px; left: 8px; background: #E6332A; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                  ★ PRINCIPAL
                </div>
              <?php else: ?>
                <div class="badge-principal" style="position: absolute; top: 8px; left: 8px; background: #E6332A; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: none; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                  ★ PRINCIPAL
                </div>
              <?php endif; ?>
              
              <!-- Iconos de acción en las esquinas -->
              <div class="foto-icon-actions" style="position: absolute; top: 8px; right: 8px; display: flex; gap: 6px; z-index: 10;">
                <button type="button" 
                        onclick="event.stopPropagation(); marcarComoPrincipal(<?php echo $img->id; ?>)"
                        class="btn-icon-principal"
                        title="Marcar como principal"
                        style="width: 32px; height: 32px; background: rgba(255,255,255,0.95); border: none; border-radius: 50%; cursor: pointer; display: <?php echo $img->es_principal ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: all 0.2s;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E6332A" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                  </svg>
                </button>
                <button type="button" 
                        onclick="event.stopPropagation(); eliminarFotoExistente(<?php echo $img->id; ?>)"
                        class="btn-icon-eliminar"
                        title="Eliminar foto"
                        style="width: 32px; height: 32px; background: rgba(239,68,68,0.95); border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: all 0.2s;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
              
              <!-- Input hidden para marcar foto como eliminada -->
              <input type="hidden" 
                     name="fotos_eliminar[]" 
                     value="" 
                     data-foto-id="<?php echo $img->id; ?>" 
                     class="input-eliminar">
            </div>
          <?php endforeach; ?>
        </div>
        
        <!-- Input hidden para foto principal existente -->
        <input type="hidden" 
               name="foto_principal_existente" 
               value="<?php 
                 foreach ($imagenes as $img) {
                   if ($img->es_principal) {
                     echo $img->id;
                     break;
                   }
                 }
               ?>" 
               id="input-foto-principal-existente">
        
        <div style="margin-bottom: 16px; padding: 12px; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px;">
          <p style="margin: 0; font-size: 13px; color: #92400e;">
            💡 <strong>Tip:</strong> Puedes agregar hasta <span id="fotos-disponibles"><?php echo 6 - count($imagenes); ?></span> foto(s) más.
          </p>
        </div>
      <?php endif; ?>
      
      <div class="gallery" id="gallery-nuevas-fotos">
        <?php 
        // Calcular slots disponibles dinámicamente
        $fotosExistentes = $modoEdicion && !empty($imagenes) ? count($imagenes) : 0;
        $maxFotos = 6;
        $slotsDisponibles = $maxFotos - $fotosExistentes;
        
        for ($i = 1; $i <= $slotsDisponibles; $i++): 
        ?>
        <label class="foto-slot" data-slot="<?php echo $i; ?>">
          <span style="font-size: 14px; color: #666; margin-bottom: 8px;">Foto <?php echo $i; ?></span>
          <input type="file" name="fotos[]" accept="image/*" id="foto_<?php echo $i; ?>" class="foto-input">
          <button type="button" onclick="document.getElementById('foto_<?php echo $i; ?>').click()" style="padding: 8px 16px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin-top: 8px;">
            Seleccionar archivo
          </button>
          <span class="file-status" style="font-size: 12px; color: #999; margin-top: 8px;">Sin archivos seleccionados</span>
          <?php if (!$modoEdicion || empty($imagenes)): ?>
          <div style="margin-top: 12px; display: flex; align-items: center; gap: 6px;">
            <input type="radio" name="foto_principal" value="<?php echo $i; ?>" <?php echo $i === 1 ? 'checked' : ''; ?>>
            <span style="font-size: 13px;">Foto principal</span>
          </div>
          <?php endif; ?>
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
        </div>
      </div>

      <!-- Paso 5: Promoción -->
      <div class="wizard-step" id="step5" data-step="5" data-status="pending">
        <div class="wizard-step-header" onclick="toggleStep(5)" role="button" tabindex="0" aria-expanded="false">
          <div class="wizard-step-number">
            <span class="step-number">5</span>
            <span class="step-check-icon" style="display: none;">✓</span>
          </div>
          <div class="wizard-step-info">
            <h3 class="wizard-step-title">Promoción</h3>
            <p class="wizard-step-description">Elige el tipo de publicación</p>
          </div>
          <div class="wizard-step-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div class="wizard-step-content" style="display: none;">
          <div class="wizard-step-body">
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
        </div>
      </div>
      
    </div><!-- Fin wizard-container -->

    <div class="sticky-actions">
      <button type="button" class="btn" onclick="guardarBorrador()">Guardar borrador</button>
      <button type="button" class="btn primary" id="btn-enviar" onclick="enviarFormulario()">
        <?php echo $modoEdicion ? 'Actualizar publicación' : 'Enviar a revisión'; ?>
      </button>
    </div>
  </form>

</main>

<script>
// ============================================================================
// LÓGICA DE VALIDACIÓN Y MARCADO DE PASOS
// ============================================================================

/**
 * Valida y marca un paso como completado
 * VALIDACIÓN SECUENCIAL: Solo se puede completar un paso si los anteriores están completos
 */
function validarYMarcarPaso(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return false;
  
  // Verificar que los pasos anteriores estén completos (excepto en modo edición)
  const modoEdicion = <?php echo $modoEdicion ? 'true' : 'false'; ?>;
  if (!modoEdicion && stepNumber > 1) {
    for (let i = 1; i < stepNumber; i++) {
      if (!validarPasoSinMarcar(i)) {
        // Si un paso anterior no está completo, deshabilitar este paso
        deshabilitarPaso(stepNumber);
        return false;
      }
    }
  }
  
  // Habilitar el paso si llegamos aquí
  habilitarPaso(stepNumber);
  
  const checkIcon = step.querySelector('.step-check');
  let isValid = false;
  
  switch(stepNumber) {
    case 1: // Tipificación
      const tipificacion = document.querySelector('input[name="tipificacion"]:checked');
      isValid = !!tipificacion;
      break;
      
    case 2: // Tipo de venta
      const tipoVenta = document.querySelector('input[name="tipo_venta"]:checked');
      isValid = !!tipoVenta;
      break;
      
    case 3: // Datos del vehículo
      const marca = document.querySelector('input[name="marca"]')?.value.trim();
      const modelo = document.querySelector('input[name="modelo"]')?.value.trim();
      const anio = document.querySelector('input[name="anio"]')?.value;
      const categoria = document.querySelector('select[name="categoria_padre_id"]')?.value;
      const subcategoria = document.querySelector('select[name="subcategoria_id"]')?.value;
      const region = document.querySelector('select[name="region_id"]')?.value;
      const comuna = document.querySelector('select[name="comuna_id"]')?.value;
      const descripcion = document.querySelector('textarea[name="descripcion"]')?.value.trim();
      
      isValid = marca && modelo && anio && categoria && subcategoria && 
                region && comuna && descripcion && descripcion.length >= 20;
      break;
      
    case 4: // Fotos
      const fotosExistentes = document.querySelectorAll('.foto-existente:not(.eliminada)').length;
      const fotosNuevas = Array.from(document.querySelectorAll('input[name="fotos[]"]'))
        .filter(input => input.files.length > 0).length;
      const totalFotos = fotosExistentes + fotosNuevas;
      isValid = totalFotos >= 1 && totalFotos <= 6;
      break;
      
    case 5: // Promoción
      const promocion = document.querySelector('input[name="promocion"]:checked');
      isValid = !!promocion;
      break;
  }
  
  // Actualizar el icono de check en el wizard
  const stepNumber_elem = step.querySelector('.step-number');
  const stepCheckIcon = step.querySelector('.step-check-icon');
  
  if (isValid) {
    step.classList.add('step-completed');
    step.dataset.status = 'completed';
    
    // Mostrar check icon y ocultar número
    if (stepNumber_elem) stepNumber_elem.style.display = 'none';
    if (stepCheckIcon) stepCheckIcon.style.display = 'inline';
    
    // Cerrar el paso completado y abrir el siguiente
    if (stepNumber < 5 && !<?php echo $modoEdicion ? 'true' : 'false'; ?>) {
      setTimeout(() => {
        closeStep(stepNumber);
        openStep(stepNumber + 1);
      }, 500);
    }
  } else {
    step.classList.remove('step-completed');
    
    // Mostrar número y ocultar check icon
    if (stepNumber_elem) stepNumber_elem.style.display = 'inline';
    if (stepCheckIcon) stepCheckIcon.style.display = 'none';
  }
  
  // Validar el siguiente paso para habilitarlo/deshabilitarlo
  if (stepNumber < 5) {
    validarYMarcarPaso(stepNumber + 1);
  }
  
  return isValid;
}

/**
 * Valida un paso sin marcarlo (solo para verificar)
 */
function validarPasoSinMarcar(stepNumber) {
  switch(stepNumber) {
    case 1:
      return !!document.querySelector('input[name="tipificacion"]:checked');
    case 2:
      return !!document.querySelector('input[name="tipo_venta"]:checked');
    case 3:
      const marca = document.querySelector('input[name="marca"]')?.value.trim();
      const modelo = document.querySelector('input[name="modelo"]')?.value.trim();
      const anio = document.querySelector('input[name="anio"]')?.value;
      const categoria = document.querySelector('select[name="categoria_padre_id"]')?.value;
      const subcategoria = document.querySelector('select[name="subcategoria_id"]')?.value;
      const region = document.querySelector('select[name="region_id"]')?.value;
      const comuna = document.querySelector('select[name="comuna_id"]')?.value;
      const descripcion = document.querySelector('textarea[name="descripcion"]')?.value.trim();
      return marca && modelo && anio && categoria && subcategoria && 
             region && comuna && descripcion && descripcion.length >= 20;
    case 4:
      const fotosExistentes = document.querySelectorAll('.foto-existente:not(.eliminada)').length;
      const fotosNuevas = Array.from(document.querySelectorAll('input[name="fotos[]"]'))
        .filter(input => input.files.length > 0).length;
      const totalFotos = fotosExistentes + fotosNuevas;
      return totalFotos >= 1 && totalFotos <= 6;
    case 5:
      return !!document.querySelector('input[name="promocion"]:checked');
  }
  return false;
}

/**
 * Deshabilita un paso visualmente
 */
function deshabilitarPaso(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return;
  
  step.classList.add('step-disabled');
  step.style.opacity = '0.5';
  step.style.pointerEvents = 'none';
  
  // Deshabilitar todos los inputs del paso
  step.querySelectorAll('input, select, textarea, button').forEach(el => {
    el.disabled = true;
  });
}

/**
 * Habilita un paso visualmente
 */
function habilitarPaso(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return;
  
  step.classList.remove('step-disabled');
  step.style.opacity = '1';
  step.style.pointerEvents = 'auto';
  
  // Habilitar todos los inputs del paso (excepto los que deben estar deshabilitados por lógica)
  step.querySelectorAll('input, select, textarea, button').forEach(el => {
    // No habilitar inputs que están deshabilitados por lógica de negocio
    if (!el.dataset.logicDisabled) {
      el.disabled = false;
    }
  });
}

/**
 * Valida todos los pasos y actualiza sus estados
 */
function validarTodosLosPasos() {
  for (let i = 1; i <= 5; i++) {
    validarYMarcarPaso(i);
  }
}

/**
 * Toggle (abrir/cerrar) un paso del acordeón
 */
function toggleStep(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return;
  
  const header = step.querySelector('.wizard-step-header');
  const content = step.querySelector('.wizard-step-content');
  const isExpanded = header.getAttribute('aria-expanded') === 'true';
  
  // Si el paso está deshabilitado, no hacer nada
  if (step.classList.contains('step-disabled')) {
    return;
  }
  
  if (isExpanded) {
    // Cerrar el paso
    content.style.display = 'none';
    header.setAttribute('aria-expanded', 'false');
    step.dataset.status = step.classList.contains('step-completed') ? 'completed' : 'pending';
  } else {
    // Abrir el paso
    content.style.display = 'block';
    header.setAttribute('aria-expanded', 'true');
    step.dataset.status = 'active';
  }
}

/**
 * Abre un paso específico del acordeón
 */
function openStep(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return;
  
  const header = step.querySelector('.wizard-step-header');
  const content = step.querySelector('.wizard-step-content');
  
  content.style.display = 'block';
  header.setAttribute('aria-expanded', 'true');
  step.dataset.status = 'active';
  
  // Scroll suave al paso
  step.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Cierra un paso específico del acordeón
 */
function closeStep(stepNumber) {
  const step = document.getElementById(`step${stepNumber}`);
  if (!step) return;
  
  const header = step.querySelector('.wizard-step-header');
  const content = step.querySelector('.wizard-step-content');
  
  content.style.display = 'none';
  header.setAttribute('aria-expanded', 'false');
  step.dataset.status = step.classList.contains('step-completed') ? 'completed' : 'pending';
}

// Validar pasos al cargar la página (útil en modo edición)
document.addEventListener('DOMContentLoaded', function() {
  validarTodosLosPasos();
  
  // En modo edición, abrir todos los pasos completados
  const modoEdicion = <?php echo $modoEdicion ? 'true' : 'false'; ?>;
  if (modoEdicion) {
    for (let i = 1; i <= 5; i++) {
      if (validarPasoSinMarcar(i)) {
        // No abrir automáticamente, dejar cerrados
      }
    }
  }
});

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
      delete ventaCompleto.dataset.logicDisabled;
      
      ventaDesarme.disabled = true;
      ventaDesarme.dataset.logicDisabled = 'true';
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
      delete ventaDesarme.dataset.logicDisabled;
      
      ventaCompleto.disabled = true;
      ventaCompleto.dataset.logicDisabled = 'true';
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
    
    // Validar pasos 1 y 2 automáticamente
    validarYMarcarPaso(1);
    validarYMarcarPaso(2);
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
    
    // Validar paso 2
    validarYMarcarPaso(2);
  });
});

// Validar paso 3 cuando cambian los campos
document.querySelectorAll('#step3 input, #step3 select, #step3 textarea').forEach(field => {
  field.addEventListener('input', () => validarYMarcarPaso(3));
  field.addEventListener('change', () => validarYMarcarPaso(3));
});

// Validar paso 5 cuando cambia la promoción
document.querySelectorAll('input[name="promocion"]').forEach(radio => {
  radio.addEventListener('change', () => validarYMarcarPaso(5));
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
      
      // Actualizar contador de fotos disponibles
      actualizarContadorFotos();
      
      // Validar paso 4
      validarYMarcarPaso(4);
    } else {
      statusSpan.textContent = 'Sin archivos seleccionados';
      statusSpan.style.color = '#999';
      
      // Eliminar preview si existe
      const preview = label.querySelector('.image-preview');
      if (preview) {
        preview.remove();
      }
      
      // Actualizar contador de fotos disponibles
      actualizarContadorFotos();
      
      // Validar paso 4
      validarYMarcarPaso(4);
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

// Función para enviar el formulario con validación
function enviarFormulario() {
  console.log('=== ENVIAR FORMULARIO ===');
  
  const form = document.getElementById('form-publicar');
  if (!form) {
    console.error('Formulario no encontrado');
    alert('Error: No se encontró el formulario');
    return;
  }
  
  // Validar formulario
  const errores = validarFormulario(false);
  console.log('Errores encontrados:', errores.length);
  console.log('Lista de errores:', errores);
  
  if (errores.length > 0) {
    console.log('Mostrando modal de validación');
    mostrarModalValidacion(errores);
    return;
  }
  
  // Si no hay errores, limpiar campo borrador y enviar
  const borradorInput = form.querySelector('input[name="guardar_borrador"]');
  if (borradorInput) {
    borradorInput.remove();
    console.log('Campo guardar_borrador eliminado');
  }
  
  // Bloquear botón y mostrar loader
  const btnEnviar = document.getElementById('btn-enviar');
  if (btnEnviar) {
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<span style="display: inline-flex; align-items: center; gap: 8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg> Enviando...</span>';
  }
  
  console.log('Enviando formulario...');
  form.submit();
}

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
// GESTIÓN DE FOTOS EXISTENTES (MODO EDICIÓN)
// ============================================================================

/**
 * Marca una foto existente como eliminada
 * @param {number} fotoId - ID de la foto en la base de datos
 */
function eliminarFotoExistente(fotoId) {
  const fotoDiv = document.querySelector(`.foto-existente[data-foto-id="${fotoId}"]`);
  if (!fotoDiv) return;
  
  // Mostrar modal de confirmación
  const modal = document.getElementById('modalEliminarFoto');
  const btnConfirmar = document.getElementById('btnConfirmarEliminarFoto');
  
  // Limpiar event listeners previos
  const nuevoBtn = btnConfirmar.cloneNode(true);
  btnConfirmar.parentNode.replaceChild(nuevoBtn, btnConfirmar);
  
  // Agregar event listener para confirmar eliminación
  nuevoBtn.addEventListener('click', function() {
    // Marcar visualmente como eliminada
    fotoDiv.style.opacity = '0.3';
    fotoDiv.style.pointerEvents = 'none';
    fotoDiv.classList.add('eliminada');
    fotoDiv.style.filter = 'grayscale(100%)';
    
    // Actualizar input hidden para enviar al backend
    const inputEliminar = fotoDiv.querySelector('.input-eliminar');
    if (inputEliminar) {
      inputEliminar.value = fotoId;
    }
    
    // Si era la foto principal, limpiar y marcar otra automáticamente
    const esPrincipal = fotoDiv.dataset.esPrincipal === '1';
    if (esPrincipal) {
      const inputPrincipal = document.getElementById('input-foto-principal-existente');
      if (inputPrincipal) {
        inputPrincipal.value = '';
      }
      
      // Intentar marcar otra foto como principal automáticamente
      const primeraFotoNoEliminada = document.querySelector('.foto-existente:not(.eliminada)');
      if (primeraFotoNoEliminada) {
        const nuevoPrincipalId = primeraFotoNoEliminada.dataset.fotoId;
        marcarComoPrincipal(nuevoPrincipalId);
      }
    }
    
    // Actualizar contador
    actualizarContadorFotos();
    
    // Validar paso 4
    validarYMarcarPaso(4);
    
    // Cerrar modal
    cerrarModalEliminarFoto();
  });
  
  // Mostrar modal
  modal.style.display = 'flex';
}

/**
 * Cierra el modal de eliminación de foto
 */
function cerrarModalEliminarFoto() {
  document.getElementById('modalEliminarFoto').style.display = 'none';
}

/**
 * Abre el modal de vista previa de imagen
 */
function abrirModalVistaPrevia(urlImagen) {
  const modal = document.getElementById('modalVistaPrevia');
  const imagen = document.getElementById('imagenVistaPrevia');
  
  imagen.src = urlImagen;
  modal.style.display = 'flex';
  
  // Prevenir scroll del body
  document.body.style.overflow = 'hidden';
}

/**
 * Cierra el modal de vista previa
 */
function cerrarModalVistaPrevia() {
  const modal = document.getElementById('modalVistaPrevia');
  modal.style.display = 'none';
  
  // Restaurar scroll del body
  document.body.style.overflow = 'auto';
}

/**
 * Marca una foto existente como principal
 * @param {number} fotoId - ID de la foto en la base de datos
 */
function marcarComoPrincipal(fotoId) {
  // Remover badge "PRINCIPAL" de todas las fotos
  document.querySelectorAll('.foto-existente').forEach(foto => {
    foto.dataset.esPrincipal = '0';
    foto.style.borderColor = '#e5e7eb';
    
    const badge = foto.querySelector('.badge-principal');
    if (badge) badge.style.display = 'none';
    
    const btnPrincipal = foto.querySelector('.btn-foto-action');
    if (btnPrincipal) btnPrincipal.style.display = 'flex';
  });
  
  // Marcar la foto seleccionada como principal
  const fotoDiv = document.querySelector(`.foto-existente[data-foto-id="${fotoId}"]`);
  if (fotoDiv && !fotoDiv.classList.contains('eliminada')) {
    fotoDiv.dataset.esPrincipal = '1';
    fotoDiv.style.borderColor = '#E6332A';
    
    const badge = fotoDiv.querySelector('.badge-principal');
    if (badge) badge.style.display = 'flex';
    
    const btnPrincipal = fotoDiv.querySelector('.btn-foto-action');
    if (btnPrincipal) btnPrincipal.style.display = 'none';
    
    // Actualizar input hidden
    const inputPrincipal = document.getElementById('input-foto-principal-existente');
    if (inputPrincipal) {
      inputPrincipal.value = fotoId;
    }
  }
}

/**
 * Actualiza el contador de fotos disponibles y slots dinámicos
 */
function actualizarContadorFotos() {
  const fotosExistentesNoEliminadas = document.querySelectorAll('.foto-existente:not(.eliminada)').length;
  const fotosNuevas = Array.from(document.querySelectorAll('input[name="fotos[]"]')).filter(input => input.files.length > 0).length;
  const totalFotos = fotosExistentesNoEliminadas + fotosNuevas;
  const maxFotos = 6;
  const fotosDisponibles = maxFotos - fotosExistentesNoEliminadas;
  
  // Actualizar contador en UI
  const contadorExistentes = document.getElementById('contador-fotos-existentes');
  if (contadorExistentes) {
    contadorExistentes.textContent = fotosExistentesNoEliminadas;
  }
  
  const contadorDisponibles = document.getElementById('fotos-disponibles');
  if (contadorDisponibles) {
    contadorDisponibles.textContent = Math.max(0, fotosDisponibles);
  }
  
  // Actualizar slots disponibles dinámicamente
  const gallery = document.getElementById('gallery-nuevas-fotos');
  if (gallery) {
    const slots = gallery.querySelectorAll('.foto-slot');
    
    // Mostrar/ocultar slots según disponibilidad
    slots.forEach((slot, index) => {
      if (index < fotosDisponibles) {
        slot.style.display = 'flex';
      } else {
        slot.style.display = 'none';
      }
    });
    
    // Si necesitamos más slots, agregarlos
    const slotsActuales = slots.length;
    if (fotosDisponibles > slotsActuales) {
      for (let i = slotsActuales + 1; i <= fotosDisponibles; i++) {
        const nuevoSlot = crearSlotFoto(i);
        gallery.appendChild(nuevoSlot);
      }
    }
  }
  
  console.log('Contador actualizado:', {
    existentes: fotosExistentesNoEliminadas,
    nuevas: fotosNuevas,
    total: totalFotos,
    disponibles: fotosDisponibles
  });
}

/**
 * Crea un nuevo slot para subir foto
 */
function crearSlotFoto(index) {
  const label = document.createElement('label');
  label.className = 'foto-slot';
  label.dataset.slot = index;
  label.style.display = 'flex';
  label.style.flexDirection = 'column';
  label.style.alignItems = 'center';
  label.style.padding = '24px 16px';
  label.style.border = '2px dashed #ddd';
  label.style.borderRadius = '8px';
  label.style.cursor = 'pointer';
  label.style.transition = 'all 0.2s';
  label.style.minHeight = '120px';
  label.style.justifyContent = 'center';
  
  label.innerHTML = `
    <span style="font-size: 14px; color: #666; margin-bottom: 8px;">Foto ${index}</span>
    <input type="file" name="fotos[]" accept="image/*" id="foto_${index}" class="foto-input" style="display: none;">
    <button type="button" onclick="document.getElementById('foto_${index}').click()" style="padding: 8px 16px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin-top: 8px;">
      Seleccionar archivo
    </button>
    <span class="file-status" style="font-size: 12px; color: #999; margin-top: 8px;">Sin archivos seleccionados</span>
  `;
  
  return label;
}

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
  
  // CORRECCIÓN: Contar fotos existentes NO eliminadas
  const fotosExistentesNoEliminadas = document.querySelectorAll('.foto-existente:not(.eliminada)').length;
  
  // Contar fotos nuevas seleccionadas
  const fotosNuevas = Array.from(document.querySelectorAll('input[name="fotos[]"]')).filter(input => input.files.length > 0).length;
  
  const totalFotos = fotosExistentesNoEliminadas + fotosNuevas;
  
  console.log('Validación de fotos:', {
    existentes: fotosExistentesNoEliminadas,
    nuevas: fotosNuevas,
    total: totalFotos
  });
  
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
  if (totalFotos > 6) {
    errores.push('Máximo 6 fotos en total');
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

// Nota: La validación se maneja en el click del botón "Enviar a revisión"
// Ver código más abajo en el event listener del btnEnviar

// Cerrar modales al hacer clic fuera
window.addEventListener('click', function(event) {
  const modalValidacion = document.getElementById('modalValidacion');
  const modalEliminarFoto = document.getElementById('modalEliminarFoto');
  
  if (event.target === modalValidacion) {
    cerrarModalValidacion();
  }
  
  if (event.target === modalEliminarFoto) {
    cerrarModalEliminarFoto();
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

<!-- Modal de Vista Previa de Imagen -->
<div id="modalVistaPrevia" class="admin-modal" style="display: none;" onclick="cerrarModalVistaPrevia()">
  <div class="modal-vista-previa-content" onclick="event.stopPropagation()" style="position: relative; max-width: 90vw; max-height: 90vh; background: transparent;">
    <button type="button" 
            onclick="cerrarModalVistaPrevia()" 
            style="position: absolute; top: -40px; right: 0; width: 40px; height: 40px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 10001;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1F2937" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
    <img id="imagenVistaPrevia" 
         src="" 
         alt="Vista previa" 
         style="max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 8px; box-shadow: 0 8px 32px rgba(0,0,0,0.5);">
  </div>
</div>

<!-- Modal de Confirmación: Eliminar Foto -->
<div id="modalEliminarFoto" class="admin-modal" style="display: none;">
  <div class="admin-modal-content admin-modal-small">
    <div class="admin-modal-header">
      <h2 class="h2" style="margin: 0; display: flex; align-items: center; gap: 12px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
          <path d="M3 6h18"></path>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
        Eliminar Foto
      </h2>
    </div>
    <div class="admin-modal-body">
      <p style="margin-bottom: 20px; color: var(--cc-text-secondary, #6B7280); font-size: 15px; line-height: 1.6;">
        ¿Estás seguro de que deseas eliminar esta foto? Esta acción no se puede deshacer.
      </p>
      
      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModalEliminarFoto()" class="btn">
          Cancelar
        </button>
        <button type="button" id="btnConfirmarEliminarFoto" class="btn" style="background: #DC2626; color: white; border-color: #DC2626;">
          Eliminar Foto
        </button>
      </div>
    </div>
  </div>
</div>



<!-- Script de autocompletado de marcas y modelos -->
<script src="/assets/js/marca-modelo-selector.js?v=<?php echo time(); ?>"></script>

<!-- Deshabilitar actualizaciones automáticas en esta página -->
<script>
// Marcar que estamos en página de publicar para evitar actualizaciones intrusivas
window.isPublishPage = true;

// Deshabilitar completamente las actualizaciones automáticas mientras se edita
document.addEventListener('DOMContentLoaded', function() {
    // Detener todos los intervalos de actualización cuando el usuario interactúa con el formulario
    const form = document.getElementById('form-publicar');
    if (form) {
        // Detectar cuando el usuario empieza a interactuar
        form.addEventListener('focusin', function() {
            window.pauseAutoUpdates = true;
        });
        
        // Reanudar después de 5 segundos de inactividad
        let inactivityTimer;
        form.addEventListener('focusout', function() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(function() {
                window.pauseAutoUpdates = false;
            }, 5000);
        });
    }
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
