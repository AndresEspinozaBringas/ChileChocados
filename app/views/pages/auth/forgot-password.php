<?php
/**
 * Vista Recuperar Contraseña - ChileChocados
 * Formulario para solicitar recuperación de contraseña
 */

use App\Helpers\Session;

$pageTitle = 'Recuperar Contraseña - ChileChocados';
$hideNav = true; // No mostrar navegación en páginas de autenticación
require_once __DIR__ . '/../../layouts/header.php';

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
                <h1>¿Olvidaste tu contraseña?</h1>
                <p>Ingresa tu email y te enviaremos un enlace para recuperarla</p>
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

            <!-- Formulario -->
            <form action="/recuperar-contrasena" method="POST" class="auth-form">
                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email registrado <span class="required">*</span>
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

                <!-- Botón -->
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="send"></i>
                    <span>Enviar enlace de recuperación</span>
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
    font-size: 1.75rem;
    font-weight: 700;
    color: #2E2E2E;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
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
    
    .auth-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
