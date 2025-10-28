<?php
/**
 * Vista de Perfil de Usuario
 * 
 * Permite al usuario ver y editar su información personal,
 * ver sus publicaciones activas y archivadas, y gestionar su cuenta
 * 
 * Variables disponibles:
 * - $usuario: Array con datos del usuario
 * - $publicacionesActivas: Array de publicaciones activas
 * - $publicacionesArchivadas: Array de publicaciones archivadas/vendidas
 * - $estadisticas: Array con estadísticas del usuario
 */

$pageTitle = 'Mi Perfil - ChileChocados';
$currentPage = 'perfil';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/nav.php';
?>

<main class="main-content">
    <div class="container">
        
        <!-- Encabezado del perfil -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-wrapper">
                        <?php if (!empty($usuario['foto_perfil'])): ?>
                            <img 
                                src="<?php echo BASE_URL . '/uploads/avatars/' . e($usuario['foto_perfil']); ?>" 
                                alt="Foto de perfil"
                                class="profile-avatar"
                                id="avatar-preview"
                            >
                        <?php else: ?>
                            <div class="profile-avatar-placeholder" id="avatar-preview">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Botón para cambiar foto -->
                        <button type="button" class="profile-avatar-change" onclick="document.getElementById('foto-input').click()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                        </button>
                        
                        <form id="form-foto" style="display: none;">
                            <input 
                                type="file" 
                                id="foto-input" 
                                name="foto_perfil" 
                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                onchange="subirFotoPerfil()"
                            >
                        </form>
                    </div>
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name">
                        <?php echo e($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                    </h1>
                    <p class="profile-email"><?php echo e($usuario['email']); ?></p>
                    
                    <div class="profile-badges">
                        <?php if ($usuario['verificado']): ?>
                            <span class="badge badge-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Verificado
                            </span>
                        <?php endif; ?>
                        
                        <span class="badge badge-info">
                            <?php 
                            $roles = [
                                'admin' => 'Administrador',
                                'vendedor' => 'Vendedor',
                                'comprador' => 'Comprador'
                            ];
                            echo $roles[$usuario['rol']] ?? 'Usuario';
                            ?>
                        </span>
                        
                        <span class="badge badge-secondary">
                            Miembro desde <?php echo date('M Y', strtotime($usuario['fecha_registro'])); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas del usuario -->
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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible">
                <?php 
                echo $_SESSION['flash_message']; 
                unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php endif; ?>

        <!-- Tabs de navegación -->
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="datos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Datos Personales
            </button>
            
            <button class="profile-tab" data-tab="publicaciones">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Mis Publicaciones
            </button>
            
            <button class="profile-tab" data-tab="seguridad">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Seguridad
            </button>
        </div>

        <!-- Contenido de las tabs -->
        <div class="profile-content">
            
            <!-- Tab: Datos Personales -->
            <div class="profile-tab-content active" id="tab-datos">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Información Personal</h2>
                        <p class="card-subtitle">Actualiza tu información de contacto</p>
                    </div>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>/perfil/actualizar" class="form">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre" 
                                    class="form-input" 
                                    value="<?php echo e($usuario['nombre']); ?>" 
                                    required
                                    minlength="2"
                                    maxlength="100"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="apellido" class="form-label">
                                    Apellido <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="apellido" 
                                    name="apellido" 
                                    class="form-input" 
                                    value="<?php echo e($usuario['apellido']); ?>" 
                                    required
                                    minlength="2"
                                    maxlength="100"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                class="form-input" 
                                value="<?php echo e($usuario['email']); ?>" 
                                disabled
                            >
                            <small class="form-help">El email no se puede modificar. Si necesitas cambiarlo, contacta a soporte.</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input 
                                    type="tel" 
                                    id="telefono" 
                                    name="telefono" 
                                    class="form-input" 
                                    value="<?php echo e($usuario['telefono'] ?? ''); ?>" 
                                    placeholder="+56 9 1234 5678"
                                >
                                <small class="form-help">Formato: +56 9 XXXX XXXX</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="rut" class="form-label">RUT</label>
                                <input 
                                    type="text" 
                                    id="rut" 
                                    class="form-input" 
                                    value="<?php echo e($usuario['rut'] ?? ''); ?>" 
                                    disabled
                                >
                                <small class="form-help">El RUT no se puede modificar</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="redes_sociales" class="form-label">Redes Sociales</label>
                            <textarea 
                                id="redes_sociales" 
                                name="redes_sociales" 
                                class="form-textarea" 
                                rows="4"
                                placeholder="Ingresa tus redes sociales (una por línea)&#10;Ejemplo:&#10;https://facebook.com/tuperfil&#10;https://instagram.com/tuusuario&#10;+56912345678 (WhatsApp)"
                            ><?php 
                                if (!empty($usuario['redes_sociales'])) {
                                    $redes = json_decode($usuario['redes_sociales'], true);
                                    if ($redes) {
                                        echo e(implode("\n", array_values($redes)));
                                    }
                                }
                            ?></textarea>
                            <small class="form-help">Agrega tus perfiles de redes sociales para que los compradores puedan contactarte</small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tab: Mis Publicaciones -->
            <div class="profile-tab-content" id="tab-publicaciones">
                
                <!-- Publicaciones Activas -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Publicaciones Activas</h2>
                        <a href="<?php echo BASE_URL; ?>/publicar" class="btn btn-primary btn-sm">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nueva Publicación
                        </a>
                    </div>
                    
                    <?php if (empty($publicacionesActivas)): ?>
                        <div class="empty-state">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                            <h3>No tienes publicaciones activas</h3>
                            <p>Crea tu primera publicación para comenzar a vender</p>
                            <a href="<?php echo BASE_URL; ?>/publicar" class="btn btn-primary">
                                Publicar Ahora
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="publications-list">
                            <?php foreach ($publicacionesActivas as $pub): ?>
                                <div class="publication-item">
                                    <div class="publication-image">
                                        <?php if ($pub['foto_principal']): ?>
                                            <img src="<?php echo BASE_URL . '/uploads/publicaciones/' . $pub['foto_principal']; ?>" alt="<?php echo e($pub['titulo']); ?>">
                                        <?php else: ?>
                                            <div class="publication-no-image">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                                    <polyline points="21 15 16 10 5 21"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="publication-content">
                                        <div class="publication-header">
                                            <h3 class="publication-title"><?php echo e($pub['titulo']); ?></h3>
                                            <span class="badge badge-<?php echo $pub['estado'] === 'aprobada' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($pub['estado']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="publication-meta">
                                            <?php if ($pub['precio']): ?>
                                                <span class="publication-price">
                                                    $<?php echo number_format($pub['precio'], 0, ',', '.'); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <span class="publication-views">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                <?php echo number_format($pub['visitas']); ?> visitas
                                            </span>
                                            
                                            <?php if ($pub['fecha_publicacion']): ?>
                                                <span class="publication-date">
                                                    <?php echo date('d/m/Y', strtotime($pub['fecha_publicacion'])); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="publication-actions">
                                        <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" class="btn btn-sm btn-outline" target="_blank">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Ver
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/publicacion/editar/<?php echo $pub['id']; ?>" class="btn btn-sm btn-outline">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Publicaciones Archivadas -->
                <?php if (!empty($publicacionesArchivadas)): ?>
                    <div class="card" style="margin-top: 24px;">
                        <div class="card-header">
                            <h2 class="card-title">Publicaciones Archivadas / Vendidas</h2>
                        </div>
                        
                        <div class="publications-list">
                            <?php foreach ($publicacionesArchivadas as $pub): ?>
                                <div class="publication-item publication-archived">
                                    <div class="publication-image">
                                        <?php if ($pub['foto_principal']): ?>
                                            <img src="<?php echo BASE_URL . '/uploads/publicaciones/' . $pub['foto_principal']; ?>" alt="<?php echo e($pub['titulo']); ?>">
                                        <?php else: ?>
                                            <div class="publication-no-image">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                                    <polyline points="21 15 16 10 5 21"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <div class="publication-overlay">
                                            <?php echo ucfirst($pub['estado']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="publication-content">
                                        <div class="publication-header">
                                            <h3 class="publication-title"><?php echo e($pub['titulo']); ?></h3>
                                            <span class="badge badge-secondary">
                                                <?php echo ucfirst($pub['estado']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="publication-meta">
                                            <?php if ($pub['precio']): ?>
                                                <span class="publication-price">
                                                    $<?php echo number_format($pub['precio'], 0, ',', '.'); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if ($pub['fecha_venta']): ?>
                                                <span class="publication-date">
                                                    Vendido: <?php echo date('d/m/Y', strtotime($pub['fecha_venta'])); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="publication-actions">
                                        <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" class="btn btn-sm btn-outline">
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab: Seguridad -->
            <div class="profile-tab-content" id="tab-seguridad">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Cambiar Contraseña</h2>
                        <p class="card-subtitle">Asegura tu cuenta con una contraseña fuerte</p>
                    </div>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>/perfil/cambiar-password" class="form" id="form-password">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label for="password_actual" class="form-label">
                                Contraseña Actual <span class="required">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_actual" 
                                name="password_actual" 
                                class="form-input" 
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="password_nueva" class="form-label">
                                Nueva Contraseña <span class="required">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_nueva" 
                                name="password_nueva" 
                                class="form-input" 
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >
                            <small class="form-help">
                                Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmar" class="form-label">
                                Confirmar Nueva Contraseña <span class="required">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmar" 
                                name="password_confirmar" 
                                class="form-input" 
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Información de seguridad adicional -->
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h2 class="card-title">Información de la Cuenta</h2>
                    </div>
                    
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Estado de la cuenta</div>
                            <div class="info-value">
                                <span class="badge badge-success">Activa</span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Miembro desde</div>
                            <div class="info-value">
                                <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                            </div>
                        </div>
                        
                        <?php if ($usuario['ultima_conexion']): ?>
                            <div class="info-item">
                                <div class="info-label">Última conexión</div>
                                <div class="info-value">
                                    <?php echo date('d/m/Y H:i', strtotime($usuario['ultima_conexion'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="info-item">
                            <div class="info-label">Email verificado</div>
                            <div class="info-value">
                                <?php if ($usuario['verificado']): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pendiente</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Estilos específicos para el perfil */
.profile-header {
    background: white;
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.profile-header-content {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
}

.profile-avatar-section {
    flex-shrink: 0;
}

.profile-avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
}

.profile-avatar,
.profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-avatar-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 600;
}

.profile-avatar-change {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #2563eb;
    color: white;
    border: 3px solid white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.profile-avatar-change:hover {
    background: #1d4ed8;
    transform: scale(1.1);
}

.profile-info {
    flex: 1;
    min-width: 0;
}

.profile-name {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: #1e293b;
}

.profile-email {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 16px 0;
}

.profile-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 24px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.profile-stat {
    text-align: center;
}

.profile-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 4px;
}

.profile-stat-label {
    font-size: 14px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.profile-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid #e2e8f0;
    overflow-x: auto;
}

.profile-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: #64748b;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.profile-tab:hover {
    color: #2563eb;
}

.profile-tab.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

.profile-tab svg {
    flex-shrink: 0;
}

.profile-tab-content {
    display: none;
}

.profile-tab-content.active {
    display: block;
}

.publications-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 16px;
}

.publication-item {
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 16px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    transition: all 0.2s;
}

.publication-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.publication-image {
    position: relative;
    width: 120px;
    height: 90px;
    border-radius: 8px;
    overflow: hidden;
    background: #e2e8f0;
}

.publication-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.publication-no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
}

.publication-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
}

.publication-content {
    flex: 1;
    min-width: 0;
}

.publication-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.publication-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.publication-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 14px;
    color: #64748b;
}

