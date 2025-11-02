<?php
/**
 * Vista: Mis Pagos Pendientes
 * Permite al usuario ver y retomar pagos inconclusos
 */

$pageTitle = 'Mis Pagos Pendientes';
$currentPage = 'pagos-pendientes';

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* Variables de color - Siguiendo normas gráficas de /admin/mensajes */
:root {
    --cc-primary: #E6332A;
    --cc-primary-pale: #FFF5F4;
    --cc-white: #FFFFFF;
    --cc-bg-default: #FAFAFA;
    --cc-bg-surface: #F9F9F9;
    --cc-bg-muted: #F5F5F5;
    --cc-border-default: #E5E5E5;
    --cc-text-primary: #2E2E2E;
    --cc-text-secondary: #666666;
    --cc-text-tertiary: #999999;
    --cc-success: #10B981;
    --cc-warning: #F59E0B;
    --cc-danger: #EF4444;
}

/* Reset de estilos base */
* {
    box-sizing: border-box;
}

.pagos-container {
    max-width: 1200px;
    margin: 32px auto;
    padding: 0 20px;
}

.page-header {
    margin-bottom: 32px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin: 0 0 8px 0;
}

.page-header p {
    font-size: 15px;
    color: var(--cc-text-secondary);
    margin: 0;
}

.pagos-grid {
    display: grid;
    gap: 20px;
    margin-bottom: 32px;
}

.pago-card {
    background: var(--cc-white);
    border: 2px solid var(--cc-border-default);
    border-radius: 12px;
    padding: 24px;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.pago-card:hover {
    border-color: var(--cc-primary);
    box-shadow: 0 4px 12px rgba(230, 51, 42, 0.1);
    transform: translateY(-2px);
}

.pago-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--cc-primary);
}

.pago-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 16px;
}

.pago-info {
    flex: 1;
}

.pago-titulo {
    font-size: 18px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pago-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: var(--cc-text-secondary);
}

.pago-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.pago-foto {
    width: 120px;
    height: 90px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--cc-bg-muted);
    flex-shrink: 0;
    border: 1px solid var(--cc-border-default);
    display: flex;
    align-items: center;
    justify-content: center;
}

.pago-foto-placeholder {
    width: 120px;
    height: 90px;
    border-radius: 8px;
    background: var(--cc-bg-muted);
    flex-shrink: 0;
    border: 1px solid var(--cc-border-default);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cc-text-tertiary);
}

.pago-body {
    margin-bottom: 20px;
}

.pago-detalles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 16px;
    background: var(--cc-bg-surface);
    border-radius: 8px;
    margin-bottom: 16px;
}

.detalle-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detalle-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--cc-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.detalle-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--cc-text-primary);
    line-height: 1.4;
}

.detalle-value.monto {
    font-size: 28px;
    color: var(--cc-primary);
    font-weight: 800;
}

