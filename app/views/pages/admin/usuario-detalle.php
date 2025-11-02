<?php  // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterTagBlock, PSR12.Files.FileHeader.SpacingAfterTagBlock, PSR12.Files.FileHeader.SpacingAfterTagBlock

/**
 * Vista: Detalle y Edición de Usuario - Panel Admin
 * Información completa del usuario con opciones de edición
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
      <h1 class="h1">
        <?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido); ?>
      </h1>
      <p class="meta">
        Usuario #<?php echo $usuario->id; ?> • 
        Registrado el <?php echo date('d/m/Y', strtotime($usuario->fecha_registro)); ?>
      </p>
    </div>
    <div style="display: flex; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="btn outline">
        ← Volver al Listado
      </a>
      <a 
        href="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>/historial" 
        class="btn"
      >
        📋 Ver Historial
      </a>
    </div>
  </div>

  <!-- Estadísticas del usuario -->
  <div class="grid cols-4" style="gap: 16px; margin-bottom: 24px;">
    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #007AFF; margin-bottom: 4px;">
        <?php echo $estadisticas->total_publicaciones; ?>
      </div>
      <div class="meta">Publicaciones</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #34C759; margin-bottom: 4px;">
        <?php echo $estadisticas->publicaciones_aprobadas; ?>
      </div>
      <div class="meta">Aprobadas</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #FF9500; margin-bottom: 4px;">
        <?php echo $estadisticas->total_mensajes; ?>
      </div>
      <div class="meta">Mensajes</div>
    </div>

    <div class="card" style="text-align: center; padding: 20px;">
      <div class="h2" style="color: #8E8E93; margin-bottom: 4px;">
        <?php echo $estadisticas->total_favoritos; ?>
      </div>
      <div class="meta">Favoritos</div>
    </div>
  </div>

  <div class="grid cols-2" style="gap: 24px;">
    
    <!-- COLUMNA IZQUIERDA: Información y Edición -->
    <div>
      
      <!-- Información del Usuario -->
      <div class="card" style="margin-bottom: 24px;">
        <h3 class="h3" style="margin-bottom: 16px;">Información Personal</h3>

        <form method="POST" action="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>/actualizar">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

          <div style="margin-bottom: 16px;">
            <label class="label">Nombre *</label>
            <input 
              type="text" 
              name="nombre" 
              class="input" 
              value="<?php echo htmlspecialchars($usuario->nombre); ?>"
              required
            >
          </div>

          <div style="margin-bottom: 16px;">
            <label class="label">Apellido *</label>
            <input 
              type="text" 
              name="apellido" 
              class="input" 
              value="<?php echo htmlspecialchars($usuario->apellido); ?>"
              required
            >
          </div>

          <div style="margin-bottom: 16px;">
            <label class="label">Email *</label>
            <input 
              type="email" 
              name="email" 
              class="input" 
              value="<?php echo htmlspecialchars($usuario->email); ?>"
              required
            >
          </div>

          <div style="margin-bottom: 16px;">
            <label class="label">Teléfono</label>
            <input 
              type="text" 
              name="telefono" 
              class="input" 
              value="<?php echo htmlspecialchars($usuario->telefono ?? ''); ?>"
              placeholder="+56 9 1234 5678"
            >
          </div>

          <div style="margin-bottom: 16px;">
            <label class="label">RUT</label>
            <input 
              type="text" 
              name="rut" 
              class="input" 
              value="<?php echo htmlspecialchars($usuario->rut ?? ''); ?>"
              placeholder="12345678-9"
            >
          </div>

          <div style="margin-bottom: 16px;">
            <label class="label">Rol *</label>
            <select name="rol" class="input" required>
              <option value="comprador" <?php echo $usuario->rol === 'comprador' ? 'selected' : ''; ?>>
                Comprador
              </option>
              <option value="vendedor" <?php echo $usuario->rol === 'vendedor' ? 'selected' : ''; ?>>
                Vendedor
              </option>
              <option value="admin" <?php echo $usuario->rol === 'admin' ? 'selected' : ''; ?>>
                Administrador
              </option>
            </select>
          </div>

          <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn primary">
              💾 Guardar Cambios
            </button>
            <button type="reset" class="btn outline">
              🔄 Restablecer
            </button>
          </div>
        </form>
      </div>

      <!-- Estado y Acciones -->
      <div class="card">
        <h3 class="h3" style="margin-bottom: 16px;">Estado y Acciones</h3>

        <div style="margin-bottom: 16px;">
          <label class="label">Estado Actual</label>
          <span class="badge" style="
            background: <?php
echo $usuario->estado === 'activo'
  ? '#34C759'
  : ($usuario->estado === 'suspendido' ? '#FF9500' : '#8E8E93');
?>; 
            color: white; 
            padding: 8px 16px; 
            border-radius: 12px; 
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
          ">
            <?php echo ucfirst($usuario->estado); ?>
          </span>
        </div>

        <div style="margin-bottom: 16px;">
          <label class="label">Verificado</label>
          <span style="font-size: 24px;">
            <?php echo $usuario->verificado ? '✅ Sí' : '❌ No'; ?>
          </span>
        </div>

        <div style="margin-bottom: 16px;">
          <label class="label">Última Conexión</label>
          <div>
            <?php if ($usuario->ultima_conexion): ?>
              <?php echo date('d/m/Y H:i', strtotime($usuario->ultima_conexion)); ?>
            <?php else: ?>
              <span class="meta">Nunca se ha conectado</span>
            <?php endif; ?>
          </div>
        </div>

        <hr style="margin: 24px 0; border: none; border-top: 1px solid #E5E7EB;">

        <div style="display: flex; flex-direction: column; gap: 12px;">
          <?php if ($usuario->estado === 'activo'): ?>
            <button 
              onclick="cambiarEstado(<?php echo $usuario->id; ?>, 'suspendido')"
              class="btn"
              style="background: #FF9500; color: white;"
            >
              ⏸️ Suspender Usuario
            </button>
          <?php elseif ($usuario->estado === 'suspendido'): ?>
            <button 
              onclick="cambiarEstado(<?php echo $usuario->id; ?>, 'activo')"
              class="btn"
              style="background: #34C759; color: white;"
            >
              ▶️ Activar Usuario
            </button>
          <?php endif; ?>

          <?php if ($usuario->id != $_SESSION['user_id']): ?>
            <button 
              onclick="eliminarUsuario(<?php echo $usuario->id; ?>)"
              class="btn"
              style="background: #FF3B30; color: white;"
            >
              🗑️ Eliminar Usuario
            </button>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- COLUMNA DERECHA: Publicaciones e Historial -->
    <div>
      
      <!-- Publicaciones del Usuario -->
      <div class="card" style="margin-bottom: 24px;">
        <h3 class="h3" style="margin-bottom: 16px;">Publicaciones Recientes</h3>

        <?php if (empty($publicaciones)): ?>
          <div class="meta" style="text-align: center; padding: 24px;">
            Este usuario no tiene publicaciones
          </div>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($publicaciones as $pub): ?>
              <div style="display: flex; gap: 12px; padding: 12px; border: 1px solid #E5E7EB; border-radius: 8px;">
                <div style="flex: 1;">
                  <div>
                    <strong><?php echo htmlspecialchars($pub->titulo); ?></strong>
                  </div>
                  <div class="meta" style="font-size: 12px;">
                    <?php echo htmlspecialchars($pub->categoria_nombre); ?> • 
                    <?php echo htmlspecialchars($pub->region_nombre); ?>
                  </div>
                  <div style="margin-top: 4px;">
                    <span class="badge" style="
                      background: <?php
    echo $pub->estado === 'aprobada'
      ? '#34C759'
      : ($pub->estado === 'pendiente' ? '#FF9500' : '#FF3B30');
?>; 
                      color: white; 
                      padding: 2px 8px; 
                      border-radius: 8px; 
                      font-size: 11px;
                    ">
                      <?php echo ucfirst($pub->estado); ?>
                    </span>
                  </div>
                </div>
                <div>
                  <a 
                    href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub->id; ?>" 
                    class="btn btn-sm primary"
                    title="Ver publicación"
                  >
                    Ver
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if ($estadisticas->total_publicaciones > count($publicaciones)): ?>
            <div style="text-align: center; margin-top: 16px;">
              <a 
                href="<?php echo BASE_URL; ?>/admin/publicaciones?usuario=<?php echo $usuario->id; ?>" 
                class="btn outline"
              >
                Ver todas las publicaciones (<?php echo $estadisticas->total_publicaciones; ?>)
              </a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Historial de Auditoría -->
      <div class="card">
        <h3 class="h3" style="margin-bottom: 16px;">Historial de Cambios</h3>

        <?php if (empty($historial)): ?>
          <div class="meta" style="text-align: center; padding: 24px;">
            No hay historial de cambios
          </div>
        <?php else: ?>
          <div style="max-height: 400px; overflow-y: auto;">
            <?php foreach (array_slice($historial, 0, 10) as $item): ?>
              <div style="padding: 12px; border-bottom: 1px solid #E5E7EB;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                  <div>
                    <strong style="text-transform: capitalize;">
                      <?php echo htmlspecialchars($item->accion); ?>
                    </strong>
                    en 
                    <code style="background: #F3F4F6; padding: 2px 6px; border-radius: 4px;">
                      <?php echo htmlspecialchars($item->tabla); ?>
                    </code>
                  </div>
                  <div class="meta" style="font-size: 11px;">
                    <?php echo date('d/m/Y H:i', strtotime($item->fecha)); ?>
                  </div>
                </div>
                <?php if ($item->admin_nombre): ?>
                  <div class="meta" style="font-size: 11px; margin-top: 4px;">
                    Por: <?php echo htmlspecialchars($item->admin_nombre . ' ' . $item->admin_apellido); ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (count($historial) > 10): ?>
            <div style="text-align: center; margin-top: 16px;">
              <a 
                href="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>/historial" 
                class="btn outline"
              >
                Ver historial completo
              </a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>

</main>

<!-- Modal de cambio de estado -->
<div id="modalCambiarEstado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div class="card" style="max-width: 500px; width: 90%; margin: 24px;">
    <h3 class="h3" style="margin-bottom: 16px;">Cambiar Estado del Usuario</h3>
    
    <form id="formCambiarEstado" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <input type="hidden" name="estado" id="nuevo_estado">

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

<!-- Modal de eliminación -->
<div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div class="card" style="max-width: 500px; width: 90%; margin: 24px;">
    <h3 class="h3" style="margin-bottom: 16px; color: #FF3B30;">⚠️ Eliminar Usuario</h3>
    
    <p style="margin-bottom: 16px;">
      ¿Estás seguro de que deseas eliminar este usuario? 
      <strong>Esta acción no se puede deshacer.</strong>
    </p>

    <p class="meta" style="margin-bottom: 16px;">
      El usuario será marcado como "eliminado" pero sus datos se conservarán en la base de datos.
    </p>
    
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario->id; ?>/eliminar">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button type="button" onclick="cerrarModalEliminar()" class="btn outline">
          Cancelar
        </button>
        <button type="submit" class="btn" style="background: #FF3B30; color: white;">
          Sí, Eliminar Usuario
        </button>
      </div>
    </form>
  </div>
</div>

<?php layout('footer'); ?>

<script>
// Cambiar estado de usuario
function cambiarEstado(id, nuevoEstado) {
  document.getElementById('nuevo_estado').value = nuevoEstado;
  
  const titulo = nuevoEstado === 'suspendido' ? 'Suspender Usuario' : 'Activar Usuario';
  document.querySelector('#modalCambiarEstado h3').textContent = titulo;
  
  document.getElementById('formCambiarEstado').action = 
    `<?php echo BASE_URL; ?>/admin/usuarios/${id}/cambiar-estado`;
  
  document.getElementById('modalCambiarEstado').style.display = 'flex';
}

// Eliminar usuario
function eliminarUsuario(id) {
  document.getElementById('modalEliminar').style.display = 'flex';
}

// Cerrar modales
function cerrarModal() {
  document.getElementById('modalCambiarEstado').style.display = 'none';
  document.getElementById('motivo_cambio').value = '';
}

function cerrarModalEliminar() {
  document.getElementById('modalEliminar').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalCambiarEstado').addEventListener('click', function(e) {
  if (e.target === this) {
    cerrarModal();
  }
});

document.getElementById('modalEliminar').addEventListener('click', function(e) {
  if (e.target === this) {
    cerrarModalEliminar();
  }
});
</script>

<style>
/* ============================================================================
 * RESPONSIVE DESIGN
 * ============================================================================ */

