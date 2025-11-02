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

<!-- CSS específico para mensajería admin -->
<style>
.admin-mensajeria-container {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 24px;
    margin-top: 24px;
    height: calc(100vh - 250px);
    max-height: 700px;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    padding: 0 24px;
}

.conversaciones-panel {
    background: var(--cc-bg-surface);
    border: 1px solid var(--cc-border-default);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.conversaciones-header {
    padding: 16px;
    border-bottom: 1px solid var(--cc-border-default);
    background: var(--cc-bg-muted);
}

.conversaciones-lista {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
    background: var(--cc-bg-surface);
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
    background: var(--cc-bg-muted);
}

.conversacion-item.activa {
    background: var(--cc-primary-pale);
    border-left: 3px solid var(--cc-primary);
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
    font-size: 13px;
    font-weight: 600;
    color: var(--cc-text-primary);
    margin: 0 0 4px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversacion-usuarios {
    font-size: 12px;
    color: var(--cc-text-secondary);
    margin-bottom: 4px;
}

.conversacion-ultimo-mensaje {
    font-size: 12px;
    color: var(--cc-text-tertiary);
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
    background: var(--cc-primary);
    color: var(--cc-white);
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
    min-width: 20px;
    text-align: center;
}

.chat-panel {
    background: var(--cc-bg-surface);
    border: 1px solid var(--cc-border-default);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--cc-border-default);
    background: var(--cc-bg-muted);
}

.chat-header-info h3 {
    margin: 0 0 8px 0;
    font-size: 16px;
    color: var(--cc-text-primary);
}

.chat-header-info p {
    margin: 0;
    font-size: 13px;
    color: var(--cc-text-secondary);
}

.chat-mensajes {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: var(--cc-bg-default);
}

.mensaje {
    display: flex;
    margin-bottom: 16px;
    gap: 12px;
}

/* Avatares con colores únicos por usuario */
.mensaje-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.mensaje-avatar:hover {
    transform: scale(1.1);
}

/* Colores únicos por usuario usando paleta del proyecto */
.mensaje-avatar[data-user-id="1"] { background: #E6332A; color: white; }
.mensaje-avatar[data-user-id="2"] { background: #c72a22; color: white; }
.mensaje-avatar[data-user-id="3"] { background: #2E2E2E; color: white; }
.mensaje-avatar[data-user-id="4"] { background: #666666; color: white; }
.mensaje-avatar[data-user-id="5"] { background: #999999; color: white; }
.mensaje-avatar[data-user-id="6"] { background: #10B981; color: white; }
.mensaje-avatar[data-user-id="7"] { background: #F59E0B; color: white; }
.mensaje-avatar[data-user-id="8"] { background: #3B82F6; color: white; }
.mensaje-avatar[data-user-id="9"] { background: #8B5CF6; color: white; }
.mensaje-avatar[data-user-id="10"] { background: #EC4899; color: white; }

/* Color por defecto para otros usuarios */
.mensaje-avatar:not([data-user-id]) {
    background: #E6332A;
    color: white;
}

.mensaje-contenido {
    max-width: 70%;
}

.mensaje-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.mensaje-autor {
    font-size: 14px;
    font-weight: 700;
    color: var(--cc-text-primary);
    padding: 2px 8px;
    border-radius: 6px;
    background: var(--cc-bg-muted);
}

/* Colores de nombre según usuario usando paleta del proyecto */
.mensaje-item[data-user-id="1"] .mensaje-autor { color: #E6332A; background: rgba(230, 51, 42, 0.1); }
.mensaje-item[data-user-id="2"] .mensaje-autor { color: #c72a22; background: rgba(199, 42, 34, 0.1); }
.mensaje-item[data-user-id="3"] .mensaje-autor { color: #2E2E2E; background: rgba(46, 46, 46, 0.1); }
.mensaje-item[data-user-id="4"] .mensaje-autor { color: #666666; background: rgba(102, 102, 102, 0.1); }
.mensaje-item[data-user-id="5"] .mensaje-autor { color: #999999; background: rgba(153, 153, 153, 0.1); }
.mensaje-item[data-user-id="6"] .mensaje-autor { color: #10B981; background: rgba(16, 185, 129, 0.1); }
.mensaje-item[data-user-id="7"] .mensaje-autor { color: #F59E0B; background: rgba(245, 158, 11, 0.1); }
.mensaje-item[data-user-id="8"] .mensaje-autor { color: #3B82F6; background: rgba(59, 130, 246, 0.1); }
.mensaje-item[data-user-id="9"] .mensaje-autor { color: #8B5CF6; background: rgba(139, 92, 246, 0.1); }
.mensaje-item[data-user-id="10"] .mensaje-autor { color: #EC4899; background: rgba(236, 72, 153, 0.1); }

.mensaje-fecha {
    font-size: 11px;
    color: var(--cc-text-tertiary);
    font-weight: 500;
}

.mensaje-texto {
    padding: 12px 16px;
    border-radius: 12px;
    background: var(--cc-bg-surface);
    border: 2px solid var(--cc-border-default);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s;
    color: var(--cc-text-primary);
}

.mensaje-texto:hover {
    border-color: var(--cc-primary);
    box-shadow: 0 2px 8px rgba(230, 51, 42, 0.1);
}

.mensaje-item {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    padding: 12px;
    border-radius: 12px;
    transition: all 0.2s;
}

.mensaje-item:hover {
    background: rgba(0,0,0,0.02);
    transform: translateX(4px);
}

/* Borde lateral según usuario usando paleta del proyecto */
.mensaje-item[data-user-id="1"] { border-left: 4px solid #E6332A; }
.mensaje-item[data-user-id="2"] { border-left: 4px solid #c72a22; }
.mensaje-item[data-user-id="3"] { border-left: 4px solid #2E2E2E; }
.mensaje-item[data-user-id="4"] { border-left: 4px solid #666666; }
.mensaje-item[data-user-id="5"] { border-left: 4px solid #999999; }
.mensaje-item[data-user-id="6"] { border-left: 4px solid #10B981; }
.mensaje-item[data-user-id="7"] { border-left: 4px solid #F59E0B; }
.mensaje-item[data-user-id="8"] { border-left: 4px solid #3B82F6; }
.mensaje-item[data-user-id="9"] { border-left: 4px solid #8B5CF6; }
.mensaje-item[data-user-id="10"] { border-left: 4px solid #EC4899; }

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

/* Responsive */
@media (max-width: 968px) {
    .admin-mensajeria-container {
        grid-template-columns: 1fr;
        height: auto;
        max-height: none;
        padding: 0 16px;
    }
    
    .conversaciones-panel {
        max-height: 300px;
    }
    
    .chat-panel {
        min-height: 500px;
    }
}

/* ============================================================================
 * DARK MODE - MENSAJERÍA ADMIN
 * ============================================================================ */

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

:root[data-theme="dark"] .chat-header-info h3 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .chat-header-info p {
    color: #9CA3AF;
}

:root[data-theme="dark"] .chat-header-info a {
    color: var(--cc-primary);
}

:root[data-theme="dark"] .chat-mensajes {
    background-color: #111827;
}

:root[data-theme="dark"] .mensaje-item {
    background-color: transparent;
}

:root[data-theme="dark"] .mensaje-item:hover {
    background-color: rgba(55, 65, 81, 0.3);
}

:root[data-theme="dark"] .mensaje-autor {
    color: #F3F4F6;
    background-color: #374151;
}

:root[data-theme="dark"] .mensaje-fecha {
    color: #9CA3AF;
}

:root[data-theme="dark"] .mensaje-texto {
    background-color: #1F2937;
    border-color: #374151;
    color: #F3F4F6;
}

:root[data-theme="dark"] .mensaje-texto:hover {
    border-color: var(--cc-primary);
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

/* Avatares en dark mode - mantener colores pero ajustar borde */
:root[data-theme="dark"] .mensaje-avatar {
    border-color: #1F2937;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* Colores de nombre en dark mode */
:root[data-theme="dark"] .mensaje-item[data-user-id="1"] .mensaje-autor { 
    color: #FCA5A5; 
    background: rgba(230, 51, 42, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="2"] .mensaje-autor { 
    color: #FCA5A5; 
    background: rgba(199, 42, 34, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="3"] .mensaje-autor { 
    color: #D1D5DB; 
    background: rgba(46, 46, 46, 0.3); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="4"] .mensaje-autor { 
    color: #D1D5DB; 
    background: rgba(102, 102, 102, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="5"] .mensaje-autor { 
    color: #D1D5DB; 
    background: rgba(153, 153, 153, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="6"] .mensaje-autor { 
    color: #6EE7B7; 
    background: rgba(16, 185, 129, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="7"] .mensaje-autor { 
    color: #FCD34D; 
    background: rgba(245, 158, 11, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="8"] .mensaje-autor { 
    color: #93C5FD; 
    background: rgba(59, 130, 246, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="9"] .mensaje-autor { 
    color: #C4B5FD; 
    background: rgba(139, 92, 246, 0.2); 
}
:root[data-theme="dark"] .mensaje-item[data-user-id="10"] .mensaje-autor { 
    color: #F9A8D4; 
    background: rgba(236, 72, 153, 0.2); 
}
</style>

<main class="admin-container" style="max-width: 1400px; margin: 0 auto; padding: 24px;">
    
    <!-- Header del panel admin -->
    <div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 class="h1">Sistema de Mensajería</h1>
            <p class="text-secondary">Monitoreo de todas las conversaciones del sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-outline">
            ← Volver al panel
        </a>
    </div>

    <!-- Container de mensajería -->
    <div class="admin-mensajeria-container">
        
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
                            <strong><?php echo htmlspecialchars($conversacionActiva['usuario1_nombre']); ?></strong>
                            (<?php echo ucfirst($conversacionActiva['usuario1_rol']); ?>)
                            ↔
                            <strong><?php echo htmlspecialchars($conversacionActiva['usuario2_nombre']); ?></strong>
                            (<?php echo ucfirst($conversacionActiva['usuario2_rol']); ?>)
                        </p>
                        <p style="font-size: 12px; margin-top: 4px;">
                            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $conversacionActiva['publicacion_id']; ?>" 
                               target="_blank" 
                               style="color: var(--cc-primary);">
                                Ver publicación →
                            </a>
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
                            <div class="mensaje-item" data-user-id="<?php echo $msg->remitente_id; ?>">
                                <div class="mensaje-avatar" data-user-id="<?php echo $msg->remitente_id; ?>">
                                    <?php echo strtoupper(substr($msg->remitente_nombre, 0, 1)); ?>
                                </div>
                                <div class="mensaje-contenido">
                                    <div class="mensaje-header">
                                        <span class="mensaje-autor">
                                            <?php echo htmlspecialchars($msg->remitente_nombre . ' ' . $msg->remitente_apellido); ?>
                                        </span>
                                        <?php if (isset($msg->remitente_rol) && $msg->remitente_rol === 'admin'): ?>
                                            <span class="badge-admin">ADMIN</span>
                                        <?php endif; ?>
                                        <span class="mensaje-fecha"><?php echo $msg->fecha_formateada; ?></span>
                                        <?php if (!$msg->leido): ?>
                                            <span style="color: var(--cc-primary); font-size: 11px; font-weight: 600;">● Nuevo</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mensaje-texto">
                                        <?php echo nl2br(htmlspecialchars($msg->mensaje)); ?>
                                    </div>
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
