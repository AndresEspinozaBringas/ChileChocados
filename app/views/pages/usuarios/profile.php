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
    position: relative;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar-upload {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 6px;
    text-align: center;
    font-size: 11px;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s;
}

.profile-avatar:hover .profile-avatar-upload {
    opacity: 1;
}

.avatar-upload-input {
    display: none;
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
    background: var(--cc-white);
    color: var(--cc-text-primary);
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

/* Forzar estilos consistentes para todos los tipos de input */
input[type="text"].form-input,
input[type="email"].form-input,
input[type="tel"].form-input {
    background: var(--cc-white) !important;
    color: var(--cc-text-primary) !important;
}

/* Prevenir estilos de autocompletado del navegador */
.form-input:-webkit-autofill,
.form-input:-webkit-autofill:hover,
.form-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px var(--cc-white) inset !important;
    -webkit-text-fill-color: var(--cc-text-primary) !important;
    box-shadow: 0 0 0 1000px var(--cc-white) inset !important;
}

.form-help {
    font-size: 13px;
    color: var(--cc-text-secondary);
    margin-top: 4px;
}

@media (max-width: 768px) {
    .profile-container {
        margin: 16px auto;
        padding: 0 16px;
    }
    
    .profile-header {
        padding: 20px;
    }
    
    .profile-header > div {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 16px !important;
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        font-size: 32px;
    }
    
    .profile-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 16px;
    }
    
    .profile-stat {
        padding: 16px 12px;
    }
    
    .profile-stat-value {
        font-size: 24px;
    }
    
    .profile-stat-label {
        font-size: 11px;
    }
    
    .profile-section {
        padding: 20px;
    }
    
    .profile-section-title {
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 12px;
    }
    
    .profile-section form > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-label {
        font-size: 13px;
    }
    
    .form-input {
        padding: 10px 14px;
        font-size: 14px;
    }
    
    .profile-section > div[style*="display: flex"] {
        flex-direction: column !important;
    }
    
    .profile-section > div[style*="display: flex"] button,
    .profile-section > div[style*="display: flex"] a {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    
    /* Grid de publicaciones */
    .profile-section > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
}

