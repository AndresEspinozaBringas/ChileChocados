<?php
use App\Helpers\Auth;

// Verificar que el usuario sea admin
Auth::requireAdmin();

$pageTitle = 'Marcas y Modelos Pendientes - Admin';
require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container-fluid admin-container">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block sidebar">
            <div class="sidebar-sticky">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Administración</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#home"></use></svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/publicaciones">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#file-text"></use></svg>
                            Publicaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/usuarios">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#users"></use></svg>
                            Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/marcas-modelos-pendientes">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#tag"></use></svg>
                            Marcas/Modelos
                            <?php if (count($pendientes) > 0): ?>
                                <span class="badge bg-warning ms-2"><?php echo count($pendientes); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/mensajes">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#message-square"></use></svg>
                            Mensajes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/reportes">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#bar-chart"></use></svg>
                            Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/configuracion">
                            <svg class="icon"><use xlink:href="/assets/icons.svg#settings"></use></svg>
                            Configuración
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Marcas y Modelos Personalizados</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Solicitudes Pendientes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <svg class="icon me-2"><use xlink:href="/assets/icons.svg#clock"></use></svg>
                        Solicitudes Pendientes
                        <?php if (count($pendientes) > 0): ?>
                            <span class="badge bg-warning ms-2"><?php echo count($pendientes); ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pendientes)): ?>
                        <div class="alert alert-info mb-0">
                            <svg class="icon me-2"><use xlink:href="/assets/icons.svg#info"></use></svg>
                            No hay solicitudes pendientes de aprobación.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Publicación</th>
                                        <th>Usuario</th>
                                        <th>Marca Ingresada</th>
                                        <th>Modelo Ingresado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $solicitud): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_creacion)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/admin/publicaciones/<?php echo $solicitud->publicacion_id; ?>" 
                                                   target="_blank">
                                                    <?php echo htmlspecialchars($solicitud->publicacion_titulo); ?>
                                                </a>
                                                <br>
                                                <small class="text-muted">ID: <?php echo $solicitud->publicacion_id; ?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($solicitud->usuario_nombre); ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($solicitud->usuario_email); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($solicitud->marca_ingresada); ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($solicitud->modelo_ingresado); ?></strong>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-success me-1" 
                                                        onclick="mostrarModalAprobar(<?php echo $solicitud->id; ?>, '<?php echo htmlspecialchars($solicitud->marca_ingresada, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($solicitud->modelo_ingresado, ENT_QUOTES); ?>')">
                                                    <svg class="icon"><use xlink:href="/assets/icons.svg#check"></use></svg>
                                                    Aprobar
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="mostrarModalRechazar(<?php echo $solicitud->id; ?>)">
                                                    <svg class="icon"><use xlink:href="/assets/icons.svg#x"></use></svg>
                                                    Rechazar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial -->
            <?php if (!empty($historial)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <svg class="icon me-2"><use xlink:href="/assets/icons.svg#list"></use></svg>
                            Historial Reciente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Publicación</th>
                                        <th>Usuario</th>
                                        <th>Marca/Modelo</th>
                                        <th>Estado</th>
                                        <th>Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historial as $item): ?>
                                        <tr>
                                            <td>
                                                <small><?php echo date('d/m/Y', strtotime($item->fecha_creacion)); ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($item->publicacion_titulo); ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($item->usuario_nombre); ?></small>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo htmlspecialchars($item->marca_ingresada); ?> 
                                                    <?php echo htmlspecialchars($item->modelo_ingresado); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = [
                                                    'pendiente' => 'bg-warning',
                                                    'aprobado' => 'bg-success',
                                                    'rechazado' => 'bg-danger',
                                                    'modificado' => 'bg-info'
                                                ][$item->estado] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($item->estado); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($item->admin_nombre ?? '-'); ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</main>

<!-- Modal Aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formAprobar">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar Marca/Modelo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <svg class="icon me-2"><use xlink:href="/assets/icons.svg#info"></use></svg>
                        Puedes aprobar tal cual o modificar la marca/modelo antes de aprobar.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Marca Sugerida (opcional)</label>
                        <input type="text" 
                               class="form-control" 
                               name="marca_sugerida" 
                               id="marca_sugerida"
                               placeholder="Dejar vacío para aprobar tal cual">
                        <small class="text-muted">Si modificas, se guardará esta versión</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Modelo Sugerido (opcional)</label>
                        <input type="text" 
                               class="form-control" 
                               name="modelo_sugerido" 
                               id="modelo_sugerido"
                               placeholder="Dejar vacío para aprobar tal cual">
                        <small class="text-muted">Si modificas, se guardará esta versión</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notas (opcional)</label>
                        <textarea class="form-control" 
                                  name="notas" 
                                  rows="3"
                                  placeholder="Notas internas sobre esta aprobación"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <svg class="icon"><use xlink:href="/assets/icons.svg#check"></use></svg>
                        Aprobar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formRechazar">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Rechazar Marca/Modelo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <svg class="icon me-2"><use xlink:href="/assets/icons.svg#alert-triangle"></use></svg>
                        La publicación permanecerá como borrador y el usuario será notificado.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" 
                                  name="motivo" 
                                  rows="4"
                                  required
                                  placeholder="Explica por qué se rechaza esta marca/modelo"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <svg class="icon"><use xlink:href="/assets/icons.svg#x"></use></svg>
                        Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function mostrarModalAprobar(id, marca, modelo) {
    const modal = new bootstrap.Modal(document.getElementById('modalAprobar'));
    const form = document.getElementById('formAprobar');
    
    // Configurar action del formulario
    form.action = '<?php echo BASE_URL; ?>/admin/marcas-modelos-pendientes/' + id + '/aprobar';
    
    // Pre-llenar campos con valores originales como placeholder
    document.getElementById('marca_sugerida').placeholder = 'Original: ' + marca;
    document.getElementById('modelo_sugerido').placeholder = 'Original: ' + modelo;
    
    // Limpiar campos
    document.getElementById('marca_sugerida').value = '';
    document.getElementById('modelo_sugerido').value = '';
    
    modal.show();
}

function mostrarModalRechazar(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalRechazar'));
    const form = document.getElementById('formRechazar');
    
    // Configurar action del formulario
    form.action = '<?php echo BASE_URL; ?>/admin/marcas-modelos-pendientes/' + id + '/rechazar';
    
    modal.show();
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
