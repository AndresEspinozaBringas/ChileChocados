<?php
/**
 * Vista: Política de Cookies
 * Ruta: /cookies
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Política de Cookies</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Política de Cookies</div>
    <p class="meta" style="margin-top: 8px;">Última actualización: Noviembre 2025</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <section style="margin-bottom: 32px;">
        <div class="h2">1. ¿Qué son las Cookies?</div>
        <p style="margin-top: 12px;">
          Las cookies son pequeños archivos de texto que se almacenan en su dispositivo (computadora, tablet o móvil) 
          cuando visita un sitio web. Permiten que el sitio recuerde sus acciones y preferencias durante un período de tiempo.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">2. ¿Cómo Usamos las Cookies?</div>
        <p style="margin-top: 12px;">
          En ChileChocados utilizamos cookies para:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Mantener su sesión iniciada mientras navega</li>
          <li>Recordar sus preferencias (idioma, tema, etc.)</li>
          <li>Analizar cómo usa nuestro sitio para mejorarlo</li>
          <li>Personalizar el contenido que ve</li>
          <li>Proteger contra fraudes y mejorar la seguridad</li>
        </ul>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">3. Tipos de Cookies que Utilizamos</div>
        
        <div style="margin-top: 16px;">
          <p><strong>3.1. Cookies Esenciales</strong></p>
          <p style="margin-top: 8px;">
            Son necesarias para el funcionamiento básico del sitio. Sin estas cookies, no podríamos proporcionar 
            servicios como inicio de sesión o carrito de compras.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li><strong>Sesión de usuario:</strong> Mantiene su sesión activa</li>
            <li><strong>CSRF Token:</strong> Protege contra ataques de falsificación</li>
            <li><strong>Preferencias:</strong> Guarda configuraciones básicas</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>3.2. Cookies de Funcionalidad</strong></p>
          <p style="margin-top: 8px;">
            Permiten que el sitio recuerde sus elecciones y proporcione características mejoradas.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li><strong>Tema:</strong> Recuerda si prefiere modo claro u oscuro</li>
            <li><strong>Idioma:</strong> Guarda su preferencia de idioma</li>
            <li><strong>Filtros:</strong> Recuerda sus filtros de búsqueda</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>3.3. Cookies Analíticas</strong></p>
          <p style="margin-top: 8px;">
            Nos ayudan a entender cómo los visitantes interactúan con nuestro sitio.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li><strong>Google Analytics:</strong> Analiza el tráfico y comportamiento</li>
            <li><strong>Métricas de rendimiento:</strong> Mide tiempos de carga</li>
            <li><strong>Mapas de calor:</strong> Visualiza interacciones de usuarios</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>3.4. Cookies de Marketing</strong></p>
          <p style="margin-top: 8px;">
            Se utilizan para mostrar anuncios relevantes y medir la efectividad de campañas.
          </p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li><strong>Publicidad dirigida:</strong> Muestra anuncios relevantes</li>
            <li><strong>Redes sociales:</strong> Permite compartir contenido</li>
            <li><strong>Remarketing:</strong> Muestra anuncios en otros sitios</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">4. Cookies de Terceros</div>
        <p style="margin-top: 12px;">
          Algunos servicios de terceros que utilizamos también pueden establecer cookies:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><strong>Google Analytics:</strong> Para análisis de tráfico</li>
          <li><strong>Facebook Pixel:</strong> Para publicidad en redes sociales</li>
          <li><strong>Flow:</strong> Para procesar pagos</li>
          <li><strong>Lucide Icons:</strong> Para mostrar iconos</li>
        </ul>
        <p style="margin-top: 8px;">
          Estas cookies están sujetas a las políticas de privacidad de los respectivos terceros.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">5. Duración de las Cookies</div>
        
        <p style="margin-top: 12px;"><strong>Cookies de Sesión:</strong></p>
        <p style="margin-top: 4px;">
          Se eliminan automáticamente cuando cierra su navegador.
        </p>

        <p style="margin-top: 12px;"><strong>Cookies Persistentes:</strong></p>
        <p style="margin-top: 4px;">
          Permanecen en su dispositivo durante un período específico o hasta que las elimine manualmente.
        </p>
        
        <table style="width: 100%; margin-top: 16px; border-collapse: collapse;">
          <thead>
            <tr style="background: #f5f5f5;">
              <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Cookie</th>
              <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Duración</th>
              <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Propósito</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="padding: 12px; border: 1px solid #ddd;">session_id</td>
              <td style="padding: 12px; border: 1px solid #ddd;">Sesión</td>
              <td style="padding: 12px; border: 1px solid #ddd;">Mantener sesión activa</td>
            </tr>
            <tr>
              <td style="padding: 12px; border: 1px solid #ddd;">theme</td>
              <td style="padding: 12px; border: 1px solid #ddd;">1 año</td>
              <td style="padding: 12px; border: 1px solid #ddd;">Recordar tema (claro/oscuro)</td>
            </tr>
            <tr>
              <td style="padding: 12px; border: 1px solid #ddd;">cookie_consent</td>
              <td style="padding: 12px; border: 1px solid #ddd;">1 año</td>
              <td style="padding: 12px; border: 1px solid #ddd;">Guardar preferencia de cookies</td>
            </tr>
            <tr>
              <td style="padding: 12px; border: 1px solid #ddd;">_ga</td>
              <td style="padding: 12px; border: 1px solid #ddd;">2 años</td>
              <td style="padding: 12px; border: 1px solid #ddd;">Google Analytics</td>
            </tr>
          </tbody>
        </table>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">6. Cómo Gestionar las Cookies</div>
        <p style="margin-top: 12px;">
          Puede controlar y/o eliminar las cookies según desee. Tiene las siguientes opciones:
        </p>

        <p style="margin-top: 16px;"><strong>6.1. Configuración del Navegador</strong></p>
        <p style="margin-top: 8px;">
          La mayoría de los navegadores permiten:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Ver qué cookies están almacenadas</li>
          <li>Eliminar cookies individualmente o todas</li>
          <li>Bloquear cookies de sitios específicos</li>
          <li>Bloquear todas las cookies de terceros</li>
          <li>Eliminar todas las cookies al cerrar el navegador</li>
        </ul>

        <p style="margin-top: 16px;"><strong>6.2. Enlaces de Ayuda por Navegador:</strong></p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Google Chrome</a></li>
          <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="noopener">Mozilla Firefox</a></li>
          <li><a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a></li>
          <li><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-las-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener">Microsoft Edge</a></li>
        </ul>

        <p style="margin-top: 16px;"><strong>6.3. Herramientas de Exclusión:</strong></p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out</a></li>
          <li><a href="https://www.facebook.com/help/568137493302217" target="_blank" rel="noopener">Facebook Ads Preferences</a></li>
        </ul>

        <p style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
          <strong>⚠️ Nota:</strong> Si bloquea o elimina cookies, algunas funciones del sitio pueden no funcionar correctamente.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">7. Actualizaciones de esta Política</div>
        <p style="margin-top: 12px;">
          Podemos actualizar esta Política de Cookies ocasionalmente para reflejar cambios en las cookies que utilizamos 
          o por razones operativas, legales o regulatorias. Le recomendamos revisar esta página periódicamente.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">8. Más Información</div>
        <p style="margin-top: 12px;">
          Para más información sobre cómo protegemos su privacidad, consulte nuestra 
          <a href="<?php echo BASE_URL; ?>/privacidad">Política de Privacidad</a>.
        </p>
        <p style="margin-top: 12px;">
          Si tiene preguntas sobre nuestra Política de Cookies, contáctenos en:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><strong>Email:</strong> <a href="mailto:privacidad@chilechocados.cl">privacidad@chilechocados.cl</a></li>
          <li><strong>Teléfono:</strong> +56 9 1234 5678</li>
        </ul>
      </section>

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
:root[data-theme="dark"] .h3 {
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

/* Tablas */
:root[data-theme="dark"] table {
  border-color: #374151 !important;
}

