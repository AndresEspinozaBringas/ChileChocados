<?php
use App\Helpers\Auth;

Auth::require();

require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
  
  <div class="card" style="padding: 40px;">
    <div style="text-align: center; margin-bottom: 32px;">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#E6332A" stroke-width="2" style="margin: 0 auto 16px;">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M12 6v6l4 2"></path>
      </svg>
      <h1 class="h1" style="margin-bottom: 8px;">Confirmar Pago</h1>
      <p class="meta" style="font-size: 16px; color: #6B7280;">
        Estás a punto de publicar tu vehículo como destacado
      </p>
    </div>

    <!-- Resumen de la publicación -->
    <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
      <h3 class="h3" style="margin-bottom: 16px;">Resumen de tu publicación</h3>
      <div style="display: grid; gap: 12px;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #6B7280;">Vehículo:</span>
          <strong><?php echo htmlspecialchars($publicacion->titulo); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #6B7280;">Tipo de publicación:</span>
          <strong style="color: #E6332A;">Destacada</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #6B7280;">Duración:</span>
          <strong><?php echo $dias; ?> días</strong>
        </div>
      </div>
    </div>

    <!-- Beneficios -->
    <div style="background: #FEF3F2; border: 1px solid #FEE2E2; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
      <h3 class="h3" style="margin-bottom: 16px; color: #E6332A;">Beneficios de publicar destacado</h3>
      <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px;">
        <li style="display: flex; align-items: start; gap: 12px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Aparece en la parte superior del listado</span>
        </li>
        <li style="display: flex; align-items: start; gap: 12px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Destacado visual con badge especial</span>
        </li>
        <li style="display: flex; align-items: start; gap: 12px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Mayor visibilidad y más consultas</span>
        </li>
        <li style="display: flex; align-items: start; gap: 12px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>Vende más rápido tu vehículo</span>
        </li>
      </ul>
    </div>

    <!-- Total a pagar -->
    <div style="background: #F9FAFB; border: 2px solid #E6332A; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 18px; font-weight: 600;">Total a pagar:</span>
        <span style="font-size: 32px; font-weight: 700; color: #E6332A;">
          <?php echo formatPrice($monto); ?>
        </span>
      </div>
      <p class="meta" style="margin-top: 8px; text-align: right;">
        Pago único por <?php echo $dias; ?> días
      </p>
    </div>

    <!-- Formulario de pago -->
    <form method="POST" action="<?php echo BASE_URL; ?>/pago/iniciar" id="formPago">
      <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
      <input type="hidden" name="publicacion_id" value="<?php echo $publicacion->id; ?>">
      <input type="hidden" name="tipo_destacado" value="<?php echo $tipo_destacado; ?>">

      <div style="display: flex; gap: 16px; justify-content: center;">
        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn" style="min-width: 150px;">
          Cancelar
        </a>
        <button type="submit" class="btn primary" id="btnPagar" style="min-width: 200px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
          </svg>
          Ir a pagar con Flow
        </button>
      </div>
    </form>

    <script>
    // Debugging y prevención de doble submit
    document.getElementById('formPago').addEventListener('submit', function(e) {
      console.log('=== FORMULARIO DE PAGO ENVIADO ===');
      console.log('Action:', this.action);
      console.log('Method:', this.method);
      
      const formData = new FormData(this);
      console.log('Datos del formulario:');
      for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
      }
      
      // Deshabilitar botón para evitar doble click
      const btn = document.getElementById('btnPagar');
      btn.disabled = true;
      btn.innerHTML = '<span style="display: inline-flex; align-items: center; gap: 8px;">Procesando...</span>';
    });
    </script>

    <!-- Información de seguridad -->
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB; text-align: center;">
      <p class="meta" style="font-size: 13px; color: #9CA3AF;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        Pago seguro procesado por Flow. Aceptamos todas las tarjetas de crédito y débito.
      </p>
    </div>
  </div>

</main>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
