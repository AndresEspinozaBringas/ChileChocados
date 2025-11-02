<?php
/**
 * Vista: Guía del Vendedor
 * Ruta: /guia-vendedor
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Guía del Vendedor</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Guía del Vendedor</div>
    <p class="meta" style="margin-top: 8px;">Aprende a vender tu vehículo siniestrado de forma efectiva y segura</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <!-- Introducción -->
      <section style="margin-bottom: 40px;">
        <p style="font-size: 18px; color: #555;">
          Vender un vehículo siniestrado requiere transparencia, buena presentación y conocimiento del mercado. 
          Esta guía te ayudará a maximizar tus oportunidades de venta y a realizar transacciones seguras.
        </p>
      </section>

      <!-- Paso 1 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          1️⃣ Prepara tu Vehículo
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Limpieza básica</div>
          <p style="margin-top: 8px;">
            Aunque esté dañado, un vehículo limpio vende mejor:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Lava el exterior para que los daños sean visibles claramente</li>
            <li>Aspira el interior y limpia superficies</li>
            <li>Retira objetos personales</li>
            <li>Limpia el motor superficialmente (sin agua a presión)</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Reúne la documentación</div>
          <p style="margin-top: 8px;">
            Tener los papeles en orden genera confianza:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Padrón o constancia de inscripción del vehículo</li>
            <li>Informe de la aseguradora (si lo tienes)</li>
            <li>Historial de mantenciones (si está disponible)</li>
            <li>Revisión técnica vigente (si aplica)</li>
            <li>Cédula de identidad del propietario</li>
          </ul>
        </div>

        <div style="padding: 16px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; margin-top: 16px;">
          <p style="margin: 0; color: #1E40AF;">
            <strong>💡 Tip:</strong> Un vehículo con documentación completa y clara puede venderse 
            hasta un 30% más rápido que uno sin papeles en orden.
          </p>
        </div>
      </section>

      <!-- Paso 2 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          2️⃣ Determina el Precio Correcto
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Investiga el mercado</div>
          <p style="margin-top: 8px;">
            Antes de poner precio:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Busca vehículos similares en ChileChocados</li>
            <li>Revisa el valor de mercado del mismo modelo en buen estado</li>
            <li>Considera el porcentaje de daño (leve: 60-80%, moderado: 40-60%, severo: 20-40% del valor original)</li>
            <li>Investiga precios de repuestos si tu vehículo es para desarme</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Factores que afectan el precio</div>
          <div style="background: #F9FAFB; padding: 16px; border-radius: 8px; margin-top: 12px;">
            <p style="margin-bottom: 12px;"><strong>Aumentan el valor:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>✅ Motor y transmisión funcionales</li>
              <li>✅ Daños solo cosméticos</li>
              <li>✅ Marca y modelo demandados</li>
              <li>✅ Bajo kilometraje</li>
              <li>✅ Documentación completa</li>
              <li>✅ Posibilidad de reparación económica</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 12px;"><strong>Disminuyen el valor:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>❌ Daño estructural severo</li>
              <li>❌ Vehículo inundado o quemado</li>
              <li>❌ Motor fundido</li>
              <li>❌ Documentación incompleta</li>
              <li>❌ Partes faltantes o robadas</li>
              <li>❌ Multas pendientes</li>
            </ul>
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Estrategia de precio</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Fija un precio ligeramente superior al mínimo que aceptarías</li>
            <li>Deja margen para negociación (10-15%)</li>
            <li>Sé realista: un precio muy alto ahuyenta compradores</li>
            <li>Considera indicar "Precio negociable" si estás abierto a ofertas</li>
          </ul>
        </div>
      </section>

      <!-- Paso 3 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          3️⃣ Crea una Publicación Efectiva
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Fotografías de calidad</div>
          <p style="margin-top: 8px;">
            Las fotos son cruciales - usa todas las 6 disponibles:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Foto 1:</strong> Vista frontal completa</li>
            <li><strong>Foto 2:</strong> Vista lateral (lado dañado)</li>
            <li><strong>Foto 3:</strong> Vista trasera</li>
            <li><strong>Foto 4:</strong> Interior/tablero</li>
            <li><strong>Foto 5:</strong> Motor</li>
            <li><strong>Foto 6:</strong> Detalles del daño específico</li>
          </ul>
          
          <div style="padding: 12px; background: #FEF3C7; border-radius: 4px; margin-top: 12px;">
            <p style="margin: 0; font-size: 14px; color: #92400E;">
              <strong>Consejo fotográfico:</strong> Toma fotos durante el día con buena luz natural. 
              Evita filtros o ediciones que oculten el estado real.
            </p>
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Título atractivo</div>
          <p style="margin-top: 8px;">
            Ejemplos de buenos títulos:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>✅ "Chevrolet Sail 2018 - Choque Frontal Leve - Motor OK"</li>
            <li>✅ "Nissan Versa 2020 - Lateral Derecho - Mecánica Perfecta"</li>
            <li>✅ "Kia Morning 2019 - Para Repuestos - Completo"</li>
            <li>❌ "Auto chocado" (muy vago)</li>
            <li>❌ "APROVECHE!!!! URGENTE!!!!" (spam)</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Descripción completa y honesta</div>
          <p style="margin-top: 8px;">
            Tu descripción debe incluir:
          </p>
          
          <div style="background: #F9FAFB; padding: 16px; border-radius: 8px; margin-top: 12px;">
            <p style="margin-bottom: 8px;"><strong>Información básica:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>Marca, modelo, año</li>
              <li>Kilometraje real</li>
              <li>Color</li>
              <li>Tipo de transmisión</li>
              <li>Tipo de combustible</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 8px;"><strong>Sobre el daño:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>Cómo ocurrió el siniestro</li>
              <li>Partes afectadas específicamente</li>
              <li>¿Los airbags se dispararon?</li>
              <li>¿Hubo daño estructural?</li>
              <li>Partes que SÍ funcionan correctamente</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 8px;"><strong>Estado mecánico:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>¿Enciende el motor?</li>
              <li>¿Está funcional la transmisión?</li>
              <li>Estado de neumáticos</li>
              <li>Última mantención realizada</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 8px;"><strong>Documentación:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>¿Tiene padrón?</li>
              <li>¿Es transferible?</li>
              <li>¿Tiene multas pendientes?</li>
              <li>¿Cuenta con informe de aseguradora?</li>
            </ul>
          </div>
        </div>

        <div style="padding: 16px; background: #FEE2E2; border-left: 4px solid #EF4444; border-radius: 4px; margin-top: 16px;">
          <p style="margin: 0; color: #991B1B;">
            <strong>⚠️ Importante:</strong> NUNCA ocultes información sobre daños. La transparencia 
            genera confianza y evita problemas legales posteriores.
          </p>
        </div>
      </section>

      <!-- Paso 4 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          4️⃣ Gestiona las Consultas
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Responde rápido y profesionalmente</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Responde mensajes dentro de las primeras 24 horas</li>
            <li>Sé cortés y profesional en todo momento</li>
            <li>Proporciona información adicional cuando te la soliciten</li>
            <li>Envía fotos extras si son necesarias</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Filtra compradores serios</div>
          <p style="margin-top: 8px;">
            Reconoce a compradores genuinos:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>✅ Hacen preguntas específicas sobre el daño</li>
            <li>✅ Preguntan por disponibilidad para revisión presencial</li>
            <li>✅ Solicitan ver documentación</li>
            <li>✅ Proponen reunirse en lugares seguros</li>
            <li>❌ Ofrecen comprar sin ver el vehículo</li>
            <li>❌ Piden que envíes dinero primero</li>
            <li>❌ Presionan para cerrar trato inmediatamente</li>
          </ul>
        </div>
      </section>

      <!-- Paso 5 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          5️⃣ Organiza Visitas Seguras
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Lugar y horario</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Reúnete en lugares públicos durante el día</li>
            <li>Si es en tu domicilio, no estés solo</li>
            <li>Permite que traigan un mecánico para revisión</li>
            <li>Ten todos los documentos listos para mostrar</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Durante la visita</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Sé transparente sobre todos los daños</li>
            <li>Permite una inspección completa</li>
            <li>Si funciona, permite que enciendan el motor</li>
            <li>Muestra toda la documentación</li>
            <li>Responde todas las preguntas honestamente</li>
          </ul>
        </div>

        <div style="padding: 16px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; margin-top: 16px;">
          <p style="margin: 0; color: #1E40AF;">
            <strong>💡 Seguridad:</strong> Informa a un familiar o amigo sobre la visita. 
            Comparte ubicación y datos del comprador potencial.
          </p>
        </div>
      </section>

      <!-- Paso 6 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          6️⃣ Negocia y Cierra la Venta
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Negociación efectiva</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Conoce tu precio mínimo antes de negociar</li>
            <li>Escucha las ofertas sin ofenderte</li>
            <li>Justifica tu precio con hechos (estado mecánico, documentación, etc.)</li>
            <li>Sé flexible pero no regales tu vehículo</li>
            <li>No tengas miedo de rechazar ofertas muy bajas</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Formas de pago seguras</div>
          <p style="margin-top: 8px;">
            Acepta solo:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>✅ Transferencia bancaria (confirmada antes de entregar)</li>
            <li>✅ Efectivo en lugares seguros y con testigos</li>
            <li>❌ Cheques (riesgo de rebote)</li>
            <li>❌ Pagos diferidos o a plazo</li>
            <li>❌ Criptomonedas (difíciles de rastrear)</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Documentación de la venta</div>
          <p style="margin-top: 8px;">
            Prepara y firma:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Contrato de compraventa (2 copias)</li>
            <li>Incluye: datos de ambas partes, descripción del vehículo, precio, fecha</li>
            <li>Declara el estado del vehículo (siniestrado, con daños, etc.)</li>
            <li>Especifica que se vende "en el estado en que se encuentra"</li>
            <li>Ambos firman todas las copias</li>
            <li>Entrega recibo de pago</li>
          </ul>
        </div>
      </section>

      <!-- Paso 7 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          7️⃣ Post-Venta
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Trámites finales</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Entrega todos los documentos originales al comprador</li>
            <li>Acompaña al comprador al Registro Civil para transferencia (opcional pero recomendado)</li>
            <li>Guarda copia del contrato de compraventa</li>
            <li>Marca tu publicación como "Vendido" en ChileChocados</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Protégete legalmente</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Asegúrate de que la transferencia se realice dentro de 30 días</li>
            <li>Si el comprador no transfiere, puedes hacer una denuncia</li>
            <li>Guarda todos los documentos por al menos 1 año</li>
          </ul>
        </div>
      </section>

      <!-- Errores Comunes -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: #EF4444; margin-bottom: 16px;">
          ❌ Errores Comunes a Evitar
        </div>
        
        <div style="background: #FEF2F2; padding: 20px; border-radius: 8px;">
          <ul style="margin-left: 20px; line-height: 2;">
            <li>Ocultar daños o dar información falsa</li>
            <li>Fotos de mala calidad o insuficientes</li>
            <li>Precio demasiado alto sin justificación</li>
            <li>No responder consultas a tiempo</li>
            <li>Entregar el vehículo antes de recibir el pago</li>
            <li>No documentar la venta adecuadamente</li>
            <li>Reunirse en lugares inseguros</li>
            <li>Aceptar métodos de pago riesgosos</li>
          </ul>
        </div>
      </section>

      <!-- Recursos -->
      <section style="margin-bottom: 32px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          📚 Recursos Útiles
        </div>
        
        <ul style="margin-left: 20px; line-height: 1.8;">
          <li><a href="<?php echo BASE_URL; ?>/publicar">Crear Publicación</a> - Publica tu vehículo ahora</li>
          <li><a href="<?php echo BASE_URL; ?>/seguridad">Consejos de Seguridad</a> - Vende de forma segura</li>
          <li><a href="<?php echo BASE_URL; ?>/preguntas-frecuentes">Preguntas Frecuentes</a> - Dudas comunes</li>
          <li><a href="<?php echo BASE_URL; ?>/contacto">Contáctanos</a> - Ayuda personalizada</li>
        </ul>
      </section>

      <div style="margin-top: 32px; padding: 16px; background: #DCFCE7; border-radius: 8px;">
        <p style="margin: 0; font-size: 14px; color: #166534;">
          <strong>✅ ¡Listo para vender!</strong> Sigue estos pasos y vende tu vehículo siniestrado 
          de forma rápida, segura y al mejor precio. ¡Éxito en tu venta!
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