:root[data-theme="dark"] thead tr {
  background: #374151 !important;
}

:root[data-theme="dark"] th,
:root[data-theme="dark"] td {
  border-color: #4B5563 !important;
  color: #D1D5DB !important;
}

/* Cajas de información */
:root[data-theme="dark"] div[style*="background: #DBEAFE"] {
  background: rgba(59, 130, 246, 0.15) !important;
  border-left-color: #3B82F6 !important;
}

:root[data-theme="dark"] div[style*="background: #DBEAFE"] p {
  color: #93C5FD !important;
}

:root[data-theme="dark"] div[style*="background: #FEF3C7"] {
  background: rgba(245, 158, 11, 0.15) !important;
  border-left-color: #F59E0B !important;
}

:root[data-theme="dark"] div[style*="background: #FEF3C7"] p {
  color: #FCD34D !important;
}

:root[data-theme="dark"] div[style*="background: #FEE2E2"] {
  background: rgba(239, 68, 68, 0.15) !important;
  border-left-color: #EF4444 !important;
}

:root[data-theme="dark"] div[style*="background: #FEE2E2"] p {
  color: #FCA5A5 !important;
}

:root[data-theme="dark"] div[style*="background: #F9FAFB"] {
  background: #374151 !important;
}

:root[data-theme="dark"] div[style*="background: #FEF2F2"] {
  background: rgba(239, 68, 68, 0.1) !important;
}

:root[data-theme="dark"] div[style*="background: #DCFCE7"] {
  background: rgba(16, 185, 129, 0.15) !important;
}

:root[data-theme="dark"] div[style*="background: #DCFCE7"] p {
  color: #6EE7B7 !important;
}

:root[data-theme="dark"] div[style*="background: #fff3cd"] {
  background: rgba(245, 158, 11, 0.15) !important;
  border-left-color: #F59E0B !important;
}

:root[data-theme="dark"] div[style*="background: #fff3cd"] p {
  color: #FCD34D !important;
}

:root[data-theme="dark"] div[style*="background: #f5f5f5"] {
  background: #374151 !important;
}

/* Títulos con colores específicos */
:root[data-theme="dark"] .h2[style*="color: var(--cc-primary)"],
:root[data-theme="dark"] div[style*="color: var(--cc-primary)"] {
  color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .h2[style*="color: #EF4444"],
:root[data-theme="dark"] div[style*="color: #EF4444"] {
  color: #EF4444 !important;
}

/* Textos con colores específicos */
:root[data-theme="dark"] p[style*="color: #555"] {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] p[style*="color: #92400E"] {
  color: #FCD34D !important;
}

:root[data-theme="dark"] p[style*="color: #1E40AF"] {
  color: #93C5FD !important;
}

:root[data-theme="dark"] p[style*="color: #991B1B"] {
  color: #FCA5A5 !important;
}

:root[data-theme="dark"] p[style*="color: #166534"] {
  color: #6EE7B7 !important;
}

:root[data-theme="dark"] p[style*="color: #666"] {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] small {
  color: #9CA3AF !important;
}
</style>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