.publication-price {
    font-size: 18px;
    font-weight: 700;
    color: #059669;
}

.publication-views {
    display: flex;
    align-items: center;
    gap: 4px;
}

.publication-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.publication-archived {
    opacity: 0.7;
}

.empty-state {
    text-align: center;
    padding: 64px 32px;
    color: #64748b;
}

.empty-state svg {
    margin: 0 auto 24px;
}

.empty-state h3 {
    font-size: 20px;
    color: #1e293b;
    margin: 0 0 8px 0;
}

.empty-state p {
    margin: 0 0 24px 0;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 16px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    font-weight: 500;
    color: #64748b;
}

.info-value {
    font-weight: 600;
    color: #1e293b;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        padding: 24px 16px;
    }
    
    .profile-header-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .profile-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .profile-tabs {
        gap: 4px;
    }
    
    .profile-tab {
        padding: 12px 16px;
        font-size: 14px;
    }
    
    .profile-tab span {
        display: none;
    }
    
    .publication-item {
        grid-template-columns: 1fr;
    }
    
    .publication-actions {
        width: 100%;
    }
    
    .publication-actions .btn {
        flex: 1;
    }
}
</style>

<script>
// Manejo de tabs
document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Remover active de todas las tabs
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.profile-tab-content').forEach(c => c.classList.remove('active'));
        
        // Activar tab seleccionada
        this.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    });
});

