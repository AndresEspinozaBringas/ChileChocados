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
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin-layout.css">

<main class="container admin-container">

  <!-- Encabezado -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
      <h1 class="h1" style="display: flex; align-items: center; gap: 12px;">
        <?php echo icon('file-text', 32); ?>
        Panel de Moderación
      </h1>
      <p class="meta">Gestiona y modera todas las publicaciones del sistema</p>
    </div>
    <div style="display: flex; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/admin" class="btn outline">
        <?php echo icon('arrow-left', 18); ?> Volver
      </a>
      <button onclick="exportarDatos()" class="btn outline">
        <?php echo icon('download', 18); ?> Exportar CSV
      </button>
      <button onclick="location.reload()" class="btn primary">
        <?php echo icon('refresh-cw', 18); ?> Actualizar
      </button>
    </div>
  </div>

  <!-- Tabs de Estado -->
  <div class="tabs-container" style="margin-bottom: 24px;">
    <div class="tabs">
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones" 
         class="tab <?php echo empty($filtros['estado']) ? 'tab-active' : ''; ?>">
        <?php echo icon('list', 18); ?>
        <span>Todas</span>
        <span class="tab-badge"><?php echo $conteo['total'] ?? 0; ?></span>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=pendiente" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'pendiente' ? 'tab-active' : ''; ?>">
        <?php echo icon('clock', 18); ?>
        <span>Pendientes</span>
        <?php if (($conteo['pendientes'] ?? 0) > 0): ?>
          <span class="tab-badge tab-badge-warning"><?php echo $conteo['pendientes']; ?></span>
        <?php endif; ?>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=aprobada" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'aprobada' ? 'tab-active' : ''; ?>">
        <?php echo icon('check-circle', 18); ?>
        <span>Aprobadas</span>
        <span class="tab-badge tab-badge-success"><?php echo $conteo['aprobadas'] ?? 0; ?></span>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=rechazada" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'rechazada' ? 'tab-active' : ''; ?>">
        <?php echo icon('x-circle', 18); ?>
        <span>Rechazadas</span>
        <span class="tab-badge tab-badge-danger"><?php echo $conteo['rechazadas'] ?? 0; ?></span>
      </a>
    </div>
  </div>

  <!-- Filtros Adicionales (Colapsables) -->
  <div class="card filtros-card" style="margin-bottom: 24px; padding: 16px 24px;">
    <button type="button" onclick="toggleFilters()" class="filter-toggle" style="width: 100%; display: flex; align-items: center; justify-content: space-between; background: none; border: none; cursor: pointer; padding: 8px 0; color: var(--cc-text-primary);">
      <span style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--cc-text-primary);">
        <?php echo icon('filter', 18); ?>
        Filtros Avanzados
      </span>
      <?php echo icon('chevron-down', 18); ?>
    </button>
    
    <div id="advanced-filters" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--cc-border-default);">
      <form method="GET" action="<?php echo BASE_URL; ?>/admin/publicaciones" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        
        <!-- Mantener estado actual -->
        <?php if (!empty($filtros['estado'])): ?>
          <input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtros['estado']); ?>">
        <?php endif; ?>
        
        <!-- Categoría -->
        <div>
          <label class="label" style="color: var(--cc-text-secondary);">Categoría</label>
          <select name="categoria" class="input" style="background: var(--cc-bg-surface); color: var(--cc-text-primary); border-color: var(--cc-border-default);">
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
          <label class="label" style="color: var(--cc-text-secondary);">Buscar</label>
          <input 
            type="text" 
            name="q" 
          class="input" 
          placeholder="Título, marca, modelo..." 
          value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>"
          style="background: var(--cc-bg-surface); color: var(--cc-text-primary); border-color: var(--cc-border-default);"
        >
      </div>

      <!-- Fecha desde -->
      <div>
        <label class="label" style="color: var(--cc-text-secondary);">Desde</label>
        <input 
          type="date" 
          name="fecha_desde" 
          class="input"
          value="<?php echo $filtros['fecha_desde'] ?? ''; ?>"
          style="background: var(--cc-bg-surface); color: var(--cc-text-primary); border-color: var(--cc-border-default); color-scheme: dark;"
        >
      </div>

      <!-- Fecha hasta -->
      <div>
        <label class="label" style="color: var(--cc-text-secondary);">Hasta</label>
        <input 
          type="date" 
          name="fecha_hasta" 
          class="input"
          value="<?php echo $filtros['fecha_hasta'] ?? ''; ?>"
          style="background: var(--cc-bg-surface); color: var(--cc-text-primary); border-color: var(--cc-border-default); color-scheme: dark;"
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

  <!-- Tabla de publicaciones (Desktop) -->
  <div class="card publicaciones-table-view">
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
                  <span style="font-weight: 600; color: var(--cc-text-secondary);">#<?php echo $pub->id; ?></span>
                </td>

                <!-- Título y usuario -->
                <td>
                  <div>
                    <a 
                      href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" 
                      target="_blank"
                      class="h4" 
                      style="color: var(--cc-primary); text-decoration: none; display: block; margin-bottom: 4px;"
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
                  <div style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                    <span class="badge">
                      <?php echo htmlspecialchars($pub->categoria_nombre ?? 'Sin categoría'); ?>
                    </span>
                    <?php if ($pub->subcategoria_nombre): ?>
                      <span class="badge" style="font-size: 11px; background: #E5E5E5;">
                        <?php echo htmlspecialchars($pub->subcategoria_nombre); ?>
                      </span>
                    <?php endif; ?>
                  </div>
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
                        class="btn btn-primary"
                        style="padding: 6px 12px; font-size: 12px; background: var(--cc-success, #10B981); border-color: var(--cc-success, #10B981); white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"
                        title="Aprobar publicación"
                      >
                        <?php echo icon('check', 14); ?>
                        <span>Aprobar</span>
                      </button>

                      <!-- Rechazar -->
                      <button 
                        onclick="mostrarModalRechazo(<?php echo $pub->id; ?>)" 
                        class="btn"
                        style="padding: 6px 12px; font-size: 12px; background: var(--cc-danger, #EF4444); border-color: var(--cc-danger, #EF4444); color: white; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"
                        title="Rechazar publicación"
                      >
                        <?php echo icon('x', 14); ?>
                        <span>Rechazar</span>
                      </button>
                    <?php endif; ?>

                    <!-- Ver detalle -->
                    <button 
                      onclick="verDetallePublicacion(<?php echo $pub->id; ?>)" 
                      class="btn btn-outline"
                      style="padding: 6px 12px; font-size: 12px; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"
                      title="Ver detalle"
                    >
                      <?php echo icon('eye', 14); ?>
                      <span>Ver</span>
                    </button>

                    <!-- Eliminar -->
                    <button 
                      onclick="eliminarPublicacion(<?php echo $pub->id; ?>)" 
                      class="btn btn-outline"
                      style="padding: 6px 12px; font-size: 12px; color: var(--cc-danger, #EF4444); border-color: var(--cc-danger, #EF4444); white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"
                      title="Eliminar permanentemente"
                    >
                      <?php echo icon('trash-2', 14); ?>
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

  </div>
  
  <!-- Vista de Cards para Móvil -->
  <div class="card publicaciones-cards-view" style="display: none;">
    <?php if (!empty($publicaciones)): ?>
      <div class="publicaciones-cards-container">
        <?php foreach ($publicaciones as $pub): ?>
          <div class="publicacion-card" id="pub-card-<?php echo $pub->id; ?>">
            <!-- Header del card -->
            <div class="publicacion-card-header">
              <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                  <span style="font-weight: 700; color: var(--cc-text-secondary); font-size: 12px;">#<?php echo $pub->id; ?></span>
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
                  <span class="badge" style="<?php echo $estadoColores[$pub->estado] ?? ''; ?> font-size: 11px;">
                    <?php echo $estadoTexto[$pub->estado] ?? ucfirst($pub->estado); ?>
                  </span>
                </div>
                <a 
                  href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" 
                  target="_blank"
                  style="color: var(--cc-primary); text-decoration: none; font-weight: 600; font-size: 14px; line-height: 1.3; display: block;"
                >
                  <?php echo htmlspecialchars($pub->titulo); ?>
                </a>
              </div>
            </div>
            
            <!-- Body del card -->
            <div class="publicacion-card-body">
              <div class="publicacion-card-row">
                <span class="publicacion-label">Usuario:</span>
                <span class="publicacion-value">
                  <?php echo htmlspecialchars($pub->usuario_nombre . ' ' . $pub->usuario_apellido); ?>
                </span>
              </div>
              <div class="publicacion-card-row">
                <span class="publicacion-label">Email:</span>
                <span class="publicacion-value" style="font-size: 12px;">
                  <?php echo htmlspecialchars($pub->usuario_email); ?>
                </span>
              </div>
              <div class="publicacion-card-row">
                <span class="publicacion-label">Categoría:</span>
                <span class="publicacion-value">
                  <span class="badge" style="font-size: 11px;">
                    <?php echo htmlspecialchars($pub->categoria_nombre ?? 'Sin categoría'); ?>
                  </span>
                </span>
              </div>
              <div class="publicacion-card-row">
                <span class="publicacion-label">Fecha:</span>
                <span class="publicacion-value">
                  <?php echo date('d/m/Y H:i', strtotime($pub->fecha_creacion)); ?>
                </span>
              </div>
            </div>
            
            <!-- Footer con acciones -->
            <div class="publicacion-card-actions">
              <?php if ($pub->estado === 'pendiente'): ?>
                <button 
                  onclick="aprobarPublicacion(<?php echo $pub->id; ?>)" 
                  class="btn btn-primary"
                  style="flex: 1; padding: 10px; font-size: 13px; background: var(--cc-success, #10B981); border-color: var(--cc-success, #10B981); display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
                >
                  <?php echo icon('check', 16); ?>
                  <span>Aprobar</span>
                </button>
                <button 
                  onclick="mostrarModalRechazo(<?php echo $pub->id; ?>)" 
                  class="btn"
                  style="flex: 1; padding: 10px; font-size: 13px; background: var(--cc-danger, #EF4444); border-color: var(--cc-danger, #EF4444); color: white; display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
                >
                  <?php echo icon('x', 16); ?>
                  <span>Rechazar</span>
                </button>
              <?php endif; ?>
              <button 
                onclick="verDetallePublicacion(<?php echo $pub->id; ?>)" 
                class="btn btn-outline"
                style="flex: 1; padding: 10px; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
              >
                <?php echo icon('eye', 16); ?>
                <span>Ver</span>
              </button>
              <button 
                onclick="eliminarPublicacion(<?php echo $pub->id; ?>)" 
                class="btn btn-outline"
                style="padding: 10px; font-size: 13px; color: var(--cc-danger, #EF4444); border-color: var(--cc-danger, #EF4444); display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
              >
                <?php echo icon('trash-2', 16); ?>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div style="text-align: center; padding: 48px; color: #999;">
        <div class="h3" style="margin-bottom: 8px;">No hay publicaciones</div>
        <p class="meta">No se encontraron publicaciones con los filtros seleccionados</p>
      </div>
    <?php endif; ?>
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
<div id="modalDetalle" class="admin-modal" style="display: none;">
  <div class="admin-modal-content admin-modal-large">
    <div class="admin-modal-header">
      <h2 class="h2" style="margin: 0;">Detalle de Publicación</h2>
      <button onclick="cerrarModal('modalDetalle')" class="btn outline" style="padding: 8px 16px;">
        ✕ Cerrar
      </button>
    </div>
    <div id="contenidoDetalle" class="admin-modal-body">
      <div style="text-align: center; padding: 48px;">
        <p class="meta">Cargando...</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Rechazar publicación -->
