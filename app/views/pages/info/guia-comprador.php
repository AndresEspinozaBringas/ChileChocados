<?php
/**
 * Vista: Guía del Comprador
 * Ruta: /guia-comprador
 */

// Incluir header
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="breadcrumbs" style="margin-top: 24px;">
    <a href="<?php echo BASE_URL; ?>">Inicio</a> / <span>Guía del Comprador</span>
  </div>

  <div class="card" style="margin-top: 16px;">
    <div class="h1">Guía del Comprador</div>
    <p class="meta" style="margin-top: 8px;">Todo lo que necesitas saber para comprar vehículos siniestrados</p>
    
    <div style="margin-top: 24px; line-height: 1.6;">
      
      <!-- Introducción -->
      <section style="margin-bottom: 40px;">
        <p style="font-size: 18px; color: #555;">
          Comprar un vehículo siniestrado puede ser una excelente oportunidad si sabes qué buscar 
          y cómo evaluar correctamente el estado del vehículo. Esta guía te ayudará a tomar decisiones 
          informadas y seguras.
        </p>
      </section>

      <!-- Paso 1 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          1️⃣ Antes de Buscar: Define tu Objetivo
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">¿Para qué quieres el vehículo?</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Reparar y usar:</strong> Busca daños leves, preferiblemente estéticos</li>
            <li><strong>Vender repuestos:</strong> Enfócate en modelos demandados</li>
            <li><strong>Proyecto personal:</strong> Considera costos de reparación vs. valor final</li>
            <li><strong>Exportar:</strong> Verifica regulaciones del país destino</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Establece tu presupuesto</div>
          <p style="margin-top: 8px;">
            Considera no solo el precio de compra, sino también:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Costo de reparación estimado</li>
            <li>Traslado del vehículo</li>
            <li>Trámites y papeles</li>
            <li>Repuestos específicos</li>
            <li>Mano de obra especializada</li>
          </ul>
        </div>
      </section>

      <!-- Paso 2 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          2️⃣ Busca y Filtra Inteligentemente
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Usa los filtros correctamente</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Tipo de daño:</strong> Choque frontal, lateral, trasero, volcado, inundación</li>
            <li><strong>Gravedad:</strong> Leve, moderado, total</li>
            <li><strong>Ubicación:</strong> Cerca de ti para revisión presencial</li>
            <li><strong>Precio:</strong> Dentro de tu presupuesto</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Lee las descripciones con atención</div>
          <p style="margin-top: 8px;">
            Busca información sobre:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Año, marca y modelo exacto</li>
            <li>Kilometraje real</li>
            <li>Tipo y extensión del daño</li>
            <li>¿Funciona el motor?</li>
            <li>Estado de documentos (padrón, revisión técnica)</li>
            <li>¿Es transferible?</li>
          </ul>
        </div>

        <div style="padding: 16px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 4px; margin-top: 16px;">
          <p style="margin: 0; color: #92400E;">
            <strong>⚠️ Alerta:</strong> Desconfía de descripciones vagas o vendedores que evitan 
            dar detalles específicos sobre los daños.
          </p>
        </div>
      </section>

      <!-- Paso 3 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          3️⃣ Contacta al Vendedor
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Haz las preguntas correctas</div>
          <p style="margin-top: 8px;">
            Antes de agendar una visita, pregunta:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>¿Cómo ocurrió el siniestro?</li>
            <li>¿Cuenta con informe de la aseguradora?</li>
            <li>¿Tiene padrón o constancia de inscripción?</li>
            <li>¿Los airbags se activaron?</li>
            <li>¿Hubo daño estructural o solo carrocería?</li>
            <li>¿Funciona el motor y transmisión?</li>
            <li>¿Está disponible para revisión mecánica?</li>
            <li>¿El precio es negociable?</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Solicita más fotos si es necesario</div>
          <p style="margin-top: 8px;">
            Pide fotografías adicionales de:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Número de chasis (VIN)</li>
            <li>Motor y compartimento</li>
            <li>Daños específicos desde varios ángulos</li>
            <li>Interior y tablero</li>
            <li>Documentación del vehículo</li>
          </ul>
        </div>
      </section>

      <!-- Paso 4 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          4️⃣ Inspección Presencial (CRÍTICO)
        </div>
        
        <div style="padding: 16px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; margin-bottom: 20px;">
          <p style="margin: 0; color: #1E40AF;">
            <strong>💡 Consejo:</strong> NUNCA compres un vehículo siniestrado sin verlo en persona. 
            Lleva a un mecánico de confianza si es posible.
          </p>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Checklist de Inspección Visual</div>
          <div style="background: #F9FAFB; padding: 16px; border-radius: 8px; margin-top: 12px;">
            <p style="margin-bottom: 12px;"><strong>Exterior:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>☐ Revisa abolladuras, rayones y daños en la pintura</li>
              <li>☐ Verifica alineación de puertas y capó</li>
              <li>☐ Inspecciona parabrisas y vidrios</li>
              <li>☐ Revisa neumáticos y llantas</li>
              <li>☐ Verifica luces delanteras y traseras</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 12px;"><strong>Interior:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>☐ Revisa asientos y tapizado</li>
              <li>☐ Prueba cinturones de seguridad</li>
              <li>☐ Verifica funcionamiento de tablero</li>
              <li>☐ Revisa consola central y controles</li>
              <li>☐ Busca signos de humedad o inundación</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 12px;"><strong>Motor y Mecánica:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>☐ Revisa nivel de aceite y líquidos</li>
              <li>☐ Busca fugas evidentes</li>
              <li>☐ Si enciende, escucha ruidos anormales</li>
              <li>☐ Verifica correa de distribución</li>
              <li>☐ Inspecciona batería y cables</li>
            </ul>

            <p style="margin-top: 16px; margin-bottom: 12px;"><strong>Documentación:</strong></p>
            <ul style="margin-left: 20px; line-height: 1.8;">
              <li>☐ Verifica número de chasis con documentos</li>
              <li>☐ Revisa padrón o constancia de inscripción</li>
              <li>☐ Solicita informe de la aseguradora</li>
              <li>☐ Verifica que no tenga multas pendientes</li>
            </ul>
          </div>
        </div>

        <div style="padding: 16px; background: #FEE2E2; border-left: 4px solid #EF4444; border-radius: 4px; margin-top: 16px;">
          <p style="margin: 0; color: #991B1B;">
            <strong>🚨 Señales de Alerta:</strong> Daño estructural severo, VIN alterado, documentos 
            irregulares, vendedor evasivo, o olor a humedad/moho (indica inundación).
          </p>
        </div>
      </section>

      <!-- Paso 5 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          5️⃣ Negociación y Compra
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Estrategias de negociación</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Investiga precios de mercado de vehículos similares</li>
            <li>Considera el costo real de reparación</li>
            <li>Usa defectos encontrados para negociar</li>
            <li>Sé respetuoso pero firme en tu oferta</li>
            <li>No tengas miedo de alejarte si el precio no es justo</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Forma de pago segura</div>
          <p style="margin-top: 8px;">
            Recomendaciones:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li><strong>Transferencia bancaria:</strong> Deja registro de la transacción</li>
            <li><strong>Efectivo:</strong> Solo en lugares seguros y durante el día</li>
            <li><strong>NUNCA:</strong> Envíes dinero por adelantado sin ver el vehículo</li>
            <li><strong>EVITA:</strong> Pagos en criptomonedas o giros internacionales</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Documentación de la compra</div>
          <p style="margin-top: 8px;">
            Asegúrate de obtener y firmar:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Contrato de compraventa simple</li>
            <li>Copia de cédula del vendedor</li>
            <li>Padrón o constancia de inscripción del vehículo</li>
            <li>Informe de la aseguradora (si disponible)</li>
            <li>Recibo de pago</li>
          </ul>
        </div>
      </section>

      <!-- Paso 6 -->
      <section style="margin-bottom: 40px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          6️⃣ Después de la Compra
        </div>
        
        <div style="margin-bottom: 20px;">
          <div class="h3">Traslado del vehículo</div>
          <p style="margin-top: 8px;">
            Si el vehículo no está en condiciones de circular:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Contrata una grúa especializada</li>
            <li>Asegura el vehículo correctamente durante el transporte</li>
            <li>Obtén permisos si es traslado interregional</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Trámites legales</div>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Realiza la transferencia en el Registro Civil</li>
            <li>Actualiza el padrón a tu nombre</li>
            <li>Paga permisos de circulación (si aplica)</li>
            <li>Obtén seguro obligatorio antes de circular</li>
          </ul>
        </div>

        <div style="margin-bottom: 20px;">
          <div class="h3">Reparación</div>
          <p style="margin-top: 8px;">
            Consejos para la reparación:
          </p>
          <ul style="margin-left: 20px; margin-top: 8px; line-height: 1.8;">
            <li>Obtén cotizaciones de varios talleres</li>
            <li>Usa repuestos originales cuando sea posible</li>
            <li>Documenta todas las reparaciones</li>
            <li>Considera certificación post-reparación</li>
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
            <li>Comprar sin ver el vehículo en persona</li>
            <li>No verificar documentación</li>
            <li>Subestimar costos de reparación</li>
            <li>No investigar el historial del vehículo</li>
            <li>Comprar por impulso sin negociar</li>
            <li>No llevar a un mecánico a la inspección</li>
            <li>Ignorar señales de alerta del vendedor</li>
            <li>Pagar por adelantado sin garantías</li>
          </ul>
        </div>
      </section>

      <!-- Recursos Adicionales -->
      <section style="margin-bottom: 32px;">
        <div class="h2" style="color: var(--cc-primary); margin-bottom: 16px;">
          📚 Recursos Adicionales
        </div>
        
        <ul style="margin-left: 20px; line-height: 1.8;">
          <li><a href="<?php echo BASE_URL; ?>/seguridad">Consejos de Seguridad</a> - Evita fraudes</li>
          <li><a href="<?php echo BASE_URL; ?>/preguntas-frecuentes">Preguntas Frecuentes</a> - Dudas comunes</li>
          <li><a href="<?php echo BASE_URL; ?>/contacto">Contáctanos</a> - Ayuda personalizada</li>
        </ul>
      </section>

      <div style="margin-top: 32px; padding: 16px; background: #DCFCE7; border-radius: 8px;">
        <p style="margin: 0; font-size: 14px; color: #166534;">
          <strong>✅ Recuerda:</strong> Comprar un vehículo siniestrado puede ser una excelente inversión 
          si lo haces con conocimiento y precaución. ¡No tengas miedo de hacer preguntas y tomar tu tiempo!
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
