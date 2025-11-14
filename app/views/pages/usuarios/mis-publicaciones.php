<?php
/**
 * Vista: Mis Publicaciones
 * Muestra todas las publicaciones del usuario con filtros
 */

$pageTitle = 'Mis Publicaciones - ChileChocados';
$currentPage = 'mis-publicaciones';

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* Estilos usando el sistema de diseño */
.publicacion-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 8px;
    margin-bottom: 12px;
    transition: all 0.2s;
    background: var(--cc-white, #FFFFFF);
}

.publicacion-item:hover {
    border-color: var(--cc-primary, #E6332A);
    box-shadow: 0 2px 8px rgba(230, 51, 42, 0.1);
}

.publicacion-img {
    width: 120px;
    height: 90px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--cc-bg-muted, #F5F5F5);
    flex-shrink: 0;
}

.publicacion-info {
    flex: 1;
    min-width: 0;
}

.publicacion-meta {
    display: flex;
    gap: 16px;
    margin-top: 8px;
    font-size: 13px;
    color: var(--cc-text-secondary, #666);
    flex-wrap: wrap;
}

.publicacion-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
}

.filters-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-select {
    padding: 8px 12px;
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 6px;
    font-size: 14px;
    background: var(--cc-white, #FFFFFF);
    color: var(--cc-text-primary, #2E2E2E);
}

.card {
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 8px;
    padding: 20px;
}

@media (max-width: 768px) {
    .container {
        padding: 16px !important;
        margin-top: 16px !important;
    }
    
    /* Header */
    main > div:first-child {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 16px !important;
    }
    
    main > div:first-child a.btn {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    
    /* Filtros */
    .filters-bar {
        flex-direction: column;
        gap: 8px;
    }
    
    .filter-select {
        width: 100%;
    }
    
    .filters-bar button {
        width: 100%;
    }
    
    /* Publicación item */
    .publicacion-item {
        flex-direction: column;
        padding: 12px;
    }
    
    .publicacion-img {
        width: 100%;
        height: 180px;
    }
    
    .publicacion-info h3 {
        font-size: 16px !important;
    }
    
    .publicacion-info > div:first-child {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    .publicacion-meta {
        gap: 12px;
        font-size: 12px;
    }
    
    /* Acciones */
    .publicacion-actions {
        flex-direction: row;
        flex-wrap: wrap;
        width: 100%;
    }
    
    .publicacion-actions a,
    .publicacion-actions button {
        flex: 1;
        min-width: calc(50% - 4px);
        font-size: 13px;
        padding: 8px 12px;
        white-space: nowrap;
    }
    
    /* Modal */
    .admin-modal {
        padding: 12px;
    }
    
    .admin-modal-content {
        max-width: 100%;
        width: 100%;
    }
    
    .admin-modal-header {
        padding: 20px;
    }
    
    .admin-modal-header h2 {
        font-size: 18px !important;
    }
    
    .admin-modal-body {
        padding: 20px;
    }
    
    .admin-modal-body form > div {
        flex-direction: column !important;
    }
    
    .admin-modal-body form button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .publicacion-actions a,
    .publicacion-actions button {
        min-width: 100%;
        flex: 1 1 100%;
    }
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] {
    --cc-white: #1F2937;
    --cc-bg-default: #111827;
    --cc-bg-surface: #1F2937;
    --cc-bg-muted: #374151;
    --cc-border-default: #374151;
    --cc-text-primary: #F3F4F6;
    --cc-text-secondary: #D1D5DB;
    --cc-text-tertiary: #9CA3AF;
}

:root[data-theme="dark"] .publicacion-item {
    background: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .publicacion-item:hover {
    border-color: var(--cc-primary);
    box-shadow: 0 2px 8px rgba(230, 51, 42, 0.3);
}

:root[data-theme="dark"] .publicacion-img {
    background: #374151;
}

:root[data-theme="dark"] .publicacion-info h3 a {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .publicacion-meta {
    color: #9CA3AF;
}

:root[data-theme="dark"] .filter-select {
    background: #374151 !important;
    border-color: #4B5563 !important;
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .filter-select:focus {
    border-color: var(--cc-primary) !important;
    background: #1F2937 !important;
}

:root[data-theme="dark"] .filter-select::placeholder {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .filter-select option {
    background: #374151;
    color: #F3F4F6;
}

:root[data-theme="dark"] .card {
    background: #1F2937 !important;
    border-color: #374151 !important;
}

:root[data-theme="dark"] .btn {
    background: #374151;
    color: #F3F4F6;
    border-color: #4B5563;
}

:root[data-theme="dark"] .btn:hover {
    background: #4B5563;
    border-color: #6B7280;
}

:root[data-theme="dark"] .btn.primary {
    background: var(--cc-primary);
    color: white;
    border-color: var(--cc-primary);
}

:root[data-theme="dark"] .btn.primary:hover {
    background: #c72a22;
    border-color: #c72a22;
}

:root[data-theme="dark"] .btn.outline {
    background: transparent;
    color: #F3F4F6;
    border-color: #4B5563;
}

:root[data-theme="dark"] .btn.outline:hover {
    background: #374151;
    border-color: #6B7280;
}

/* Badges en dark mode */
:root[data-theme="dark"] .badge {
    opacity: 0.9;
}

/* Botones de acción específicos */
:root[data-theme="dark"] .publicacion-actions button[style*="background: #d1fae5"] {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6EE7B7 !important;
    border-color: #10B981 !important;
}

:root[data-theme="dark"] .publicacion-actions button[style*="background: #fee2e2"] {
    background: rgba(239, 68, 68, 0.2) !important;
    color: #FCA5A5 !important;
    border-color: #EF4444 !important;
}

/* Mensaje de rechazo en dark mode */
:root[data-theme="dark"] .publicacion-info > div[style*="background: #FEE2E2"] {
    background: rgba(239, 68, 68, 0.15) !important;
    border-left-color: #EF4444 !important;
}

:root[data-theme="dark"] .publicacion-info > div[style*="background: #FEE2E2"] div[style*="color: #DC2626"] {
    color: #FCA5A5 !important;
}

:root[data-theme="dark"] .publicacion-info > div[style*="background: #FEE2E2"] div[style*="color: #991B1B"] {
    color: #FCA5A5 !important;
}

/* Estado vacío */
:root[data-theme="dark"] .card > div[style*="text-align: center"] {
    color: #9CA3AF;
}

:root[data-theme="dark"] .card > div[style*="text-align: center"] h3 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .card > div[style*="text-align: center"] p {
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .card > div[style*="text-align: center"] svg {
    opacity: 0.2;
}

/* Títulos y textos */
:root[data-theme="dark"] .h2,
:root[data-theme="dark"] .h3,
:root[data-theme="dark"] .h4 {
    color: #F3F4F6;
}

:root[data-theme="dark"] .meta {
    color: #9CA3AF;
}

/* Asegurar que el contenedor principal tenga fondo oscuro */
:root[data-theme="dark"] .container {
    background: transparent;
}

:root[data-theme="dark"] main {
    background: transparent;
}
</style>

<main class="container" style="margin-top: 24px;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 class="h2" style="margin: 0 0 8px 0;">Mis Publicaciones</h1>
            <p class="meta" style="margin: 0;">Gestiona tus vehículos publicados</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/publicar" class="btn primary">
            + Nueva Publicación
        </a>
    </div>

    <!-- Filtros -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="filters-bar">
            <select class="filter-select" id="filter-estado">
                <option value="">Todos los estados</option>
                <option value="borrador">Borrador</option>
                <option value="pendiente">Pendiente</option>
                <option value="aprobada">Aprobada</option>
                <option value="rechazada">Rechazada</option>
                <option value="vendida">Vendida</option>
            </select>
            
            <select class="filter-select" id="filter-orden">
                <option value="reciente">Más recientes</option>
                <option value="antiguo">Más antiguos</option>
                <option value="visitas">Más visitas</option>
                <option value="precio-alto">Precio mayor</option>
                <option value="precio-bajo">Precio menor</option>
            </select>
            
            <input 
                type="text" 
                class="filter-select" 
                placeholder="Buscar por título..."
                id="filter-buscar"
                style="flex: 1; min-width: 200px;"
            >
            
            <button class="btn" onclick="limpiarFiltros()">Limpiar</button>
        </div>
    </div>

    <!-- Lista de Publicaciones -->
    <div class="card">
        <?php if (empty($publicaciones)): ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--cc-text-secondary);">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 16px; opacity: 0.3;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                </svg>
                <h3 class="h3" style="margin: 0 0 8px 0;">No tienes publicaciones</h3>
                <p style="margin: 0 0 20px 0;">Comienza publicando tu primer vehículo</p>
                <a href="<?php echo BASE_URL; ?>/publicar" class="btn primary">
                    + Publicar vehículo
                </a>
            </div>
        <?php else: ?>
            <div id="publicaciones-lista">
                <?php foreach ($publicaciones as $pub): ?>
                    <div class="publicacion-item" data-estado="<?php echo e($pub['estado']); ?>" data-titulo="<?php echo e(strtolower($pub['titulo'])); ?>">
                        <img 
                            src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo e($pub['foto_principal']); ?>" 
                            alt="<?php echo e($pub['titulo']); ?>"
                            class="publicacion-img"
                            onerror="this.style.display='none'"
                        >
                        <div class="publicacion-info">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h3 class="h4" style="margin: 0;">
                                    <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo e($pub['titulo']); ?>
                                    </a>
                                </h3>
                                <span class="badge" style="background: <?php 
                                    echo $pub['estado'] === 'aprobada' ? '#D1FAE5' : 
                                        ($pub['estado'] === 'pendiente' ? '#FEF3C7' : 
                                        ($pub['estado'] === 'rechazada' ? '#FEE2E2' : 
                                        ($pub['estado'] === 'borrador' ? '#FFF1F0' : '#e2e3e5'))); 
                                ?>; color: <?php 
                                    echo $pub['estado'] === 'aprobada' ? '#059669' : 
                                        ($pub['estado'] === 'pendiente' ? '#D97706' : 
                                        ($pub['estado'] === 'rechazada' ? '#DC2626' : 
                                        ($pub['estado'] === 'borrador' ? '#E6332A' : '#383d41'))); 
                                ?>;">
                                    <?php echo strtoupper($pub['estado']); ?>
                                </span>
                            </div>
                            
                            <div style="font-size: 18px; font-weight: 600; color: var(--cc-primary); margin-bottom: 8px;">
                                <?php echo formatPrice($pub['precio']); ?>
                            </div>
                            
                            <div class="publicacion-meta">
                                <span>
                                    <?php echo icon('eye', 14); ?> <?php echo number_format($pub['visitas'] ?? 0); ?> visitas
                                </span>
                                <span>
                                    <?php echo icon('calendar', 14); ?> 
                                    <?php 
                                    if (!empty($pub['fecha_publicacion'])) {
                                        echo date('d/m/Y', strtotime($pub['fecha_publicacion']));
                                    } elseif (!empty($pub['fecha_creacion'])) {
                                        echo date('d/m/Y', strtotime($pub['fecha_creacion']));
                                    } else {
                                        echo 'Sin fecha';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <?php if ($pub['estado'] === 'rechazada' && !empty($pub['motivo_rechazo'])): ?>
                                <div style="margin-top: 12px; padding: 12px; background: #FEE2E2; border-left: 3px solid #DC2626; border-radius: 4px;">
                                    <div style="display: flex; align-items: start; gap: 8px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: #DC2626; font-size: 13px; margin-bottom: 4px;">
                                                Motivo del rechazo:
                                            </div>
                                            <div style="color: #991B1B; font-size: 13px; line-height: 1.5;">
                                                <?php echo nl2br(htmlspecialchars($pub['motivo_rechazo'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="publicacion-actions">
                            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" class="btn" style="white-space: nowrap;">
                                <?php echo icon('eye', 16); ?> Ver
                            </a>
                            
                            <?php if ($pub['estado'] === 'aprobada'): ?>
                                <button onclick="marcarComoVendido(<?php echo $pub['id']; ?>, '<?php echo htmlspecialchars($pub['titulo']); ?>')" class="btn" style="white-space: nowrap; background: #d1fae5; color: #065f46; border-color: #a7f3d0;">
                                    <?php echo icon('check-circle', 16); ?> Marcar Vendido
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?php echo BASE_URL; ?>/publicaciones/<?php echo $pub['id']; ?>/editar" class="btn" style="white-space: nowrap;">
                                <?php echo icon('edit', 16); ?> Editar
                            </a>
                            <button onclick="eliminarPublicacion(<?php echo $pub['id']; ?>, '<?php echo htmlspecialchars($pub['titulo']); ?>')" class="btn" style="white-space: nowrap; background: #fee2e2; color: #dc2626; border-color: #fecaca;">
                                <?php echo icon('trash-2', 16); ?> Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<script>
// Filtros
const filterEstado = document.getElementById('filter-estado');
const filterOrden = document.getElementById('filter-orden');
const filterBuscar = document.getElementById('filter-buscar');

function aplicarFiltros() {
    const items = document.querySelectorAll('.publicacion-item');
    const estado = filterEstado?.value || '';
    const buscar = filterBuscar?.value.toLowerCase() || '';
    
    items.forEach(item => {
        const itemEstado = item.dataset.estado;
        const itemTitulo = item.dataset.titulo;
        
        const matchEstado = !estado || itemEstado === estado;
        const matchBuscar = !buscar || itemTitulo.includes(buscar);
        
        if (matchEstado && matchBuscar) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function limpiarFiltros() {
    if (filterEstado) filterEstado.value = '';
    if (filterOrden) filterOrden.value = 'reciente';
    if (filterBuscar) filterBuscar.value = '';
    aplicarFiltros();
}

// Event listeners
filterEstado?.addEventListener('change', aplicarFiltros);
filterBuscar?.addEventListener('input', aplicarFiltros);

// Función para marcar como vendido
function marcarComoVendido(id, titulo) {
    document.getElementById('modalVendidoTitulo').textContent = titulo;
    document.getElementById('formVendido').action = '<?php echo BASE_URL; ?>/publicaciones/' + id + '/marcar-vendido';
    document.getElementById('modalVendido').style.display = 'flex';
}

// Función para eliminar publicación
function eliminarPublicacion(id, titulo) {
    // Mostrar modal de confirmación
    document.getElementById('modalEliminarTitulo').textContent = titulo;
    document.getElementById('formEliminar').action = '<?php echo BASE_URL; ?>/publicaciones/' + id + '/eliminar';
    document.getElementById('modalEliminar').style.display = 'flex';
}

// Cerrar modal
function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modales al hacer clic fuera
document.getElementById('modalVendido')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal('modalVendido');
    }
});

document.getElementById('modalEliminar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal('modalEliminar');
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modalVendido');
        cerrarModal('modalEliminar');
    }
});

// ============================================================================
// POLLING: Verificar actualizaciones cada 30 segundos
// ============================================================================

let ultimaActualizacion = Date.now();
let pollingInterval = null;

async function verificarActualizaciones() {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/publicaciones/verificar-cambios?timestamp=' + ultimaActualizacion);
        if (!response.ok) return;
        
        const data = await response.json();
        
        if (data.hay_cambios) {
            mostrarBannerActualizacion(data.cambios);
        }
    } catch (error) {
        console.error('Error verificando actualizaciones:', error);
    }
}

function mostrarBannerActualizacion(cambios) {
    // Verificar si ya existe el banner
    if (document.getElementById('banner-actualizacion')) return;
    
    const banner = document.createElement('div');
    banner.id = 'banner-actualizacion';
    banner.style.cssText = `
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        gap: 16px;
        animation: slideDown 0.3s ease-out;
        max-width: 90%;
        width: auto;
    `;
    
    let mensaje = '¡Hay actualizaciones disponibles!';
    if (cambios && cambios.length > 0) {
        const count = cambios.length;
        mensaje = `${count} publicación${count > 1 ? 'es' : ''} actualizada${count > 1 ? 's' : ''}`;
    }
    
    banner.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div style="flex: 1;">
            <div style="font-weight: 600; font-size: 15px;">${mensaje}</div>
            <div style="font-size: 13px; opacity: 0.9; margin-top: 2px;">Click para recargar y ver los cambios</div>
        </div>
        <button onclick="location.reload()" style="
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            Recargar
        </button>
        <button onclick="cerrarBanner()" style="
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px;
            opacity: 0.7;
            transition: opacity 0.2s;
        " onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    `;
    
    document.body.appendChild(banner);
    
    // Detener el polling una vez que se muestra el banner
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function cerrarBanner() {
    const banner = document.getElementById('banner-actualizacion');
    if (banner) {
        banner.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => banner.remove(), 300);
        
        // Reiniciar polling
        iniciarPolling();
    }
}

function iniciarPolling() {
    // Limpiar intervalo anterior si existe
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    
    // Iniciar nuevo intervalo de 30 segundos
    pollingInterval = setInterval(verificarActualizaciones, 30000);
}

// Iniciar polling al cargar la página
if (<?php echo !empty($publicaciones) ? 'true' : 'false'; ?>) {
    iniciarPolling();
}

// Detener polling al salir de la página
window.addEventListener('beforeunload', () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});

// Animaciones CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
    }
    
    @media (max-width: 768px) {
        #banner-actualizacion {
            top: 70px !important;
            left: 16px !important;
            right: 16px !important;
            transform: none !important;
            width: auto !important;
            max-width: none !important;
            flex-direction: column;
            text-align: center;
        }
        
        #banner-actualizacion button {
            width: 100%;
        }
    }
`;
document.head.appendChild(style);
</script>

<!-- Modal: Marcar como Vendido -->
<div id="modalVendido" class="admin-modal" style="display: none;">
    <div class="admin-modal-content admin-modal-small">
        <div class="admin-modal-header">
            <h2 class="h2" style="margin: 0;">Marcar como Vendido</h2>
        </div>
        <div class="admin-modal-body">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 16px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#065f46" stroke-width="2.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            </div>
            <p class="meta" style="margin-bottom: 16px; line-height: 1.6; text-align: center;">
                ¿Confirmas que has vendido la publicación:
            </p>
            <p style="font-weight: 700; color: var(--cc-text-primary); margin-bottom: 24px; text-align: center;">
                "<span id="modalVendidoTitulo"></span>"
            </p>
            <p class="meta" style="margin-bottom: 24px; color: #065f46; text-align: center;">
                La publicación se marcará como vendida y dejará de aparecer en las búsquedas.
            </p>
            
            <form id="formVendido" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModal('modalVendido')" class="btn outline">
                        Cancelar
                    </button>
                    <button type="submit" class="btn" style="background: #10B981; color: white; border-color: #10B981;">
                        Sí, marcar como vendido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Eliminar publicación -->
<div id="modalEliminar" class="admin-modal" style="display: none;">
    <div class="admin-modal-content admin-modal-small">
        <div class="admin-modal-header">
            <h2 class="h2" style="margin: 0;">Eliminar Publicación</h2>
        </div>
        <div class="admin-modal-body">
            <p class="meta" style="margin-bottom: 16px; line-height: 1.6;">
                ¿Estás seguro de que deseas eliminar la publicación:
            </p>
            <p style="font-weight: 700; color: var(--cc-text-primary); margin-bottom: 24px;">
                "<span id="modalEliminarTitulo"></span>"
            </p>
            <p class="meta" style="margin-bottom: 24px; color: var(--cc-danger);">
                Esta acción no se puede deshacer.
            </p>
            
            <form id="formEliminar" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModal('modalEliminar')" class="btn outline">
                        Cancelar
                    </button>
                    <button type="submit" class="btn" style="background: var(--cc-danger); color: white; border-color: var(--cc-danger);">
                        Sí, eliminar
                    </button>
                </div>
            </form>
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

/* ============================================================================
 * DARK MODE - MODALES
 * ============================================================================ */

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

:root[data-theme="dark"] .admin-modal-body p[style*="font-weight: 700"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .admin-modal-body p[style*="color: #065f46"] {
    color: #6EE7B7 !important;
}

:root[data-theme="dark"] .admin-modal-body p[style*="color: var(--cc-danger)"] {
    color: #FCA5A5 !important;
}

:root[data-theme="dark"] .admin-modal-body div[style*="background: #d1fae5"] {
    background: rgba(16, 185, 129, 0.15) !important;
}

:root[data-theme="dark"] .admin-modal-body div[style*="background: #d1fae5"] svg {
    stroke: #6EE7B7 !important;
}

:root[data-theme="dark"] .admin-modal-body::-webkit-scrollbar-track {
    background: #374151;
}

:root[data-theme="dark"] .admin-modal-body::-webkit-scrollbar-thumb {
    background: #4B5563;
}

:root[data-theme="dark"] .admin-modal-body::-webkit-scrollbar-thumb:hover {
    background: #6B7280;
}

/* Botones específicos en modales */
:root[data-theme="dark"] .admin-modal-body button[style*="background: #10B981"] {
    background: #10B981 !important;
    color: white !important;
    border-color: #10B981 !important;
}

:root[data-theme="dark"] .admin-modal-body button[style*="background: #10B981"]:hover {
    background: #059669 !important;
    border-color: #059669 !important;
}

:root[data-theme="dark"] .admin-modal-body button[style*="background: var(--cc-danger)"] {
    background: #EF4444 !important;
    color: white !important;
    border-color: #EF4444 !important;
}

:root[data-theme="dark"] .admin-modal-body button[style*="background: var(--cc-danger)"]:hover {
    background: #DC2626 !important;
    border-color: #DC2626 !important;
}
</style>

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