.pago-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.pago-acciones {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Los estilos de botones se heredan del CSS global */

.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-pendiente {
    background: rgba(245, 158, 11, 0.15);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.badge-en-proceso, .badge-en_proceso {
    background: rgba(59, 130, 246, 0.15);
    color: #2563EB;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-expirado {
    background: rgba(107, 114, 128, 0.15);
    color: #4B5563;
    border: 1px solid rgba(107, 114, 128, 0.3);
}

.alerta-expiracion {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-left: 4px solid #F59E0B;
    border-radius: 8px;
    font-size: 13px;
    color: #B45309;
    font-weight: 600;
}

.alerta-expiracion.urgente {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-left: 4px solid #EF4444;
    color: #B91C1C;
}

.empty-state {
    text-align: center;
    padding: 64px 20px;
    background: var(--cc-white);
    border: 2px dashed var(--cc-border-default);
    border-radius: 12px;
}

.empty-state svg {
    margin-bottom: 16px;
    opacity: 0.4;
    color: var(--cc-text-tertiary);
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin: 0 0 8px 0;
}

.empty-state p {
    font-size: 14px;
    color: var(--cc-text-secondary);
    margin: 0 0 24px 0;
    line-height: 1.6;
}

/* Mejoras de contraste y legibilidad */
.pago-card {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.pago-titulo svg {
    color: var(--cc-primary);
    flex-shrink: 0;
}

.pago-meta-item svg {
    color: var(--cc-text-tertiary);
    flex-shrink: 0;
}

.alerta-expiracion svg {
    flex-shrink: 0;
}



/* Responsive */
@media (max-width: 768px) {
    .pago-header {
        flex-direction: column;
    }
    
    .pago-foto {
        width: 100%;
        height: 200px;
    }
    
    .pago-footer {
        flex-direction: column;
        align-items: stretch;
    }
    
    .pago-acciones {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
        width: 100%;
    }
}
</style>

<main class="pagos-container">
    
    <!-- Header -->
    <div class="page-header">
        <h1>Mis Pagos Pendientes</h1>
        <p>Aquí puedes ver y retomar los pagos que no completaste</p>
    </div>

    <?php if (empty($pagosPendientes)): ?>
        
        <!-- Estado vacío -->
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 6v6l4 2"></path>
            </svg>
            <h3>No tienes pagos pendientes</h3>
            <p>Todos tus pagos están completos o no has iniciado ninguno</p>
            <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn primary">
                Ver mis publicaciones
            </a>
        </div>

    <?php else: ?>
        
        <!-- Grid de pagos pendientes -->
        <div class="pagos-grid">
            <?php foreach ($pagosPendientes as $pago): ?>
                <?php
                    // Calcular tiempo restante
                    $fechaExpiracion = strtotime($pago->fecha_expiracion ?? '+48 hours', strtotime($pago->fecha_creacion));
                    $tiempoRestante = $fechaExpiracion - time();
                    $horasRestantes = floor($tiempoRestante / 3600);
                    $esUrgente = $horasRestantes < 6;
                    $haExpirado = $tiempoRestante <= 0;
                ?>
                
                <div class="pago-card">
                    
                    <!-- Header -->
                    <div class="pago-header">
                        <div class="pago-info">
                            <h3 class="pago-titulo">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                                <?php echo htmlspecialchars($pago->publicacion_titulo); ?>
                            </h3>
                            <div class="pago-meta">
                                <span class="pago-meta-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Creado <?php echo timeAgo($pago->fecha_creacion); ?>
                                </span>
                                <span class="badge badge-<?php echo str_replace('_', '-', $pago->estado); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $pago->estado)); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($pago->foto_principal)): ?>
                            <img 
                                src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo htmlspecialchars($pago->foto_principal); ?>" 
                                alt="<?php echo htmlspecialchars($pago->publicacion_titulo); ?>"
                                class="pago-foto"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <div class="pago-foto-placeholder" style="display: none;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="pago-foto-placeholder">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Body -->
                    <div class="pago-body">
                        
                        <!-- Detalles del pago -->
                        <div class="pago-detalles">
                            <div class="detalle-item">
                                <span class="detalle-label">Monto</span>
                                <span class="detalle-value monto"><?php echo formatPrice($pago->monto); ?></span>
                            </div>
                            <div class="detalle-item">
                                <span class="detalle-label">Tipo</span>
                                <span class="detalle-value">
                                    <?php 
                                        $dias = str_replace('destacado_', '', $pago->tipo);
                                        echo "Destacado $dias días";
                                    ?>
                                </span>
                            </div>
                            <div class="detalle-item">
                                <span class="detalle-label">Orden</span>
                                <span class="detalle-value" style="font-size: 14px; font-family: monospace;">
                                    #<?php echo $pago->flow_orden; ?>
                                </span>
                            </div>
                            <div class="detalle-item">
                                <span class="detalle-label">Intentos</span>
                                <span class="detalle-value"><?php echo $pago->intentos ?? 1; ?></span>
                            </div>
                        </div>

                        <!-- Alerta de expiración -->
                        <?php if (!$haExpirado): ?>
                            <div class="alerta-expiracion <?php echo $esUrgente ? 'urgente' : ''; ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <?php if ($esUrgente): ?>
                                    <strong>¡Urgente!</strong> Este pago expira en <?php echo $horasRestantes; ?> horas
                                <?php else: ?>
                                    Este pago expira en <?php echo $horasRestantes; ?> horas
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alerta-expiracion urgente">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Este pago ha expirado
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer con acciones -->
                    <div class="pago-footer">
                        <div class="pago-acciones">
                            <?php if (!$haExpirado): ?>
                                <a href="<?php echo BASE_URL; ?>/pago/retomar/<?php echo $pago->id; ?>" class="btn primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                    </svg>
                                    Continuar pago
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/pago/preparar/<?php echo $pago->publicacion_id; ?>" class="btn primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Crear nuevo pago
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pago->publicacion_id; ?>" class="btn" target="_blank">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Ver publicación
                            </a>
                            
                            <button type="button" onclick="mostrarModalCancelar(<?php echo $pago->id; ?>)" class="btn" style="border-color: var(--cc-danger); color: var(--cc-danger);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Cancelar pago
                            </button>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</main>

<!-- Modal: Cancelar pago -->
<div id="modalCancelar" class="admin-modal" style="display: none;">
    <div class="admin-modal-content admin-modal-small">
        <div class="admin-modal-header">
            <h2 class="h2" style="margin: 0;">Cancelar Pago</h2>
        </div>
        <div class="admin-modal-body">
            <p class="meta" style="margin-bottom: 24px; line-height: 1.6;">
                ¿Estás seguro de que deseas cancelar este pago? Esta acción no se puede deshacer.
            </p>
            
            <form id="formCancelar" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModal('modalCancelar')" class="btn outline">
                        No, mantener
                    </button>
                    <button type="submit" class="btn" style="background: var(--cc-danger); color: white; border-color: var(--cc-danger);">
                        Sí, cancelar pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mostrar modal de cancelación
function mostrarModalCancelar(pagoId) {
    const form = document.getElementById('formCancelar');
    form.action = '<?php echo BASE_URL; ?>/pago/cancelar/' + pagoId;
    document.getElementById('modalCancelar').style.display = 'flex';
}

// Cerrar modal
function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalCancelar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal('modalCancelar');
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modalCancelar');
    }
});
</script>

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

/* Scrollbar personalizado para el modal */
.admin-modal-body::-webkit-scrollbar {
    width: 10px;
}

.admin-modal-body::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 10px;
}

.admin-modal-body::-webkit-scrollbar-thumb {
    background: #9CA3AF;
    border-radius: 10px;
}

.admin-modal-body::-webkit-scrollbar-thumb:hover {
    background: #6B7280;
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