// Subir foto de perfil
function subirFotoPerfil() {
    const input = document.getElementById('foto-input');
    const file = input.files[0];
    
    if (!file) return;
    
    // Validar tipo de archivo
    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!tiposPermitidos.includes(file.type)) {
        alert('Tipo de archivo no permitido. Solo JPG, PNG, GIF o WEBP');
        return;
    }
    
    // Validar tamaño (5MB)
    if (file.size > 5242880) {
        alert('La imagen es muy grande. Máximo 5MB');
        return;
    }
    
    // Crear FormData
    const formData = new FormData();
    formData.append('foto_perfil', file);
    
    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatar-preview');
        preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="profile-avatar">';
    };
    reader.readAsDataURL(file);
    
    // Enviar archivo
    fetch('<?php echo BASE_URL; ?>/perfil/subir-foto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Foto actualizada correctamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al subir la foto');
        console.error(error);
    });
}

// Validación del formulario de contraseña
document.getElementById('form-password')?.addEventListener('submit', function(e) {
    const nueva = document.getElementById('password_nueva').value;
    const confirmar = document.getElementById('password_confirmar').value;
    
    if (nueva !== confirmar) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
    
    // Validar requisitos de contraseña
    if (nueva.length < 8) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 8 caracteres');
        return false;
    }
    
    if (!/[A-Z]/.test(nueva) || !/[a-z]/.test(nueva) || !/[0-9]/.test(nueva)) {
        e.preventDefault();
        alert('La contraseña debe contener mayúsculas, minúsculas y números');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
