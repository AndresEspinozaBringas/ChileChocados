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

.conversacion-info {
    flex: 1;
    min-width: 0;
}

.conversacion-titulo {
    font-size: 13px;
    font-weight: 600;
    color: var(--cc-text-primary, #2E2E2E);
    margin: 0 0 4px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversacion-usuarios {
    font-size: 12px;
    color: var(--cc-text-secondary, #666);
    margin-bottom: 4px;
}

.conversacion-ultimo-mensaje {
    font-size: 12px;
    color: var(--cc-text-tertiary, #999);
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

.chat-header-info h3 {
    margin: 0 0 8px 0;
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

/* Colores únicos por usuario (basado en ID) */
.mensaje-avatar[data-user-id="1"] { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.mensaje-avatar[data-user-id="2"] { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.mensaje-avatar[data-user-id="3"] { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
.mensaje-avatar[data-user-id="4"] { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
.mensaje-avatar[data-user-id="5"] { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
.mensaje-avatar[data-user-id="6"] { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white; }
.mensaje-avatar[data-user-id="7"] { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
.mensaje-avatar[data-user-id="8"] { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; }
.mensaje-avatar[data-user-id="9"] { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333; }
.mensaje-avatar[data-user-id="10"] { background: linear-gradient(135deg, #ff6e7f 0%, #bfe9ff 100%); color: white; }

/* Color por defecto para otros usuarios */
.mensaje-avatar:not([data-user-id]) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    color: var(--cc-text-primary, #2E2E2E);
    padding: 2px 8px;
    border-radius: 6px;
    background: rgba(0,0,0,0.03);
}

/* Colores de nombre según usuario */
.mensaje-item[data-user-id="1"] .mensaje-autor { color: #667eea; background: rgba(102, 126, 234, 0.1); }
.mensaje-item[data-user-id="2"] .mensaje-autor { color: #f5576c; background: rgba(245, 87, 108, 0.1); }
.mensaje-item[data-user-id="3"] .mensaje-autor { color: #00a8e8; background: rgba(0, 168, 232, 0.1); }
.mensaje-item[data-user-id="4"] .mensaje-autor { color: #00d084; background: rgba(0, 208, 132, 0.1); }
.mensaje-item[data-user-id="5"] .mensaje-autor { color: #fa709a; background: rgba(250, 112, 154, 0.1); }
.mensaje-item[data-user-id="6"] .mensaje-autor { color: #30cfd0; background: rgba(48, 207, 208, 0.1); }
.mensaje-item[data-user-id="7"] .mensaje-autor { color: #38b2ac; background: rgba(56, 178, 172, 0.1); }
.mensaje-item[data-user-id="8"] .mensaje-autor { color: #ff9a9e; background: rgba(255, 154, 158, 0.1); }
.mensaje-item[data-user-id="9"] .mensaje-autor { color: #fcb69f; background: rgba(252, 182, 159, 0.1); }
.mensaje-item[data-user-id="10"] .mensaje-autor { color: #ff6e7f; background: rgba(255, 110, 127, 0.1); }

.mensaje-fecha {
    font-size: 11px;
    color: var(--cc-text-tertiary, #999);
    font-weight: 500;
}

.mensaje-texto {
    padding: 12px 16px;
    border-radius: 12px;
    background: var(--cc-white, #FFFFFF);
    border: 2px solid var(--cc-border-default, #E5E5E5);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s;
}

.mensaje-texto:hover {
    border-color: var(--cc-primary, #E6332A);
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

/* Borde lateral según usuario */
.mensaje-item[data-user-id="1"] { border-left: 4px solid #667eea; }
.mensaje-item[data-user-id="2"] { border-left: 4px solid #f5576c; }
.mensaje-item[data-user-id="3"] { border-left: 4px solid #00a8e8; }
.mensaje-item[data-user-id="4"] { border-left: 4px solid #00d084; }
.mensaje-item[data-user-id="5"] { border-left: 4px solid #fa709a; }
.mensaje-item[data-user-id="6"] { border-left: 4px solid #30cfd0; }
.mensaje-item[data-user-id="7"] { border-left: 4px solid #38b2ac; }
.mensaje-item[data-user-id="8"] { border-left: 4px solid #ff9a9e; }
.mensaje-item[data-user-id="9"] { border-left: 4px solid #fcb69f; }
.mensaje-item[data-user-id="10"] { border-left: 4px solid #ff6e7f; }

.mensaje-vacio {
    text-align: center;
    padding: 48px 20px;
    color: var(--cc-text-tertiary, #999);
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
    }
    
    .conversaciones-panel {
        max-height: 300px;
    }
    
    .chat-panel {
        min-height: 500px;
    }
}
</style>

<main class="admin-container">
    
    <!-- Header del panel admin -->
    <div class="admin-header">
        <div>
            <h1 class="h1">Sistema de Mensajería</h1>
            <p class="text-secondary">Monitoreo de todas las conversaciones del sistema</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin" class="btn">
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
