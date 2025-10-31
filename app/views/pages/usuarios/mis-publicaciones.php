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
}

@media (max-width: 768px) {
    .publicacion-item {
        flex-direction: column;
    }
    
    .publicacion-img {
        width: 100%;
        height: 180px;
    }
    
    .publicacion-actions {
        flex-direction: row;
    }
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
                        </div>
                        <div class="publicacion-actions">
                            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" class="btn" style="white-space: nowrap;">
                                <?php echo icon('eye', 16); ?> Ver
                            </a>
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

// Función para eliminar publicación
function eliminarPublicacion(id, titulo) {
    if (confirm(`¿Estás seguro de que deseas eliminar la publicación "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        // Crear formulario dinámico
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo BASE_URL; ?>/publicaciones/' + id + '/eliminar';
        
        // Agregar token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?php echo generateCsrfToken(); ?>';
        form.appendChild(csrfInput);
        
        // Agregar al body y enviar
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
