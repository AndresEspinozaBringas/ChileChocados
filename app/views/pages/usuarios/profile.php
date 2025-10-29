<?php
/**
 * Vista de Perfil de Usuario
 * Versión simplificada usando el sistema de diseño existente
 */

$pageTitle = 'Mi Perfil - ChileChocados';
$currentPage = 'perfil';

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* Estilos específicos para perfil usando variables del sistema */
.profile-header {
    background: var(--cc-white, #FFFFFF);
    border: 1px solid var(--cc-border-default, #E5E5E5);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--cc-primary-pale, #FFF5F4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 600;
    color: var(--cc-primary, #E6332A);
    margin-bottom: 16px;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 24px;
}

.profile-stat {
    text-align: center;
    padding: 16px;
    background: var(--cc-bg-muted, #F5F5F5);
    border-radius: 8px;
}

.profile-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--cc-primary, #E6332A);
    margin-bottom: 4px;
}

.profile-stat-label {
    font-size: 13px;
    color: var(--cc-text-secondary, #666);
}

@media (max-width: 768px) {
    .profile-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<main class="container" style="margin-top: 24px;">
    
    <!-- Encabezado del perfil -->
    <div class="profile-header">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>
            
            <div style="flex: 1;">
                <h1 class="h2" style="margin: 0 0 8px 0;">
                    <?php echo e($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                </h1>
                <p class="meta" style="margin: 0;">
                    <?php echo e($usuario['email']); ?>
                    <?php if (!empty($usuario['telefono'])): ?>
                        · <?php echo e($usuario['telefono']); ?>
                    <?php endif; ?>
                </p>
                <div style="margin-top: 8px;">
                    <span class="badge" style="background: var(--cc-primary-pale); color: var(--cc-primary);">
                        <?php echo ucfirst($usuario['rol']); ?>
                    </span>
                    <span class="badge" style="background: var(--cc-bg-muted); color: var(--cc-text-secondary); margin-left: 8px;">
                        Miembro desde <?php echo date('M Y', strtotime($usuario['fecha_registro'])); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="profile-stats">
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo number_format($estadisticas['total_publicaciones']); ?></div>
                <div class="profile-stat-label">Publicaciones</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo number_format($estadisticas['publicaciones_activas']); ?></div>
                <div class="profile-stat-label">Activas</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo number_format($estadisticas['ventas_realizadas']); ?></div>
                <div class="profile-stat-label">Vendidas</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo number_format($estadisticas['total_visitas']); ?></div>
                <div class="profile-stat-label">Visitas</div>
            </div>
        </div>
    </div>

    <!-- Mensajes flash -->
    <?php 
    $flash = getFlash();
    if ($flash): 
    ?>
        <div class="card" style="background: <?php echo $flash['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; border-color: <?php echo $flash['type'] === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>; margin-bottom: 24px;">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>

    <!-- Datos Personales -->
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="h3" style="margin: 0;">Datos Personales</h2>
            <button class="btn" onclick="toggleEditMode()">Editar</button>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">Nombre</label>
                <p class="meta" style="margin: 0;"><?php echo e($usuario['nombre']); ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">Apellido</label>
                <p class="meta" style="margin: 0;"><?php echo e($usuario['apellido']); ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">Email</label>
                <p class="meta" style="margin: 0;"><?php echo e($usuario['email']); ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">Teléfono</label>
                <p class="meta" style="margin: 0;"><?php echo e($usuario['telefono']); ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">RUT</label>
                <p class="meta" style="margin: 0;"><?php echo e($usuario['rut']); ?></p>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 14px;">Rol</label>
                <p class="meta" style="margin: 0;"><?php echo ucfirst($usuario['rol']); ?></p>
            </div>
        </div>
    </div>

    <!-- Acceso rápido a publicaciones -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="h3" style="margin: 0 0 8px 0;">Mis Publicaciones</h2>
                <p class="meta" style="margin: 0;">Tienes <?php echo $estadisticas['publicaciones_activas']; ?> publicaciones activas</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn primary">
                Ver todas mis publicaciones →
            </a>
        </div>
    </div>

</main>

<script>
function toggleEditMode() {
    alert('Funcionalidad de edición en desarrollo. Por ahora usa datos mock.');
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
