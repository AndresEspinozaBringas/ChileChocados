<?php
/**
 * Vista de Login - ChileChocados
 * Formulario de inicio de sesión
 */

use App\Helpers\Session;

$pageTitle = 'Iniciar Sesión - ChileChocados';
$hideNav = true; // No mostrar navegación en páginas de autenticación
require_once __DIR__ . '/../../layouts/header.php';

$error = Session::getFlash('error');
$success = Session::getFlash('success');
$info = Session::getFlash('info');
?>

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper auth-login">
            <!-- Header -->
            <div class="auth-header">
                <a href="/" class="auth-logo">
                    <img src="/assets/images/logo.png" alt="ChileChocados">
                </a>
                <h1>Bienvenido de vuelta</h1>
                <p>Inicia sesión para continuar</p>
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

            <?php if ($info): ?>
                <div class="alert alert-info">
                    <i data-lucide="info"></i>
                    <span><?= htmlspecialchars($info) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form action="/login" method="POST" class="auth-form" id="loginForm">
                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i data-lucide="mail" class="input-icon"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control"
                            placeholder="tu@email.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password">
                        Contraseña <span class="required">*</span>
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
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Recordarme y Olvidé contraseña -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span class="checkbox-custom"></span>
                        <span>Recordarme (7 días)</span>
                    </label>

                    <a href="/recuperar-contrasena" class="forgot-link">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón de login -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="log-in"></i>
                    <span>Iniciar sesión</span>
                </button>

                <!-- Divider -->
                <div class="divider">
                    <span>O</span>
                </div>

                <!-- Link a registro -->
                <a href="/registro" class="btn btn-outline btn-block">
                    <i data-lucide="user-plus"></i>
                    <span>Crear cuenta nueva</span>
                </a>

                <!-- Info de seguridad -->
                <div class="security-info">
                    <i data-lucide="shield"></i>
                    <p>Conexión segura protegida con cifrado SSL</p>
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
    max-width: 450px;
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
    padding: 0.75rem 1rem 0.75rem 3rem;
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

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.9rem;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #ccc;
    border-radius: 4px;
    margin-right: 0.5rem;
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
    font-size: 12px;
}

.forgot-link {
    color: #E6332A;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.forgot-link:hover {
    text-decoration: underline;
}

.btn-block {
    width: 100%;
}

.divider {
    position: relative;
    text-align: center;
    margin: 2rem 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e0e0e0;
}

.divider span {
    position: relative;
    background: white;
    padding: 0 1rem;
    color: #999;
    font-size: 0.9rem;
}

.btn-outline {
    background: white;
    border: 2px solid #e0e0e0;
    color: #2E2E2E;
}

.btn-outline:hover {
    background: #f5f5f5;
    border-color: #ccc;
}

.security-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
}

.security-info i {
    width: 18px;
    height: 18px;
    color: #4CAF50;
}

.security-info p {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
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

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

@media (max-width: 768px) {
    .auth-wrapper {
        padding: 2rem 1.5rem;
    }
    
    .auth-header h1 {
        font-size: 1.5rem;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
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
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
