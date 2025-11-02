<?php
/**
 * Simulador de Flow - Modo de Prueba Local
 * Simula la pantalla de pago de Flow para testing
 */

$token = $_GET['token'] ?? null;

if (!$token) {
    die('Token no proporcionado');
}

require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container" style="max-width: 700px; margin: 60px auto; padding: 20px;">
  
  <!-- Header con diseño admin -->
  <div style="background: white; border: 2px solid #E5E5E5; border-radius: 12px; padding: 32px; text-align: center; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #E6332A 0%, #C02A23 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(230, 51, 42, 0.3);">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
        <line x1="1" y1="10" x2="23" y2="10"></line>
      </svg>
    </div>
    
    <h1 style="color: #2E2E2E; margin-bottom: 8px; font-size: 28px; font-weight: 700;">Simulador de Flow</h1>
    <p style="color: #666; font-size: 15px; font-weight: 500;">Modo de Prueba Local</p>
  </div>

  <div style="background: white; border: 2px solid #E5E5E5; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    
    <!-- Alerta de modo prueba -->
    <div style="background: #FEF3C7; border: 2px solid #FDE68A; border-left: 4px solid #F59E0B; border-radius: 8px; padding: 16px; margin-bottom: 32px; display: flex; align-items: start; gap: 12px;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
        <line x1="12" y1="9" x2="12" y2="13"></line>
        <line x1="12" y1="17" x2="12.01" y2="17"></line>
      </svg>
      <div>
        <p style="margin: 0; font-size: 14px; color: #92400E; font-weight: 600; line-height: 1.6;">
          <strong>Modo de Prueba</strong>
        </p>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #B45309; line-height: 1.5;">
          Esta es una simulación de Flow. No se realizarán cargos reales.
        </p>
      </div>
    </div>

    <!-- Detalles del pago -->
    <div style="margin-bottom: 32px;">
      <h3 style="font-size: 18px; font-weight: 700; color: #2E2E2E; margin-bottom: 16px;">Detalles del Pago</h3>
      
      <div style="background: #F9F9F9; border: 2px solid #E5E5E5; border-radius: 8px; padding: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
          <tr style="border-bottom: 1px solid #E5E5E5;">
            <td style="padding: 12px 0; color: #666; font-weight: 600; font-size: 14px;">Token:</td>
            <td style="padding: 12px 0; text-align: right;">
              <code style="font-size: 12px; font-family: monospace; color: #2E2E2E; background: white; padding: 6px 12px; border-radius: 4px; border: 1px solid #E5E5E5; display: inline-block;"><?php echo substr($token, 0, 20); ?>...</code>
            </td>
          </tr>
          <tr>
            <td style="padding: 12px 0; color: #666; font-weight: 600; font-size: 14px;">Estado:</td>
            <td style="padding: 12px 0; text-align: right;">
              <span style="color: #D97706; background: #FEF3C7; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; border: 1px solid #FDE68A; display: inline-block;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Pendiente
              </span>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Acciones -->
    <div style="margin-bottom: 32px;">
      <h3 style="font-size: 18px; font-weight: 700; color: #2E2E2E; margin-bottom: 12px;">Simular Resultado</h3>
      <p style="color: #666; margin-bottom: 24px; font-size: 14px; line-height: 1.6;">
        Selecciona el resultado que deseas simular:
      </p>

      <div style="display: grid; gap: 12px;">
        <!-- Pago Exitoso -->
        <form method="POST" action="<?php echo BASE_URL; ?>/pago/simulador/procesar">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="resultado" value="exitoso">
          <button type="submit" class="btn primary" style="width: 100%; padding: 14px 24px; font-size: 15px; font-weight: 600; justify-content: center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Simular Pago Exitoso
          </button>
        </form>

        <!-- Pago Rechazado -->
        <form method="POST" action="<?php echo BASE_URL; ?>/pago/simulador/procesar">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="resultado" value="rechazado">
          <button type="submit" class="btn" style="width: 100%; padding: 14px 24px; font-size: 15px; font-weight: 600; background: #EF4444; color: white; border-color: #EF4444; justify-content: center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            Simular Pago Rechazado
          </button>
        </form>

        <!-- Cancelar -->
        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn outline" style="width: 100%; padding: 14px 24px; font-size: 15px; font-weight: 600; text-align: center; display: flex; align-items: center; justify-content: center; box-sizing: border-box; text-decoration: none;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Volver a mis publicaciones
        </a>
      </div>
    </div>

    <!-- Información adicional -->
    <div style="padding-top: 24px; border-top: 2px solid #E5E5E5;">
      <div style="display: flex; align-items: start; gap: 12px; background: #F9F9F9; padding: 16px; border-radius: 8px; border: 1px solid #E5E5E5;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.6;">
          Este simulador te permite probar el flujo completo de pago sin usar credenciales reales de Flow. Los resultados son inmediatos y no afectan tu cuenta.
        </p>
      </div>
    </div>

  </div>

</main>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