<div id="modalRechazo" class="admin-modal" style="display: none;">
  <div class="admin-modal-content admin-modal-small">
    <div class="admin-modal-header">
      <h2 class="h2" style="margin: 0;">Rechazar Publicación</h2>
    </div>
    <div class="admin-modal-body">
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
</div>

<!-- JavaScript para funcionalidad AJAX -->
<script>
// Ver detalle completo de publicación
function verDetallePublicacion(id) {
  document.getElementById('modalDetalle').style.display = 'flex';
  document.getElementById('contenidoDetalle').innerHTML = '<div style="text-align: center; padding: 48px;"><p class="meta">Cargando...</p></div>';

  fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}?ajax=1`)
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
      
      // Debug: ver estructura de datos
      console.log('Publicación:', pub);
      console.log('Fotos:', fotos);

      // Generar HTML de las fotos
      let fotosHtml = '';
      if (fotos && fotos.length > 0) {
        fotosHtml = '<div class="foto-grid">';
        fotos.forEach((foto, index) => {
          console.log(`Foto ${index}:`, foto);
          
          // La ruta en BD es relativa desde /uploads/publicaciones/, ej: "2025/10/archivo.jpg"
          let rutaRelativa = foto.ruta || foto.ruta_archivo || '';
          
          // Construir URL completa
          let rutaCompleta = '<?php echo BASE_URL; ?>/uploads/publicaciones/' + rutaRelativa;
          
          console.log(`Ruta construida: ${rutaCompleta}`);
          
          fotosHtml += `
            <div class="foto-item" onclick="window.open('${rutaCompleta}', '_blank')" title="Click para ver en tamaño completo" style="cursor: pointer;">
              <img 
                src="${rutaCompleta}" 
                alt="Foto ${index + 1}"
                onerror="console.error('Error cargando imagen:', this.src); this.parentElement.innerHTML='<div style=\\'display:flex;align-items:center;justify-content:center;height:100%;background:#F3F4F6;color:#9CA3AF;font-size:10px;flex-direction:column;padding:8px;text-align:center;\\'>❌<br>Error<br><small style=\\'word-break:break-all;\\'>${rutaRelativa}</small></div>';"
              >
              ${foto.es_principal == 1 ? '<span class="foto-principal-badge">★ Principal</span>' : ''}
            </div>
          `;
        });
        fotosHtml += '</div>';
      } else {
        fotosHtml = '<div style="text-align: center; padding: 40px; background: #F9FAFB; border-radius: 12px; border: 2px dashed #E5E7EB;"><p class="meta" style="color: #9CA3AF;">📷 No hay fotos disponibles para esta publicación</p></div>';
      }

      let html = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
          <!-- Columna izquierda: Información -->
          <div>
            <div class="modal-section">
              <div class="modal-section-title">📝 Información de la Publicación</div>
              <table class="info-table">
                <tr>
                  <td>Título:</td>
                  <td><strong>${pub.titulo}</strong></td>
                </tr>
                <tr>
                  <td>Marca:</td>
                  <td>${pub.marca || 'N/A'}</td>
                </tr>
                <tr>
                  <td>Modelo:</td>
                  <td>${pub.modelo || 'N/A'}</td>
                </tr>
                <tr>
                  <td>Año:</td>
                  <td>${pub.anio || 'N/A'}</td>
                </tr>
                <tr>
                  <td>Precio:</td>
                  <td style="font-size: 20px; font-weight: 700; color: var(--cc-primary, #E6332A);">
                    ${pub.precio ? '$' + Number(pub.precio).toLocaleString('es-CL') : 'A convenir'}
                  </td>
                </tr>
                <tr>
                  <td>Categoría:</td>
                  <td>
                    <span class="badge" style="background: var(--cc-primary-pale, #FFF1F0); color: var(--cc-primary, #E6332A);">
                      ${pub.categoria_nombre}
                    </span>
                    ${pub.subcategoria_nombre ? `<br><span class="badge" style="margin-top: 4px;">${pub.subcategoria_nombre}</span>` : ''}
                  </td>
                </tr>
                <tr>
                  <td>Ubicación:</td>
                  <td>📍 ${pub.region_nombre}${pub.comuna_nombre ? ', ' + pub.comuna_nombre : ''}</td>
                </tr>
              </table>
            </div>

            <div class="modal-section">
              <div class="modal-section-title">📄 Descripción</div>
              <div style="white-space: pre-wrap; line-height: 1.6; color: #374151;">
                ${pub.descripcion || '<span class="meta" style="color: #9CA3AF;">Sin descripción</span>'}
              </div>
            </div>

            ${pub.motivo_rechazo ? `
              <div style="padding: 20px; background: #FEF2F2; border-left: 4px solid #EF4444; border-radius: 12px; border: 1px solid #FEE2E2;">
                <div style="font-size: 16px; font-weight: 700; color: #DC2626; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                  ❌ Motivo de Rechazo
                </div>
                <p style="white-space: pre-wrap; color: #991B1B; line-height: 1.6;">${pub.motivo_rechazo}</p>
              </div>
            ` : ''}
          </div>

          <!-- Columna derecha: Usuario y fotos -->
          <div>
            <div class="modal-section">
              <div class="modal-section-title">👤 Información del Usuario</div>
              <table class="info-table">
                <tr>
                  <td>Nombre:</td>
                  <td><strong>${pub.usuario_nombre} ${pub.usuario_apellido}</strong></td>
                </tr>
                <tr>
                  <td>Email:</td>
                  <td>
                    <a href="mailto:${pub.usuario_email}" style="color: var(--cc-primary, #E6332A); text-decoration: none; font-weight: 600;">
                      📧 ${pub.usuario_email}
                    </a>
                  </td>
                </tr>
                ${pub.usuario_telefono ? `
                <tr>
                  <td>Teléfono:</td>
                  <td>
                    <a href="tel:${pub.usuario_telefono}" style="color: var(--cc-primary, #E6332A); text-decoration: none; font-weight: 600;">
                      📱 ${pub.usuario_telefono}
                    </a>
                  </td>
                </tr>` : ''}
              </table>
            </div>

            <div class="modal-section">
              <div class="modal-section-title">📷 Fotos del Vehículo (${fotos.length})</div>
              ${fotosHtml}
            </div>
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
  background-color: var(--cc-bg-surface);
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  animation: adminModalFadeIn 0.3s ease-out;
  border: 1px solid var(--cc-border-default);
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.admin-modal-large {
  max-width: 1200px;
  width: 95%;
}

.admin-modal-small {
  max-width: 500px;
  width: 95%;
}

/* Modal header */
.admin-modal-header {
  padding: 24px 32px;
  border-bottom: 2px solid var(--cc-border-default);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
  background: var(--cc-bg-muted);
}

/* Modal body */
.admin-modal-body {
  padding: 32px;
  overflow-y: auto;
  flex: 1;
  background: var(--cc-bg-surface);
  color: var(--cc-text-primary);
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

/* Scrollbar personalizado para el modal */
.admin-modal-body::-webkit-scrollbar {
  width: 10px;
}

.admin-modal-body::-webkit-scrollbar-track {
  background: var(--cc-bg-muted);
  border-radius: 10px;
}

.admin-modal-body::-webkit-scrollbar-thumb {
  background: var(--cc-text-tertiary);
  border-radius: 10px;
}

.admin-modal-body::-webkit-scrollbar-thumb:hover {
  background: var(--cc-text-secondary);
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  background: var(--cc-bg-muted);
  color: var(--cc-text-secondary);
}

/* Mejoras para las imágenes en el modal */
.foto-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 16px;
}

.foto-item {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: var(--cc-bg-muted);
  aspect-ratio: 4/3;
  border: 2px solid var(--cc-border-default);
  transition: all 0.2s ease;
  cursor: pointer;
}

.foto-item:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  border-color: var(--cc-primary, #E6332A);
}

.foto-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.foto-principal-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: var(--cc-primary, #E6332A);
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Tabla de información mejorada */
.info-table {
  width: 100%;
  border-collapse: collapse;
}

.info-table tr {
  border-bottom: 1px solid var(--cc-border-default);
}

.info-table tr:last-child {
  border-bottom: none;
}

.info-table td {
  padding: 12px 0;
  vertical-align: top;
}

.info-table td:first-child {
  font-weight: 600;
  color: var(--cc-text-secondary);
  width: 140px;
}

.info-table td:last-child {
  color: var(--cc-text-primary);
}

/* Secciones del modal */
.modal-section {
  background: var(--cc-bg-muted);
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  border: 1px solid var(--cc-border-default);
}

.modal-section-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--cc-primary);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ============================================================================
 * RESPONSIVE DESIGN
 * ============================================================================ */

/* Tablets y pantallas medianas */
@media (max-width: 968px) {
  .admin-modal-content {
    padding: 20px;
    width: 100%;
    max-height: 95vh;
  }
  
  .foto-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
  }
  
  /* Modal detalle en 1 columna */
  .admin-modal-body > div[style*="grid-template-columns"] {
    grid-template-columns: 1fr !important;
  }
}

/* Móviles */
@media (max-width: 768px) {
  /* Container principal */
  .admin-container {
    padding: 16px !important;
  }
  
  /* Header */
  main > div:first-child {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 16px !important;
  }
  
  main > div:first-child h1 {
    font-size: 24px !important;
  }
  
  main > div:first-child > div:last-child {
    width: 100%;
    flex-direction: column !important;
  }
  
  main > div:first-child > div:last-child .btn {
    width: 100%;
    justify-content: center;
  }
  
  /* Tabs responsive */
  .tabs-container {
    padding: 6px;
  }
  
  .tabs {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  
  .tabs::-webkit-scrollbar {
    display: none;
  }
  
  .tab {
    padding: 10px 14px;
    font-size: 13px;
  }
  
  .tab span:not(.tab-badge) {
    display: none;
  }
  
  .tab-badge {
    min-width: 22px;
    height: 22px;
    font-size: 11px;
  }
  
  /* Filtros */
  .card {
    padding: 12px 16px !important;
  }
  
  #advanced-filters form {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }
  
  #advanced-filters form > div:last-child {
    flex-direction: column !important;
  }
  
  #advanced-filters form > div:last-child .btn {
    width: 100%;
  }
  
  /* Tabla responsive */
  .table {
    font-size: 12px;
  }
  
  .table thead th {
    padding: 10px 8px;
    font-size: 11px;
  }
  
  .table tbody td {
    padding: 12px 8px;
  }
  
  /* Ocultar columnas menos importantes */
  .table thead th:nth-child(3),
  .table tbody td:nth-child(3) {
    display: none;
  }
  
  .table thead th:nth-child(5),
  .table tbody td:nth-child(5) {
    display: none;
  }
  
  /* Ajustar columna de título */
  .table tbody td:nth-child(2) .h4 {
    font-size: 14px !important;
    line-height: 1.3 !important;
  }
  
  .table tbody td:nth-child(2) .meta {
    font-size: 11px !important;
  }
  
  /* Botones de acción más compactos */
  .table tbody td:last-child > div {
    flex-direction: column !important;
    gap: 6px !important;
    align-items: stretch !important;
  }
  
  .table tbody td:last-child .btn {
    width: 100% !important;
    padding: 8px 10px !important;
    font-size: 11px !important;
    justify-content: center;
  }
  
  .table tbody td:last-child .btn span {
    display: inline !important;
  }
  
  /* Paginación */
  .card > div:last-child {
    padding: 16px !important;
  }
  
  .card > div:last-child > div {
    gap: 6px !important;
  }
  
  .card > div:last-child .btn {
    padding: 8px 12px !important;
    font-size: 12px !important;
  }
  
  /* Modales en móvil */
  .admin-modal {
    padding: 10px;
  }
  
  .admin-modal-content {
    max-height: 95vh;
    border-radius: 12px;
  }
  
  .admin-modal-header {
    padding: 16px 20px;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .admin-modal-header h2 {
    font-size: 18px !important;
  }
  
  .admin-modal-header .btn {
    width: 100%;
    justify-content: center;
  }
  
  .admin-modal-body {
    padding: 20px;
  }
  
  /* Grid del modal en 1 columna */
  .admin-modal-body > div[style*="grid"] {
    grid-template-columns: 1fr !important;
    gap: 20px !important;
  }
  
  /* Secciones del modal */
  .modal-section {
    padding: 16px;
    margin-bottom: 16px;
  }
  
  .modal-section-title {
    font-size: 14px !important;
  }
  
  /* Tabla de info en modal */
  .info-table td {
    padding: 10px 0;
    font-size: 13px;
  }
  
  .info-table td:first-child {
    width: 100px;
    font-size: 12px;
  }
  
  /* Grid de fotos */
  .foto-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 10px !important;
  }
  
  .foto-item {
    aspect-ratio: 1 !important;
  }
  
  .foto-principal-badge {
    font-size: 10px;
    padding: 3px 8px;
  }
  
  /* Acciones del modal */
  .admin-modal-body > div:last-child {
    flex-direction: column !important;
    gap: 10px !important;
  }
  
  .admin-modal-body > div:last-child .btn {
    width: 100% !important;
    justify-content: center;
  }
  
  /* Modal de rechazo */
  .admin-modal-small {
    max-width: 95%;
  }
  
  #formRechazo > div:last-child {
    flex-direction: column !important;
  }
  
  #formRechazo > div:last-child .btn {
    width: 100%;
  }
}

/* Móviles pequeños */
@media (max-width: 480px) {
  .admin-container {
    padding: 12px !important;
  }
  
  main > div:first-child h1 {
    font-size: 20px !important;
  }
  
  .tab {
    padding: 8px 12px;
  }
  
  .table {
    font-size: 11px;
  }
  
  .table thead th {
    padding: 8px 6px;
    font-size: 10px;
  }
  
  .table tbody td {
    padding: 10px 6px;
  }
  
  .table tbody td:nth-child(2) .h4 {
    font-size: 13px !important;
  }
  
  .table tbody td:last-child .btn {
    padding: 6px 8px !important;
    font-size: 10px !important;
  }
  
  .foto-grid {
    grid-template-columns: 1fr !important;
  }
}

/* ============================================================================
 * VISTA DE CARDS PARA MÓVIL
 * ============================================================================ */

.publicaciones-cards-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.publicacion-card {
  background: var(--cc-bg-surface);
  border: 2px solid var(--cc-border-default);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.publicacion-card:hover {
  border-color: var(--cc-primary);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.publicacion-card-header {
  padding: 16px;
  background: var(--cc-bg-muted);
  border-bottom: 1px solid var(--cc-border-default);
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.publicacion-card-body {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: var(--cc-bg-surface);
}

.publicacion-card-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  font-size: 13px;
}

.publicacion-label {
  font-weight: 600;
  color: var(--cc-text-secondary);
  min-width: 80px;
  flex-shrink: 0;
}

.publicacion-value {
  font-weight: 500;
  color: var(--cc-text-primary);
  text-align: right;
  word-break: break-word;
}

.publicacion-card-actions {
  padding: 12px 16px;
  background: var(--cc-bg-muted);
  border-top: 1px solid var(--cc-border-default);
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

/* Mostrar/ocultar vistas según tamaño de pantalla */
@media (min-width: 769px) {
  .publicaciones-cards-view {
    display: none !important;
  }
  
  .publicaciones-table-view {
    display: block !important;
  }
}

@media (max-width: 768px) {
  .publicaciones-table-view {
    display: none !important;
  }
  
  .publicaciones-cards-view {
    display: block !important;
  }
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] .publicacion-card {
  background: #1F2937;
  border-color: #374151;
}

:root[data-theme="dark"] .publicacion-card:hover {
  border-color: var(--cc-primary, #E6332A);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
}

:root[data-theme="dark"] .publicacion-card-header {
  background: #111827;
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .publicacion-card-actions {
  background: #111827;
  border-top-color: #374151;
}

:root[data-theme="dark"] .transaction-card {
  background: #1F2937;
  border-color: #374151;
}

:root[data-theme="dark"] .transaction-card-header {
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .admin-modal-content {
  background: #1F2937;
  border-color: #374151;
}

:root[data-theme="dark"] .admin-modal-header {
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .admin-modal-body {
  background: #1F2937;
}

:root[data-theme="dark"] .modal-section {
  background: #111827;
  border-color: #374151;
}

:root[data-theme="dark"] .info-table tr {
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .foto-item {
  background: #374151;
  border-color: #4B5563;
}

:root[data-theme="dark"] .foto-item:hover {
  border-color: var(--cc-primary, #E6332A);
}

/* Elementos con color hardcodeado - Dark Mode */
:root[data-theme="dark"] [style*="color: #999"],
:root[data-theme="dark"] [style*="color:#999"] {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="text-align: center"][style*="padding"] {
  color: #D1D5DB;
}

/* Divs de "no hay fotos" - Dark Mode */
:root[data-theme="dark"] div[style*="background: #F9FAFB"] {
  background: #111827 !important;
  border-color: #374151 !important;
}
  
  /* ============================================================================
   * TABS DE ESTADO
   * ============================================================================ */
  .tabs-container {
    background: var(--cc-bg-surface, white);
    border-radius: var(--cc-radius-lg, 12px);
    border: 2px solid var(--cc-border-default, #D4D4D4);
    padding: 8px;
  }
  
  .tabs {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: var(--cc-radius-md, 8px);
    font-size: var(--cc-text-sm, 14px);
    font-weight: var(--cc-font-semibold, 600);
    color: var(--cc-text-secondary, #4A4A4A);
    text-decoration: none;
    transition: var(--cc-transition, all 0.2s ease);
    white-space: nowrap;
    border: 2px solid transparent;
  }
  
  .tab:hover {
    background: var(--cc-bg-muted, #F5F5F5);
    color: var(--cc-text-primary, #1A1A1A);
  }
  
  .tab-active {
    background: var(--cc-primary, #E6332A);
    color: var(--cc-white, white);
    border-color: var(--cc-primary, #E6332A);
  }
  
  .tab-active:hover {
    background: var(--cc-primary-dark, #B82920);
    border-color: var(--cc-primary-dark, #B82920);
  }
  
  .tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: var(--cc-gray-200, #E8E8E8);
    color: var(--cc-text-primary, #1A1A1A);
    font-size: var(--cc-text-xs, 12px);
    font-weight: var(--cc-font-bold, 700);
    border-radius: 12px;
    line-height: 1;
  }
  
  .tab-active .tab-badge {
    background: rgba(255, 255, 255, 0.3);
    color: var(--cc-white, white);
  }
  
  .tab-badge-warning {
    background: var(--cc-warning, #F59E0B);
    color: var(--cc-white, white);
  }
  
  .tab-badge-success {
    background: var(--cc-success, #10B981);
    color: var(--cc-white, white);
  }
  
  .tab-badge-danger {
    background: var(--cc-danger, #EF4444);
    color: var(--cc-white, white);
  }
  
  .tab-active .tab-badge-warning,
  .tab-active .tab-badge-success,
  .tab-active .tab-badge-danger {
    background: rgba(255, 255, 255, 0.3);
  }
  
  /* Responsive tabs */
  @media (max-width: 768px) {
    .tabs {
      overflow-x: scroll;
    }
    
    .tab span:not(.tab-badge) {
      display: none;
    }
    
    .tab {
      padding: 12px 16px;
    }
  }
  
  /* Filter toggle animation */
  .filter-toggle svg {
    transition: transform 0.3s ease;
  }
  
  .filter-toggle.active svg {
    transform: rotate(180deg);
  }

  /* ============================================================================
   * DARK MODE - ELEMENTOS ADICIONALES
   * ============================================================================ */
  
  /* Card de filtros */
  :root[data-theme="dark"] .filtros-card {
    background: var(--cc-bg-surface) !important;
    border-color: var(--cc-border-default) !important;
  }
  
  /* Labels y inputs en dark mode */
  :root[data-theme="dark"] .label {
    color: var(--cc-text-secondary) !important;
  }
  
  :root[data-theme="dark"] .input,
  :root[data-theme="dark"] .select,
  :root[data-theme="dark"] input[type="text"],
  :root[data-theme="dark"] input[type="date"],
  :root[data-theme="dark"] select {
    background: var(--cc-bg-surface) !important;
    color: var(--cc-text-primary) !important;
    border-color: var(--cc-border-default) !important;
  }
  
  :root[data-theme="dark"] input[type="date"] {
    color-scheme: dark;
  }
  
  :root[data-theme="dark"] .input::placeholder {
    color: var(--cc-text-tertiary) !important;
  }
  
  /* Botón de filtros */
  :root[data-theme="dark"] .filter-toggle {
    color: var(--cc-text-primary) !important;
  }
  
  :root[data-theme="dark"] .filter-toggle span {
    color: var(--cc-text-primary) !important;
  }
  
  /* Botones outline en dark mode */
  :root[data-theme="dark"] .btn.outline {
    background: transparent !important;
    color: var(--cc-text-primary) !important;
    border-color: var(--cc-border-default) !important;
  }
  
  :root[data-theme="dark"] .btn.outline:hover {
    background: var(--cc-bg-muted) !important;
    border-color: var(--cc-border-strong) !important;
  }
  
  /* Título del modal en dark mode */
  :root[data-theme="dark"] .admin-modal-header h2 {
    color: var(--cc-text-primary) !important;
  }
  
  /* Botón cerrar del modal */
  :root[data-theme="dark"] .admin-modal-header .btn {
    background: var(--cc-bg-muted) !important;
    color: var(--cc-text-primary) !important;
    border-color: var(--cc-border-default) !important;
  }
  
  /* Vista de cards móvil - fondo transparente en dark mode */
  :root[data-theme="dark"] .publicaciones-cards-view.card {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  
  /* Container de cards */
  :root[data-theme="dark"] .publicaciones-cards-container {
    background: transparent !important;
  }
</style>

<!-- Cargar sistema de feedback -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin-feedback.js"></script>

<script>
// Toggle filtros avanzados
function toggleFilters() {
  const filters = document.getElementById('advanced-filters');
  const toggle = document.querySelector('.filter-toggle');
  
  if (filters.style.display === 'none' || filters.style.display === '') {
    filters.style.display = 'block';
    toggle.classList.add('active');
  } else {
    filters.style.display = 'none';
    toggle.classList.remove('active');
  }
}

// Exportar datos a CSV
function exportarDatos() {
  // Obtener parámetros actuales de la URL
  const params = new URLSearchParams(window.location.search);
  const exportUrl = `<?php echo BASE_URL; ?>/admin/export/publicaciones?${params.toString()}`;
  
  Toast.info('Generando archivo CSV...');
  
  // Descargar archivo
  window.location.href = exportUrl;
  
  setTimeout(() => {
    Toast.success('Archivo descargado exitosamente');
  }, 1000);
}

// Aprobar publicación con feedback
async function aprobarPublicacion(id) {
  const confirmed = await Confirm.approve('esta publicación');
  if (!confirmed) return;
  
  const button = event.target.closest('button');
  ButtonLoader.start(button, 'Aprobando...');
  
  try {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');
    
    const response = await fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}/aprobar`, {
      method: 'POST',
      body: formData
    });
    
    if (response.ok) {
      ButtonLoader.success(button, '¡Aprobada!');
      Toast.success('Publicación aprobada exitosamente');
      
      // Animar y remover fila o card, luego recargar para actualizar contadores
      setTimeout(() => {
        const row = button.closest('tr');
        const card = button.closest('.publicacion-card');
        
        if (row) {
          Animate.fadeOut(row, () => {
            location.reload();
          });
        } else if (card) {
          Animate.fadeOut(card, () => {
            location.reload();
          });
        } else {
          location.reload();
        }
      }, 1000);
    } else {
      ButtonLoader.stop(button);
      Toast.error('Error al aprobar la publicación');
      Animate.shake(button);
    }
  } catch (error) {
    ButtonLoader.stop(button);
    Toast.error('Error de conexión con el servidor');
    Animate.shake(button);
  }
}

