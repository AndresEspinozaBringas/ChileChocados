<?php
/**
 * Vista: Cómo Funciona
 * Ruta: /como-funciona
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Cómo Funciona</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">¿Cómo Funciona ChileChocados?</div>
    <p class="meta" style="margin-top: 8px;">Tu marketplace de confianza para vehículos siniestrados</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">📖 ¿Qué es ChileChocados?</div>
        <p style="margin-top: 12px;">
          ChileChocados es el marketplace líder en Chile para la compra y venta de vehículos siniestrados, 
          en desarme o con daños. Conectamos a compradores y vendedores de manera segura, transparente y eficiente.
        </p>
        <p style="margin-top: 8px;">
          Ya sea que busques un vehículo para reparar, repuestos específicos o quieras vender tu vehículo siniestrado, 
          ChileChocados es tu plataforma ideal.
        </p>
      </section>

      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">💼 Para Vendedores</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 1: Regístrate Gratis</div>
          <p style="margin-top: 8px;">
            Crea tu cuenta en menos de 2 minutos. Solo necesitas tu email y algunos datos básicos.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Registro 100% gratuito</li>
            <li>Sin costos ocultos</li>
            <li>Proceso simple y rápido</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 2: Publica tu Vehículo</div>
          <p style="margin-top: 8px;">
            Completa el formulario con la información de tu vehículo y sube fotos.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Sube hasta 6 fotos de calidad</li>
            <li>Describe el estado del vehículo</li>
            <li>Define si vendes completo o por partes</li>
            <li>Establece tu precio (si vendes completo)</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 3: Revisión y Aprobación</div>
          <p style="margin-top: 8px;">
            Nuestro equipo revisa tu publicación para garantizar calidad y seguridad.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Revisión en menos de 24 horas</li>
            <li>Te notificamos por email</li>
            <li>Si hay observaciones, puedes corregir</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 4: Recibe Consultas</div>
          <p style="margin-top: 8px;">
            Los compradores interesados te contactarán directamente a través de la plataforma.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Sistema de mensajería interno</li>
            <li>Notificaciones en tiempo real</li>
            <li>Tus datos protegidos</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 5: Cierra la Venta</div>
          <p style="margin-top: 8px;">
            Coordina con el comprador y completa la transacción de forma segura.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Negocia directamente con el comprador</li>
            <li>Define forma de pago y entrega</li>
            <li>Marca como vendido cuando cierres</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">🛒 Para Compradores</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 1: Explora el Catálogo</div>
          <p style="margin-top: 8px;">
            Navega por miles de vehículos siniestrados disponibles en todo Chile.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Filtros avanzados por marca, modelo, año, región</li>
            <li>Búsqueda por tipo de daño</li>
            <li>Fotos de alta calidad</li>
            <li>Información detallada de cada vehículo</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 2: Encuentra lo que Buscas</div>
          <p style="margin-top: 8px;">
            Usa nuestros filtros para encontrar exactamente lo que necesitas.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Vehículos completos para reparar</li>
            <li>Vehículos en desarme para repuestos</li>
            <li>Filtra por precio, ubicación y más</li>
            <li>Guarda tus favoritos</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 3: Contacta al Vendedor</div>
          <p style="margin-top: 8px;">
            Comunícate directamente con el vendedor para hacer consultas.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Sistema de mensajería seguro</li>
            <li>Pregunta todo lo que necesites saber</li>
            <li>Solicita fotos adicionales</li>
            <li>Coordina una visita si es necesario</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 4: Inspecciona el Vehículo</div>
          <p style="margin-top: 8px;">
            Antes de comprar, revisa personalmente el vehículo.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Verifica el estado real</li>
            <li>Revisa documentación</li>
            <li>Lleva un mecánico si es necesario</li>
            <li>Confirma que todo coincida con la publicación</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Paso 5: Completa la Compra</div>
          <p style="margin-top: 8px;">
            Negocia el precio final y cierra la transacción de forma segura.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Acuerda forma de pago</li>
            <li>Firma documentos necesarios</li>
            <li>Realiza transferencia de dominio</li>
            <li>¡Disfruta tu compra!</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">📦 Tipos de Publicaciones</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">🚗 Venta Completa</div>
          <p style="margin-top: 8px;">
            Vehículo completo para reparar o usar como está. Incluye precio fijo y toda la documentación.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Precio definido</li>
            <li>Documentación incluida</li>
            <li>Ideal para reparar</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">🔧 Desarme</div>
          <p style="margin-top: 8px;">
            Vehículo vendido por partes. Consulta disponibilidad de repuestos específicos con el vendedor.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Venta por partes</li>
            <li>Precios a consultar</li>
            <li>Ideal para repuestos</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">⭐ Destacadas</div>
          <p style="margin-top: 8px;">
            Publicaciones premium con mayor visibilidad. Aparecen primero en búsquedas y en la página principal.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Mayor visibilidad</li>
            <li>Más consultas</li>
            <li>Venta más rápida</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">🔒 Seguridad y Confianza</div>
        <p style="margin-top: 12px;">
          En ChileChocados nos tomamos muy en serio la seguridad de nuestros usuarios:
        </p>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">✓ Verificación de Publicaciones</div>
          <p style="margin-top: 8px;">
            Todas las publicaciones son revisadas por nuestro equipo antes de ser publicadas.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">✓ Sistema de Mensajería Seguro</div>
          <p style="margin-top: 8px;">
            Comunícate sin exponer tus datos personales hasta que estés listo.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">✓ Canal de Denuncias</div>
          <p style="margin-top: 8px;">
            Reporta cualquier actividad sospechosa o fraudulenta de forma confidencial.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">✓ Consejos de Seguridad</div>
          <p style="margin-top: 8px;">
            Guías y recomendaciones para comprar y vender de forma segura.
          </p>
        </div>
      </section>

      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">❓ Preguntas Frecuentes</div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">¿Es gratis publicar en ChileChocados?</div>
          <p style="margin-top: 8px;">
            Sí, publicar es completamente gratis. Solo pagas si deseas destacar tu publicación para mayor visibilidad.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Cuánto tiempo tarda en aprobarse una publicación?</div>
          <p style="margin-top: 8px;">
            Generalmente menos de 24 horas. Te notificaremos por email cuando esté aprobada.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿ChileChocados maneja los pagos?</div>
          <p style="margin-top: 8px;">
            No, las transacciones se realizan directamente entre comprador y vendedor. Solo cobramos por servicios de destacado.
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Puedo editar mi publicación después de publicarla?</div>
          <p style="margin-top: 8px;">
            Sí, puedes editar tu publicación en cualquier momento desde "Mis Publicaciones".
          </p>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">¿Qué hago si encuentro una publicación fraudulenta?</div>
          <p style="margin-top: 8px;">
            Repórtala inmediatamente a través de nuestro <a href="<?php echo BASE_URL; ?>/denuncias">Canal de Denuncias</a>.
          </p>
        </div>

        <p style="margin-top: 20px;">
          <a href="<?php echo BASE_URL; ?>/preguntas-frecuentes">Ver todas las preguntas frecuentes →</a>
        </p>
      </section>

    </div>
  </div>
</main>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
