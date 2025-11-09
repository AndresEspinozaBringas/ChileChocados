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
    align-items: flex-end;
}

.mensaje.enviado {
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
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .conversaciones-header {
    background: #374151 !important;
    border-bottom-color: #4B5563 !important;
}

:root[data-theme="dark"] .conversacion-item {
    color: #D1D5DB !important;
}

:root[data-theme="dark"] .conversacion-item:hover {
    background: #374151 !important;
}

:root[data-theme="dark"] .conversacion-item.activa {
    background: rgba(230, 51, 42, 0.15) !important;
    border-left-color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .conversacion-foto {
    background: #374151 !important;
}

:root[data-theme="dark"] .conversacion-titulo {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .conversacion-usuario {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .conversacion-ultimo-mensaje {
    color: #D1D5DB !important;
}

:root[data-theme="dark"] .conversacion-fecha {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .badge-no-leidos {
    background: var(--cc-primary) !important;
    color: white !important;
}

/* Panel de chat */
:root[data-theme="dark"] .chat-panel {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .chat-header {
    background: #374151 !important;
    border-bottom-color: #4B5563 !important;
}

:root[data-theme="dark"] .chat-header-titulo h3 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .chat-header-titulo p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .chat-mensajes {
    background: #111827 !important;
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

:root[data-theme="dark"] .mensaje.enviado .mensaje-contenido {
    background: var(--cc-primary) !important;
    color: white !important;
    border-color: var(--cc-primary) !important;
}

:root[data-theme="dark"] .mensaje-texto {
    color: inherit !important;
}

:root[data-theme="dark"] .mensaje-fecha {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .mensaje.enviado .mensaje-fecha {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* Input de chat */
:root[data-theme="dark"] .chat-input-container {
    background: #1F2937 !important;
    border-top-color: #374151 !important;
}

:root[data-theme="dark"] .chat-input {
    background: #374151 !important;
    border-color: #4B5563 !important;
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .chat-input::placeholder {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .chat-input:focus {
    border-color: var(--cc-primary) !important;
    box-shadow: 0 0 0 3px rgba(240, 80, 69, 0.2) !important;
}

:root[data-theme="dark"] .btn-enviar {
    background: var(--cc-primary) !important;
    color: white !important;
}

:root[data-theme="dark"] .btn-enviar:hover {
    background: #c72a22 !important;
}

:root[data-theme="dark"] .btn-enviar:disabled {
    background: #4B5563 !important;
    color: #9CA3AF !important;
}

/* Mensaje vacío */
:root[data-theme="dark"] .mensaje-vacio {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .mensaje-vacio h3 {
    color: #D1D5DB !important;
}

:root[data-theme="dark"] .mensaje-vacio p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .mensaje-vacio svg {
    color: #6B7280 !important;
}

/* Divs con estilos inline */
:root[data-theme="dark"] div[style*="padding: 32px 16px"] {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="padding: 32px 16px"] p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="background: var(--cc-primary-pale"] {
    background: rgba(230, 51, 42, 0.15) !important;
}

/* Iconos */
:root[data-theme="dark"] .icon-usuario {
    color: var(--cc-primary) !important;
}

/* Separadores en conversación */
:root[data-theme="dark"] .conversacion-usuario span[style*="color: #ccc"] {
    color: #6B7280 !important;
}

/* Botón Comprar */
:root[data-theme="dark"] .btn-comprar {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
}

:root[data-theme="dark"] .btn-comprar:hover {
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4) !important;
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
                        
                        <?php 
                        // Mostrar botón "Comprar" solo si:
                        // 1. NO eres el dueño de la publicación
                        // 2. NO eres admin
                        
                        // Obtener el dueño de la publicación
                        $publicacionModel = new \App\Models\Publicacion();
                        $publicacion = $publicacionModel->find($conversacionActiva['publicacion_id']);
                        $esDuenoPublicacion = $publicacion && $publicacion->usuario_id == $_SESSION['user_id'];
                        $esAdmin = isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
                        
                        if (!$esDuenoPublicacion && !$esAdmin): 
                        ?>
                            <button 
                                onclick="enviarSolicitudCompra(<?php echo $conversacionActiva['publicacion_id']; ?>, <?php echo $conversacionActiva['otro_usuario_id']; ?>)"
                                class="btn-comprar"
                                style="
                                    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                                    color: white;
                                    border: none;
                                    padding: 10px 20px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    font-size: 14px;
                                    cursor: pointer;
                                    display: flex;
                                    align-items: center;
                                    gap: 8px;
                                    transition: all 0.2s;
                                    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
                                    white-space: nowrap;
                                "
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.2)'"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Comprar
                            </button>
                        <?php endif; ?>
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
                            <?php 
                            $esEnviado = ($msg->remitente_id == $userId);
                            // Determinar avatar a mostrar
                            if ($esEnviado) {
                                // Mensaje enviado - mostrar avatar del usuario actual
                                $avatarUrl = !empty($_SESSION['user_avatar']) 
                                    ? BASE_URL . '/uploads/avatars/' . $_SESSION['user_avatar']
                                    : null;
                                $avatarInicial = strtoupper(substr($_SESSION['user_nombre'], 0, 1));
                            } else {
                                // Mensaje recibido - mostrar avatar del otro usuario
                                $avatarUrl = !empty($conversacionActiva['otro_usuario_avatar']) 
                                    ? BASE_URL . '/uploads/avatars/' . $conversacionActiva['otro_usuario_avatar']
                                    : null;
                                $avatarInicial = strtoupper(substr($conversacionActiva['otro_usuario_nombre'], 0, 1));
                            }
                            ?>
                            <div class="mensaje <?php echo $esEnviado ? 'enviado' : 'recibido'; ?>">
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

<!-- Modal: Solicitud de Compra -->
<div id="modalSolicitudCompra" class="admin-modal" style="display: none;">
    <div class="admin-modal-content admin-modal-small">
        <div class="admin-modal-header">
            <h2 class="h2" style="margin: 0;">Solicitud de Compra</h2>
        </div>
        <div class="admin-modal-body">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 16px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
            </div>
            <p class="meta" style="margin-bottom: 16px; line-height: 1.6; text-align: center; font-size: 15px;">
                ¿Deseas enviar una solicitud de compra al vendedor?
            </p>
            <p class="meta" style="margin-bottom: 24px; color: #10B981; text-align: center; font-size: 14px;">
                Se le notificará de tu interés en adquirir este vehículo.
            </p>
            
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" onclick="cerrarModalCompra()" class="btn outline" style="min-width: 120px;">
                    Cancelar
                </button>
                <button 
                    type="button" 
                    onclick="confirmarSolicitudCompra(this.closest('.admin-modal').dataset.publicacionId, this.closest('.admin-modal').dataset.vendedorId)" 
                    class="btn" 
                    style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: none; min-width: 120px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);"
                >
                    Sí, enviar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal overlay */
.admin-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.75);
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Modal content container */
.admin-modal-content {
    background-color: #FFFFFF;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    animation: adminModalFadeIn 0.3s ease-out;
    border: 1px solid rgba(0, 0, 0, 0.1);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.admin-modal-small {
    max-width: 500px;
    width: 95%;
}

/* Modal header */
.admin-modal-header {
    padding: 24px 32px;
    border-bottom: 2px solid #E5E7EB;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

/* Modal body */
.admin-modal-body {
    padding: 32px;
    overflow-y: auto;
    flex: 1;
}

@keyframes adminModalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Dark mode para modal */
:root[data-theme="dark"] .admin-modal {
    background-color: rgba(0, 0, 0, 0.85);
}

:root[data-theme="dark"] .admin-modal-content {
    background-color: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .admin-modal-header {
    border-bottom-color: #374151;
}

:root[data-theme="dark"] .admin-modal-header h2 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .admin-modal-body {
    color: #D1D5DB;
}

:root[data-theme="dark"] .admin-modal-body p {
    color: #D1D5DB;
}

:root[data-theme="dark"] .admin-modal-body p[style*="color: #10B981"] {
    color: #6EE7B7 !important;
}

:root[data-theme="dark"] .admin-modal-body div[style*="background: linear-gradient(135deg, #10B981"] {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
}

/* Cerrar modal al hacer click fuera */
</style>

<script>
// Cerrar modal al hacer click fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalSolicitudCompra');
    if (modal && e.target === modal) {
        cerrarModalCompra();
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalCompra();
    }
});
</script>

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
            
            // Avatar del usuario actual
            const avatarHtml = <?php if (!empty($_SESSION['user_avatar'])): ?>
                `<img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $_SESSION['user_avatar']; ?>" alt="Avatar" class="mensaje-avatar">`;
            <?php else: ?>
                `<div class="mensaje-avatar-placeholder"><?php echo strtoupper(substr($_SESSION['user_nombre'], 0, 1)); ?></div>`;
            <?php endif; ?>
            
            const mensajeHtml = `
                <div class="mensaje enviado">
                    ${avatarHtml}
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
            
            // IMPORTANTE: Actualizar ultimoMensajeId para evitar duplicados en el polling
            if (data.mensaje.id && typeof ultimoMensajeId !== 'undefined') {
                ultimoMensajeId = data.mensaje.id;
            }
            
            // Limpiar input
            inputMensaje.value = '';
            inputMensaje.style.height = 'auto';
            
            // Disparar evento personalizado para que otras partes de la app puedan reaccionar
            window.dispatchEvent(new CustomEvent('mensajeEnviado', { 
                detail: { mensaje: data.mensaje } 
            }));
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
                    // Determinar avatar según quién envió el mensaje
                    let avatarHtml;
                    if (msg.es_propio) {
                        // Mensaje propio
                        avatarHtml = <?php if (!empty($_SESSION['user_avatar'])): ?>
                            `<img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $_SESSION['user_avatar']; ?>" alt="Avatar" class="mensaje-avatar">`;
                        <?php else: ?>
                            `<div class="mensaje-avatar-placeholder"><?php echo strtoupper(substr($_SESSION['user_nombre'], 0, 1)); ?></div>`;
                        <?php endif; ?>
                    } else {
                        // Mensaje del otro usuario
                        avatarHtml = <?php if (!empty($conversacionActiva['otro_usuario_avatar'])): ?>
                            `<img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $conversacionActiva['otro_usuario_avatar']; ?>" alt="Avatar" class="mensaje-avatar">`;
                        <?php else: ?>
                            `<div class="mensaje-avatar-placeholder"><?php echo strtoupper(substr($conversacionActiva['otro_usuario_nombre'], 0, 1)); ?></div>`;
                        <?php endif; ?>
                    }
                    
                    const mensajeHtml = `
                        <div class="mensaje ${msg.es_propio ? 'enviado' : 'recibido'}">
                            ${avatarHtml}
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
                if (!ultimoMensaje.es_propio) {
                    if (document.hidden) {
                        // Actualizar título de la página para notificar
                        document.title = '(1) Nuevo mensaje - ChileChocados';
                    }
                    
                    // Disparar evento de mensaje recibido
                    window.dispatchEvent(new CustomEvent('mensajeRecibido', { 
                        detail: { mensaje: ultimoMensaje } 
                    }));
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
        // Actualizar contador cuando el usuario vuelve a la página
        if (typeof window.actualizarContadorMensajes === 'function') {
            window.actualizarContadorMensajes();
        }
    }
});

// Actualizar contador cuando se carga la página de mensajes
if (typeof window.actualizarContadorMensajes === 'function') {
    window.actualizarContadorMensajes();
}
<?php endif; ?>

// ============================================================================
// FUNCIÓN: Enviar Solicitud de Compra
// ============================================================================

async function enviarSolicitudCompra(publicacionId, vendedorId) {
    // Mostrar modal de confirmación
    mostrarModalCompra(publicacionId, vendedorId);
}

async function confirmarSolicitudCompra(publicacionId, vendedorId) {
    const btn = document.querySelector('.btn-comprar');
    
    // Cerrar modal
    cerrarModalCompra();
    
    // Deshabilitar botón
    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg> Enviando...';
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/solicitud-compra', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                publicacion_id: publicacionId,
                vendedor_id: vendedorId,
                csrf_token: '<?php echo generateCsrfToken(); ?>'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar mensaje de éxito
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> ¡Enviado!';
            btn.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
            
            // Mostrar notificación
            mostrarNotificacion('Solicitud enviada', 'El vendedor ha sido notificado de tu interés en comprar.', 'success');
            
            // Revertir botón después de 3 segundos
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg> Comprar';
            }, 3000);
        } else {
            throw new Error(data.message || 'Error al enviar solicitud');
        }
    } catch (error) {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg> Comprar';
        mostrarNotificacion('Error', error.message || 'No se pudo enviar la solicitud. Intenta nuevamente.', 'error');
    }
}

function mostrarModalCompra(publicacionId, vendedorId) {
    const modal = document.getElementById('modalSolicitudCompra');
    if (modal) {
        modal.style.display = 'flex';
        modal.dataset.publicacionId = publicacionId;
        modal.dataset.vendedorId = vendedorId;
    }
}

function cerrarModalCompra() {
    const modal = document.getElementById('modalSolicitudCompra');
    if (modal) {
        modal.style.display = 'none';
    }
}

function mostrarNotificacion(titulo, mensaje, tipo = 'info') {
    // Crear notificación toast
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 10000;
        background: ${tipo === 'success' ? '#10B981' : tipo === 'error' ? '#EF4444' : '#3B82F6'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    toast.innerHTML = `
        <div style="font-weight: 600; margin-bottom: 4px;">${titulo}</div>
        <div style="font-size: 14px; opacity: 0.9;">${mensaje}</div>
    `;
    
    document.body.appendChild(toast);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Agregar animaciones
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }
    `;
    document.head.appendChild(style);
}

</script>

<?php
// Cargar footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
