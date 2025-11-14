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
  <div class="card" style="margin-bottom: 32px; padding: 16px 24px;">
    <button type="button" onclick="toggleFilters()" class="filter-toggle" style="width: 100%; display: flex; align-items: center; justify-content: space-between; background: none; border: none; cursor: pointer; padding: 8px 0;">
      <span style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
        <?php echo icon('filter', 18); ?>
        Filtros Avanzados
      </span>
      <?php echo icon('chevron-down', 18); ?>
    </button>
    
    <div id="advanced-filters" style="display: none; margin-top: 16px; padding-top: 16px; padding-bottom: 16px; border-top: 1px solid var(--cc-border-default, #D4D4D4);">
      <form method="GET" action="<?php echo BASE_URL; ?>/admin/usuarios" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        
        <!-- Mantener estado actual -->
        <?php if (!empty($filtros['estado'])): ?>
          <input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtros['estado']); ?>">
        <?php endif; ?>
        
        <!-- Rol -->
        <div style="flex: 0 0 200px;">
          <label class="label" style="display: block; margin-bottom: 8px;">Rol</label>
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

        <!-- Búsqueda -->
        <div style="flex: 1; min-width: 350px;">
          <label class="label" style="display: block; margin-bottom: 8px;">Búsqueda</label>
          <input 
            type="text" 
            name="q" 
            class="input" 
            placeholder="Nombre, email, RUT..." 
            value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>"
          >
        </div>

        <!-- Botones -->
        <div style="display: flex; gap: 8px;">
          <button type="submit" class="btn primary">
            <?php echo icon('search', 16); ?>
            Filtrar
          </button>
          <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="btn outline">
            <?php echo icon('x', 16); ?>
            Limpiar
          </a>
        </div>
      </form>
    </div>

  <!-- Tabla de usuarios (Desktop) -->
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
    
    <!-- Vista de Cards para Móvil -->
    <div class="usuarios-cards-view">
      <?php if (empty($usuarios)): ?>
        <div style="text-align: center; padding: 48px; color: #999;">
          <div class="h3" style="margin-bottom: 8px;">No hay usuarios</div>
          <p class="meta">No se encontraron usuarios con los filtros seleccionados</p>
        </div>
      <?php else: ?>
        <div class="usuarios-cards-container">
          <?php foreach ($usuarios as $usuario): ?>
            <div class="usuario-card">
              <!-- Header del card -->
              <div class="usuario-card-header">
                <div class="usuario-card-avatar">
                  <?php if ($usuario->foto_perfil): ?>
                    <img 
                      src="<?php echo BASE_URL . '/' . $usuario->foto_perfil; ?>" 
                      alt="<?php echo htmlspecialchars($usuario->nombre); ?>"
                    >
                  <?php else: ?>
                    <div class="usuario-card-avatar-placeholder">
                      <?php echo strtoupper(substr($usuario->nombre, 0, 1)); ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="usuario-card-info">
                  <div class="usuario-card-name">
                    <?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido); ?>
                    <?php if ($usuario->verificado): ?>
                      <span style="color: #34C759; font-size: 14px;">✓</span>
                    <?php endif; ?>
                  </div>
                  <div class="usuario-card-id">
                    #<?php echo $usuario->id; ?> • 
                    <span class="badge" style="
                      background: <?php 
                        echo $usuario->rol === 'admin' ? '#FF3B30' : 
                             ($usuario->rol === 'vendedor' ? '#007AFF' : '#8E8E93'); 
                      ?>; 
                      color: white; 
                      padding: 2px 8px; 
                      border-radius: 8px; 
                      font-size: 10px;
                    ">
                      <?php echo ucfirst($usuario->rol); ?>
                    </span>
                  </div>
                </div>
                <div>
                  <span class="badge" style="
                    background: <?php 
                      echo $usuario->estado === 'activo' ? '#34C759' : 
                           ($usuario->estado === 'suspendido' ? '#FF9500' : '#8E8E93'); 
                    ?>; 
                    color: white; 
                    padding: 4px 10px; 
                    border-radius: 10px; 
                    font-size: 11px;
                    font-weight: 600;
                  ">
                    <?php echo ucfirst($usuario->estado); ?>
                  </span>
                </div>
              </div>
              
              <!-- Body del card -->
              <div class="usuario-card-body">
                <div class="usuario-card-row">
                  <span class="usuario-label">Email:</span>
                  <span class="usuario-value">
                    <a href="mailto:<?php echo htmlspecialchars($usuario->email); ?>" style="color: #0066CC; text-decoration: none;">
                      <?php echo htmlspecialchars($usuario->email); ?>
                    </a>
                  </span>
                </div>
                
                <?php if ($usuario->telefono): ?>
                <div class="usuario-card-row">
                  <span class="usuario-label">Teléfono:</span>
                  <span class="usuario-value">
                    <a href="tel:<?php echo htmlspecialchars($usuario->telefono); ?>" style="color: #0066CC; text-decoration: none;">
                      <?php echo htmlspecialchars($usuario->telefono); ?>
                    </a>
                  </span>
                </div>
                <?php endif; ?>
                
                <div class="usuario-card-row">
                  <span class="usuario-label">RUT:</span>
                  <span class="usuario-value">
                    <?php echo htmlspecialchars($usuario->rut ?? 'No registrado'); ?>
                  </span>
                </div>
                
                <div class="usuario-card-row">
                  <span class="usuario-label">Publicaciones:</span>
                  <span class="usuario-value">
                    <strong><?php echo $usuario->total_publicaciones; ?></strong> total
                    <br>
                    <small style="color: #6B7280;"><?php echo $usuario->publicaciones_aprobadas; ?> aprobadas</small>
                  </span>
                </div>
                
                <div class="usuario-card-row">
                  <span class="usuario-label">Registro:</span>
                  <span class="usuario-value">
                    <?php echo date('d/m/Y', strtotime($usuario->fecha_registro)); ?>
                  </span>
                </div>
                
                <div class="usuario-card-row">
                  <span class="usuario-label">Última conexión:</span>
                  <span class="usuario-value">
                    <?php if ($usuario->ultima_conexion): ?>
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
                    <?php else: ?>
                      <span style="color: #9CA3AF;">Nunca</span>
                    <?php endif; ?>
                  </span>
                </div>
              </div>
              
              <!-- Footer con acciones -->
              <div class="usuario-card-actions">
                <a 
                  href="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>" 
                  class="btn btn-primary"
                  style="display: inline-flex; align-items: center; gap: 6px;"
                >
                  <?php echo icon('eye', 16); ?>
                  <span>Ver Detalle</span>
                </a>
                
                <?php if ($usuario->estado === 'activo'): ?>
                  <button 
                    onclick="cambiarEstadoUsuario(<?php echo $usuario->id; ?>, 'suspendido', this)"
                    class="btn btn-outline"
                    style="display: inline-flex; align-items: center; gap: 6px; color: var(--cc-warning, #F59E0B); border-color: var(--cc-warning, #F59E0B);"
                  >
                    <?php echo icon('user-x', 16); ?>
                    <span>Suspender</span>
                  </button>
                <?php elseif ($usuario->estado === 'suspendido'): ?>
                  <button 
                    onclick="cambiarEstadoUsuario(<?php echo $usuario->id; ?>, 'activo', this)"
                    class="btn"
                    style="display: inline-flex; align-items: center; gap: 6px; background: var(--cc-success, #10B981); border-color: var(--cc-success, #10B981); color: white;"
                  >
                    <?php echo icon('user-check', 16); ?>
                    <span>Activar</span>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
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
  
  /* ============================================================================
   * RESPONSIVE DESIGN
   * ============================================================================ */
  
  /* Tablets y pantallas medianas */
  @media (max-width: 968px) {
    .table {
      font-size: 13px;
    }
    
    .table thead th,
    .table tbody td {
      padding: 10px 8px;
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
      flex-direction: column !important;
      align-items: stretch !important;
    }
    
    #advanced-filters form > div {
      flex: 1 1 100% !important;
      min-width: 100% !important;
    }
    
    #advanced-filters form > div:last-child {
      flex-direction: column !important;
    }
    
    #advanced-filters form > div:last-child .btn {
      width: 100% !important;
    }
    
    /* Ocultar tabla en móvil */
    .table-responsive {
      display: none !important;
    }
    
    /* Paginación */
    .card > div:last-child {
      flex-direction: column !important;
      gap: 12px !important;
      padding: 16px !important;
    }
    
    .card > div:last-child > div:first-child {
      text-align: center;
    }
    
    .card > div:last-child > div:last-child {
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .card > div:last-child .btn {
      padding: 8px 12px !important;
      font-size: 12px !important;
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
  }
  
  /* ============================================================================
   * VISTA DE CARDS PARA MÓVIL
   * ============================================================================ */
  
  .usuarios-cards-view {
    display: none;
  }
  
  .usuarios-cards-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  
  .usuario-card {
    background: #FFFFFF;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
  }
  
  .usuario-card:hover {
    border-color: var(--cc-primary, #E6332A);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .usuario-card-header {
    padding: 16px;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .usuario-card-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  
  .usuario-card-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
  }
  
  .usuario-card-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #6B7280;
    font-size: 20px;
  }
  
  .usuario-card-info {
    flex: 1;
    min-width: 0;
  }
  
  .usuario-card-name {
    font-weight: 700;
    font-size: 15px;
    color: #111827;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  
  .usuario-card-id {
    font-size: 12px;
    color: #6B7280;
    font-weight: 600;
  }
  
  .usuario-card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .usuario-card-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
  }
  
  .usuario-label {
    font-weight: 600;
    color: #6B7280;
    min-width: 100px;
    flex-shrink: 0;
  }
  
  .usuario-value {
    font-weight: 500;
    color: #111827;
    text-align: right;
    word-break: break-word;
  }
  
  .usuario-card-actions {
    padding: 12px 16px;
    background: #F9FAFB;
    border-top: 1px solid #E5E7EB;
    display: flex;
    gap: 8px;
  }
  
  .usuario-card-actions .btn {
    flex: 1;
    justify-content: center;
    padding: 10px;
    font-size: 13px;
  }
  
  /* Mostrar/ocultar vistas según tamaño de pantalla */
  @media (min-width: 769px) {
    .usuarios-cards-view {
      display: none !important;
    }
    
    .table-responsive {
      display: block !important;
    }
  }
  
  @media (max-width: 768px) {
    .table-responsive {
      display: none !important;
    }
    
    .usuarios-cards-view {
      display: block !important;
    }
  }
  
  /* ============================================================================
   * DARK MODE
   * ============================================================================ */
  
  :root[data-theme="dark"] .usuario-card {
    background: #1F2937;
    border-color: #374151;
  }
  
  :root[data-theme="dark"] .usuario-card:hover {
    border-color: var(--cc-primary, #E6332A);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
  }
  
  :root[data-theme="dark"] .usuario-card-header {
    background: #374151;
    border-bottom-color: #4B5563;
  }
  
  :root[data-theme="dark"] .usuario-card-avatar-placeholder {
    background: #4B5563;
    color: #D1D5DB;
  }
  
  :root[data-theme="dark"] .usuario-card-actions {
    background: #374151;
    border-top-color: #4B5563;
  }
  
  :root[data-theme="dark"] .usuario-card-name {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] .usuario-card-id {
    color: #9CA3AF !important;
  }
  
  /* Elementos con color hardcodeado - Dark Mode */
  :root[data-theme="dark"] [style*="color: #999"],
  :root[data-theme="dark"] [style*="color:#999"] {
    color: #9CA3AF !important;
  }
  
  :root[data-theme="dark"] div[style*="text-align: center"][style*="padding"] {
    color: #D1D5DB;
  }
  
  /* Tabs en Dark Mode */
  :root[data-theme="dark"] .tabs-container {
    background: #1F2937;
    border-color: #374151;
  }
  
  :root[data-theme="dark"] .tab {
    color: #D1D5DB;
  }
  
  :root[data-theme="dark"] .tab:hover {
    background: #374151;
    color: #F3F4F6;
  }
  
  :root[data-theme="dark"] .tab-active {
    background: var(--cc-primary, #E6332A);
    color: var(--cc-white, white);
    border-color: var(--cc-primary, #E6332A);
  }
  
  :root[data-theme="dark"] .tab-badge {
    background: #374151;
    color: #D1D5DB;
  }
  
  :root[data-theme="dark"] .tab-active .tab-badge {
    background: rgba(255, 255, 255, 0.3);
    color: var(--cc-white, white);
  }
  
  /* Card en Dark Mode */
  :root[data-theme="dark"] .card {
    background: #1F2937 !important;
    border-color: #374151 !important;
  }
  
  /* Cards con estilos inline - Dark Mode */
  :root[data-theme="dark"] div.card[style],
  :root[data-theme="dark"] .card[style] {
    background: #1F2937 !important;
    border-color: #374151 !important;
  }
  
  /* Filter Toggle en Dark Mode */
  :root[data-theme="dark"] .filter-toggle {
    color: #F3F4F6;
  }
  
  :root[data-theme="dark"] .filter-toggle span {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] #advanced-filters {
    border-top-color: #374151;
  }
  
  /* Botones outline en Dark Mode */
  :root[data-theme="dark"] .btn.outline {
    background: #374151 !important;
    color: #F3F4F6 !important;
    border-color: #4B5563 !important;
  }
  
  :root[data-theme="dark"] .btn.outline:hover {
    background: #4B5563 !important;
    border-color: #6B7280 !important;
  }
  
  /* Inputs y selects en filtros - Dark Mode */
  :root[data-theme="dark"] .input,
  :root[data-theme="dark"] select.input {
    background: #374151 !important;
    border-color: #4B5563 !important;
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] .input::placeholder {
    color: #9CA3AF !important;
  }
  
  :root[data-theme="dark"] .label {
    color: #D1D5DB !important;
  }
  
  /* Tabla en Dark Mode */
  :root[data-theme="dark"] .table thead {
    background: #111827;
  }
  
  :root[data-theme="dark"] .table th {
    color: #F3F4F6;
    border-bottom-color: #374151;
  }
  
  :root[data-theme="dark"] .table td {
    color: #D1D5DB;
    border-bottom-color: #374151;
  }
  
  :root[data-theme="dark"] .table tbody tr:hover {
    background: #374151;
  }
  
  /* Textos en Dark Mode */
  :root[data-theme="dark"] .usuario-card-name,
  :root[data-theme="dark"] .usuario-value {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] .usuario-card-id,
  :root[data-theme="dark"] .usuario-label {
    color: #9CA3AF !important;
  }
  
  /* Avatar placeholder en tabla */
  :root[data-theme="dark"] div[style*="background: #E5E7EB"] {
    background: #374151 !important;
    color: #9CA3AF !important;
  }
  
  /* Enlaces en Dark Mode */
  :root[data-theme="dark"] a[href^="mailto"],
  :root[data-theme="dark"] a[href^="tel"] {
    color: #60A5FA !important;
  }
  
  /* Paginación en Dark Mode */
  :root[data-theme="dark"] .card > div:last-child {
    border-top-color: #374151;
  }
  
  /* Modal en Dark Mode */
  :root[data-theme="dark"] #modalCambiarEstado .card {
    background: #1F2937;
    border-color: #374151;
  }
  
  :root[data-theme="dark"] #modalCambiarEstado .h3 {
    color: #F3F4F6;
  }
  
  /* Badges con estilos inline - Dark Mode */
  :root[data-theme="dark"] .badge[style*="background: #FF3B30"],
  :root[data-theme="dark"] .badge[style*="background: #007AFF"],
  :root[data-theme="dark"] .badge[style*="background: #8E8E93"],
  :root[data-theme="dark"] .badge[style*="background: #34C759"],
  :root[data-theme="dark"] .badge[style*="background: #FF9500"] {
    /* Mantener colores de badges ya que son distintivos */
  }
  
  /* Textos con color inline - Dark Mode */
  :root[data-theme="dark"] div[style*="color: #111827"],
  :root[data-theme="dark"] strong[style*="color: #111827"] {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] div[style*="color: #6B7280"],
  :root[data-theme="dark"] small[style*="color: #6B7280"] {
    color: #9CA3AF !important;
  }
  
  :root[data-theme="dark"] span[style*="color: #9CA3AF"] {
    color: #9CA3AF !important;
  }
  
  /* Botones en header - Dark Mode */
  :root[data-theme="dark"] main > div:first-child .btn {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] main > div:first-child .btn.primary {
    background: var(--cc-primary) !important;
    color: #FFFFFF !important;
  }
  
  /* Tabs números - Dark Mode */
  :root[data-theme="dark"] .tab span:not(.tab-badge) {
    color: inherit !important;
  }
  
  /* Usuario card body - Dark Mode */
  :root[data-theme="dark"] .usuario-card-body {
    background: #1F2937;
  }
  
  /* Mejorar contraste de textos en cards */
  :root[data-theme="dark"] .usuario-value strong {
    color: #F3F4F6 !important;
  }
  
  :root[data-theme="dark"] .usuario-value small {
    color: #9CA3AF !important;
  }
  
  /* Botones de acción en cards - Dark Mode */
  :root[data-theme="dark"] .usuario-card-actions .btn-primary {
    background: var(--cc-primary) !important;
    color: #FFFFFF !important;
  }
  
  :root[data-theme="dark"] .usuario-card-actions .btn-outline {
    background: transparent !important;
    color: #F59E0B !important;
    border-color: #F59E0B !important;
  }
  
  /* Select y opciones - Dark Mode */
  :root[data-theme="dark"] select.input option {
    background: #374151;
    color: #F3F4F6;
  }
  
  /* Paginación meta text - Dark Mode */
  :root[data-theme="dark"] .card > div:last-child .meta {
    color: #9CA3AF !important;
  }
  
  /* Botones de paginación - Dark Mode */
  :root[data-theme="dark"] .card > div:last-child .btn.outline {
    background: #374151 !important;
    color: #F3F4F6 !important;
    border-color: #4B5563 !important;
  }
  
  :root[data-theme="dark"] .card > div:last-child .btn.primary {
    background: var(--cc-primary) !important;
    color: #FFFFFF !important;
  }
  
  /* Filtros avanzados - Dark Mode */
  :root[data-theme="dark"] #advanced-filters form {
    background: transparent !important;
  }
  
  :root[data-theme="dark"] #advanced-filters form > div {
    background: transparent !important;
  }
  
  /* Labels en filtros - Dark Mode */
  :root[data-theme="dark"] #advanced-filters .label,
  :root[data-theme="dark"] .card .label {
    color: #D1D5DB !important;
  }
  
  /* Texto "Búsqueda" - Dark Mode */
  :root[data-theme="dark"] .card form label,
  :root[data-theme="dark"] .card label {
    color: #D1D5DB !important;
  }
  
  /* Divs con estilos inline - Dark Mode */
  :root[data-theme="dark"] .card > div[style*="padding"],
  :root[data-theme="dark"] .card > div[style*="margin"] {
    background: transparent !important;
  }
  
  /* Usuario cards container - Dark Mode */
  :root[data-theme="dark"] .usuarios-cards-container {
    background: transparent !important;
  }
  
  /* Asegurar que todos los divs dentro de cards sean oscuros */
  :root[data-theme="dark"] .card > div,
  :root[data-theme="dark"] .card > form,
  :root[data-theme="dark"] .card > form > div {
    background: transparent !important;
  }
  
  /* Table responsive container - Dark Mode */
  :root[data-theme="dark"] .table-responsive {
    background: transparent !important;
  }
  
  /* Sobrescribir cualquier background white inline */
  :root[data-theme="dark"] [style*="background: white"],
  :root[data-theme="dark"] [style*="background: #FFFFFF"],
  :root[data-theme="dark"] [style*="background: #FFF"],
  :root[data-theme="dark"] [style*="background:#FFFFFF"],
  :root[data-theme="dark"] [style*="background:#FFF"] {
    background: #1F2937 !important;
  }
  
  /* Sobrescribir background-color white inline */
  :root[data-theme="dark"] [style*="background-color: white"],
  :root[data-theme="dark"] [style*="background-color: #FFFFFF"],
  :root[data-theme="dark"] [style*="background-color: #FFF"],
  :root[data-theme="dark"] [style*="background-color:#FFFFFF"],
  :root[data-theme="dark"] [style*="background-color:#FFF"] {
    background-color: #1F2937 !important;
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

<!-- Script de tema (modo claro/oscuro) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    
    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
    
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

</main>
</body>
</html>
