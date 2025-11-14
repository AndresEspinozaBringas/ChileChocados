<?php
/**
 * Vista Reset Password - ChileChocados
 * Formulario para establecer nueva contraseña
 */

use App\Helpers\Session;

$pageTitle = 'Nueva Contraseña - ChileChocados';
$hideNav = true; // No mostrar navegación en páginas de autenticación
require_once __DIR__ . '/../../layouts/header.php';

$error = Session::getFlash('error');
// El token viene como parámetro del método resetPassword() del controlador
// y está disponible en la variable $token
?>

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <!-- Header -->
            <div class="auth-header">
                <a href="<?php echo BASE_URL; ?>/" class="auth-logo">
                    <img src="<?php echo BASE_URL; ?>/assets/logo-chch.svg" alt="ChileChocados">
                </a>
                <h1>Nueva contraseña</h1>
                <p>Ingresa tu nueva contraseña segura</p>
            </div>

            <!-- Mensajes flash -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i data-lucide="alert-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form action="/reset-password/<?= htmlspecialchars($token) ?>" method="POST" class="auth-form" id="resetForm">

                <!-- Nueva contraseña -->
                <div class="form-group">
                    <label for="password">
                        Nueva contraseña <span class="required">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control"
                            placeholder="••••••••"
                            required
                            minlength="8"
                            autofocus
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
                </div>

                <!-- Confirmar contraseña -->
                <div class="form-group">
                    <label for="password_confirm">
                        Confirmar contraseña <span class="required">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-control"
                            placeholder="••••••••"
                            required
                            minlength="8"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirm')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Botón -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="check"></i>
                    <span>Actualizar contraseña</span>
                </button>

                <!-- Links -->
                <div class="auth-footer">
                    <p><a href="/login">Volver al inicio de sesión</a></p>
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
    padding: 3rem 1rem;
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

.auth-page .container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.auth-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    max-width: 450px;
    width: 100%;
    padding: 3rem;
    margin: 0 auto;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-logo img {
    height: 45px;
    width: auto;
    margin-bottom: 1.5rem;
}

.auth-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2E2E2E;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: #666;
    font-size: 0.95rem;
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

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    width: 20px;
    height: 20px;
}

.form-control {
    width: 100%;
    padding: 0.75rem 3rem 0.75rem 3rem;
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
    z-index: 2;
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

.form-help {
    color: #666;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: block;
}

.btn-block {
    width: 100%;
    margin-top: 1.5rem;
}

.auth-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
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

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

@media (max-width: 768px) {
    .auth-wrapper {
        padding: 2rem 1.5rem;
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
document.getElementById('resetForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
});
</script>

</body>
</html>
