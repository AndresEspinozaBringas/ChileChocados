<?php
/**
 * Vista: Panel de Moderación de Publicaciones
 * Gestiona y modera todas las publicaciones del sistema
 * CORRECCIONES:
 * - Eliminada columna "Foto" de la tabla
 * - Mejorado diseño del modal
 * - Corregidas rutas de imágenes
 */

// La verificación de admin se hace en el controlador
layout('header');
layout('nav');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">

<main class="container" style="padding: 24px 0;">

  <!-- Encabezado -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
      <h1 class="h1">Panel de Moderación</h1>
      <p class="meta">Gestiona y modera todas las publicaciones del sistema</p>
    </div>
    <div style="display: flex; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/admin" class="btn outline">
        ← Volver al Dashboard
      </a>
      <button onclick="location.reload()" class="btn primary">
        🔄 Actualizar
      </button>
    </div>
  </div>

  <!-- Contador de estados -->
  <div class="grid cols-4" style="gap: 16px; margin-bottom: 24px;">
    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #007AFF; margin-bottom: 4px;">
        <?php echo $conteo['total'] ?? 0; ?>
      </div>
      <div class="meta">Total Publicaciones</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #FF9500; margin-bottom: 4px;">
        <?php echo $conteo['pendientes'] ?? 0; ?>
      </div>
      <div class="meta">Pendientes Aprobación</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #34C759; margin-bottom: 4px;">
        <?php echo $conteo['aprobadas'] ?? 0; ?>
      </div>
      <div class="meta">Aprobadas</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #FF3B30; margin-bottom: 4px;">
        <?php echo $conteo['rechazadas'] ?? 0; ?>
      </div>
      <div class="meta">Rechazadas</div>
    </div>
  </div>

  <!-- Filtros -->
  <div class="card" style="margin-bottom: 24px; padding: 24px;">
    <h3 class="h3" style="margin-bottom: 16px;">Filtros</h3>
    <form method="GET" action="<?php echo BASE_URL; ?>/admin/publicaciones" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
      
      <!-- Estado -->
      <div>
        <label class="label">Estado</label>
        <select name="estado" class="input">
          <option value="">Todas</option>
          <option value="pendiente" <?php echo ($filtros['estado'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
          <option value="aprobada" <?php echo ($filtros['estado'] ?? '') === 'aprobada' ? 'selected' : ''; ?>>Aprobadas</option>
          <option value="rechazada" <?php echo ($filtros['estado'] ?? '') === 'rechazada' ? 'selected' : ''; ?>>Rechazadas</option>
        </select>
      </div>

      <!-- Categoría -->
      <div>
        <label class="label">Categoría</label>
        <select name="categoria" class="input">
          <option value="">Todas las categorías</option>
          <?php foreach ($categorias ?? [] as $cat): ?>
            <option value="<?php echo $cat->id; ?>" <?php echo ($filtros['categoria_id'] ?? '') == $cat->id ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat->nombre); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Búsqueda -->
      <div>
        <label class="label">Buscar</label>
        <input 
          type="text" 
          name="q" 
          class="input" 
          placeholder="Título, marca, modelo..." 
          value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>"
        >
      </div>

      <!-- Fecha desde -->
      <div>
        <label class="label">Desde</label>
        <input 
          type="date" 
          name="fecha_desde" 
          class="input"
          value="<?php echo $filtros['fecha_desde'] ?? ''; ?>"
        >
      </div>

      <!-- Fecha hasta -->
      <div>
        <label class="label">Hasta</label>
        <input 
          type="date" 
          name="fecha_hasta" 
          class="input"
          value="<?php echo $filtros['fecha_hasta'] ?? ''; ?>"
        >
      </div>

      <!-- Botones de acción -->
      <div style="display: flex; gap: 8px;">
        <button type="submit" class="btn primary" style="flex: 1;">
          Filtrar
        </button>
        <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="btn outline">
          Limpiar
        </a>
      </div>
    </form>
  </div>

  <!-- Tabla de publicaciones -->
  <div class="card">
    <div style="overflow-x: auto;">
      <table class="table">
        <thead>
          <tr>
            <th style="width: 50px;">ID</th>
            <th style="width: 35%;">Título / Usuario</th>
            <th style="width: 110px;">Categoría</th>
            <th style="width: 100px;">Estado</th>
            <th style="width: 110px;">Fecha</th>
            <th style="width: 240px; text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $pub): ?>
              <tr id="pub-<?php echo $pub->id; ?>">
                <!-- ID -->
                <td>
                  <span style="font-weight: 600; color: #666;">#<?php echo $pub->id; ?></span>
                </td>

                <!-- Título y usuario -->
                <td>
                  <div>
                    <a 
                      href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" 
                      target="_blank"
                      class="h4" 
                      style="color: #0066CC; text-decoration: none; display: block; margin-bottom: 4px;"
                    >
                      <?php echo htmlspecialchars($pub->titulo); ?>
                    </a>
                    <div class="meta" style="font-size: 13px;">
                      Por: <?php echo htmlspecialchars($pub->usuario_nombre . ' ' . $pub->usuario_apellido); ?>
                      <br>
                      📧 <?php echo htmlspecialchars($pub->usuario_email); ?>
                    </div>
                  </div>
                </td>

                <!-- Categoría -->
                <td>
                  <span class="badge">
                    <?php echo htmlspecialchars($pub->categoria_nombre ?? 'Sin categoría'); ?>
                  </span>
                  <?php if ($pub->subcategoria_nombre): ?>
                    <br>
                    <span class="badge" style="margin-top: 4px; font-size: 11px;">
                      <?php echo htmlspecialchars($pub->subcategoria_nombre); ?>
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Estado -->
                <td>
                  <?php
                  $estadoColores = [
                    'pendiente' => 'background: #FF9500; color: white;',
                    'aprobada' => 'background: #34C759; color: white;',
                    'rechazada' => 'background: #FF3B30; color: white;'
                  ];
                  $estadoTexto = [
                    'pendiente' => 'Pendiente',
                    'aprobada' => 'Aprobada',
                    'rechazada' => 'Rechazada'
                  ];
                  ?>
                  <span class="badge" style="<?php echo $estadoColores[$pub->estado] ?? ''; ?>">
                    <?php echo $estadoTexto[$pub->estado] ?? ucfirst($pub->estado); ?>
                  </span>
                </td>

                <!-- Fecha -->
                <td>
                  <div class="meta" style="font-size: 13px;">
                    <?php echo date('d/m/Y', strtotime($pub->fecha_creacion)); ?>
                    <br>
                    <?php echo date('H:i', strtotime($pub->fecha_creacion)); ?>
                  </div>
                </td>

                <!-- Acciones -->
                <td>
                  <div style="display: flex; gap: 6px; justify-content: flex-end; flex-wrap: nowrap; align-items: center;">
                    <?php if ($pub->estado === 'pendiente'): ?>
                      <!-- Aprobar -->
                      <button 
                        onclick="aprobarPublicacion(<?php echo $pub->id; ?>)" 
                        class="btn primary"
                        style="padding: 6px 10px; font-size: 12px; background: #34C759; white-space: nowrap;"
                        title="Aprobar publicación"
                      >
                        ✓ Aprobar
                      </button>

                      <!-- Rechazar -->
                      <button 
                        onclick="mostrarModalRechazo(<?php echo $pub->id; ?>)" 
                        class="btn"
                        style="padding: 6px 10px; font-size: 12px; background: #FF3B30; color: white; white-space: nowrap;"
                        title="Rechazar publicación"
                      >
                        ✕ Rechazar
                      </button>
                    <?php endif; ?>

                    <!-- Ver detalle -->
                    <button 
                      onclick="verDetallePublicacion(<?php echo $pub->id; ?>)" 
                      class="btn outline"
                      style="padding: 6px 10px; font-size: 12px; white-space: nowrap;"
                      title="Ver detalle"
                    >
                      👁️ Ver
                    </button>

                    <!-- Eliminar -->
                    <button 
                      onclick="eliminarPublicacion(<?php echo $pub->id; ?>)" 
                      class="btn outline"
                      style="padding: 6px 10px; font-size: 12px; color: #FF3B30; border-color: #FF3B30; white-space: nowrap;"
                      title="Eliminar permanentemente"
                    >
                      🗑️
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 48px; color: #999;">
                <div class="h3" style="margin-bottom: 8px;">No hay publicaciones</div>
                <p class="meta">No se encontraron publicaciones con los filtros seleccionados</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <?php if (!empty($publicaciones) && isset($pagination) && $pagination['total_pages'] > 1): ?>
      <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #E5E5E5;">
        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
          <?php if ($pagination['current_page'] > 1): ?>
            <a href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria'] ?? '', 'q' => $filtros['q'] ?? '']); ?>" class="btn outline">
              ← Anterior
            </a>
          <?php endif; ?>

          <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
            <a 
              href="?page=<?php echo $i; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria'] ?? '', 'q' => $filtros['q'] ?? '']); ?>" 
              class="btn <?php echo $i === $pagination['current_page'] ? 'primary' : 'outline'; ?>"
            >
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <a href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria'] ?? '', 'q' => $filtros['q'] ?? '']); ?>" class="btn outline">
              Siguiente →
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

</main>

<!-- Modal: Ver detalle de publicación -->
<div id="modalDetalle" class="modal" style="display: none;">
  <div class="modal-content" style="max-width: 1200px !important; width: 95% !important; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #E5E5E5;">
      <h2 class="h2" style="margin: 0;">Detalle de Publicación</h2>
      <button onclick="cerrarModal('modalDetalle')" class="btn outline" style="padding: 8px 16px;">
        ✕ Cerrar
      </button>
    </div>
    <div id="contenidoDetalle">
      <div style="text-align: center; padding: 48px;">
        <p class="meta">Cargando...</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Rechazar publicación -->
<div id="modalRechazo" class="modal" style="display: none;">
  <div class="modal-content" style="max-width: 500px;">
    <h2 class="h2" style="margin-bottom: 16px;">Rechazar Publicación</h2>
    <p class="meta" style="margin-bottom: 24px;">
      Indica el motivo del rechazo. Esta información será enviada al usuario.
    </p>
    
    <form id="formRechazo" onsubmit="event.preventDefault(); rechazarPublicacion();">
      <input type="hidden" id="publicacion_id_rechazo" value="">
      
      <div style="margin-bottom: 16px;">
        <label class="label">Motivo del rechazo *</label>
        <textarea 
          id="motivo_rechazo" 
          class="input" 
          rows="5" 
          placeholder="Ej: Las imágenes no son claras, falta información sobre el estado del vehículo..."
          required
        ></textarea>
      </div>

      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModal('modalRechazo')" class="btn outline">
          Cancelar
        </button>
        <button type="submit" class="btn primary" style="background: #FF3B30;">
          Confirmar Rechazo
        </button>
      </div>
    </form>
  </div>
</div>

<!-- JavaScript para funcionalidad AJAX -->
<script>
// Ver detalle completo de publicación
function verDetallePublicacion(id) {
  document.getElementById('modalDetalle').style.display = 'flex';
  document.getElementById('contenidoDetalle').innerHTML = '<div style="text-align: center; padding: 48px;"><p class="meta">Cargando...</p></div>';

  fetch(`<?php echo BASE_URL; ?>/admin?action=ver&id=${id}&ajax=1`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        document.getElementById('contenidoDetalle').innerHTML = 
          `<div style="text-align: center; padding: 48px; color: #FF3B30;">
            <p class="h3">Error al cargar el detalle</p>
            <p class="meta">${data.error}</p>
          </div>`;
        return;
      }

      const pub = data.publicacion;
      const fotos = data.fotos || [];
      const historial = data.historial || [];

      // Generar HTML de las fotos
      let fotosHtml = '';
      if (fotos && fotos.length > 0) {
        fotosHtml = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-top: 16px;">';
        fotos.forEach((foto, index) => {
          fotosHtml += `
            <div style="position: relative; border-radius: 8px; overflow: hidden; background: #f5f5f5; aspect-ratio: 4/3;">
              <img 
                src="<?php echo BASE_URL; ?>/uploads/${foto.ruta_archivo}" 
                alt="Foto ${index + 1}"
                style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                onclick="window.open('<?php echo BASE_URL; ?>/uploads/${foto.ruta_archivo}', '_blank')"
              >
              ${foto.es_principal ? '<span style="position: absolute; top: 8px; left: 8px; background: #007AFF; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Principal</span>' : ''}
            </div>
          `;
        });
        fotosHtml += '</div>';
      } else {
        fotosHtml = '<p class="meta" style="margin-top: 16px;">No hay fotos disponibles</p>';
      }

      let html = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
          <!-- Columna izquierda: Información -->
          <div>
            <h3 class="h3" style="margin-bottom: 16px; color: #007AFF;">📝 Información de la Publicación</h3>
            <div style="background: #F9F9F9; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
              <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Título:</td>
                  <td style="padding: 8px 0;">${pub.titulo}</td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Marca:</td>
                  <td style="padding: 8px 0;">${pub.marca || 'N/A'}</td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Modelo:</td>
                  <td style="padding: 8px 0;">${pub.modelo || 'N/A'}</td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Año:</td>
                  <td style="padding: 8px 0;">${pub.anio || 'N/A'}</td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Precio:</td>
                  <td style="padding: 8px 0; font-size: 18px; font-weight: 600; color: #007AFF;">
                    ${pub.precio ? '$' + Number(pub.precio).toLocaleString('es-CL') : 'N/A'}
                  </td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Categoría:</td>
                  <td style="padding: 8px 0;">${pub.categoria_nombre}</td>
                </tr>
                ${pub.subcategoria_nombre ? `
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Subcategoría:</td>
                  <td style="padding: 8px 0;">${pub.subcategoria_nombre}</td>
                </tr>` : ''}
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Ubicación:</td>
                  <td style="padding: 8px 0;">
                    ${pub.region_nombre}${pub.comuna_nombre ? ', ' + pub.comuna_nombre : ''}
                  </td>
                </tr>
              </table>
            </div>

            <h4 class="h4" style="margin: 24px 0 12px; color: #007AFF;">📄 Descripción</h4>
            <div style="background: #F9F9F9; padding: 16px; border-radius: 8px; white-space: pre-wrap; line-height: 1.6;">
              ${pub.descripcion || '<span class="meta">Sin descripción</span>'}
            </div>

            ${pub.motivo_rechazo ? `
              <div style="margin-top: 24px; padding: 16px; background: #FFF3F3; border-left: 4px solid #FF3B30; border-radius: 4px;">
                <h4 class="h4" style="color: #FF3B30; margin-bottom: 8px;">❌ Motivo de Rechazo</h4>
                <p style="white-space: pre-wrap;">${pub.motivo_rechazo}</p>
              </div>
            ` : ''}
          </div>

          <!-- Columna derecha: Usuario y fotos -->
          <div>
            <h3 class="h3" style="margin-bottom: 16px; color: #007AFF;">👤 Información del Usuario</h3>
            <div style="background: #F9F9F9; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
              <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Nombre:</td>
                  <td style="padding: 8px 0;">${pub.usuario_nombre} ${pub.usuario_apellido}</td>
                </tr>
                <tr style="border-bottom: 1px solid #E5E5E5;">
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Email:</td>
                  <td style="padding: 8px 0;">
                    <a href="mailto:${pub.usuario_email}" style="color: #007AFF; text-decoration: none;">
                      ${pub.usuario_email}
                    </a>
                  </td>
                </tr>
                ${pub.usuario_telefono ? `
                <tr>
                  <td style="padding: 8px 0; font-weight: 600; color: #666;">Teléfono:</td>
                  <td style="padding: 8px 0;">
                    <a href="tel:${pub.usuario_telefono}" style="color: #007AFF; text-decoration: none;">
                      ${pub.usuario_telefono}
                    </a>
                  </td>
                </tr>` : ''}
              </table>
            </div>

            <h3 class="h3" style="margin-bottom: 12px; color: #007AFF;">📷 Fotos (${fotos.length})</h3>
            ${fotosHtml}
          </div>
        </div>

        <!-- Acciones del modal -->
        <div style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #E5E5E5; display: flex; gap: 12px; justify-content: flex-end;">
          ${pub.estado === 'pendiente' ? `
            <button onclick="aprobarPublicacion(${pub.id})" class="btn primary" style="background: #34C759;">
              ✓ Aprobar Publicación
            </button>
            <button onclick="cerrarModal('modalDetalle'); mostrarModalRechazo(${pub.id});" class="btn" style="background: #FF3B30; color: white;">
              ✕ Rechazar Publicación
            </button>
          ` : ''}
          <button onclick="cerrarModal('modalDetalle')" class="btn outline">
            Cerrar
          </button>
        </div>
      `;

      document.getElementById('contenidoDetalle').innerHTML = html;
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('contenidoDetalle').innerHTML = 
        `<div style="text-align: center; padding: 48px; color: #FF3B30;">
          <p class="h3">Error al cargar el detalle</p>
          <p class="meta">No se pudo conectar con el servidor</p>
        </div>`;
    });
}

// Aprobar publicación
function aprobarPublicacion(id) {
  if (!confirm('¿Aprobar esta publicación?')) return;

  fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}/aprobar`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    alert('Error de conexión');
  });
}

