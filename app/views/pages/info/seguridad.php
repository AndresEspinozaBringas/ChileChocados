<?php
/**
 * Vista: Consejos de Seguridad
 * Ruta: /seguridad
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Consejos de Seguridad</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Consejos de Seguridad</div>
    <p class="meta" style="margin-top: 8px;">Compra y vende de forma segura en ChileChocados</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <!-- Introducción -->
      <section style="margin-bottom: 40px;">
        <p style="font-size: 16px;">
          <strong>🛡️ Tu seguridad es nuestra prioridad.</strong> En ChileChocados trabajamos para 
          crear un ambiente seguro, pero tú también debes tomar precauciones. Lee estos consejos 
          antes de realizar cualquier transacción.
        </p>
      </section>

      <!-- Seguridad General -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          🔐 Seguridad General
        </div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">1. Verifica la identidad del usuario</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Solicita cédula de identidad antes de cualquier transacción</li>
            <li>Verifica que el nombre coincida con la documentación del vehículo</li>
            <li>Desconfía de usuarios recién registrados sin historial</li>
            <li>Revisa las calificaciones y comentarios de otros usuarios</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">2. Comunícate solo por la plataforma</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Usa nuestro sistema de mensajería interno</li>
            <li>Mantén un registro de todas las conversaciones</li>
            <li>Si te piden salir de la plataforma inmediatamente, es una señal de alerta</li>
            <li>Nunca compartas información bancaria por mensaje</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">3. Reúnete en lugares seguros</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Prefiere lugares públicos con cámaras de seguridad</li>
            <li>Reúnete durante el día (9:00 - 18:00 hrs)</li>
            <li>Lleva a alguien contigo si es posible</li>
            <li>Informa a un familiar sobre tu ubicación</li>
            <li>Si te sientes inseguro, cancela la reunión</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p>
            <strong>⚠️ Lugares recomendados:</strong> Estacionamientos de centros comerciales, 
            frente a comisarías, estaciones de servicio concurridas, o áreas públicas bien iluminadas.
          </p>
        </div>
      </section>

      <!-- Para Compradores -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          🛒 Seguridad para Compradores
        </div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">Antes de comprar</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>NUNCA compres sin ver el vehículo en persona</strong></li>
            <li>Desconfía de precios demasiado bajos (si parece demasiado bueno, probablemente lo sea)</li>
            <li>No envíes dinero por adelantado bajo ninguna circunstancia</li>
            <li>Solicita el máximo de información y fotos antes de la visita</li>
            <li>Verifica que el vendedor sea el propietario legítimo</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Durante la inspección</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Lleva a un mecánico de confianza si es posible</li>
            <li>Revisa que el número de chasis coincida con los documentos</li>
            <li>Toma fotos de todo (vehículo, documentos, vendedor)</li>
            <li>No te dejes presionar para decidir rápidamente</li>
            <li>Si algo no cuadra, aléjate de la negociación</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Al pagar</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Prefiere transferencias bancarias registradas</li>
            <li>Si pagas en efectivo, hazlo en un banco o lugar seguro</li>
            <li>Recibe un recibo firmado por el pago completo</li>
            <li>No entregues dinero hasta tener todos los documentos</li>
            <li>Firma un contrato de compraventa antes de pagar</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <div class="h3">🚨 SEÑALES DE FRAUDE</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Vendedor presiona para cerrar el trato inmediatamente</li>
            <li>Precio excesivamente bajo sin explicación clara</li>
            <li>Se niega a mostrar documentación o dar información</li>
            <li>Pide pago anticipado o depósito "para reservar"</li>
            <li>Solo acepta efectivo o métodos de pago no rastreables</li>
            <li>Historias inconsistentes sobre el origen del vehículo</li>
            <li>No permite llevar mecánico o hacer revisión completa</li>
            <li>Documentación sospechosa o alterada</li>
          </ul>
        </div>
      </section>

      <!-- Para Vendedores -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          💼 Seguridad para Vendedores
        </div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">Al publicar</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>No incluyas tu número de teléfono o dirección en la publicación</li>
            <li>Usa solo el sistema de mensajería de ChileChocados</li>
            <li>Sé transparente sobre el estado del vehículo</li>
            <li>No des información bancaria públicamente</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Al coordinar visitas</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Verifica la identidad del comprador antes de reunirte</li>
            <li>No estés solo durante la inspección</li>
            <li>No permitas que el comprador se lleve el vehículo "a prueba"</li>
            <li>Guarda objetos de valor y documentos importantes</li>
            <li>Si te sientes amenazado, termina la reunión y llama a Carabineros</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Al recibir el pago</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Confirma la transferencia bancaria antes de entregar el vehículo</li>
            <li>Si aceptas efectivo, cuenta el dinero en el momento</li>
            <li>No aceptes cheques (pueden rebotar)</li>
            <li>Entrega el vehículo solo cuando el pago esté confirmado</li>
            <li>Firma un contrato de compraventa y entrega copia al comprador</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <div class="h3">🚨 SEÑALES DE FRAUDE</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Comprador ofrece pagar más del precio pedido</li>
            <li>Quiere "reservar" con pago adelantado sin ver el vehículo</li>
            <li>Insiste en pagar con cheques o métodos no verificables</li>
            <li>Pide que envíes el vehículo antes de recibir el pago</li>
            <li>Ofrece pago internacional o en moneda extranjera</li>
            <li>Actúa con urgencia extrema sin razón aparente</li>
            <li>Se niega a firmar documentación formal</li>
          </ul>
        </div>
      </section>

      <!-- Documentación -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          📄 Documentación Segura
        </div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">Documentos que DEBES verificar</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Padrón o constancia de inscripción del vehículo</li>
            <li>Cédula de identidad del vendedor (debe coincidir con el padrón)</li>
            <li>Certificado de anotaciones vigentes (sin multas ni prendas)</li>
            <li>Informe de la aseguradora (si está disponible)</li>
            <li>Revisión técnica (si el vehículo está en condiciones de circular)</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">Cómo verificar la documentación</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Compara el número de chasis del vehículo con el del padrón</li>
            <li>Verifica que la foto del padrón coincida con el vehículo</li>
            <li>Revisa que no haya alteraciones en los documentos</li>
            <li>Consulta el estado del vehículo en el Registro Civil online</li>
            <li>Si tienes dudas, consulta con un abogado o gestor vehicular</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p>
            <strong>💡 Recurso útil:</strong> Puedes verificar el estado de un vehículo en 
            <a href="https://www.registrocivil.cl" target="_blank">www.registrocivil.cl</a>
          </p>
        </div>
      </section>

      <!-- Pagos Seguros -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          💳 Métodos de Pago Seguros
        </div>
        
        <div style="margin-bottom: 24px;">
          <div class="h3">✅ MÉTODOS SEGUROS</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Transferencia bancaria:</strong> Deja registro y es rastreable</li>
            <li><strong>Efectivo en persona:</strong> Solo en lugares seguros y con testigos</li>
            <li><strong>Pago en banco:</strong> Ambas partes van al banco juntas</li>
          </ul>
        </div>

        <div style="margin-bottom: 24px;">
          <div class="h3">❌ MÉTODOS RIESGOSOS</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Cheques:</strong> Pueden rebotar o ser falsos</li>
            <li><strong>Pagos diferidos:</strong> Riesgo de impago</li>
            <li><strong>Criptomonedas:</strong> Difíciles de rastrear y reversibles</li>
            <li><strong>Giros internacionales:</strong> Altas comisiones y riesgo de fraude</li>
            <li><strong>Aplicaciones de terceros desconocidas:</strong> Sin respaldo</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p>
            <strong>⚠️ Regla de oro:</strong> NO entregues el vehículo antes de confirmar que recibiste 
            el pago completo. NO pagues antes de tener el vehículo y los documentos en tu poder.
          </p>
        </div>
      </section>

      <!-- Reportar Problemas -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          🚨 ¿Detectaste algo sospechoso?
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Reporta inmediatamente si:</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Sospechas que una publicación es fraudulenta</li>
            <li>Un usuario te solicita información bancaria</li>
            <li>Te piden salir de la plataforma para negociar</li>
            <li>Recibes amenazas o mensajes inapropiados</li>
            <li>Detectas documentación falsa o alterada</li>
            <li>Fuiste víctima de un fraude o intento de fraude</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Cómo reportar</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>En la plataforma:</strong> Usa el botón "Reportar" en cada publicación o perfil</li>
            <li><strong>Por email:</strong> <a href="mailto:seguridad@chilechocados.cl">seguridad@chilechocados.cl</a></li>
            <li><strong>Formulario:</strong> <a href="<?php echo BASE_URL; ?>/reportar">Ir al formulario de reporte</a></li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p>
            <strong>✅ Tu reporte nos ayuda:</strong> Cada reporte nos permite identificar y bloquear 
            usuarios fraudulentos, manteniendo la plataforma segura para todos.
          </p>
        </div>
      </section>

      <!-- Contactos de Emergencia -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          📞 Contactos de Emergencia
        </div>
        
        <p style="margin-bottom: 16px; font-weight: 600;">
          Si te sientes en peligro o fuiste víctima de un delito:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
          <li><strong>Carabineros de Chile:</strong> 133</li>
          <li><strong>Policía de Investigaciones (PDI):</strong> 134</li>
          <li><strong>Emergencias:</strong> 131</li>
          <li><strong>Fiscalía - Denuncias online:</strong> <a href="https://www.fiscaliadechile.cl" target="_blank">www.fiscaliadechile.cl</a></li>
        </ul>
      </section>

      <!-- Consejos Adicionales -->
      <section style="margin-bottom: 32px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          💡 Consejos Adicionales
        </div>
        
        <ul style="margin-left: 20px; line-height: 1.8;">
          <li>Confía en tu instinto - si algo no se siente bien, probablemente no lo es</li>
          <li>No compartas información personal innecesaria</li>
          <li>Toma tu tiempo - las buenas oportunidades pueden esperar</li>
          <li>Investiga al usuario - revisa su historial y calificaciones</li>
          <li>Documenta todo - fotos, mensajes, recibos</li>
          <li>Lee nuestros <a href="<?php echo BASE_URL; ?>/terminos">Términos y Condiciones</a></li>
          <li>Conoce tus derechos como consumidor</li>
        </ul>
      </section>

      <div style="margin-top: 32px;">
        <p style="font-size: 16px; text-align: center; font-weight: 600;">
          🛡️ Recuerda: La seguridad es responsabilidad de todos. Sigue estos consejos y 
          ayúdanos a mantener ChileChocados como una comunidad segura y confiable.
        </p>
      </div>

    </div>
  </div>
</main>

<style>
/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] .breadcrumbs {
  color: #9CA3AF;
}

:root[data-theme="dark"] .breadcrumbs a {
  color: var(--cc-primary);
}

:root[data-theme="dark"] .breadcrumbs span {
  color: #D1D5DB;
}

:root[data-theme="dark"] .card {
  background: #1F2937 !important;
  border-color: #374151 !important;
}

:root[data-theme="dark"] .h1,
:root[data-theme="dark"] .h2,
:root[data-theme="dark"] .h3,
:root[data-theme="dark"] .h4 {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .meta {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] p {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] li {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] strong {
  color: #F3F4F6;
}

:root[data-theme="dark"] a {
  color: var(--cc-primary);
}

:root[data-theme="dark"] a:hover {
  color: #c72a22;
}

/* Títulos con colores específicos */
:root[data-theme="dark"] .h2[style*="color: var(--cc-primary)"],
:root[data-theme="dark"] div[style*="color: var(--cc-primary)"] {
  color: var(--cc-primary) !important;
}

/* Textos con colores específicos */
:root[data-theme="dark"] p[style*="font-size: 16px"] {
  color: #D1D5DB !important;
}
</style>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