@media (max-width: 480px) {
    .profile-stats {
        grid-template-columns: 1fr;
    }
    
    .profile-header h1 {
        font-size: 22px !important;
    }
    
    .profile-header p {
        font-size: 14px !important;
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

:root[data-theme="dark"] .profile-header {
    background: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .profile-avatar {
    background: rgba(230, 51, 42, 0.15);
    border-color: var(--cc-primary);
}

:root[data-theme="dark"] .profile-stat {
    background: #111827;
    border-color: #374151;
}

:root[data-theme="dark"] .profile-stat-label {
    color: #9CA3AF;
}

:root[data-theme="dark"] .profile-section {
    background: #1F2937;
    border-color: #374151;
}

:root[data-theme="dark"] .profile-section-title {
    color: #F3F4F6;
    border-bottom-color: #374151;
}

:root[data-theme="dark"] .form-label {
    color: #D1D5DB;
}

:root[data-theme="dark"] .form-input {
    background: #374151 !important;
    border-color: #4B5563;
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] .form-input:focus {
    border-color: var(--cc-primary);
    background: #1F2937 !important;
}

:root[data-theme="dark"] .form-input:disabled {
    background: #4B5563 !important;
    color: #9CA3AF !important;
}

:root[data-theme="dark"] .form-input::placeholder {
    color: #9CA3AF;
}

/* Forzar estilos consistentes en modo oscuro para todos los tipos de input */
:root[data-theme="dark"] input[type="text"].form-input,
:root[data-theme="dark"] input[type="email"].form-input,
:root[data-theme="dark"] input[type="tel"].form-input {
    background: #374151 !important;
    color: #F3F4F6 !important;
}

/* Prevenir estilos de autocompletado del navegador en modo oscuro */
:root[data-theme="dark"] .form-input:-webkit-autofill,
:root[data-theme="dark"] .form-input:-webkit-autofill:hover,
:root[data-theme="dark"] .form-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px #374151 inset !important;
    -webkit-text-fill-color: #F3F4F6 !important;
    box-shadow: 0 0 0 1000px #374151 inset !important;
}

:root[data-theme="dark"] .form-help {
    color: #9CA3AF;
}

/* Badges y spans con estilos inline */
:root[data-theme="dark"] span[style*="background: var(--cc-bg-muted)"] {
    background: #374151 !important;
    color: #D1D5DB !important;
}

/* Títulos y textos */
:root[data-theme="dark"] h1,
:root[data-theme="dark"] h2 {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] p[style*="color: var(--cc-text-secondary)"] {
    color: #D1D5DB !important;
}

/* Publicaciones cards */
:root[data-theme="dark"] div[style*="border: 2px solid var(--cc-border-default)"] {
    border-color: #374151 !important;
    background: #1F2937;
}

:root[data-theme="dark"] div[style*="border: 2px solid var(--cc-border-default)"]:hover {
    border-color: var(--cc-primary) !important;
}

:root[data-theme="dark"] div[style*="background: var(--cc-bg-muted)"] {
    background: #374151 !important;
}

:root[data-theme="dark"] div[style*="color: var(--cc-text-primary)"] {
    color: #F3F4F6 !important;
}

:root[data-theme="dark"] div[style*="color: var(--cc-text-secondary)"] {
    color: #9CA3AF !important;
}

/* Mensajes flash */
:root[data-theme="dark"] div[style*="background: rgba(16, 185, 129, 0.1)"] {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: #10B981 !important;
    color: #6EE7B7 !important;
}

:root[data-theme="dark"] div[style*="background: rgba(239, 68, 68, 0.1)"] {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: #EF4444 !important;
    color: #FCA5A5 !important;
}

/* Botones */
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
    background: var(--cc-primary-dark);
    border-color: var(--cc-primary-dark);
}

/* Imágenes de publicaciones */
:root[data-theme="dark"] img {
    opacity: 0.9;
}

:root[data-theme="dark"] img:hover {
    opacity: 1;
}
</style>

<main class="profile-container">
    
    <!-- Encabezado del perfil -->
    <div class="profile-header">
        <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
            <div class="profile-avatar" onclick="document.getElementById('avatar-upload').click()">
                <?php if (!empty($usuario['avatar'])): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo htmlspecialchars($usuario['avatar']); ?>" 
                         alt="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                         id="avatar-preview">
                <?php else: ?>
                    <span id="avatar-initial"><?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?></span>
                <?php endif; ?>
                <div class="profile-avatar-upload">
                    <?php echo icon('camera', 14); ?> Cambiar foto
                </div>
            </div>
            <input type="file" 
                   id="avatar-upload" 
                   class="avatar-upload-input" 
                   accept="image/jpeg,image/jpg,image/png,image/webp"
                   onchange="subirAvatar(this)">
            
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

// Subir avatar
function subirAvatar(input) {
    const file = input.files[0];
    
    if (!file) return;
    
    // Validar tipo de archivo
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Por favor selecciona una imagen válida (JPG, PNG o WebP)');
        input.value = '';
        return;
    }
    
    // Validar tamaño (máximo 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen no debe superar 2MB');
        input.value = '';
        return;
    }
    
    // Mostrar preview inmediato
    const reader = new FileReader();
    reader.onload = function(e) {
        const avatarContainer = document.querySelector('.profile-avatar');
        const existingImg = document.getElementById('avatar-preview');
        const initial = document.getElementById('avatar-initial');
        
        if (existingImg) {
            existingImg.src = e.target.result;
        } else if (initial) {
            initial.remove();
            const img = document.createElement('img');
            img.id = 'avatar-preview';
            img.src = e.target.result;
            img.alt = 'Avatar';
            avatarContainer.insertBefore(img, avatarContainer.firstChild);
        }
    };
    reader.readAsDataURL(file);
    
    // Subir archivo
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');
    
    // Mostrar indicador de carga
    const uploadIndicator = document.querySelector('.profile-avatar-upload');
    const originalText = uploadIndicator.innerHTML;
    uploadIndicator.innerHTML = '<?php echo icon("loader", 14); ?> Subiendo...';
    uploadIndicator.style.opacity = '1';
    
    fetch('<?php echo BASE_URL; ?>/perfil/actualizar-avatar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar avatar en el header también
            const headerAvatar = document.querySelector('.user-avatar');
            if (headerAvatar && headerAvatar.tagName === 'IMG') {
                headerAvatar.src = '<?php echo BASE_URL; ?>/uploads/avatars/' + data.avatar + '?t=' + Date.now();
            } else if (headerAvatar) {
                // Reemplazar placeholder con imagen
                const img = document.createElement('img');
                img.src = '<?php echo BASE_URL; ?>/uploads/avatars/' + data.avatar + '?t=' + Date.now();
                img.alt = '<?php echo htmlspecialchars($usuario['nombre']); ?>';
                img.className = 'user-avatar';
                headerAvatar.parentNode.replaceChild(img, headerAvatar);
            }
            
            uploadIndicator.innerHTML = '<?php echo icon("check", 14); ?> ¡Actualizado!';
            setTimeout(() => {
                uploadIndicator.innerHTML = originalText;
                uploadIndicator.style.opacity = '0';
            }, 2000);
        } else {
            alert('Error: ' + (data.message || 'No se pudo subir la imagen'));
            uploadIndicator.innerHTML = originalText;
            uploadIndicator.style.opacity = '0';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al subir la imagen. Por favor intenta nuevamente.');
        uploadIndicator.innerHTML = originalText;
        uploadIndicator.style.opacity = '0';
    });
}
</script>

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
