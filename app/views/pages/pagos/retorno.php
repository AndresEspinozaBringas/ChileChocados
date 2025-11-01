<?php
use App\Helpers\Auth;

Auth::require();

require_once APP_PATH . '/views/layouts/header.php';

// Determinar el estado del pago
$esExitoso = $pago->estado === 'aprobado';
$esRechazado = $pago->estado === 'rechazado';
$esPendiente = $pago->estado === 'pendiente';
?>

<main class="container" style="max-width: 700px; margin: 60px auto; padding: 20px;">
  
  <?php if ($esExitoso): ?>
    <!-- PAGO EXITOSO -->
    <div class="card" style="padding: 48px; text-align: center;">
      <div style="width: 80px; height: 80px; background: #D1FAE5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>

      <h1 class="h1" style="color: #10B981; margin-bottom: 16px;">¡Pago Exitoso!</h1>
      
      <p style="font-size: 18px; color: #6B7280; margin-bottom: 32px;">
        Tu publicación ha sido destacada exitosamente
      </p>

      <!-- Detalles del pago -->
      <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; margin-bottom: 32px; text-align: left;">
        <h3 class="h3" style="margin-bottom: 16px;">Detalles del pago</h3>
        <div style="display: grid; gap: 12px;">
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #6B7280;">Publicación:</span>
            <strong><?php echo htmlspecialchars($pago->titulo); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #6B7280;">Monto pagado:</span>
            <strong style="color: #10B981;"><?php echo formatPrice($pago->monto); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #6B7280;">Orden Flow:</span>
            <strong><?php echo htmlspecialchars($pago->flow_orden); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #6B7280;">Fecha:</span>
            <strong><?php echo date('d/m/Y H:i', strtotime($pago->fecha_pago)); ?></strong>
          </div>
        </div>
      </div>

      <!-- Próximos pasos -->
      <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 12px; padding: 24px; margin-bottom: 32px; text-align: left;">
        <h3 class="h3" style="margin-bottom: 16px; color: #1E40AF;">Próximos pasos</h3>
        <ol style="margin: 0; padding-left: 20px; display: grid; gap: 8px;">
          <li>Tu publicación está pendiente de revisión por nuestro equipo</li>
          <li>Una vez aprobada, se activará el destacado automáticamente</li>
          <li>Recibirás una notificación cuando esté publicada</li>
        </ol>
      </div>

      <div style="display: flex; gap: 16px; justify-content: center;">
        <a href="<?php echo BASE_URL; ?>/publicaciones/<?php echo $pago->publicacion_id; ?>" class="btn">
          Ver publicación
        </a>
        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn primary">
          Ir a mis publicaciones
        </a>
      </div>
    </div>

  <?php elseif ($esRechazado): ?>
    <!-- PAGO RECHAZADO -->
    <div class="card" style="padding: 48px; text-align: center;">
      <div style="width: 80px; height: 80px; background: #FEE2E2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="3">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
      </div>

      <h1 class="h1" style="color: #DC2626; margin-bottom: 16px;">Pago Rechazado</h1>
      
      <p style="font-size: 18px; color: #6B7280; margin-bottom: 32px;">
        No se pudo procesar tu pago. Por favor, intenta nuevamente.
      </p>

      <!-- Detalles -->
      <div style="background: #FEF3F2; border: 1px solid #FEE2E2; border-radius: 12px; padding: 24px; margin-bottom: 32px; text-align: left;">
        <h3 class="h3" style="margin-bottom: 16px;">Posibles causas</h3>
        <ul style="margin: 0; padding-left: 20px; display: grid; gap: 8px;">
          <li>Fondos insuficientes en la tarjeta</li>
          <li>Datos de la tarjeta incorrectos</li>
          <li>Tarjeta bloqueada o vencida</li>
          <li>Límite de compra excedido</li>
        </ul>
      </div>

      <!-- Formulario para reintentar -->
      <form method="POST" action="<?php echo BASE_URL; ?>/pago/reintentar">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="pago_id" value="<?php echo $pago->id; ?>">
        
        <div style="display: flex; gap: 16px; justify-content: center;">
          <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn">
            Volver a mis publicaciones
          </a>
          <button type="submit" class="btn primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
              <polyline points="23 4 23 10 17 10"></polyline>
              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
            Reintentar pago
          </button>
        </div>
      </form>

      <p class="meta" style="margin-top: 24px; font-size: 13px;">
        Si el problema persiste, contacta a tu banco o prueba con otra tarjeta
      </p>
    </div>

  <?php else: ?>
    <!-- PAGO PENDIENTE -->
    <div class="card" style="padding: 48px; text-align: center;">
      <div style="width: 80px; height: 80px; background: #FEF3C7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="3">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
      </div>

      <h1 class="h1" style="color: #F59E0B; margin-bottom: 16px;">Pago Pendiente</h1>
      
      <p style="font-size: 18px; color: #6B7280; margin-bottom: 32px;">
        Tu pago está siendo procesado. Esto puede tomar unos minutos.
      </p>

      <div style="background: #FFFBEB; border: 1px solid #FEF3C7; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
        <p style="margin: 0;">
          Recibirás una notificación por email cuando se confirme el pago.
          También puedes revisar el estado en "Mis Publicaciones".
        </p>
      </div>

      <div style="display: flex; gap: 16px; justify-content: center;">
        <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn primary">
          Ir a mis publicaciones
        </a>
      </div>
    </div>
  <?php endif; ?>

</main>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