@media (max-width: 1024px) {
  .grid.cols-4 {
    grid-template-columns: repeat(2, 1fr) !important;
  }
  
  .grid.cols-2 {
    grid-template-columns: 1fr !important;
  }
}

/* Formularios - alineación correcta */
.card form > div {
  margin-bottom: 16px;
}

.card form .label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  font-size: 14px;
}

.card form .input,
.card form input[type="text"],
.card form input[type="email"],
.card form input[type="tel"],
.card form select {
  width: 100%;
  padding: 10px 14px;
  font-size: 14px;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  box-sizing: border-box;
}

.card form select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 36px;
}

@media (max-width: 768px) {
  /* Container principal */
  .admin-container {
    padding: 16px !important;
  }
  
  /* Eliminar espacio en blanco al inicio */
  main.admin-container {
    padding-top: 16px !important;
  }
  
  /* Header */
  main > div:first-child {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 12px !important;
    margin-bottom: 16px !important;
  }
  
  main > div:first-child h1 {
    font-size: 22px !important;
    margin-bottom: 4px !important;
  }
  
  main > div:first-child .meta {
    font-size: 12px !important;
  }
  
  main > div:first-child > div:last-child {
    width: 100%;
    flex-direction: column !important;
    gap: 8px !important;
  }
  
  main > div:first-child > div:last-child .btn {
    width: 100%;
    justify-content: center;
    padding: 10px 16px !important;
  }
  
  /* Grid de estadísticas */
  .grid.cols-4 {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 12px !important;
  }
  
  .grid.cols-4 .card {
    padding: 16px !important;
  }
  
  .grid.cols-4 .h2 {
    font-size: 24px !important;
  }
  
  /* Grid de columnas */
  .grid.cols-2 {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
  }
  
  /* Cards */
  .card {
    padding: 16px !important;
  }
  
  .card h3 {
    font-size: 18px !important;
  }
  
  /* Formularios */
  .card form > div {
    margin-bottom: 12px !important;
  }
  
  .card form > div:last-child {
    flex-direction: column !important;
  }
  
  .card form > div:last-child .btn {
    width: 100%;
  }
  
  /* Botones de acciones */
  .card > div:last-child {
    gap: 8px !important;
  }
  
  /* Publicaciones */
  .card > div > div[style*="display: flex"] {
    flex-direction: column !important;
    gap: 8px !important;
    padding: 12px !important;
  }
  
  .card > div > div[style*="display: flex"] > div:first-child {
    width: 100%;
  }
  
  .card > div > div[style*="display: flex"] > div:last-child {
    width: 100%;
  }
  
  .card > div > div[style*="display: flex"] > div:last-child .btn {
    width: 100%;
    padding: 10px !important;
  }
  
  /* Historial */
  .card > div[style*="max-height"] {
    max-height: 300px !important;
  }
  
  .card > div[style*="max-height"] > div {
    padding: 10px !important;
  }
  
  .card > div[style*="max-height"] > div > div {
    flex-direction: column !important;
    gap: 8px !important;
  }
  
  /* Botones de acción en estado */
  .card > div:last-child button {
    width: 100%;
    padding: 12px !important;
    font-size: 14px !important;
  }
  
  /* Modales */
  #modalCambiarEstado .card,
  #modalEliminar .card {
    margin: 16px !important;
    padding: 20px !important;
  }
  
  #modalCambiarEstado form > div:last-child,
  #modalEliminar form > div:last-child {
    flex-direction: column !important;
  }
  
  #modalCambiarEstado form > div:last-child .btn,
  #modalEliminar form > div:last-child .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .admin-container {
    padding: 12px !important;
  }
  
  main > div:first-child h1 {
    font-size: 20px !important;
  }
  
  .grid.cols-4 {
    grid-template-columns: 1fr !important;
  }
  
  .grid.cols-4 .h2 {
    font-size: 28px !important;
  }
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

