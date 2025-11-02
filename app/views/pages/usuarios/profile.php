<?php
/**
 * Vista de Perfil de Usuario
 * Muestra y permite editar los datos del perfil del usuario autenticado
 */

$pageTitle = 'Mi Perfil - ChileChocados';
$currentPage = 'perfil';

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* Variables y estilos del perfil */
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

.profile-container {
    max-width: 1200px;
    margin: 32px auto;
    padding: 0 20px;
}

.profile-header {
    background: var(--cc-white);
    border: 2px solid var(--cc-border-default);
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 24px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--cc-primary-pale);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
    color: var(--cc-primary);
    border: 3px solid var(--cc-primary);
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 24px;
}

.profile-stat {
    text-align: center;
    padding: 20px;
    background: var(--cc-bg-surface);
    border-radius: 8px;
    border: 1px solid var(--cc-border-default);
}

.profile-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--cc-primary);
    margin-bottom: 4px;
}

.profile-stat-label {
    font-size: 13px;
    color: var(--cc-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.profile-section {
    background: var(--cc-white);
    border: 2px solid var(--cc-border-default);
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 24px;
}

.profile-section-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--cc-border-default);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--cc-text-primary);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--cc-border-default);
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--cc-primary);
    box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.1);
}

.form-input:disabled {
    background: var(--cc-bg-muted);
    cursor: not-allowed;
}

.form-help {
    font-size: 13px;
    color: var(--cc-text-secondary);
    margin-top: 4px;
}

