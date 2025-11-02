<?php
/**
 * Vista: Canal de Denuncias
 * Ruta: /denuncias
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Canal de Denuncias</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Canal de Denuncias</div>
    <p class="meta" style="margin-top: 8px;">Última actualización: Noviembre 2025</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <section style="margin-bottom: 32px;">
        <div class="h2">1. Propósito del Canal</div>
        <p style="margin-top: 12px;">
          El Canal de Denuncias de ChileChocados es un medio confidencial y seguro para reportar:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Conductas ilegales o fraudulentas</li>
          <li>Violaciones a nuestros Términos y Condiciones</li>
          <li>Publicaciones engañosas o falsas</li>
          <li>Comportamiento abusivo o acoso</li>
          <li>Uso indebido de la plataforma</li>
          <li>Problemas de seguridad o privacidad</li>
        </ul>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">2. ¿Qué Puedes Denunciar?</div>
        
        <div style="margin-top: 16px;">
          <p><strong>2.1. Publicaciones Fraudulentas</strong></p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Vehículos que no existen o información falsa</li>
            <li>Fotos que no corresponden al vehículo</li>
            <li>Precios engañosos o estafas</li>
            <li>Documentación falsificada</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>2.2. Comportamiento Inapropiado</strong></p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Acoso o amenazas a otros usuarios</li>
            <li>Lenguaje ofensivo o discriminatorio</li>
            <li>Spam o publicidad no autorizada</li>
            <li>Suplantación de identidad</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>2.3. Problemas de Seguridad</strong></p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Vulnerabilidades de seguridad</li>
            <li>Accesos no autorizados</li>
            <li>Robo de información</li>
            <li>Malware o virus</li>
          </ul>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>2.4. Violaciones Legales</strong></p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li>Venta de vehículos robados</li>
            <li>Lavado de dinero</li>
            <li>Evasión de impuestos</li>
            <li>Cualquier actividad ilegal</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">3. Cómo Realizar una Denuncia</div>
        
        <div style="margin-top: 16px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #E6332A;">
          <p><strong>Formulario de Denuncia</strong></p>
          <p style="margin-top: 8px;">
            Complete el siguiente formulario con la mayor cantidad de detalles posible:
          </p>
          
          <form action="<?php echo BASE_URL; ?>/denuncias/enviar" method="POST" style="margin-top: 16px;">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div style="margin-bottom: 16px;">
              <label style="display: block; font-weight: 600; margin-bottom: 8px;">Tipo de Denuncia *</label>
              <select name="tipo" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Seleccione...</option>
                <option value="publicacion_fraudulenta">Publicación Fraudulenta</option>
                <option value="comportamiento_inapropiado">Comportamiento Inapropiado</option>
                <option value="problema_seguridad">Problema de Seguridad</option>
                <option value="violacion_legal">Violación Legal</option>
                <option value="otro">Otro</option>
              </select>
            </div>

            <div style="margin-bottom: 16px;">
              <label style="display: block; font-weight: 600; margin-bottom: 8px;">URL o ID de la Publicación (si aplica)</label>
              <input type="text" name="url" placeholder="https://chilechocados.cl/publicacion/123" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 16px;">
              <label style="display: block; font-weight: 600; margin-bottom: 8px;">Descripción Detallada *</label>
              <textarea name="descripcion" required rows="6" placeholder="Describa la situación con el mayor detalle posible..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
            </div>

            <div style="margin-bottom: 16px;">
              <label style="display: block; font-weight: 600; margin-bottom: 8px;">Su Email (opcional)</label>
              <input type="email" name="email" placeholder="su@email.com" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
              <small style="display: block; margin-top: 4px; color: #666;">
                Si desea recibir seguimiento de su denuncia
              </small>
            </div>

            <div style="margin-bottom: 16px;">
              <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                <input type="checkbox" name="anonimo" value="1" style="margin-right: 8px;">
                Enviar de forma anónima
              </label>
            </div>

            <button type="submit" class="btn primary" style="padding: 12px 24px; background: #E6332A; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
              Enviar Denuncia
            </button>
          </form>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>Otros Canales:</strong></p>
          <ul style="margin-left: 20px; margin-top: 8px;">
            <li><strong>Email:</strong> <a href="mailto:denuncias@chilechocados.cl">denuncias@chilechocados.cl</a></li>
            <li><strong>WhatsApp:</strong> +56 9 1234 5678 (solo denuncias urgentes)</li>
            <li><strong>Teléfono:</strong> +56 2 1234 5678 (horario de oficina)</li>
          </ul>
        </div>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">4. Confidencialidad y Anonimato</div>
        <p style="margin-top: 12px;">
          Garantizamos:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><strong>Confidencialidad:</strong> Su identidad será protegida en todo momento</li>
          <li><strong>Anonimato:</strong> Puede realizar denuncias sin proporcionar sus datos</li>
          <li><strong>No represalias:</strong> No habrá consecuencias negativas por denunciar de buena fe</li>
          <li><strong>Protección de datos:</strong> Su información será tratada según nuestra Política de Privacidad</li>
        </ul>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">5. Proceso de Investigación</div>
        <p style="margin-top: 12px;">
          Una vez recibida su denuncia:
        </p>
        <ol style="margin-left: 20px; margin-top: 8px;">
          <li style="margin-bottom: 8px;">
            <strong>Recepción:</strong> Confirmamos la recepción de su denuncia (si proporcionó email)
          </li>
          <li style="margin-bottom: 8px;">
            <strong>Evaluación:</strong> Nuestro equipo evalúa la gravedad y urgencia
          </li>
          <li style="margin-bottom: 8px;">
            <strong>Investigación:</strong> Investigamos los hechos reportados
          </li>
          <li style="margin-bottom: 8px;">
            <strong>Acción:</strong> Tomamos las medidas apropiadas según el caso
          </li>
          <li style="margin-bottom: 8px;">
            <strong>Seguimiento:</strong> Le informamos del resultado (si proporcionó email)
          </li>
        </ol>
        <p style="margin-top: 12px;">
          <strong>Tiempo de respuesta:</strong> Nos comprometemos a responder en un plazo máximo de 5 días hábiles.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">6. Medidas que Podemos Tomar</div>
        <p style="margin-top: 12px;">
          Dependiendo de la gravedad de la denuncia, podemos:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Advertir al usuario infractor</li>
          <li>Suspender temporalmente la cuenta</li>
          <li>Eliminar publicaciones o contenido</li>
          <li>Suspender permanentemente la cuenta</li>
          <li>Reportar a las autoridades competentes</li>
          <li>Tomar acciones legales</li>
        </ul>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">7. Denuncias Falsas</div>
        <p style="margin-top: 12px;">
          Las denuncias falsas o malintencionadas:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Perjudican a usuarios inocentes</li>
          <li>Desperdician recursos de investigación</li>
          <li>Pueden resultar en acciones legales contra el denunciante</li>
          <li>Pueden llevar a la suspensión de su cuenta</li>
        </ul>
        <p style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
          <strong>⚠️ Importante:</strong> Solo realice denuncias si tiene motivos razonables para creer que existe una violación.
        </p>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">8. Protección Legal</div>
        <p style="margin-top: 12px;">
          Este canal está protegido por:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li>Ley N° 20.393 sobre Responsabilidad Penal de las Personas Jurídicas</li>
          <li>Ley N° 19.628 sobre Protección de la Vida Privada</li>
          <li>Código Penal de Chile</li>
          <li>Normativas de protección al denunciante</li>
        </ul>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">9. Preguntas Frecuentes</div>
        
        <div style="margin-top: 16px;">
          <p><strong>¿Puedo denunciar de forma anónima?</strong></p>
          <p style="margin-top: 4px;">
            Sí, puede realizar denuncias sin proporcionar su identidad. Sin embargo, proporcionar un email 
            nos permite darle seguimiento y solicitar información adicional si es necesario.
          </p>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>¿Cuánto tiempo toma la investigación?</strong></p>
          <p style="margin-top: 4px;">
            Depende de la complejidad del caso. Casos simples pueden resolverse en 1-2 días, mientras que 
            casos complejos pueden tomar hasta 2 semanas.
          </p>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>¿Qué pasa si mi denuncia es rechazada?</strong></p>
          <p style="margin-top: 4px;">
            Le informaremos las razones del rechazo. Si tiene nueva evidencia, puede presentar una nueva denuncia.
          </p>
        </div>

        <div style="margin-top: 16px;">
          <p><strong>¿Puedo denunciar a un usuario específico?</strong></p>
          <p style="margin-top: 4px;">
            Sí, puede denunciar comportamientos específicos de usuarios. Proporcione el ID de usuario o 
            enlaces a sus publicaciones.
          </p>
        </div>
      </section>

      <section style="margin-bottom: 32px;">
        <div class="h2">10. Contacto</div>
        <p style="margin-top: 12px;">
          Para consultas sobre el Canal de Denuncias:
        </p>
        <ul style="margin-left: 20px; margin-top: 8px;">
          <li><strong>Email:</strong> <a href="mailto:denuncias@chilechocados.cl">denuncias@chilechocados.cl</a></li>
          <li><strong>Teléfono:</strong> +56 2 1234 5678</li>
          <li><strong>Horario:</strong> Lunes a Viernes, 9:00 - 18:00</li>
        </ul>
      </section>

    </div>
  </div>
</main>

<?php
// Incluir footer
require_once APP_PATH . '/views/layouts/footer.php';
?>
