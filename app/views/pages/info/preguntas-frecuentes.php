<?php
/**
 * Vista: Preguntas Frecuentes
 * Ruta: /preguntas-frecuentes
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Preguntas Frecuentes</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Preguntas Frecuentes</div>
    <p class="meta" style="margin-top: 8px;">Encuentra respuestas a las dudas más comunes</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <!-- Sección: General -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">📋 General</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué es ChileChocados?</div>
          <p style="margin-top: 8px;">
            ChileChocados es un marketplace especializado en la compra y venta de vehículos siniestrados, 
            en desarme o con daños en Chile. Conectamos a vendedores con compradores interesados en este 
            tipo de vehículos, ya sea para reparación, repuestos o proyectos personales.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Es seguro comprar en ChileChocados?</div>
          <p style="margin-top: 8px;">
            Sí. Implementamos medidas de seguridad como verificación de usuarios, sistema de calificaciones, 
            y recomendaciones para realizar transacciones seguras. Siempre recomendamos revisar el vehículo 
            en persona y realizar transacciones en lugares seguros.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Necesito registrarme para usar el sitio?</div>
          <p style="margin-top: 8px;">
            Puedes navegar y buscar vehículos sin registrarte. Sin embargo, para publicar vehículos o 
            contactar vendedores, necesitas crear una cuenta gratuita.
          </p>
        </div>
      </section>

      <!-- Sección: Para Compradores -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">🛒 Para Compradores</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cómo puedo buscar vehículos?</div>
          <p style="margin-top: 8px;">
            Usa nuestra barra de búsqueda para buscar por marca, modelo o palabra clave. También puedes 
            filtrar por categoría, precio, ubicación y tipo de daño desde la página de listado.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cómo contacto al vendedor?</div>
          <p style="margin-top: 8px;">
            Una vez registrado, puedes enviar un mensaje directo al vendedor desde la página del vehículo 
            haciendo clic en "Contactar Vendedor". El vendedor recibirá una notificación y podrá responder 
            a través de nuestra plataforma.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Puedo negociar el precio?</div>
          <p style="margin-top: 8px;">
            Sí, puedes negociar directamente con el vendedor. Algunos anuncios indican "Precio negociable" 
            explícitamente, pero siempre puedes hacer una oferta respetuosa.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué debo revisar antes de comprar?</div>
          <p style="margin-top: 8px;">
            Recomendamos revisar: documentación del vehículo, historial de daños, estado mecánico, 
            posibilidad de transferencia, y costo estimado de reparaciones. Consulta nuestra 
            <a href="<?php echo BASE_URL; ?>/guia-comprador">Guía del Comprador</a> para más detalles.
          </p>
        </div>
      </section>

      <!-- Sección: Para Vendedores -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">💼 Para Vendedores</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cuánto cuesta publicar?</div>
          <p style="margin-top: 8px;">
            Las publicaciones básicas son <strong>gratuitas</strong>. Ofrecemos servicios premium como 
            publicaciones destacadas para mayor visibilidad por un costo adicional.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cuántas fotos puedo subir?</div>
          <p style="margin-top: 8px;">
            Puedes subir entre 1 y 6 fotos por publicación. Recomendamos incluir fotos de todos los ángulos 
            del vehículo y de los daños específicos para atraer compradores serios.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cuánto tiempo dura mi publicación?</div>
          <p style="margin-top: 8px;">
            Las publicaciones gratuitas permanecen activas por 60 días. Puedes renovarlas antes de que expiren 
            o marcar el vehículo como vendido cuando completes la venta.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Puedo editar mi publicación después de publicarla?</div>
          <p style="margin-top: 8px;">
            Sí, puedes editar el precio, descripción y fotos en cualquier momento desde tu panel de 
            "Mis Publicaciones". No puedes cambiar la marca o modelo una vez publicado.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué información debo incluir en mi anuncio?</div>
          <p style="margin-top: 8px;">
            Incluye: marca, modelo, año, tipo de daño, documentación disponible, ubicación, precio, 
            y una descripción detallada del estado. Consulta nuestra 
            <a href="<?php echo BASE_URL; ?>/guia-vendedor">Guía del Vendedor</a> para mejores prácticas.
          </p>
        </div>
      </section>

      <!-- Sección: Pagos y Transacciones -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">💳 Pagos y Transacciones</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿ChileChocados procesa los pagos?</div>
          <p style="margin-top: 8px;">
            No. ChileChocados es una plataforma de conexión entre compradores y vendedores. Las transacciones 
            se realizan directamente entre las partes. No somos responsables de los pagos ni transferencias.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué métodos de pago puedo usar?</div>
          <p style="margin-top: 8px;">
            El método de pago lo acuerdan comprador y vendedor directamente. Recomendamos transferencias 
            bancarias o efectivo en lugares seguros. Evita enviar dinero antes de ver el vehículo.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cobran comisión por la venta?</div>
          <p style="margin-top: 8px;">
            No cobramos comisión por ventas realizadas a través de nuestra plataforma. Solo cobramos por 
            servicios premium opcionales como publicaciones destacadas.
          </p>
        </div>
      </section>

      <!-- Sección: Cuenta y Perfil -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">👤 Cuenta y Perfil</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cómo creo una cuenta?</div>
          <p style="margin-top: 8px;">
            Haz clic en "Registrarse" en el menú principal, completa el formulario con tu información y 
            verifica tu correo electrónico. El proceso toma menos de 2 minutos.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Olvidé mi contraseña, qué hago?</div>
          <p style="margin-top: 8px;">
            En la página de login, haz clic en "¿Olvidaste tu contraseña?". Te enviaremos un enlace a tu 
            correo para crear una nueva contraseña.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Puedo eliminar mi cuenta?</div>
          <p style="margin-top: 8px;">
            Sí. Ve a Configuración > Cuenta y selecciona "Eliminar cuenta". Esta acción es permanente y 
            eliminará todas tus publicaciones y mensajes.
          </p>
        </div>
      </section>

      <!-- Sección: Seguridad -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">🔒 Seguridad</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cómo evito fraudes?</div>
          <p style="margin-top: 8px;">
            Sigue nuestros <a href="<?php echo BASE_URL; ?>/seguridad">Consejos de Seguridad</a>: 
            reúnete en persona, verifica documentación, no envíes dinero por adelantado, y desconfía de 
            ofertas demasiado buenas.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué hago si detecto una publicación fraudulenta?</div>
          <p style="margin-top: 8px;">
            Reporta la publicación usando el botón "Reportar" en la página del vehículo. Nuestro equipo 
            la revisará y tomará las medidas necesarias.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Mis datos personales están seguros?</div>
          <p style="margin-top: 8px;">
            Sí. Protegemos tu información según nuestra 
            <a href="<?php echo BASE_URL; ?>/privacidad">Política de Privacidad</a>. No compartimos tus 
            datos con terceros sin tu consentimiento.
          </p>
        </div>
      </section>

      <!-- Sección: Soporte -->
      <section style="margin-bottom: 32px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">💬 Soporte</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cómo contacto al soporte?</div>
          <p style="margin-top: 8px;">
            Puedes contactarnos por:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Email: <a href="mailto:soporte@chilechocados.cl">soporte@chilechocados.cl</a></li>
            <li>Formulario de contacto: <a href="<?php echo BASE_URL; ?>/contacto">Ir a Contacto</a></li>
            <li>WhatsApp: +56 9 XXXX XXXX (Lunes a Viernes, 9:00 - 18:00)</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cuál es el tiempo de respuesta del soporte?</div>
          <p style="margin-top: 8px;">
            Respondemos consultas en un plazo máximo de 24-48 horas hábiles. Para casos urgentes, 
            contáctanos vía WhatsApp.
          </p>
        </div>
      </section>

      <div style="margin-top: 32px; padding: 16px; background: #f5f5f5; border-radius: 8px;">
        <p style="margin: 0; font-size: 14px; color: #666;">
          <strong>¿No encuentras lo que buscas?</strong> 
          Contáctanos en <a href="mailto:soporte@chilechocados.cl">soporte@chilechocados.cl</a> 
          y con gusto te ayudaremos.
        </p>
      </div>

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
