<?php
/**
 * Vista: Gestión de Usuarios - Panel Admin
 * Listado de todos los usuarios del sistema con filtros y acciones
 * 
 * @author ChileChocados
 * @date 2025-10-30
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
        <?php echo icon('users', 32); ?>
        Gestión de Usuarios
      </h1>
      <p class="meta">Administra todos los usuarios del sistema</p>
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
      <a href="<?php echo BASE_URL; ?>/admin/usuarios" 
         class="tab <?php echo empty($filtros['estado']) ? 'tab-active' : ''; ?>">
        <?php echo icon('users', 18); ?>
        <span>Todos</span>
        <span class="tab-badge"><?php echo number_format($stats->total ?? 0); ?></span>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/usuarios?estado=activo" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'activo' ? 'tab-active' : ''; ?>">
        <?php echo icon('user-check', 18); ?>
        <span>Activos</span>
        <span class="tab-badge tab-badge-success"><?php echo number_format($stats->activos ?? 0); ?></span>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/usuarios?estado=suspendido" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'suspendido' ? 'tab-active' : ''; ?>">
        <?php echo icon('user-x', 18); ?>
        <span>Suspendidos</span>
        <?php if (($stats->suspendidos ?? 0) > 0): ?>
          <span class="tab-badge tab-badge-warning"><?php echo number_format($stats->suspendidos); ?></span>
        <?php endif; ?>
      </a>
      
      <a href="<?php echo BASE_URL; ?>/admin/usuarios?estado=eliminado" 
         class="tab <?php echo ($filtros['estado'] ?? '') === 'eliminado' ? 'tab-active' : ''; ?>">
        <?php echo icon('trash-2', 18); ?>
        <span>Eliminados</span>
        <span class="tab-badge tab-badge-danger"><?php echo number_format($stats->eliminados ?? 0); ?></span>
      </a>
    </div>
  </div>

  <!-- Filtros Adicionales (Colapsables) -->
  <div class="card" style="margin-bottom: 24px; padding: 16px 24px;">
    <button type="button" onclick="toggleFilters()" class="filter-toggle" style="width: 100%; display: flex; align-items: center; justify-content: space-between; background: none; border: none; cursor: pointer; padding: 8px 0;">
      <span style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
        <?php echo icon('filter', 18); ?>
        Filtros Avanzados
      </span>
      <?php echo icon('chevron-down', 18); ?>
    </button>
    
    <div id="advanced-filters" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--cc-border-default, #D4D4D4);">
      <form method="GET" action="<?php echo BASE_URL; ?>/admin/usuarios" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        
        <!-- Mantener estado actual -->
        <?php if (!empty($filtros['estado'])): ?>
          <input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtros['estado']); ?>">
        <?php endif; ?>
        
        <!-- Rol -->
        <div>
          <label class="label">Rol</label>
          <select name="rol" class="input">
            <option value="">Todos</option>
            <option value="admin" <?php echo ($filtros['rol'] ?? '') === 'admin' ? 'selected' : ''; ?>>
              Administradores
            </option>
            <option value="vendedor" <?php echo ($filtros['rol'] ?? '') === 'vendedor' ? 'selected' : ''; ?>>
              Vendedores
            </option>
            <option value="comprador" <?php echo ($filtros['rol'] ?? '') === 'comprador' ? 'selected' : ''; ?>>
              Compradores
            </option>
          </select>
        </div>
        </select>
      </div>

      <!-- Búsqueda -->
      <div>
        <label class="label">Búsqueda</label>
        <input 
          type="text" 
          name="q" 
          class="input" 
          placeholder="Nombre, email, RUT..." 
          value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>"
        >
      </div>

      <!-- Botón -->
      <div style="display: flex; align-items: flex-end;">
        <button type="submit" class="btn primary" style="width: 100%;">
          Filtrar
        </button>
      </div>
    </form>
  </div>

  <!-- Tabla de usuarios -->
  <div class="card">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Publicaciones</th>
            <th>Registro</th>
            <th>Última Conexión</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($usuarios)): ?>
            <tr>
              <td colspan="9" style="text-align: center; padding: 48px;">
                <div class="meta">No se encontraron usuarios</div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($usuarios as $usuario): ?>
              <tr>
                <!-- ID -->
                <td>
                  <strong>#<?php echo $usuario->id; ?></strong>
                </td>

                <!-- Usuario -->
                <td>
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <?php if ($usuario->foto_perfil): ?>
                      <img 
                        src="<?php echo BASE_URL . '/' . $usuario->foto_perfil; ?>" 
                        alt="<?php echo htmlspecialchars($usuario->nombre); ?>"
                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                      >
                    <?php else: ?>
                      <div style="width: 40px; height: 40px; border-radius: 50%; background: #E5E7EB; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6B7280;">
                        <?php echo strtoupper(substr($usuario->nombre, 0, 1)); ?>
                      </div>
                    <?php endif; ?>
                    <div>
                      <strong>
                        <?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido); ?>
                      </strong>
                      <?php if ($usuario->verificado): ?>
                        <span style="color: #34C759; font-size: 12px;">✓ Verificado</span>
                      <?php endif; ?>
                      <div class="meta" style="font-size: 12px;">
                        RUT: <?php echo htmlspecialchars($usuario->rut ?? 'No registrado'); ?>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td>
                  <a href="mailto:<?php echo htmlspecialchars($usuario->email); ?>">
                    <?php echo htmlspecialchars($usuario->email); ?>
                  </a>
                  <?php if ($usuario->telefono): ?>
                    <div class="meta" style="font-size: 12px;">
                      📞 <?php echo htmlspecialchars($usuario->telefono); ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Rol -->
                <td>
                  <span class="badge" style="
                    background: <?php 
                      echo $usuario->rol === 'admin' ? '#FF3B30' : 
                           ($usuario->rol === 'vendedor' ? '#007AFF' : '#8E8E93'); 
                    ?>; 
                    color: white; 
                    padding: 4px 12px; 
                    border-radius: 12px; 
                    font-size: 12px;
                    font-weight: 600;
                  ">
                    <?php echo ucfirst($usuario->rol); ?>
                  </span>
                </td>

                <!-- Estado -->
                <td>
                  <span class="badge" style="
                    background: <?php 
                      echo $usuario->estado === 'activo' ? '#34C759' : 
                           ($usuario->estado === 'suspendido' ? '#FF9500' : '#8E8E93'); 
                    ?>; 
                    color: white; 
                    padding: 4px 12px; 
                    border-radius: 12px; 
                    font-size: 12px;
                    font-weight: 600;
                  ">
                    <?php echo ucfirst($usuario->estado); ?>
                  </span>
                </td>

                <!-- Publicaciones -->
                <td>
                  <strong><?php echo $usuario->total_publicaciones; ?></strong> total
                  <div class="meta" style="font-size: 11px;">
                    <?php echo $usuario->publicaciones_aprobadas; ?> aprobadas
                  </div>
                </td>

                <!-- Fecha de registro -->
                <td>
                  <div style="font-size: 13px;">
                    <?php echo date('d/m/Y', strtotime($usuario->fecha_registro)); ?>
                  </div>
                  <div class="meta" style="font-size: 11px;">
                    <?php echo date('H:i', strtotime($usuario->fecha_registro)); ?>
                  </div>
                </td>

                <!-- Última conexión -->
                <td>
                  <?php if ($usuario->ultima_conexion): ?>
                    <div style="font-size: 13px;">
                      <?php 
                        $dias = floor((time() - strtotime($usuario->ultima_conexion)) / 86400);
                        if ($dias === 0) {
                          echo 'Hoy';
                        } elseif ($dias === 1) {
                          echo 'Ayer';
                        } else {
                          echo "Hace {$dias} días";
                        }
                      ?>
                    </div>
                    <div class="meta" style="font-size: 11px;">
                      <?php echo date('d/m/Y', strtotime($usuario->ultima_conexion)); ?>
                    </div>
                  <?php else: ?>
                    <span class="meta">Nunca</span>
                  <?php endif; ?>
                </td>

                <!-- Acciones -->
                <td>
                  <div style="display: flex; gap: 6px;">
                    <a 
                      href="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>" 
                      class="btn btn-sm btn-primary"
                      style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px;"
                      title="Ver detalle"
                    >
                      <?php echo icon('eye', 14); ?>
                      <span>Ver</span>
                    </a>
                    <?php if ($usuario->estado === 'activo'): ?>
                      <button 
                        onclick="cambiarEstadoUsuario(<?php echo $usuario->id; ?>, 'suspendido', this)"
                        class="btn btn-sm btn-outline"
                        style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; color: var(--cc-warning, #F59E0B); border-color: var(--cc-warning, #F59E0B);"
                        title="Suspender usuario"
                      >
                        <?php echo icon('user-x', 14); ?>
                      </button>
                    <?php elseif ($usuario->estado === 'suspendido'): ?>
                      <button 
                        onclick="cambiarEstadoUsuario(<?php echo $usuario->id; ?>, 'activo', this)"
                        class="btn btn-sm"
                        style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; background: var(--cc-success, #10B981); border-color: var(--cc-success, #10B981); color: white;"
                        title="Activar usuario"
                      >
                        <?php echo icon('user-check', 14); ?>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <?php if ($pagination['total_pages'] > 1): ?>
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid #E5E7EB;">
        <div class="meta">
          Mostrando 
          <?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
          - 
          <?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']); ?>
          de 
          <?php echo $pagination['total_items']; ?> usuarios
        </div>

        <div style="display: flex; gap: 8px;">
          <?php if ($pagination['current_page'] > 1): ?>
            <a 
              href="?page=<?php echo $pagination['current_page'] - 1; ?>&rol=<?php echo $filtros['rol']; ?>&estado=<?php echo $filtros['estado']; ?>&q=<?php echo urlencode($filtros['busqueda']); ?>" 
              class="btn btn-sm outline"
            >
              ← Anterior
            </a>
          <?php endif; ?>

          <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
            <a 
              href="?page=<?php echo $i; ?>&rol=<?php echo $filtros['rol']; ?>&estado=<?php echo $filtros['estado']; ?>&q=<?php echo urlencode($filtros['busqueda']); ?>" 
              class="btn btn-sm <?php echo $i === $pagination['current_page'] ? 'primary' : 'outline'; ?>"
            >
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <a 
              href="?page=<?php echo $pagination['current_page'] + 1; ?>&rol=<?php echo $filtros['rol']; ?>&estado=<?php echo $filtros['estado']; ?>&q=<?php echo urlencode($filtros['busqueda']); ?>" 
              class="btn btn-sm outline"
            >
              Siguiente →
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

</main>

<!-- Modal de cambio de estado -->
<div id="modalCambiarEstado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div class="card" style="max-width: 500px; width: 90%; margin: 24px;">
    <h3 class="h3" style="margin-bottom: 16px;">Cambiar Estado del Usuario</h3>
    
    <form id="formCambiarEstado" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <input type="hidden" name="estado" id="nuevo_estado">
      <input type="hidden" id="usuario_id_cambio">

      <div style="margin-bottom: 16px;">
        <label class="label">Motivo (opcional)</label>
        <textarea 
          name="motivo" 
          id="motivo_cambio"
          class="input" 
          rows="4" 
          placeholder="Explica el motivo del cambio de estado..."
        ></textarea>
      </div>

      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModal()" class="btn outline">
          Cancelar
        </button>
        <button type="submit" class="btn primary">
          Confirmar Cambio
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Estilos para tabs -->
<style>
  /* Tabs de Estado */
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
  }
  
  .tab-active .tab-badge {
    background: rgba(255, 255, 255, 0.3);
    color: var(--cc-white, white);
  }
  
  .tab-badge-success {
    background: var(--cc-success, #10B981);
    color: var(--cc-white, white);
  }
  
  .tab-badge-warning {
    background: var(--cc-warning, #F59E0B);
    color: var(--cc-white, white);
  }
  
  .tab-badge-danger {
    background: var(--cc-danger, #EF4444);
    color: var(--cc-white, white);
  }
  
  .filter-toggle svg {
    transition: transform 0.3s ease;
  }
  
  .filter-toggle.active svg {
    transform: rotate(180deg);
  }
</style>

<!-- Cargar sistema de feedback -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin-feedback.js"></script>

<?php layout('footer'); ?>

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
  const params = new URLSearchParams(window.location.search);
  const exportUrl = `<?php echo BASE_URL; ?>/admin/export/usuarios?${params.toString()}`;
  
  Toast.info('Generando archivo CSV...');
  window.location.href = exportUrl;
  
  setTimeout(() => {
    Toast.success('Archivo descargado exitosamente');
  }, 1000);
}

// Cambiar estado de usuario con feedback
async function cambiarEstadoUsuario(id, nuevoEstado, button) {
  const accion = nuevoEstado === 'suspendido' ? 'suspender' : 'activar';
  const mensaje = nuevoEstado === 'suspendido' 
    ? '¿Deseas suspender este usuario? No podrá acceder al sistema.'
    : '¿Deseas activar este usuario? Podrá acceder nuevamente al sistema.';
  
  const confirmed = await Confirm.show({
    title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} Usuario?`,
    message: mensaje,
    confirmText: `Sí, ${accion}`,
    type: nuevoEstado === 'suspendido' ? 'warning' : 'info'
  });
  
  if (!confirmed) return;
  
  ButtonLoader.start(button, `${accion.charAt(0).toUpperCase() + accion.slice(1)}ando...`);
  
  try {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');
    formData.append('estado', nuevoEstado);
    formData.append('motivo', `Estado cambiado a ${nuevoEstado} por administrador`);
    
    const response = await fetch(`<?php echo BASE_URL; ?>/admin/usuarios/${id}/cambiar-estado`, {
      method: 'POST',
      body: formData
    });
    
    if (response.ok) {
      ButtonLoader.success(button, '¡Listo!');
      Toast.success(`Usuario ${nuevoEstado === 'suspendido' ? 'suspendido' : 'activado'} exitosamente`);
      
      // Recargar después de 1 segundo
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      ButtonLoader.stop(button);
      Toast.error('Error al cambiar el estado del usuario');
      Animate.shake(button);
    }
  } catch (error) {
    ButtonLoader.stop(button);
    Toast.error('Error de conexión con el servidor');
    Animate.shake(button);
  }
}

// Cerrar modal (mantener compatibilidad)
function cerrarModal() {
  const modal = document.getElementById('modalCambiarEstado');
  if (modal) {
    modal.style.display = 'none';
    const motivo = document.getElementById('motivo_cambio');
    if (motivo) motivo.value = '';
  }
}
</script>
