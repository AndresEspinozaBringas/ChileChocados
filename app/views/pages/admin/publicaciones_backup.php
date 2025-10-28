<?php

/**
 * Vista: Panel de Gestión de Publicaciones (Admin)
 * Permite al administrador aprobar, rechazar y moderar publicaciones
 */
$pageTitle = 'Gestión de Publicaciones - Admin';
require_once __DIR__ . '/../../layouts/header.php';
?>

<main class="container" style="padding: 32px 0;">
  
  <!-- Título y acciones principales -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
    <div>
      <h1 class="h1" style="margin: 0;">Panel de Moderación</h1>
      <p class="meta" style="margin-top: 4px;">Gestiona y modera todas las publicaciones del sistema</p>
    </div>
    <div style="display: flex; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/admin" class="btn outline">
        ← Volver al Dashboard
      </a>
      <button class="btn primary" onclick="location.reload()">
        ↻ Actualizar
      </button>
    </div>
  </div>

  <!-- KPIs / Estadísticas -->
  <div class="grid cols-4" style="gap: 16px; margin-bottom: 32px;">
    <div class="card" style="text-align: center; padding: 24px; border-left: 4px solid #0066CC;">
      <div class="h1" style="color: #0066CC; margin-bottom: 8px;">
        <?php echo number_format($stats->total ?? 0); ?>
      </div>
      <div class="meta">Total Publicaciones</div>
    </div>
    
    <div class="card" style="text-align: center; padding: 24px; border-left: 4px solid #FF9500;">
      <div class="h1" style="color: #FF9500; margin-bottom: 8px;">
        <?php echo number_format($stats->pendientes ?? 0); ?>
      </div>
      <div class="meta">Pendientes Aprobación</div>
    </div>
    
    <div class="card" style="text-align: center; padding: 24px; border-left: 4px solid #34C759;">
      <div class="h1" style="color: #34C759; margin-bottom: 8px;">
        <?php echo number_format($stats->aprobadas ?? 0); ?>
      </div>
      <div class="meta">Aprobadas</div>
    </div>
    
    <div class="card" style="text-align: center; padding: 24px; border-left: 4px solid #FF3B30;">
      <div class="h1" style="color: #FF3B30; margin-bottom: 8px;">
        <?php echo number_format($stats->rechazadas ?? 0); ?>
      </div>
      <div class="meta">Rechazadas</div>
    </div>
  </div>

  <!-- Filtros de búsqueda -->
  <div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?php echo BASE_URL; ?>/admin/publicaciones" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
      
      <!-- Filtro por estado -->
      <div>
        <label class="label">Estado</label>
        <select name="estado" class="input">
          <option value="">Todas</option>
          <option value="pendiente" <?php echo ($filtros['estado'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>
            Pendientes
          </option>
          <option value="aprobada" <?php echo ($filtros['estado'] ?? '') === 'aprobada' ? 'selected' : ''; ?>>
            Aprobadas
          </option>
          <option value="rechazada" <?php echo ($filtros['estado'] ?? '') === 'rechazada' ? 'selected' : ''; ?>>
            Rechazadas
          </option>
          <option value="borrador" <?php echo ($filtros['estado'] ?? '') === 'borrador' ? 'selected' : ''; ?>>
            Borradores
          </option>
        </select>
      </div>

      <!-- Filtro por categoría -->
      <div>
        <label class="label">Categoría</label>
        <select name="categoria" class="input">
          <option value="">Todas las categorías</option>
          <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?php echo $categoria->id; ?>" 
                      <?php echo ($filtros['categoria_id'] ?? '') == $categoria->id ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($categoria->nombre); ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <!-- Búsqueda por texto -->
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
            <th style="width: 60px;">ID</th>
            <th style="width: 80px;">Foto</th>
            <th>Título / Usuario</th>
            <th style="width: 120px;">Categoría</th>
            <th style="width: 100px;">Estado</th>
            <th style="width: 120px;">Fecha</th>
            <th style="width: 200px; text-align: center;">Acciones</th>
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

                <!-- Foto miniatura -->
                <td>
                  <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #f5f5f5;">
                    <?php if ($pub->foto_principal): ?>
                      <img 
                        src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo $pub->foto_principal; ?>" 
                        alt="<?php echo htmlspecialchars($pub->titulo); ?>"
                        style="width: 100%; height: 100%; object-fit: cover;"
                      >
                    <?php else: ?>
                      <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #999;">
                        Sin foto
                      </div>
                    <?php endif; ?>
                  </div>
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
                      <?php echo htmlspecialchars($pub->usuario_email); ?>
                    </div>
                  </div>
                </td>

                <!-- Categoría -->
                <td>
                  <span style="font-size: 13px;">
                    <?php echo htmlspecialchars($pub->categoria_nombre); ?>
                    <?php if ($pub->subcategoria_nombre): ?>
                      <br><span class="meta"><?php echo htmlspecialchars($pub->subcategoria_nombre); ?></span>
                    <?php endif; ?>
                  </span>
                </td>

                <!-- Estado -->
                <td>
                  <?php
                  $badge_colors = [
                    'pendiente' => 'background: #FF9500; color: white;',
                    'aprobada' => 'background: #34C759; color: white;',
                    'rechazada' => 'background: #FF3B30; color: white;',
                    'borrador' => 'background: #8E8E93; color: white;',
                  ];
                  $color = $badge_colors[$pub->estado] ?? 'background: #E5E5EA; color: #000;';
                  ?>
                  <span style="<?php echo $color; ?> padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block;">
                    <?php echo ucfirst($pub->estado); ?>
                  </span>
                </td>

                <!-- Fecha -->
                <td>
                  <span style="font-size: 13px;">
                    <?php echo date('d/m/Y', strtotime($pub->fecha_creacion)); ?>
                    <br>
                    <span class="meta"><?php echo date('H:i', strtotime($pub->fecha_creacion)); ?></span>
                  </span>
                </td>

                <!-- Acciones -->
                <td>
                  <div style="display: flex; gap: 4px; justify-content: center; flex-wrap: wrap;">
                    <button 
                      class="btn outline" 
                      onclick="verDetallePublicacion(<?php echo $pub->id; ?>)"
                      style="padding: 6px 12px; font-size: 13px;"
                      title="Ver detalle completo"
                    >
                      👁️ Ver
                    </button>

                    <?php if ($pub->estado === 'pendiente'): ?>
                      <button 
                        class="btn" 
                        onclick="aprobarPublicacion(<?php echo $pub->id; ?>)"
                        style="padding: 6px 12px; font-size: 13px; background: #34C759; color: white;"
                        title="Aprobar publicación"
                      >
                        ✓ Aprobar
                      </button>
                      <button 
                        class="btn" 
                        onclick="mostrarModalRechazo(<?php echo $pub->id; ?>)"
                        style="padding: 6px 12px; font-size: 13px; background: #FF3B30; color: white;"
                        title="Rechazar publicación"
                      >
                        ✗ Rechazar
                      </button>
                    <?php endif; ?>

                    <button 
                      class="btn outline" 
                      onclick="eliminarPublicacion(<?php echo $pub->id; ?>)"
                      style="padding: 6px 12px; font-size: 13px; color: #FF3B30; border-color: #FF3B30;"
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
              <td colspan="7" style="text-align: center; padding: 48px;">
                <p class="meta">No se encontraron publicaciones con los filtros seleccionados</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <?php if (!empty($paginacion) && $paginacion['total_pages'] > 1): ?>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #E5E5EA;">
        <div class="meta">
          Mostrando <?php echo (($paginacion['current_page'] - 1) * $paginacion['per_page']) + 1; ?> 
          - 
          <?php echo min($paginacion['current_page'] * $paginacion['per_page'], $paginacion['total_records']); ?>
          de <?php echo $paginacion['total_records']; ?> publicaciones
        </div>

        <div style="display: flex; gap: 8px;">
          <?php if ($paginacion['current_page'] > 1): ?>
            <a href="?page=<?php echo $paginacion['current_page'] - 1; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria_id'] ?? '', 'q' => $filtros['busqueda'] ?? '']); ?>" class="btn outline">
              ← Anterior
            </a>
          <?php endif; ?>

          <?php for ($i = max(1, $paginacion['current_page'] - 2); $i <= min($paginacion['total_pages'], $paginacion['current_page'] + 2); $i++): ?>
            <a 
              href="?page=<?php echo $i; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria_id'] ?? '', 'q' => $filtros['busqueda'] ?? '']); ?>" 
              class="btn <?php echo $i === $paginacion['current_page'] ? 'primary' : 'outline'; ?>"
            >
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <?php if ($paginacion['current_page'] < $paginacion['total_pages']): ?>
            <a href="?page=<?php echo $paginacion['current_page'] + 1; ?><?php echo http_build_query(['estado' => $filtros['estado'] ?? '', 'categoria' => $filtros['categoria_id'] ?? '', 'q' => $filtros['busqueda'] ?? '']); ?>" class="btn outline">
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
  <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h2 class="h2">Detalle de Publicación</h2>
      <button onclick="cerrarModal('modalDetalle')" class="btn outline">✕ Cerrar</button>
    </div>
    <div id="contenidoDetalle">
      <div style="text-align: center; padding: 48px;">
        <p>Cargando...</p>
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
  document.getElementById('contenidoDetalle').innerHTML = '<div style="text-align: center; padding: 48px;"><p>Cargando...</p></div>';

  fetch(`<?php echo BASE_URL; ?>/admin?action=ver&id=${id}&ajax=1`)
    .then(response => response.json())
    .then(data => {
      const pub = data.publicacion;
      const fotos = data.fotos || [];
      const historial = data.historial || [];

      let html = `
        <div class="grid cols-2" style="gap: 24px;">
          <div>
            <h3 class="h3">Información de la Publicación</h3>
            <div style="margin-top: 16px;">
              <p><strong>Título:</strong> ${pub.titulo}</p>
              <p><strong>Marca:</strong> ${pub.marca || 'N/A'}</p>
              <p><strong>Modelo:</strong> ${pub.modelo || 'N/A'}</p>
              <p><strong>Año:</strong> ${pub.anio || 'N/A'}</p>
              <p><strong>Precio:</strong> ${pub.precio ? '$' + Number(pub.precio).toLocaleString() : 'N/A'}</p>
              <p><strong>Categoría:</strong> ${pub.categoria_nombre}</p>
              ${pub.subcategoria_nombre ? `<p><strong>Subcategoría:</strong> ${pub.subcategoria_nombre}</p>` : ''}
              <p><strong>Región:</strong> ${pub.region_nombre}</p>
              ${pub.comuna_nombre ? `<p><strong>Comuna:</strong> ${pub.comuna_nombre}</p>` : ''}
            </div>

            <h4 class="h4" style="margin-top: 24px;">Descripción</h4>
            <p style="margin-top: 8px; white-space: pre-wrap;">${pub.descripcion || 'Sin descripción'}</p>

            ${pub.motivo_rechazo ? `
              <div style="margin-top: 24px; padding: 16px; background: #FFF3F3; border-left: 4px solid #FF3B30; border-radius: 8px;">
                <h4 class="h4" style="color: #FF3B30;">Motivo de Rechazo</h4>
                <p style="margin-top: 8px;">${pub.motivo_rechazo}</p>
              </div>
            ` : ''}
          </div>

          <div>
            <h3 class="h3">Usuario</h3>
            <div style="margin-top: 16px;">
              <p><strong>Nombre:</strong> ${pub.usuario_nombre} ${pub.usuario_apellido}</p>
              <p><strong>Email:</strong> ${pub.usuario_email}</p>
              <p><strong>Teléfono:</strong> ${pub.usuario_telefono || 'N/A'}</p>
            </div>

            <h3 class="h3" style="margin-top: 24px;">Fotos (${fotos.length})</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 16px;">
              ${fotos.map(foto => `
                <img 
                  src="<?php echo BASE_URL; ?>/uploads/publicaciones/${foto.ruta_archivo}" 
                  alt="Foto"
                  style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; cursor: pointer;"
                  onclick="window.open(this.src, '_blank')"
                >
              `).join('')}
            </div>

            <h3 class="h3" style="margin-top: 24px;">Historial de Moderación</h3>
            <div style="margin-top: 16px; max-height: 300px; overflow-y: auto;">
              ${historial.length > 0 ? historial.map(h => `
                <div style="padding: 12px; background: #F5F5F5; border-radius: 8px; margin-bottom: 8px;">
                  <p style="font-size: 13px;"><strong>${h.accion}</strong> por ${h.usuario_nombre || 'Sistema'}</p>
                  <p class="meta" style="font-size: 12px;">${new Date(h.fecha).toLocaleString()}</p>
                </div>
              `).join('') : '<p class="meta">Sin historial</p>'}
            </div>
          </div>
        </div>
      `;

      document.getElementById('contenidoDetalle').innerHTML = html;
    })
    .catch(error => {
      document.getElementById('contenidoDetalle').innerHTML = '<div style="text-align: center; padding: 48px;"><p style="color: red;">Error al cargar el detalle</p></div>';
    });
}

// Aprobar publicación
function aprobarPublicacion(id) {
  if (!confirm('¿Estás seguro de aprobar esta publicación?')) return;

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

.modal-content {
  background-color: white;
  padding: 32px;
  border-radius: 12px;
  width: 100%;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
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
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
