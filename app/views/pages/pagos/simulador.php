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

<main class="container" style="max-width: 600px; margin: 60px auto; padding: 20px;">
  
  <div class="card" style="padding: 40px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="margin: 0 auto 20px;">
      <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
      <line x1="1" y1="10" x2="23" y2="10"></line>
    </svg>
    
    <h1 style="color: white; margin-bottom: 8px;">Simulador de Flow</h1>
    <p style="color: rgba(255,255,255,0.9); font-size: 14px;">Modo de Prueba Local</p>
  </div>

  <div class="card" style="padding: 40px; margin-top: -20px; border-top-left-radius: 0; border-top-right-radius: 0;">
    
    <div style="background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
      <p style="margin: 0; font-size: 14px; color: #92400E;">
        <strong>⚠️ Modo de Prueba:</strong> Esta es una simulación de Flow. No se realizarán cargos reales.
      </p>
    </div>

    <h3 class="h3" style="margin-bottom: 16px;">Detalles del Pago</h3>
    
    <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
      <div style="display: grid; gap: 12px;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #6B7280;">Token:</span>
          <strong style="font-size: 12px; font-family: monospace;"><?php echo substr($token, 0, 20); ?>...</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #6B7280;">Estado:</span>
          <strong style="color: #10B981;">Pendiente</strong>
        </div>
      </div>
    </div>

    <h3 class="h3" style="margin-bottom: 16px;">Simular Resultado</h3>
    <p style="color: #6B7280; margin-bottom: 24px; font-size: 14px;">
      Selecciona el resultado que deseas simular:
    </p>

    <div style="display: grid; gap: 16px;">
      <!-- Pago Exitoso -->
      <form method="POST" action="<?php echo BASE_URL; ?>/pago/simulador/procesar">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="resultado" value="exitoso">
        <button type="submit" class="btn primary" style="width: 100%; padding: 16px; font-size: 16px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          Simular Pago Exitoso
        </button>
      </form>

      <!-- Pago Rechazado -->
      <form method="POST" action="<?php echo BASE_URL; ?>/pago/simulador/procesar">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="resultado" value="rechazado">
        <button type="submit" class="btn" style="width: 100%; padding: 16px; font-size: 16px; background: #DC2626; color: white;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
          Simular Pago Rechazado
        </button>
      </form>

      <!-- Cancelar -->
      <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn" style="width: 100%; padding: 16px; font-size: 16px; text-align: center; display: block; box-sizing: border-box;">
        Cancelar
      </a>
    </div>

    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
      <p class="meta" style="font-size: 13px; color: #9CA3AF; text-align: center;">
        Este simulador te permite probar el flujo completo de pago sin usar credenciales reales de Flow.
      </p>
    </div>

  </div>

</main>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
