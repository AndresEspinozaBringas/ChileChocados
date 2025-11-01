<?php
/**
 * Vista: Mensajería Interna
 * Sistema de chat 1-1 entre compradores y vendedores
 */

// Variables disponibles desde el controlador
$pageTitle = $data['title'] ?? 'Mensajes';
$conversaciones = $data['conversaciones'] ?? [];
$conversacionActiva = $data['conversacion_activa'] ?? null;
$mensajes = $data['mensajes'] ?? [];
$userId = $data['user_id'] ?? 1;

// Cargar layouts
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/nav.php';
?>

<!-- CSS específico para mensajería -->
<style>
.mensajeria-container {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 20px;
    margin-top: 24px;
    height: calc(100vh - 200px);
    max-height: 700px;
}

.conversaciones-panel {
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.conversaciones-header {
    padding: 16px;
    border-bottom: 1px solid var(--cc-border-default, #E5E5E5);
    background: var(--cc-bg-surface, #F9F9F9);
}

.conversaciones-lista {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}

.conversacion-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
    margin-bottom: 4px;
}

.conversacion-item:hover {
    background: var(--cc-bg-muted, #F5F5F5);
}

.conversacion-item.activa {
    background: var(--cc-primary-pale, #FFF5F4);
    border-left: 3px solid var(--cc-primary, #E6332A);
}

.conversacion-foto {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
    background: var(--cc-bg-muted, #F5F5F5);
}

.icon-usuario {
    color: var(--cc-primary, #E6332A);
}

.conversacion-info {
    flex: 1;
    min-width: 0;
}

.conversacion-titulo {
    font-size: 14px;
    font-weight: 600;
    color: var(--cc-text-primary, #2E2E2E);
    margin: 0 0 4px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversacion-usuario {
    font-size: 12px;
    color: var(--cc-text-tertiary, #999);
    margin-bottom: 4px;
}

.conversacion-ultimo-mensaje {
    font-size: 13px;
    color: var(--cc-text-secondary, #666);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversacion-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.conversacion-fecha {
    font-size: 11px;
    color: var(--cc-text-tertiary, #999);
}

.badge-no-leidos {
    background: var(--cc-primary, #E6332A);
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
    min-width: 20px;
    text-align: center;
}

.chat-panel {
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--cc-border-default, #E5E5E5);
    background: var(--cc-bg-surface, #F9F9F9);
}

.chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-header-titulo {
    flex: 1;
}

.chat-header-titulo h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
    color: var(--cc-text-primary, #2E2E2E);
}

.chat-header-titulo p {
    margin: 0;
    font-size: 13px;
    color: var(--cc-text-secondary, #666);
}

.chat-mensajes {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: var(--cc-bg-default, #FAFAFA);
}

.mensaje {
    display: flex;
    margin-bottom: 16px;
    gap: 12px;
}

.mensaje.enviado {
    flex-direction: row-reverse;
}

.mensaje-contenido {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 12px;
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
}

.mensaje.enviado .mensaje-contenido {
    background: var(--cc-primary, #E6332A);
    color: white;
    border-color: var(--cc-primary, #E6332A);
}

.mensaje-texto {
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
    word-wrap: break-word;
}

.mensaje-fecha {
    font-size: 11px;
    color: var(--cc-text-tertiary, #999);
    margin-top: 4px;
}

.mensaje.enviado .mensaje-fecha {
    color: rgba(255, 255, 255, 0.8);
}

.chat-input-container {
    padding: 16px 20px;
    border-top: 1px solid var(--cc-border-default, #E5E5E5);
    background: var(--cc-white, #FFFFFF);
}

.chat-input-form {
    display: flex;
    gap: 12px;
}

.chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    min-height: 44px;
    max-height: 120px;
}

.chat-input:focus {
    outline: none;
    border-color: var(--cc-primary, #E6332A);
    box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.1);
}

.btn-enviar {
    padding: 12px 24px;
    background: var(--cc-primary, #E6332A);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-enviar:hover {
    background: #d42d24;
    transform: translateY(-1px);
}

.btn-enviar:active {
    transform: translateY(0);
}

.btn-enviar:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.mensaje-vacio {
    text-align: center;
    padding: 48px 20px;
    color: var(--cc-text-tertiary, #999);
}

.mensaje-vacio svg {
    width: 64px;
    height: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 968px) {
    .mensajeria-container {
        grid-template-columns: 1fr;
        height: auto;
        max-height: none;
    }
    
    .conversaciones-panel {
        max-height: 300px;
    }
    
    .chat-panel {
        min-height: 500px;
    }
}

@media (max-width: 640px) {
    .mensaje-contenido {
        max-width: 85%;
    }
    
    .chat-input-form {
        flex-direction: column;
        gap: 8px;
    }
    
    .btn-enviar {
        width: 100%;
    }
}
</style>

<main class="container" style="max-width: 1400px; padding: 24px;">
    
    <!-- Breadcrumb -->
    <div class="breadcrumbs" style="margin-bottom: 16px;">
        <?php if ($conversacionActiva && isset($conversacionActiva['publicacion_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $conversacionActiva['publicacion_id']; ?>">
                ← Volver a la publicación
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>">← Volver al inicio</a>
        <?php endif; ?>
    </div>

    <!-- Título -->
    <h1 class="h1" style="margin-bottom: 24px;">Mensajería Interna</h1>

    <!-- Container de mensajería -->
    <div class="mensajeria-container">
        
        <!-- PANEL IZQUIERDO: Lista de conversaciones -->
        <aside class="conversaciones-panel">
            <div class="conversaciones-header">
                <h3 class="h3" style="margin: 0;">Conversaciones</h3>
            </div>
            
            <div class="conversaciones-lista">
                <?php if (empty($conversaciones)): ?>
                    <div style="padding: 32px 16px; text-align: center; color: #999;">
                        <p>No tienes conversaciones activas</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversaciones as $conv): ?>
                        <?php 
                            $esActiva = $conversacionActiva && 
                                       $conversacionActiva['publicacion_id'] == $conv['publicacion_id'] && 
                                       $conversacionActiva['otro_usuario_id'] == $conv['otro_usuario_id'];
                        ?>
                        <a 
                            href="<?php echo BASE_URL; ?>/mensajes?conversacion=<?php echo $conv['conversacion_key']; ?>" 
                            class="conversacion-item <?php echo $esActiva ? 'activa' : ''; ?>"
                        >
                            <?php if ($conv['foto_principal']): ?>
                                <img 
                                    src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo $conv['foto_principal']; ?>" 
                                    alt="<?php echo htmlspecialchars($conv['publicacion_titulo']); ?>"
                                    class="conversacion-foto"
                                >
                            <?php else: ?>
                                <div class="conversacion-foto" style="display: flex; align-items: center; justify-content: center; background: var(--cc-primary-pale, #FFF5F4);">
                                    <?php echo icon('image', 28, 'icon-usuario'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="conversacion-info">
                                <h4 class="conversacion-titulo">
                                    <?php echo htmlspecialchars($conv['publicacion_titulo']); ?>
                                </h4>
                                <p class="conversacion-usuario">
                                    <?php echo htmlspecialchars($conv['otro_usuario_nombre']); ?>
                                    <span style="color: #ccc;">·</span>
                                    <?php echo ucfirst($conv['otro_usuario_tipo']); ?>
                                </p>
                                <?php if ($conv['ultimo_mensaje']): ?>
                                    <p class="conversacion-ultimo-mensaje">
                                        <?php echo htmlspecialchars($conv['ultimo_mensaje']); ?>
                                    </p>
                                <?php endif; ?>
                                <div class="conversacion-meta">
                                    <span class="conversacion-fecha">
                                        <?php echo $conv['ultimo_mensaje_fecha_relativa']; ?>
                                    </span>
                                    <?php if ($conv['mensajes_no_leidos'] > 0): ?>
                                        <span class="badge-no-leidos">
                                            <?php echo $conv['mensajes_no_leidos']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>

        <!-- PANEL DERECHO: Chat activo -->
        <section class="chat-panel">
            <?php if ($conversacionActiva): ?>
                
                <!-- Header del chat -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <?php if (isset($conversacionActiva['otro_usuario_foto']) && $conversacionActiva['otro_usuario_foto']): ?>
                            <img 
                                src="<?php echo BASE_URL; ?>/uploads/usuarios/<?php echo $conversacionActiva['otro_usuario_foto']; ?>" 
                                alt="<?php echo htmlspecialchars($conversacionActiva['otro_usuario_nombre']); ?>"
                                style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;"
                            >
                        <?php else: ?>
                            <div style="width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--cc-primary-pale, #FFF5F4);">
                                <?php echo icon($conversacionActiva['otro_usuario_tipo'] === 'vendedor' ? 'user-check' : 'user', 24, 'icon-usuario'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="chat-header-titulo">
                            <h3><?php echo htmlspecialchars($conversacionActiva['publicacion_titulo']); ?></h3>
                            <p>Conversación con <?php echo htmlspecialchars($conversacionActiva['otro_usuario_nombre']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Área de mensajes -->
                <div class="chat-mensajes" id="chatMensajes">
                    <?php if (empty($mensajes)): ?>
                        <div class="mensaje-vacio">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <p>Aún no hay mensajes en esta conversación</p>
                            <p style="font-size: 13px; margin-top: 8px;">Envía el primer mensaje para iniciar la conversación</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mensajes as $msg): ?>
                            <div class="mensaje <?php echo ($msg->remitente_id == $userId) ? 'enviado' : 'recibido'; ?>">
                                <div class="mensaje-contenido">
                                    <p class="mensaje-texto"><?php echo nl2br(htmlspecialchars($msg->mensaje)); ?></p>
                                    <div class="mensaje-fecha"><?php echo $msg->fecha_formateada; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Input de mensaje -->
                <div class="chat-input-container">
                    <form class="chat-input-form" id="formEnviarMensaje" onsubmit="return enviarMensaje(event);">
                        <input type="hidden" name="publicacion_id" value="<?php echo $conversacionActiva['publicacion_id']; ?>">
                        <input type="hidden" name="destinatario_id" value="<?php echo $conversacionActiva['otro_usuario_id']; ?>">
                        <textarea 
                            class="chat-input" 
                            id="inputMensaje"
                            name="mensaje" 
                            placeholder="Escribe un mensaje..." 
                            rows="1"
                            required
                        ></textarea>
                        <button type="submit" class="btn-enviar" id="btnEnviar">
                            Enviar
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <!-- Sin conversación seleccionada -->
                <div class="mensaje-vacio" style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <h3 style="color: #666; margin-top: 24px;">Selecciona una conversación</h3>
                    <p style="color: #999; margin-top: 8px;">Elige una conversación de la lista para ver los mensajes</p>
                </div>
            <?php endif; ?>
        </section>

    </div>

</main>

<!-- JavaScript para funcionalidad de mensajería -->
<script>
// Auto-resize del textarea
const inputMensaje = document.getElementById('inputMensaje');
if (inputMensaje) {
    inputMensaje.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

// Scroll automático al final de los mensajes
const chatMensajes = document.getElementById('chatMensajes');
if (chatMensajes) {
    chatMensajes.scrollTop = chatMensajes.scrollHeight;
}

// Función para enviar mensaje
function enviarMensaje(event) {
    event.preventDefault();
    
    const form = event.target;
    const inputMensaje = document.getElementById('inputMensaje');
    const btnEnviar = document.getElementById('btnEnviar');
    const mensaje = inputMensaje.value.trim();
    
    if (!mensaje) return false;
    
    // Deshabilitar botón mientras se envía
    btnEnviar.disabled = true;
    btnEnviar.textContent = 'Enviando...';
    
    // Preparar datos del formulario
    const formData = new FormData(form);
    
    // Enviar mensaje al servidor
    fetch('<?php echo BASE_URL; ?>/mensajes/enviar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Agregar mensaje al chat
            const chatMensajes = document.getElementById('chatMensajes');
            const mensajeHtml = `
                <div class="mensaje enviado">
                    <div class="mensaje-contenido">
                        <p class="mensaje-texto">${escapeHtml(mensaje).replace(/\n/g, '<br>')}</p>
                        <div class="mensaje-fecha">${data.mensaje.fecha_formateada}</div>
                    </div>
                </div>
            `;
            
            // Si el chat estaba vacío, remover el mensaje de "sin mensajes"
            const mensajeVacio = chatMensajes.querySelector('.mensaje-vacio');
            if (mensajeVacio) {
                mensajeVacio.remove();
            }
            
            chatMensajes.insertAdjacentHTML('beforeend', mensajeHtml);
            chatMensajes.scrollTop = chatMensajes.scrollHeight;
            
            // Limpiar input
            inputMensaje.value = '';
            inputMensaje.style.height = 'auto';
        } else {
            alert('Error al enviar el mensaje: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el mensaje. Por favor, intenta de nuevo.');
    })
    .finally(() => {
        btnEnviar.disabled = false;
        btnEnviar.textContent = 'Enviar';
    });
    
    return false;
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Enviar con Enter (Ctrl+Enter para nueva línea)
if (inputMensaje) {
    inputMensaje.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey) {
            e.preventDefault();
            document.getElementById('formEnviarMensaje').dispatchEvent(new Event('submit'));
        }
    });
}

// ============================================
// POLLING AUTOMÁTICO PARA NUEVOS MENSAJES
// ============================================
<?php if ($conversacionActiva && !empty($mensajes)): ?>
let ultimoMensajeId = <?php echo end($mensajes)->id; ?>;
<?php elseif ($conversacionActiva): ?>
let ultimoMensajeId = 0;
<?php endif; ?>

<?php if ($conversacionActiva): ?>
// Función para verificar nuevos mensajes
function verificarNuevosMensajes() {
    const publicacionId = <?php echo $conversacionActiva['publicacion_id']; ?>;
    const otroUsuarioId = <?php echo $conversacionActiva['otro_usuario_id']; ?>;
    
    fetch(`<?php echo BASE_URL; ?>/mensajes/obtener-nuevos?publicacion_id=${publicacionId}&otro_usuario_id=${otroUsuarioId}&ultimo_mensaje_id=${ultimoMensajeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mensajes && data.mensajes.length > 0) {
                const chatMensajes = document.getElementById('chatMensajes');
                
                // Verificar si el usuario está al final del scroll
                const estaAlFinal = chatMensajes.scrollHeight - chatMensajes.scrollTop <= chatMensajes.clientHeight + 100;
                
                // Agregar cada mensaje nuevo
                data.mensajes.forEach(msg => {
                    const mensajeHtml = `
                        <div class="mensaje ${msg.es_propio ? 'enviado' : 'recibido'}">
                            <div class="mensaje-contenido">
                                <p class="mensaje-texto">${escapeHtml(msg.mensaje).replace(/\n/g, '<br>')}</p>
                                <div class="mensaje-fecha">${msg.fecha_formateada}</div>
                            </div>
                        </div>
                    `;
                    
                    // Si el chat estaba vacío, remover el mensaje de "sin mensajes"
                    const mensajeVacio = chatMensajes.querySelector('.mensaje-vacio');
                    if (mensajeVacio) {
                        mensajeVacio.remove();
                    }
                    
                    chatMensajes.insertAdjacentHTML('beforeend', mensajeHtml);
                    
                    // Actualizar último mensaje ID
                    ultimoMensajeId = msg.id;
                });
                
                // Hacer scroll solo si el usuario estaba al final
                if (estaAlFinal) {
                    chatMensajes.scrollTop = chatMensajes.scrollHeight;
                }
                
                // Mostrar notificación si el mensaje es de otro usuario
                const ultimoMensaje = data.mensajes[data.mensajes.length - 1];
                if (!ultimoMensaje.es_propio && document.hidden) {
                    // Actualizar título de la página para notificar
                    document.title = '(1) Nuevo mensaje - ChileChocados';
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar nuevos mensajes:', error);
        });
}

// Iniciar polling cada 3 segundos
const pollingInterval = setInterval(verificarNuevosMensajes, 3000);

// Limpiar interval cuando se cierra la página
window.addEventListener('beforeunload', function() {
    clearInterval(pollingInterval);
});

// Restaurar título cuando el usuario vuelve a la página
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        document.title = 'Mensajes - ChileChocados';
    }
});
<?php endif; ?>
</script>

<?php
// Cargar footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