// Rechazar publicación con feedback
async function mostrarModalRechazo(id) {
  document.getElementById('publicacion_id_rechazo').value = id;
  document.getElementById('motivo_rechazo').value = '';
  document.getElementById('modalRechazo').style.display = 'flex';
}

async function rechazarPublicacion() {
  const id = document.getElementById('publicacion_id_rechazo').value;
  const motivo = document.getElementById('motivo_rechazo').value;
  
  if (!motivo.trim()) {
    Toast.warning('Debes proporcionar un motivo de rechazo');
    Animate.shake(document.getElementById('motivo_rechazo'));
    return;
  }
  
  const button = document.querySelector('#formRechazo button[type="submit"]');
  ButtonLoader.start(button, 'Rechazando...');
  
  try {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');
    formData.append('motivo_rechazo', motivo);
    
    const response = await fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}/rechazar`, {
      method: 'POST',
      body: formData
    });
    
    if (response.ok) {
      ButtonLoader.success(button, '¡Rechazada!');
      Toast.success('Publicación rechazada');
      cerrarModal('modalRechazo');
      
      // Animar y remover fila o card, luego recargar para actualizar contadores
      setTimeout(() => {
        const row = document.querySelector(`#pub-${id}`);
        const card = document.querySelector(`#pub-card-${id}`);
        
        if (row) {
          Animate.fadeOut(row, () => {
            location.reload();
          });
        } else if (card) {
          Animate.fadeOut(card, () => {
            location.reload();
          });
        } else {
          location.reload();
        }
      }, 500);
    } else {
      ButtonLoader.stop(button);
      Toast.error('Error al rechazar la publicación');
    }
  } catch (error) {
    ButtonLoader.stop(button);
    Toast.error('Error de conexión con el servidor');
  }
}

// Eliminar publicación con confirmación
async function eliminarPublicacion(id) {
  const confirmed = await Confirm.delete('esta publicación');
  if (!confirmed) return;
  
  const button = event.target.closest('button');
  ButtonLoader.start(button, 'Eliminando...');
  
  try {
    const response = await fetch(`<?php echo BASE_URL; ?>/admin/publicaciones/${id}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      Toast.success('Publicación eliminada permanentemente');
      
      const row = button.closest('tr');
      const card = button.closest('.publicacion-card');
      
      if (row) {
        Animate.fadeOut(row, () => {
          const tbody = row.closest('tbody');
          if (tbody && tbody.querySelectorAll('tr').length === 0) {
            location.reload();
          }
        });
      } else if (card) {
        Animate.fadeOut(card, () => {
          const container = card.closest('.publicaciones-cards-container');
          if (container && container.querySelectorAll('.publicacion-card').length === 0) {
            location.reload();
          }
        });
      }
    } else {
      ButtonLoader.stop(button);
      Toast.error('Error al eliminar la publicación');
      Animate.shake(button);
    }
  } catch (error) {
    ButtonLoader.stop(button);
    Toast.error('Error de conexión con el servidor');
    Animate.shake(button);
  }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