/* Cards en Dark Mode */
:root[data-theme="dark"] .card {
  background: #1F2937 !important;
  border-color: #374151 !important;
}

/* Títulos en Dark Mode */
:root[data-theme="dark"] .h1,
:root[data-theme="dark"] .h2,
:root[data-theme="dark"] .h3,
:root[data-theme="dark"] h1,
:root[data-theme="dark"] h2,
:root[data-theme="dark"] h3 {
  color: #F3F4F6 !important;
}

/* Meta text en Dark Mode */
:root[data-theme="dark"] .meta,
:root[data-theme="dark"] p.meta {
  color: #9CA3AF !important;
}

/* Estadísticas - mantener colores distintivos pero mejorar contraste */
:root[data-theme="dark"] .card .h2[style*="color: #007AFF"] {
  color: #60A5FA !important;
}

:root[data-theme="dark"] .card .h2[style*="color: #34C759"] {
  color: #34D399 !important;
}

:root[data-theme="dark"] .card .h2[style*="color: #FF9500"] {
  color: #FBBF24 !important;
}

:root[data-theme="dark"] .card .h2[style*="color: #8E8E93"] {
  color: #9CA3AF !important;
}

/* Inputs en Dark Mode */
:root[data-theme="dark"] .input,
:root[data-theme="dark"] select.input,
:root[data-theme="dark"] textarea.input,
:root[data-theme="dark"] input[type="text"],
:root[data-theme="dark"] input[type="email"],
:root[data-theme="dark"] input[type="number"],
:root[data-theme="dark"] input[type="tel"],
:root[data-theme="dark"] select,
:root[data-theme="dark"] textarea {
  background: #374151 !important;
  border-color: #4B5563 !important;
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .input:focus,
:root[data-theme="dark"] select.input:focus,
:root[data-theme="dark"] textarea.input:focus,
:root[data-theme="dark"] input[type="text"]:focus,
:root[data-theme="dark"] input[type="email"]:focus,
:root[data-theme="dark"] input[type="number"]:focus,
:root[data-theme="dark"] input[type="tel"]:focus,
:root[data-theme="dark"] select:focus,
:root[data-theme="dark"] textarea:focus {
  border-color: var(--cc-primary) !important;
  background: #1F2937 !important;
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .input::placeholder,
:root[data-theme="dark"] textarea.input::placeholder,
:root[data-theme="dark"] input::placeholder,
:root[data-theme="dark"] textarea::placeholder {
  color: #9CA3AF !important;
}

/* Labels en Dark Mode */
:root[data-theme="dark"] .label,
:root[data-theme="dark"] label {
  color: #D1D5DB !important;
}

/* Select arrow en Dark Mode */
:root[data-theme="dark"] .card form select,
:root[data-theme="dark"] select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239CA3AF' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
  color-scheme: dark;
}

/* Options en Dark Mode */
:root[data-theme="dark"] select option {
  background: #374151;
  color: #F3F4F6;
}

/* Badges con estilos inline - mantener colores distintivos */
:root[data-theme="dark"] .badge[style*="background: #34C759"],
:root[data-theme="dark"] .badge[style*="background: #FF9500"],
:root[data-theme="dark"] .badge[style*="background: #8E8E93"],
:root[data-theme="dark"] .badge[style*="background: #FF3B30"],
:root[data-theme="dark"] .badge[style*="background: #007AFF"] {
  /* Mantener colores de badges ya que son distintivos */
}

/* Divisores en Dark Mode */
:root[data-theme="dark"] hr {
  border-top-color: #374151 !important;
}

/* Publicaciones en Dark Mode */
:root[data-theme="dark"] div[style*="border: 1px solid #E5E7EB"] {
  border-color: #374151 !important;
  background: #1F2937 !important;
}

:root[data-theme="dark"] div[style*="border: 1px solid #E5E7EB"] strong {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] div[style*="border: 1px solid #E5E7EB"] .meta {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="border-bottom: 1px solid #E5E7EB"] {
  border-bottom-color: #374151 !important;
}

/* Botones Ver en publicaciones */
:root[data-theme="dark"] div[style*="border: 1px solid #E5E7EB"] .btn {
  background: var(--cc-primary) !important;
  color: #FFFFFF !important;
}

/* Code blocks en Dark Mode */
:root[data-theme="dark"] code {
  background: #374151 !important;
  color: #F3F4F6 !important;
}

/* Textos con color inline - Dark Mode */
:root[data-theme="dark"] div[style*="color: #007AFF"],
:root[data-theme="dark"] div[style*="color: #34C759"],
:root[data-theme="dark"] div[style*="color: #FF9500"],
:root[data-theme="dark"] div[style*="color: #8E8E93"] {
  /* Mantener colores distintivos para estadísticas */
}

:root[data-theme="dark"] strong {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] div {
  color: #D1D5DB;
}

/* Formularios - mejorar contraste de labels */
:root[data-theme="dark"] form div {
  color: inherit;
}

/* Publicaciones en dark mode */
:root[data-theme="dark"] div[style*="border: 1px solid #E5E7EB"] strong {
  color: #F3F4F6 !important;
}

/* Botones outline en dark mode */
:root[data-theme="dark"] .btn.outline,
:root[data-theme="dark"] a.btn.outline {
  background: #374151 !important;
  color: #F3F4F6 !important;
  border-color: #4B5563 !important;
}

:root[data-theme="dark"] .btn.outline:hover,
:root[data-theme="dark"] a.btn.outline:hover {
  background: #4B5563 !important;
  border-color: #6B7280 !important;
}

/* Botones normales (no primary, no outline) */
:root[data-theme="dark"] .btn:not(.primary):not(.outline),
:root[data-theme="dark"] a.btn:not(.primary):not(.outline) {
  background: #374151 !important;
  color: #F3F4F6 !important;
  border-color: #4B5563 !important;
}

/* Emojis y símbolos - asegurar visibilidad */
:root[data-theme="dark"] span[style*="font-size: 24px"] {
  filter: brightness(1.2);
}

/* Modales en Dark Mode */
:root[data-theme="dark"] #modalCambiarEstado,
:root[data-theme="dark"] #modalEliminar {
  background: rgba(0, 0, 0, 0.8);
}

:root[data-theme="dark"] #modalCambiarEstado .card,
:root[data-theme="dark"] #modalEliminar .card {
  background: #1F2937;
  border-color: #374151;
}

:root[data-theme="dark"] #modalEliminar .h3 {
  color: #FF3B30 !important;
}

:root[data-theme="dark"] #modalEliminar p {
  color: #D1D5DB;
}

/* Botones con estilos inline - Dark Mode */
:root[data-theme="dark"] button[style*="background: #FF9500"],
:root[data-theme="dark"] button[style*="background: #34C759"],
:root[data-theme="dark"] button[style*="background: #FF3B30"] {
  /* Mantener colores distintivos de botones de acción */
}

/* Scrollbar en Dark Mode */
:root[data-theme="dark"] div[style*="overflow-y: auto"]::-webkit-scrollbar {
  width: 8px;
}

:root[data-theme="dark"] div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
  background: #1F2937;
}

:root[data-theme="dark"] div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
  background: #4B5563;
  border-radius: 4px;
}

:root[data-theme="dark"] div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
  background: #6B7280;
}
</style>