@media (max-width: 768px) {
    .profile-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<main class="profile-container">
    
    <!-- Encabezado del perfil -->
    <div class="profile-header">
        <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>
            
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; color: var(--cc-text-primary); margin: 0 0 8px 0;">
                    <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                </h1>
                <p style="font-size: 15px; color: var(--cc-text-secondary); margin: 0 0 12px 0;">
                    <?php echo htmlspecialchars($usuario['email']); ?>
                    <?php if (!empty($usuario['telefono'])): ?>
                        · <?php echo htmlspecialchars($usuario['telefono']); ?>
                    <?php endif; ?>
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <span style="background: var(--cc-primary-pale); color: var(--cc-primary); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; text-transform: uppercase;">
                        <?php echo ucfirst($usuario['rol']); ?>
                    </span>
                    <span style="background: var(--cc-bg-muted); color: var(--cc-text-secondary); padding: 6px 12px; border-radius: 6px; font-size: 13px;">
                        Miembro desde <?php echo date('M Y', strtotime($usuario['fecha_registro'])); ?>
                    </span>
                    <?php if ($usuario['verificado']): ?>
                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                        ✓ Verificado
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="profile-stats">
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo $estadisticas['total_publicaciones'] ?? 0; ?></div>
                <div class="profile-stat-label">Publicaciones</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo $estadisticas['publicaciones_activas'] ?? 0; ?></div>
                <div class="profile-stat-label">Activas</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo $estadisticas['ventas_realizadas'] ?? 0; ?></div>
                <div class="profile-stat-label">Vendidas</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value"><?php echo number_format($estadisticas['total_visitas'] ?? 0); ?></div>
                <div class="profile-stat-label">Visitas</div>
            </div>
        </div>
    </div>

    <!-- Mensajes flash -->
    <?php if (isset($_SESSION['success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.1); border: 2px solid #10B981; border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #065F46;">
        <strong>✓ Éxito:</strong> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div style="background: rgba(239, 68, 68, 0.1); border: 2px solid #EF4444; border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #991B1B;">
        <strong>✗ Error:</strong> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <!-- Formulario de edición de perfil -->
    <div class="profile-section">
        <h2 class="profile-section-title">Información Personal</h2>
        
        <form method="POST" action="<?php echo BASE_URL; ?>/perfil/actualizar">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre *</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                        required
                        minlength="2"
                        maxlength="100"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="apellido">Apellido *</label>
                    <input 
                        type="text" 
                        id="apellido" 
                        name="apellido" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($usuario['apellido']); ?>"
                        required
                        minlength="2"
                        maxlength="100"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($usuario['email']); ?>"
                        required
                    >
                    <div class="form-help">Tu email de contacto</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                        placeholder="+56912345678"
                        pattern="(\+?56)?9\d{8}"
                    >
                    <div class="form-help">Formato: +56912345678 o 912345678</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="rut">RUT</label>
                    <input 
                        type="text" 
                        id="rut" 
                        name="rut" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($usuario['rut'] ?? ''); ?>"
                        placeholder="12.345.678-9"
                    >
                    <div class="form-help">Formato: 12.345.678-9</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="rol">Rol</label>
                    <input 
                        type="text" 
                        id="rol" 
                        class="form-input" 
                        value="<?php echo ucfirst($usuario['rol']); ?>"
                        disabled
                    >
                    <div class="form-help">El rol no puede ser modificado</div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn primary">
                    Guardar Cambios
                </button>
                <a href="<?php echo BASE_URL; ?>/mis-publicaciones" class="btn">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Publicaciones Activas -->
    <?php if (!empty($publicacionesActivas)): ?>
    <div class="profile-section">
        <h2 class="profile-section-title">Mis Publicaciones Activas</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
            <?php foreach ($publicacionesActivas as $pub): ?>
            <a href="<?php echo BASE_URL; ?>/publicacion/<?php echo $pub['id']; ?>" style="text-decoration: none; color: inherit;">
                <div style="border: 2px solid var(--cc-border-default); border-radius: 8px; overflow: hidden; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--cc-primary)'" onmouseout="this.style.borderColor='var(--cc-border-default)'">
                    <div style="width: 100%; height: 150px; background: var(--cc-bg-muted);">
                        <?php if (!empty($pub['foto_principal'])): ?>
                        <img src="<?php echo BASE_URL; ?>/uploads/publicaciones/<?php echo htmlspecialchars($pub['foto_principal']); ?>" 
                             alt="<?php echo htmlspecialchars($pub['titulo']); ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <div style="padding: 12px;">
                        <div style="font-size: 14px; font-weight: 600; color: var(--cc-text-primary); margin-bottom: 4px;">
                            <?php echo htmlspecialchars($pub['titulo']); ?>
                        </div>
                        <div style="font-size: 16px; font-weight: 700; color: var(--cc-primary);">
                            <?php echo formatPrice($pub['precio']); ?>
                        </div>
                        <div style="font-size: 12px; color: var(--cc-text-secondary); margin-top: 4px;">
                            <?php echo $pub['visitas'] ?? 0; ?> visitas
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</main>

<script>
// Validación de RUT en tiempo real
document.getElementById('rut')?.addEventListener('blur', function() {
    const rut = this.value.trim();
    if (rut && !validarRUT(rut)) {
        this.style.borderColor = 'var(--cc-danger)';
        alert('El RUT ingresado no es válido');
    } else {
        this.style.borderColor = 'var(--cc-border-default)';
    }
});

function validarRUT(rut) {
    // Limpiar RUT
    rut = rut.replace(/\./g, '').replace(/-/g, '');
    
    if (rut.length < 2) return false;
    
    const cuerpo = rut.slice(0, -1);
    const dv = rut.slice(-1).toUpperCase();
    
    // Calcular DV
    let suma = 0;
    let multiplo = 2;
    
    for (let i = cuerpo.length - 1; i >= 0; i--) {
        suma += parseInt(cuerpo.charAt(i)) * multiplo;
        multiplo = multiplo === 7 ? 2 : multiplo + 1;
    }
    
    const dvEsperado = 11 - (suma % 11);
    const dvCalculado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : dvEsperado.toString();
    
    return dv === dvCalculado;
}

// Formatear RUT mientras se escribe
document.getElementById('rut')?.addEventListener('input', function() {
    let rut = this.value.replace(/\./g, '').replace(/-/g, '');
    
    if (rut.length > 1) {
        const cuerpo = rut.slice(0, -1);
        const dv = rut.slice(-1);
        
        // Formatear con puntos
        const cuerpoFormateado = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        this.value = cuerpoFormateado + '-' + dv;
    }
});

// Formatear teléfono
document.getElementById('telefono')?.addEventListener('input', function() {
    let tel = this.value.replace(/\D/g, '');
    
    if (tel.startsWith('56')) {
        tel = '+' + tel;
    } else if (tel.startsWith('9') && tel.length === 9) {
        tel = '+56' + tel;
    }
    
    this.value = tel;
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
