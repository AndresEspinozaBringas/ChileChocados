<?php
/**
 * Vista: Aprobación/Confirmación de Publicación
 * Ruta: /publicaciones/approval
 * Muestra confirmación tras crear una publicación
 * 
 * Variables disponibles:
 * - $data['publicacion'] - Datos de la publicación recién creada
 */

// Incluir header
require_once __DIR__ . '/../../layouts/header.php';
?>

<main class="container" style="padding: 48px 16px;">
  
  <!-- Mensaje de éxito -->
  <div class="card" style="max-width: 800px; margin: 0 auto; text-align: center; padding: 48px 32px;">
    
    <!-- Ícono de éxito -->
    <div style="margin-bottom: 24px;">
      <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto;">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="m9 12 2 2 4-4"></path>
      </svg>
    </div>
    
    <!-- Título principal -->
    <h1 class="h1" style="color: #22C55E; margin-bottom: 16px;">
      ¡Publicación Creada con Éxito!
    </h1>
    
    <!-- Mensaje descriptivo -->
    <p class="meta" style="font-size: 16px; line-height: 1.6; margin-bottom: 32px; max-width: 600px; margin-left: auto; margin-right: auto;">
      Tu vehículo ha sido registrado exitosamente. Nuestro equipo revisará tu publicación en las próximas 24 horas. 
      Te notificaremos por email cuando esté aprobada y visible para los compradores.
    </p>
    
    <!-- Información de la publicación -->
    <?php if (isset($data['publicacion'])): ?>
      <div class="card" style="background: #F3F4F6; padding: 24px; margin-bottom: 32px; text-align: left;">
        <div class="h3" style="margin-bottom: 16px;">Resumen de tu publicación:</div>
        
        <div class="grid cols-2" style="gap: 16px;">
          <div>
            <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Título:</p>
            <p style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($data['publicacion']->titulo ?? 'Tu vehículo'); ?></p>
          </div>
          
          <div>
            <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Marca y Modelo:</p>
            <p style="font-weight: 600; color: #111827;">
              <?php echo htmlspecialchars($data['publicacion']->marca ?? ''); ?> 
              <?php echo htmlspecialchars($data['publicacion']->modelo ?? ''); ?>
            </p>
          </div>
          
          <div>
            <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Año:</p>
            <p style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($data['publicacion']->anio ?? ''); ?></p>
          </div>
          
          <div>
            <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Tipo de venta:</p>
            <p style="font-weight: 600; color: #111827;">
              <?php 
                if ($data['publicacion']->tipo_venta === 'completo') {
                  echo 'Vehículo completo';
                } else {
                  echo 'En desarme (piezas)';
                }
              ?>
            </p>
          </div>
          
          <?php if ($data['publicacion']->tipo_venta === 'completo' && !empty($data['publicacion']->precio)): ?>
            <div>
              <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Precio:</p>
              <p style="font-weight: 600; color: #0066CC; font-size: 18px;">
                $<?php echo number_format($data['publicacion']->precio, 0, ',', '.'); ?>
              </p>
            </div>
          <?php endif; ?>
          
          <div>
            <p style="font-size: 14px; color: #6B7280; margin-bottom: 4px;">Estado:</p>
            <span style="background: #FEF3C7; color: #92400E; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; display: inline-block;">
              Pendiente de revisión
            </span>
          </div>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Información sobre el proceso -->
    <div style="background: #EFF6FF; border-left: 4px solid #0066CC; padding: 16px; margin-bottom: 32px; text-align: left; border-radius: 8px;">
      <p style="font-weight: 600; color: #0066CC; margin-bottom: 8px;">¿Qué sucede ahora?</p>
      <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="padding: 8px 0; display: flex; align-items: flex-start;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066CC" stroke-width="2" style="margin-right: 8px; flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
          <span>Nuestro equipo revisará tu publicación en las próximas 24 horas</span>
        </li>
        <li style="padding: 8px 0; display: flex; align-items: flex-start;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066CC" stroke-width="2" style="margin-right: 8px; flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
          <span>Te enviaremos un correo cuando sea aprobada</span>
        </li>
        <li style="padding: 8px 0; display: flex; align-items: flex-start;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066CC" stroke-width="2" style="margin-right: 8px; flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
          <span>Una vez aprobada, será visible para miles de compradores</span>
        </li>
        <li style="padding: 8px 0; display: flex; align-items: flex-start;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0066CC" stroke-width="2" style="margin-right: 8px; flex-shrink: 0; margin-top: 2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m9 12 2 2 4-4"></path>
          </svg>
          <span>Recibirás notificaciones cuando alguien se interese</span>
        </li>
      </ul>
    </div>
    
    <!-- Botones de acción -->
    <div class="grid cols-3" style="gap: 16px; margin-top: 32px;">
      
      <!-- Ver mi publicación (si está disponible) -->
      <?php if (isset($data['publicacion']) && $data['publicacion']->id): ?>
        <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $data['publicacion']->id; ?>" class="btn btn-secondary">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          Ver mi publicación
        </a>
      <?php endif; ?>
      
      <!-- Publicar otra -->
      <a href="<?php echo BASE_URL; ?>/publicar" class="btn btn-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="M12 5v14M5 12h14"></path>
        </svg>
        Publicar otro vehículo
      </a>
      
      <!-- Volver al inicio -->
      <a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        Volver al inicio
      </a>
      
    </div>
    
    <!-- Tips adicionales -->
    <div style="margin-top: 48px; padding-top: 32px; border-top: 1px solid #E5E7EB;">
      <p style="font-weight: 600; color: #111827; margin-bottom: 16px;">💡 Tips para vender más rápido:</p>
      <div class="grid cols-2" style="gap: 16px; text-align: left;">
        <div style="padding: 16px; background: #F9FAFB; border-radius: 8px;">
          <p style="font-weight: 600; color: #111827; margin-bottom: 8px;">Fotos de calidad</p>
          <p style="font-size: 14px; color: #6B7280;">Las publicaciones con 4-6 fotos claras se venden 3x más rápido</p>
        </div>
        <div style="padding: 16px; background: #F9FAFB; border-radius: 8px;">
          <p style="font-weight: 600; color: #111827; margin-bottom: 8px;">Descripción detallada</p>
          <p style="font-size: 14px; color: #6B7280;">Menciona el tipo de daño, estado mecánico y documentación</p>
        </div>
        <div style="padding: 16px; background: #F9FAFB; border-radius: 8px;">
          <p style="font-weight: 600; color: #111827; margin-bottom: 8px;">Precio competitivo</p>
          <p style="font-size: 14px; color: #6B7280;">Revisa precios similares en la plataforma</p>
        </div>
        <div style="padding: 16px; background: #F9FAFB; border-radius: 8px;">
          <p style="font-weight: 600; color: #111827; margin-bottom: 8px;">Responde rápido</p>
          <p style="font-size: 14px; color: #6B7280;">Los vendedores que responden en menos de 2 horas cierran más ventas</p>
        </div>
      </div>
    </div>
    
    <!-- CTA destacado -->
    <div class="card" style="background: linear-gradient(135deg, #0066CC 0%, #004C99 100%); color: white; padding: 32px; margin-top: 32px; text-align: center;">
      <p style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: white;">¿Quieres más visibilidad?</p>
      <p style="color: rgba(255,255,255,0.9); margin-bottom: 20px;">Destaca tu publicación y aparece en los primeros resultados</p>
      <a href="<?php echo BASE_URL; ?>/planes-destacado" class="btn" style="background: white; color: #0066CC; border: none;">
        Ver planes de destacado
      </a>
    </div>
    
  </div>
  
</main>

<?php
// Incluir footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
