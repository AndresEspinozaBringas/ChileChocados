<?php
/**
 * Vista: Contacto
 * Formulario de contacto
 */

require_once __DIR__ . '/../layouts/header.php';
?>

<main class="container" style="margin-top: 24px; margin-bottom: 60px;">
    
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 class="h1" style="margin: 0 0 12px 0;">Contáctanos</h1>
            <p class="meta" style="margin: 0; font-size: 16px;">
                ¿Tienes alguna pregunta o sugerencia? Estamos aquí para ayudarte
            </p>
        </div>

        <!-- Mensajes flash -->
        <?php 
        $flash = getFlash();
        if ($flash): 
        ?>
            <div class="card" style="background: <?php echo $flash['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; border-color: <?php echo $flash['type'] === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>; margin-bottom: 24px; padding: 16px;">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Información de contacto -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 40px;">
            <div class="card" style="text-align: center; padding: 24px;">
                <div style="font-size: 32px; margin-bottom: 12px;">📧</div>
                <h3 class="h4" style="margin: 0 0 8px 0;">Email</h3>
                <p class="meta" style="margin: 0;">contacto@chilechocados.cl</p>
            </div>
            <div class="card" style="text-align: center; padding: 24px;">
                <div style="font-size: 32px; margin-bottom: 12px;">📱</div>
                <h3 class="h4" style="margin: 0 0 8px 0;">Teléfono</h3>
                <p class="meta" style="margin: 0;">+56 9 1234 5678</p>
            </div>
            <div class="card" style="text-align: center; padding: 24px;">
                <div style="font-size: 32px; margin-bottom: 12px;">⏰</div>
                <h3 class="h4" style="margin: 0 0 8px 0;">Horario</h3>
                <p class="meta" style="margin: 0;">Lun - Vie: 9:00 - 18:00</p>
            </div>
        </div>

        <!-- Formulario de contacto -->
        <div class="card">
            <h2 class="h3" style="margin: 0 0 24px 0;">Envíanos un mensaje</h2>
            
            <form action="<?php echo BASE_URL; ?>/contacto/enviar" method="POST">
                
                <!-- Nombre y Email -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                            Nombre completo *
                        </label>
                        <input 
                            type="text" 
                            name="nombre" 
                            required
                            placeholder="Tu nombre"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;"
                        >
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                            Email *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            placeholder="tu@email.com"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;"
                        >
                    </div>
                </div>

                <!-- Teléfono y Asunto -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                            Teléfono
                        </label>
                        <input 
                            type="tel" 
                            name="telefono" 
                            placeholder="+56 9 1234 5678"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;"
                        >
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                            Asunto
                        </label>
                        <select 
                            name="asunto"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;"
                        >
                            <option value="">Seleccionar...</option>
                            <option value="consulta">Consulta general</option>
                            <option value="soporte">Soporte técnico</option>
                            <option value="publicacion">Sobre una publicación</option>
                            <option value="sugerencia">Sugerencia</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Mensaje -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                        Mensaje *
                    </label>
                    <textarea 
                        name="mensaje" 
                        required
                        rows="6"
                        placeholder="Escribe tu mensaje aquí..."
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; resize: vertical; font-family: inherit;"
                    ></textarea>
                </div>

                <!-- Botón enviar -->
                <button type="submit" class="btn primary" style="width: 100%;">
                    Enviar mensaje
                </button>
                
                <p class="meta" style="margin: 16px 0 0 0; text-align: center; font-size: 13px;">
                    * Campos obligatorios
                </p>
            </form>
        </div>

        <!-- Información adicional -->
        <div class="card" style="margin-top: 24px; background: var(--cc-bg-muted, #F5F5F5);">
            <h3 class="h4" style="margin: 0 0 12px 0;">¿Necesitas ayuda inmediata?</h3>
            <p class="meta" style="margin: 0 0 12px 0;">
                Revisa nuestras <a href="<?php echo BASE_URL; ?>/preguntas-frecuentes" style="color: var(--cc-primary);">Preguntas Frecuentes</a> 
                o consulta nuestra <a href="<?php echo BASE_URL; ?>/vender" style="color: var(--cc-primary);">Guía de Cómo Funciona</a>.
            </p>
            <p class="meta" style="margin: 0;">
                Tiempo de respuesta promedio: <strong>24 horas</strong>
            </p>
        </div>

    </div>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
