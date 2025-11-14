<?php
/**
 * Vista Admin: Mensajería del Sistema
 * El admin puede ver todas las conversaciones entre usuarios
 */

$pageTitle = $data['title'] ?? 'Mensajes - Admin';
$conversaciones = $data['conversaciones'] ?? [];
$conversacionActiva = $data['conversacion_activa'] ?? null;
$mensajes = $data['mensajes'] ?? [];
$userId = $data['user_id'] ?? 1;

require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- CSS específico para mensajería admin (copiado de mensajes de usuario) -->
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
    background: var(--cc-bg-muted);
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

.conversacion-usuarios {
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
    color: var(--cc-text-tertiary);
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

.chat-header-info h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
    color: var(--cc-text-primary, #2E2E2E);
}

.chat-header-info p {
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
    align-items: flex-end;
}

/* Mensajes del usuario 1 (izquierda) */
.mensaje.usuario1 {
    flex-direction: row;
}

/* Mensajes del usuario 2 (derecha) */
.mensaje.usuario2 {
    flex-direction: row-reverse;
}

.mensaje-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid var(--cc-border-default, #E5E5E5);
}

.mensaje-avatar-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--cc-primary-pale, #FFF5F4);
    color: var(--cc-primary, #E6332A);
    font-weight: 700;
    font-size: 14px;
    border: 2px solid var(--cc-border-default, #E5E5E5);
}

.mensaje-contenido {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 12px;
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
}

/* Fondo rojo para mensajes del usuario 2 (derecha) */
.mensaje.usuario2 .mensaje-contenido {
    background: var(--cc-primary, #E6332A);
    color: white;
    border-color: var(--cc-primary, #E6332A);
}

.mensaje-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.mensaje-autor {
    font-size: 13px;
    font-weight: 600;
    color: var(--cc-primary, #E6332A);
}

/* Color blanco para autor en mensajes rojos */
.mensaje.usuario2 .mensaje-autor {
    color: rgba(255, 255, 255, 0.9);
}

.mensaje-fecha {
    font-size: 11px;
    color: var(--cc-text-tertiary, #999);
    margin-top: 4px;
}

/* Color blanco para fecha en mensajes rojos */
.mensaje.usuario2 .mensaje-fecha {
    color: rgba(255, 255, 255, 0.8);
}

.mensaje-texto {
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
    word-wrap: break-word;
    color: var(--cc-text-primary, #2E2E2E);
}

/* Color blanco para texto en mensajes rojos */
.mensaje.usuario2 .mensaje-texto {
    color: white;
}

.mensaje-vacio {
    text-align: center;
    padding: 48px 20px;
    color: var(--cc-text-tertiary);
}

.mensaje-vacio svg {
    color: var(--cc-text-tertiary);
}

.badge-admin {
    background: linear-gradient(135deg, #E6332A 0%, #c72a22 100%);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(230, 51, 42, 0.3);
}

.badge-nuevo {
    color: var(--cc-primary);
    font-size: 11px;
    font-weight: 600;
}

/* Badge nuevo en mensajes rojos */
.mensaje.usuario2 .badge-nuevo {
    color: rgba(255, 255, 255, 0.9);
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
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] main.container {
    background: #111827 !important;
}

/* Breadcrumbs */
:root[data-theme="dark"] .breadcrumbs a {
    color: #9CA3AF !important;
}

/* Títulos */
:root[data-theme="dark"] .h1,
:root[data-theme="dark"] h1.h1 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .h3,
:root[data-theme="dark"] h3.h3 {
    color: #F3F4F6 !important;
}

/* Panel de conversaciones */
:root[data-theme="dark"] .conversaciones-panel {
    background-color: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .conversaciones-header {
    background-color: #111827;
    border-bottom-color: #374151;
}

:root[data-theme="dark"] .conversaciones-lista {
    background-color: #1F2937;
}

:root[data-theme="dark"] .conversacion-item {
    color: #F3F4F6;
}

:root[data-theme="dark"] .conversacion-item:hover {
    background-color: #374151;
}

:root[data-theme="dark"] .conversacion-item.activa {
    background-color: rgba(230, 51, 42, 0.15);
    border-left-color: var(--cc-primary);
}

:root[data-theme="dark"] .conversacion-foto {
    background-color: #374151;
}

:root[data-theme="dark"] .conversacion-titulo {
    color: #F3F4F6;
}

:root[data-theme="dark"] .conversacion-usuarios {
    color: #9CA3AF;
}

:root[data-theme="dark"] .conversacion-ultimo-mensaje {
    color: #6B7280;
}

:root[data-theme="dark"] .conversacion-fecha {
    color: #6B7280;
}

:root[data-theme="dark"] .chat-panel {
    background-color: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .chat-header {
    background-color: #111827;
    border-bottom-color: #374151;
}

:root[data-theme="dark"] .chat-header-titulo h3 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .chat-header-titulo p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .chat-header-info h3 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .chat-header-info p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .chat-mensajes {
    background-color: #111827;
}

/* Mensajes */
:root[data-theme="dark"] .mensaje-avatar {
    border-color: #4B5563 !important;
}

:root[data-theme="dark"] .mensaje-avatar-placeholder {
    background: rgba(230, 51, 42, 0.15) !important;
    border-color: #4B5563 !important;
}

:root[data-theme="dark"] .mensaje-contenido {
    background: #374151 !important;
    border-color: #4B5563 !important;
    color: #F3F4F6 !important;
}

/* Mensajes rojos en dark mode */
:root[data-theme="dark"] .mensaje.usuario2 .mensaje-contenido {
    background: var(--cc-primary) !important;
    color: white !important;
    border-color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .mensaje-autor {
    color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .mensaje.usuario2 .mensaje-autor {
    color: rgba(255, 255, 255, 0.9) !important;
}

:root[data-theme="dark"] .mensaje-texto {
    color: inherit !important;
}

:root[data-theme="dark"] .mensaje.usuario2 .mensaje-texto {
    color: white !important;
}

:root[data-theme="dark"] .mensaje-fecha {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .mensaje.usuario2 .mensaje-fecha {
    color: rgba(255, 255, 255, 0.8) !important;
}

:root[data-theme="dark"] .mensaje-vacio {
    color: #6B7280;
}

:root[data-theme="dark"] .mensaje-vacio svg {
    color: #6B7280;
}

:root[data-theme="dark"] .badge-admin {
    background: linear-gradient(135deg, var(--cc-primary) 0%, #c72a22 100%);
}
</style>

<main class="container" style="max-width: 1400px; padding: 24px;">
    
    <!-- Breadcrumb -->
    <div class="breadcrumbs" style="margin-bottom: 16px;">
        <a href="<?php echo BASE_URL; ?>/admin">← Volver al panel admin</a>
    </div>

    <!-- Título -->
    <h1 class="h1" style="margin-bottom: 24px;">Sistema de Mensajería</h1>

    <!-- Container de mensajería -->
    <div class="mensajeria-container">
        
        <!-- PANEL IZQUIERDO: Lista de conversaciones -->
        <aside class="conversaciones-panel">
            <div class="conversaciones-header">
                <h3 class="h3" style="margin: 0;">Todas las Conversaciones</h3>
                <p style="font-size: 12px; color: #999; margin: 4px 0 0 0;">
                    <?php echo count($conversaciones); ?> conversaciones activas
                </p>
            </div>
            
            <div class="conversaciones-lista">
                <?php if (empty($conversaciones)): ?>
                    <div style="padding: 32px 16px; text-align: center; color: #999;">
                        <p>No hay conversaciones en el sistema</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversaciones as $conv): ?>
                        <?php 
                            $esActiva = $conversacionActiva && 
                                       $conversacionActiva['conversacion_key'] == $conv['conversacion_key'];
                        ?>
                        <a 
                            href="<?php echo BASE_URL; ?>/admin/mensajes?conversacion=<?php echo $conv['conversacion_key']; ?>" 
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
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <div class="conversacion-info">
                                <h4 class="conversacion-titulo">
                                    <?php echo htmlspecialchars($conv['publicacion_titulo']); ?>
                                </h4>
                                <p class="conversacion-usuarios">
                                    <?php echo htmlspecialchars($conv['conversacion_titulo']); ?>
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
                        <h3><?php echo htmlspecialchars($conversacionActiva['publicacion_titulo']); ?></h3>
                        <p>
                            Conversación entre 
                            <strong><?php echo htmlspecialchars($conversacionActiva['usuario1_nombre']); ?></strong>
                            y
                            <strong><?php echo htmlspecialchars($conversacionActiva['usuario2_nombre']); ?></strong>
                        </p>
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
                        </div>
                    <?php else: ?>
                        <?php foreach ($mensajes as $msg): ?>
                            <?php 
                            // Obtener avatar del remitente
                            $avatarUrl = !empty($msg->remitente_avatar) 
                                ? BASE_URL . '/uploads/avatars/' . $msg->remitente_avatar
                                : null;
                            $avatarInicial = strtoupper(substr($msg->remitente_nombre, 0, 1));
                            
                            // Determinar si es usuario1 o usuario2 basado en el ID del remitente
                            $esUsuario1 = ($msg->remitente_id == $conversacionActiva['usuario1_id']);
                            $claseUsuario = $esUsuario1 ? 'usuario1' : 'usuario2';
                            ?>
                            <div class="mensaje <?php echo $claseUsuario; ?>">
                                <?php if ($avatarUrl): ?>
                                    <img src="<?php echo $avatarUrl; ?>" 
                                         alt="Avatar" 
                                         class="mensaje-avatar">
                                <?php else: ?>
                                    <div class="mensaje-avatar-placeholder">
                                        <?php echo $avatarInicial; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mensaje-contenido">
                                    <div class="mensaje-header">
                                        <span class="mensaje-autor">
                                            <?php echo htmlspecialchars($msg->remitente_nombre . ' ' . $msg->remitente_apellido); ?>
                                        </span>
                                        <?php if (isset($msg->remitente_rol) && $msg->remitente_rol === 'admin'): ?>
                                            <span class="badge-admin">ADMIN</span>
                                        <?php endif; ?>
                                        <?php if (!$msg->leido): ?>
                                            <span class="badge-nuevo">● Nuevo</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mensaje-texto"><?php echo nl2br(htmlspecialchars($msg->mensaje)); ?></p>
                                    <div class="mensaje-fecha"><?php echo $msg->fecha_formateada; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

<script>
// Scroll automático al final de los mensajes
const chatMensajes = document.getElementById('chatMensajes');
if (chatMensajes) {
    chatMensajes.scrollTop = chatMensajes.scrollHeight;
}

// ============================================
// POLLING AUTOMÁTICO PARA NUEVOS MENSAJES (ADMIN)
// ============================================
<?php if ($conversacionActiva && !empty($mensajes)): ?>
let ultimoMensajeId = <?php echo end($mensajes)->id; ?>;
<?php elseif ($conversacionActiva): ?>
let ultimoMensajeId = 0;
<?php endif; ?>

<?php if ($conversacionActiva): ?>
// Extraer IDs de usuarios de la clave de conversación
const conversacionKey = '<?php echo $conversacionActiva['conversacion_key']; ?>';
const partes = conversacionKey.split('-');
const publicacionId = parseInt(partes[0]);
const usuario1Id = parseInt(partes[1]);
const usuario2Id = parseInt(partes[2]);

// Función para verificar nuevos mensajes
function verificarNuevosMensajes() {
    // Usar el endpoint de obtener nuevos mensajes
    // Necesitamos pasar uno de los usuarios como "otro_usuario_id"
    fetch(`<?php echo BASE_URL; ?>/mensajes/obtener-nuevos?publicacion_id=${publicacionId}&otro_usuario_id=${usuario2Id}&ultimo_mensaje_id=${ultimoMensajeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mensajes && data.mensajes.length > 0) {
                const chatMensajes = document.getElementById('chatMensajes');
                
                // Verificar si el usuario está al final del scroll
                const estaAlFinal = chatMensajes.scrollHeight - chatMensajes.scrollTop <= chatMensajes.clientHeight + 100;
                
                // Agregar cada mensaje nuevo
                data.mensajes.forEach(msg => {
                    // Determinar el nombre del remitente
                    const esUsuario1 = msg.remitente_id == usuario1Id;
                    const nombreRemitente = esUsuario1 ? '<?php echo htmlspecialchars($conversacionActiva['usuario1_nombre']); ?>' : '<?php echo htmlspecialchars($conversacionActiva['usuario2_nombre']); ?>';
                    const inicialRemitente = nombreRemitente.charAt(0).toUpperCase();
                    
                    const mensajeHtml = `
                        <div class="mensaje">
                            <div class="mensaje-avatar">
                                ${inicialRemitente}
                            </div>
                            <div class="mensaje-contenido">
                                <div class="mensaje-header">
                                    <span class="mensaje-autor">${escapeHtml(nombreRemitente)}</span>
                                    <span class="mensaje-fecha">${msg.fecha_formateada}</span>
                                    ${!msg.leido ? '<span style="color: var(--cc-primary); font-size: 11px;">● No leído</span>' : ''}
                                </div>
                                <div class="mensaje-texto">
                                    ${escapeHtml(msg.mensaje).replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    chatMensajes.insertAdjacentHTML('beforeend', mensajeHtml);
                    
                    // Actualizar último mensaje ID
                    ultimoMensajeId = msg.id;
                });
                
                // Hacer scroll solo si el usuario estaba al final
                if (estaAlFinal) {
                    chatMensajes.scrollTop = chatMensajes.scrollHeight;
                }
                
                // Mostrar notificación si la página está oculta
                if (document.hidden) {
                    document.title = '(Nuevo) Mensajes - Admin';
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar nuevos mensajes:', error);
        });
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
        document.title = 'Mensajes - Panel Admin';
    }
});
<?php endif; ?>
</script>

<!-- Script de tema (modo claro/oscuro) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    
    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
    
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

</main>
</body>
</html>
