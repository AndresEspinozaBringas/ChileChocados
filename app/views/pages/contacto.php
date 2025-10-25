<?php
/**
 * Vista: Contacto
 * Ruta: /contacto
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Contacto</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Contáctanos</div>
    <p class="meta" style="margin-top: 8px;">
      ¿Tienes dudas, sugerencias o necesitas ayuda? Estamos aquí para ayudarte.
    </p>

    <?php if (isset($data['success'])): ?>
      <div class="alert alert-success" style="margin-top: 16px; padding: 16px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724;">
        <strong>✓ Éxito:</strong> <?php echo htmlspecialchars($data['success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($data['error'])): ?>
      <div class="alert alert-error" style="margin-top: 16px; padding: 16px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24;">
        <strong>⚠ Error:</strong> <?php echo htmlspecialchars($data['error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>/contacto/enviar" class="form" style="margin-top: 24px;">
      
      <!-- Token CSRF -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      
      <div class="form two">
        <label>
          Nombre completo *
          <input 
            type="text" 
            name="nombre" 
            placeholder="Juan Pérez" 
            required 
            minlength="3"
            maxlength="100"
          >
        </label>

        <label>
          Email *
          <input 
            type="email" 
            name="email" 
            placeholder="tu@email.com" 
            required
          >
        </label>
      </div>

      <label style="margin-top: 12px;">
        Asunto *
        <input 
          type="text" 
          name="asunto" 
          placeholder="¿En qué podemos ayudarte?" 
          required 
          minlength="5"
          maxlength="200"
        >
      </label>

      <label style="margin-top: 12px;">
        Mensaje *
        <textarea 
          name="mensaje" 
          rows="6" 
          placeholder="Escribe tu mensaje aquí..."
          required
          minlength="10"
          maxlength="1000"
        ></textarea>
      </label>

      <p class="meta" style="margin-top: 8px;">
        * Campos obligatorios
      </p>

      <div style="margin-top: 24px;">
        <button type="submit" class="btn primary">
          Enviar mensaje
        </button>
        <a href="<?php echo BASE_URL; ?>" class="btn" style="margin-left: 12px;">
          Cancelar
        </a>
      </div>
    </form>
  </div>

  <!-- Información adicional -->
  <div class="grid cols-3" style="margin-top: 24px;">
    
    <div class="card">
      <span class="iconify" style="font-size: 32px; color: #0066CC;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
          <polyline points="22,6 12,13 2,6"></polyline>
        </svg>
      </span>
      <div class="h3" style="margin-top: 12px;">Email</div>
      <p class="meta" style="margin-top: 8px;">
        <a href="mailto:soporte@chilechocados.cl">soporte@chilechocados.cl</a>
      </p>
      <p class="meta" style="margin-top: 4px;">
        Respondemos en 24-48 horas
      </p>
    </div>

    <div class="card">
      <span class="iconify" style="font-size: 32px; color: #0066CC;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
      </span>
      <div class="h3" style="margin-top: 12px;">Horario</div>
      <p class="meta" style="margin-top: 8px;">
        Lunes a Viernes<br>
        9:00 - 18:00 hrs
      </p>
      <p class="meta" style="margin-top: 4px;">
        Fines de semana: Cerrado
      </p>
    </div>

    <div class="card">
      <span class="iconify" style="font-size: 32px; color: #0066CC;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
      </span>
      <div class="h3" style="margin-top: 12px;">Ubicación</div>
      <p class="meta" style="margin-top: 8px;">
        Santiago, Chile<br>
        Región Metropolitana
      </p>
      <p class="meta" style="margin-top: 4px;">
        Servicio en todo Chile
      </p>
    </div>

  </div>

  <!-- FAQ Rápido -->
  <div class="card" style="margin-top: 24px;">
    <div class="h2">Preguntas Frecuentes</div>
    
    <div style="margin-top: 16px;">
      <div class="h3">¿Cómo publico un vehículo?</div>
      <p class="meta" style="margin-top: 8px;">
        Regístrate en la plataforma, completa el formulario de publicación con fotos y datos 
        del vehículo. Tu publicación será revisada antes de aparecer en el sitio.
      </p>
    </div>

    <div style="margin-top: 16px;">
      <div class="h3">¿Cuánto cuesta publicar?</div>
      <p class="meta" style="margin-top: 8px;">
        Las publicaciones normales son <strong>gratuitas</strong>. Si deseas destacar tu anuncio, 
        puedes hacerlo por $5.000 CLP mediante pago con Flow.
      </p>
    </div>

    <div style="margin-top: 16px;">
      <div class="h3">¿Cómo me contactan los compradores?</div>
      <p class="meta" style="margin-top: 8px;">
        A través del sistema de mensajería interno de la plataforma. Recibirás notificaciones 
        por email cuando tengas mensajes nuevos.
      </p>
    </div>

    <div style="margin-top: 16px;">
      <div class="h3">¿ChileChocados verifica los vehículos?</div>
      <p class="meta" style="margin-top: 8px;">
        No realizamos inspecciones físicas. Revisamos que las publicaciones cumplan nuestras 
        políticas, pero recomendamos a compradores verificar personalmente antes de comprar.
      </p>
    </div>

    <div style="margin-top: 16px;">
      <div class="h3">¿Necesito ayuda con un problema técnico?</div>
      <p class="meta" style="margin-top: 8px;">
        Usa el formulario arriba describiendo el problema. Incluye capturas de pantalla si es 
        posible. Nuestro equipo técnico te ayudará.
      </p>
    </div>
  </div>

  <div class="breadcrumbs" style="margin-top: 24px; margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>">← Volver al inicio</a>
  </div>
</main>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