// Mostrar modal de rechazo
function mostrarModalRechazo(id) {
  document.getElementById('publicacion_id_rechazo').value = id;
  document.getElementById('motivo_rechazo').value = '';
  document.getElementById('modalRechazo').style.display = 'flex';
}

// Rechazar publicación
function rechazarPublicacion() {
  const id = document.getElementById('publicacion_id_rechazo').value;
  const motivo = document.getElementById('motivo_rechazo').value;

  if (!motivo.trim()) {
    alert('Debes proporcionar un motivo de rechazo');
    return;
  }

  fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}/rechazar`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `motivo=${encodeURIComponent(motivo)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      cerrarModal('modalRechazo');
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    alert('Error de conexión');
  });
}

// Eliminar publicación
function eliminarPublicacion(id) {
  if (!confirm('¿ELIMINAR PERMANENTEMENTE esta publicación? Esta acción no se puede deshacer.')) return;
  if (!confirm('¿Estás ABSOLUTAMENTE SEGURO? Se eliminarán también todas las fotos y datos relacionados.')) return;

  fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}`, {
    method: 'DELETE'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    alert('Error de conexión');
  });
}

// Cerrar modales
function cerrarModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = 'none';
  }
}
</script>

<!-- Estilos para modales -->
<style>
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* Estilos específicos para modal de detalle - sobrescriben admin.css */
#modalDetalle .modal-content {
  background-color: white;
  padding: 24px;
  border-radius: 12px;
  max-width: 1200px !important;
  width: 95% !important;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
  animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  background: #E5E5E5;
  color: #666;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
