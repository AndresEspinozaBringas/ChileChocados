<?php
/**
 * Vista: Historial Completo de Usuario - Panel Admin
 * Muestra el historial de auditoría completo de un usuario
 * 
 * @author ChileChocados
 * @date 2025-10-30
 */

// La verificación de admin se hace en el controlador
layout('header');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">

<main class="container" style="padding: 24px 0;">

  <!-- Encabezado -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
      <h1 class="h1">Historial de Actividad</h1>
      <p class="meta">
        Usuario: 
        <strong><?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido); ?></strong>
        (<?php echo htmlspecialchars($usuario->email); ?>)
      </p>
    </div>
    <div style="display: flex; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/admin/usuarios/<?php echo $usuario_id; ?>" class="btn outline">
        ← Volver al Perfil
      </a>
      <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="btn outline">
        Ver Todos los Usuarios
      </a>
    </div>
  </div>

  <!-- Timeline de Historial -->
  <div class="card">
    <?php if (empty($historial)): ?>
      <div style="text-align: center; padding: 48px;">
        <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
        <h3 class="h3" style="margin-bottom: 8px;">No hay historial</h3>
        <p class="meta">Este usuario aún no tiene actividad registrada en el sistema</p>
      </div>
    <?php else: ?>
      <div style="position: relative; padding-left: 40px;">
        <!-- Línea vertical del timeline -->
        <div style="position: absolute; left: 20px; top: 0; bottom: 0; width: 2px; background: #E5E7EB;"></div>

        <?php foreach ($historial as $item): ?>
          <div style="position: relative; margin-bottom: 24px;">
            <!-- Punto del timeline -->
            <div style="
              position: absolute; 
              left: -28px; 
              width: 16px; 
              height: 16px; 
              border-radius: 50%; 
              background: <?php 
                echo $item->accion === 'crear' ? '#34C759' : 
                     ($item->accion === 'actualizar' ? '#007AFF' : '#FF3B30'); 
              ?>;
              border: 3px solid white;
              box-shadow: 0 0 0 2px <?php 
                echo $item->accion === 'crear' ? '#34C759' : 
                     ($item->accion === 'actualizar' ? '#007AFF' : '#FF3B30'); 
              ?>;
            "></div>

            <!-- Contenido del evento -->
            <div style="background: #F9FAFB; border-radius: 8px; padding: 16px; margin-left: 8px;">
              
              <!-- Header del evento -->
              <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                <div>
                  <span style="
                    background: <?php 
                      echo $item->accion === 'crear' ? '#34C759' : 
                           ($item->accion === 'actualizar' ? '#007AFF' : '#FF3B30'); 
                    ?>;
                    color: white;
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                  ">
                    <?php echo htmlspecialchars($item->accion); ?>
                  </span>
                  <span style="margin-left: 8px; font-weight: 600;">
                    <?php echo htmlspecialchars($item->tabla); ?>
                  </span>
                  <span class="meta">
                    #<?php echo $item->registro_id; ?>
                  </span>
                </div>
                <div class="meta" style="font-size: 12px;">
                  <?php echo date('d/m/Y H:i:s', strtotime($item->fecha)); ?>
                </div>
              </div>

              <!-- Información del admin que realizó la acción -->
              <?php if ($item->admin_nombre): ?>
                <div class="meta" style="margin-bottom: 12px; font-size: 13px;">
                  👤 Realizado por: 
                  <strong>
                    <?php echo htmlspecialchars($item->admin_nombre . ' ' . $item->admin_apellido); ?>
                  </strong>
                </div>
              <?php endif; ?>

              <!-- IP del usuario -->
              <?php if ($item->ip): ?>
                <div class="meta" style="margin-bottom: 12px; font-size: 12px;">
                  🌐 IP: <code style="background: #E5E7EB; padding: 2px 6px; border-radius: 4px;">
                    <?php echo htmlspecialchars($item->ip); ?>
                  </code>
                </div>
              <?php endif; ?>

              <!-- Datos anteriores y nuevos -->
              <?php if ($item->datos_anteriores || $item->datos_nuevos): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px;">
                  
                  <!-- Datos anteriores -->
                  <?php if ($item->datos_anteriores): ?>
                    <?php $datosAnt = json_decode($item->datos_anteriores, true); ?>
                    <?php if ($datosAnt): ?>
                      <div>
                        <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #6B7280;">
                          ← Antes
                        </div>
                        <div style="background: white; border-radius: 6px; padding: 12px; font-size: 12px; font-family: 'Courier New', monospace;">
                          <?php foreach ($datosAnt as $key => $value): ?>
                            <div style="margin-bottom: 4px;">
                              <span style="color: #8E8E93;">
                                <?php echo htmlspecialchars($key); ?>:
                              </span>
                              <span style="color: #1D1D1F;">
                                <?php echo is_array($value) ? json_encode($value) : htmlspecialchars($value); ?>
                              </span>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>

                  <!-- Datos nuevos -->
                  <?php if ($item->datos_nuevos): ?>
                    <?php $datosNuev = json_decode($item->datos_nuevos, true); ?>
                    <?php if ($datosNuev): ?>
                      <div>
                        <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #6B7280;">
                          Después →
                        </div>
                        <div style="background: white; border-radius: 6px; padding: 12px; font-size: 12px; font-family: 'Courier New', monospace;">
                          <?php foreach ($datosNuev as $key => $value): ?>
                            <div style="margin-bottom: 4px;">
                              <span style="color: #8E8E93;">
                                <?php echo htmlspecialchars($key); ?>:
                              </span>
                              <span style="color: #1D1D1F;">
                                <?php echo is_array($value) ? json_encode($value) : htmlspecialchars($value); ?>
                              </span>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>

                </div>
              <?php endif; ?>

            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Botón para exportar -->
  <?php if (!empty($historial)): ?>
    <div style="text-align: center; margin-top: 24px;">
      <button 
        onclick="window.print()" 
        class="btn outline"
        style="margin-right: 12px;"
      >
        🖨️ Imprimir Historial
      </button>
      <button 
        onclick="exportarJSON()" 
        class="btn outline"
      >
        📥 Exportar JSON
      </button>
    </div>
  <?php endif; ?>

</main>

<?php layout('footer'); ?>

<script>
// Exportar historial como JSON
function exportarJSON() {
  const historial = <?php echo json_encode($historial); ?>;
  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(historial, null, 2));
  const downloadAnchorNode = document.createElement('a');
  downloadAnchorNode.setAttribute("href", dataStr);
  downloadAnchorNode.setAttribute("download", "historial_usuario_<?php echo $usuario_id; ?>.json");
  document.body.appendChild(downloadAnchorNode);
  downloadAnchorNode.click();
  downloadAnchorNode.remove();
}
</script>

<style>
@media print {
  .btn, nav, header { display: none !important; }
  body { background: white; }
}
</style>
