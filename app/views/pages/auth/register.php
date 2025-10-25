<?php
/**
 * Vista de Registro - ChileChocados
 * Formulario de registro de nuevos usuarios
 */

use App\Helpers\Session;

$pageTitle = 'Registro - ChileChocados';
$hideNav = true; // No mostrar navegación en páginas de autenticación
require_once __DIR__ . '/../../layouts/header.php';

// Obtener datos antiguos y errores de la sesión
$old = Session::getFlash('old') ?? [];
$errors = Session::getFlash('errors') ?? [];
$error = Session::getFlash('error');
$success = Session::getFlash('success');
?>

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <!-- Header -->
            <div class="auth-header">
                <a href="/" class="auth-logo">
                    <img src="/assets/images/logo.png" alt="ChileChocados">
                </a>
                <h1>Crear cuenta</h1>
                <p>Únete a la comunidad de ChileChocados</p>
            </div>

            <!-- Mensajes flash -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i data-lucide="alert-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulario de registro -->
            <form action="/registro" method="POST" class="auth-form" id="registerForm">
                <!-- Nombre y Apellido -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">
                            Nombre <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-control <?= isset($errors['nombre']) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                            required
                            maxlength="100"
                        >
                        <?php if (isset($errors['nombre'])): ?>
                            <span class="error-message"><?= $errors['nombre'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="apellido">
                            Apellido <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="apellido" 
                            name="apellido" 
                            class="form-control <?= isset($errors['apellido']) ? 'error' : '' ?>"
                            value="<?= htmlspecialchars($old['apellido'] ?? '') ?>"
                            required
                            maxlength="100"
                        >
                        <?php if (isset($errors['apellido'])): ?>
                            <span class="error-message"><?= $errors['apellido'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                    >
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono">
                        Teléfono <span class="optional">(opcional)</span>
                    </label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        class="form-control <?= isset($errors['telefono']) ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                        placeholder="+56 9 1234 5678"
                    >
                    <small class="form-help">Formato: +56 9 XXXX XXXX</small>
                    <?php if (isset($errors['telefono'])): ?>
                        <span class="error-message"><?= $errors['telefono'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tipo de usuario -->
                <div class="form-group">
                    <label>
                        Tipo de cuenta <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input 
                                type="radio" 
                                name="rol" 
                                value="comprador" 
                                <?= ($old['rol'] ?? 'comprador') === 'comprador' ? 'checked' : '' ?>
                            >
                            <span class="radio-custom"></span>
                            <div class="radio-content">
                                <strong>Comprador</strong>
                                <small>Busco vehículos siniestrados</small>
                            </div>
                        </label>

                        <label class="radio-label">
                            <input 
                                type="radio" 
                                name="rol" 
                                value="vendedor"
                                <?= ($old['rol'] ?? '') === 'vendedor' ? 'checked' : '' ?>
                            >
                            <span class="radio-custom"></span>
                            <div class="radio-content">
                                <strong>Vendedor</strong>
                                <small>Quiero publicar vehículos</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password">
                        Contraseña <span class="required">*</span>
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control <?= isset($errors['password']) ? 'error' : '' ?>"
                            required
                            minlength="8"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar"></div>
                        <span class="strength-text"></span>
                    </div>
                    <small class="form-help">Mínimo 8 caracteres, incluye mayúsculas, minúsculas y números</small>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Confirmar contraseña -->
                <div class="form-group">
                    <label for="password_confirm">
                        Confirmar contraseña <span class="required">*</span>
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-control <?= isset($errors['password_confirm']) ? 'error' : '' ?>"
                            required
                            minlength="8"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirm')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password_confirm'])): ?>
                        <span class="error-message"><?= $errors['password_confirm'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Términos y condiciones -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        <span class="checkbox-custom"></span>
                        <span>
                            Acepto los <a href="/terminos" target="_blank">Términos y Condiciones</a> 
                            y la <a href="/privacidad" target="_blank">Política de Privacidad</a>
                        </span>
                    </label>
                </div>

                <!-- Botón de registro -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="user-plus"></i>
                    <span>Crear cuenta</span>
                </button>

                <!-- Link a login -->
                <div class="auth-footer">
                    <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

.auth-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    max-width: 600px;
    width: 100%;
    padding: 3rem;
    margin: 0 1rem;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-logo img {
    height: 50px;
    margin-bottom: 1.5rem;
}

.auth-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #2E2E2E;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: #666;
    font-size: 1rem;
}

.auth-form {
    margin-top: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2E2E2E;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.required {
    color: #E6332A;
}

.optional {
    color: #999;
    font-weight: 400;
    font-size: 0.85rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #E6332A;
    box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.1);
}

.form-control.error {
    border-color: #E6332A;
}

.error-message {
    color: #E6332A;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}

.form-help {
    color: #666;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}

.radio-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.radio-label {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.radio-label:hover {
    border-color: #E6332A;
    background: rgba(230, 51, 42, 0.02);
}

.radio-label input[type="radio"] {
    display: none;
}

.radio-label input[type="radio"]:checked + .radio-custom {
    border-color: #E6332A;
    background: #E6332A;
}

.radio-label input[type="radio"]:checked + .radio-custom::after {
    opacity: 1;
}

.radio-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 50%;
    margin-right: 1rem;
    position: relative;
    transition: all 0.3s ease;
}

.radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.radio-content {
    flex: 1;
}

.radio-content strong {
    display: block;
    color: #2E2E2E;
    margin-bottom: 0.25rem;
}

.radio-content small {
    color: #666;
    font-size: 0.85rem;
}

.password-input {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 0.25rem;
}

.toggle-password:hover {
    color: #E6332A;
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.strength-text {
    font-size: 0.85rem;
    color: #666;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 4px;
    margin-right: 0.75rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: #E6332A;
    border-color: #E6332A;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    color: white;
    font-size: 14px;
}

.checkbox-label span:last-child {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
}

.checkbox-label a {
    color: #E6332A;
    text-decoration: none;
}

.checkbox-label a:hover {
    text-decoration: underline;
}

.btn-block {
    width: 100%;
    margin-top: 2rem;
}

.auth-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
}

.auth-footer p {
    color: #666;
    font-size: 0.95rem;
}

.auth-footer a {
    color: #E6332A;
    text-decoration: none;
    font-weight: 600;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

@media (max-width: 768px) {
    .auth-wrapper {
        padding: 2rem 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .auth-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<script>
// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.target.closest('button').querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    
    // Reload Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Password strength checker
document.getElementById('password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    
    let strength = 0;
    let text = '';
    let color = '';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            text = 'Débil';
            color = '#E6332A';
            break;
        case 2:
            text = 'Media';
            color = '#F5C542';
            break;
        case 3:
            text = 'Buena';
            color = '#4CAF50';
            break;
        case 4:
            text = 'Excelente';
            color = '#2E7D32';
            break;
    }
    
    strengthBar.style.background = `linear-gradient(to right, ${color} ${strength * 25}%, #e0e0e0 ${strength * 25}%)`;
    strengthText.textContent = text;
    strengthText.style.color = color;
});

// Form validation
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
