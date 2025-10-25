<?php
/**
 * Vista Verify Email - ChileChocados
 * Página informativa después del registro
 */

$pageTitle = 'Verifica tu Email - ChileChocados';
require_once __DIR__ . '/../../layouts/header.php';
?>

<main class="verify-page">
    <div class="container">
        <div class="verify-wrapper">
            <!-- Icono -->
            <div class="verify-icon">
                <i data-lucide="mail-check"></i>
            </div>

            <!-- Contenido -->
            <div class="verify-content">
                <h1>¡Verifica tu email!</h1>
                <p class="subtitle">
                    Te hemos enviado un correo de verificación a tu dirección de email.
                </p>

                <div class="verify-steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Revisa tu bandeja</h3>
                            <p>Busca el email de ChileChocados en tu bandeja de entrada</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Haz clic en el enlace</h3>
                            <p>Abre el email y haz clic en el botón de verificación</p>
                        </div>
                    </div>

                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>¡Listo!</h3>
                            <p>Tu cuenta estará verificada y podrás iniciar sesión</p>
                        </div>
                    </div>
                </div>

                <!-- Info adicional -->
                <div class="verify-info">
                    <div class="info-box">
                        <i data-lucide="info"></i>
                        <div>
                            <strong>¿No recibiste el email?</strong>
                            <p>Revisa tu carpeta de spam o correo no deseado</p>
                        </div>
                    </div>

                    <div class="info-box">
                        <i data-lucide="clock"></i>
                        <div>
                            <strong>El enlace expira en 24 horas</strong>
                            <p>Si no verificas tu email en este tiempo, deberás registrarte nuevamente</p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="verify-actions">
                    <a href="/" class="btn btn-primary">
                        <i data-lucide="home"></i>
                        <span>Ir al inicio</span>
                    </a>
                    <a href="/login" class="btn btn-outline">
                        <i data-lucide="log-in"></i>
                        <span>Iniciar sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.verify-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

.verify-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    max-width: 600px;
    width: 100%;
    padding: 3rem;
    margin: 0 1rem;
    text-align: center;
}

.verify-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #E6332A 0%, #c42a23 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.verify-icon i {
    width: 50px;
    height: 50px;
    color: white;
}

.verify-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #2E2E2E;
    margin-bottom: 1rem;
}

.subtitle {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 3rem;
}

.verify-steps {
    text-align: left;
    margin-bottom: 3rem;
}

.step {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 12px;
}

.step:last-child {
    margin-bottom: 0;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #E6332A;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.step-content h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2E2E2E;
    margin-bottom: 0.5rem;
}

.step-content p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0;
}

.verify-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-box {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    text-align: left;
}

.info-box i {
    width: 24px;
    height: 24px;
    color: #F5C542;
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.info-box strong {
    display: block;
    color: #2E2E2E;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.info-box p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.4;
}

.verify-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.verify-actions .btn {
    flex: 1;
    min-width: 180px;
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

@media (max-width: 768px) {
    .verify-wrapper {
        padding: 2rem 1.5rem;
    }
    
    .verify-content h1 {
        font-size: 1.5rem;
    }
    
    .subtitle {
        font-size: 1rem;
    }
    
    .step {
        gap: 1rem;
        padding: 1rem;
    }
    
    .verify-actions {
        flex-direction: column;
    }
    
    .verify-actions .btn {
        width: 100%;
    }
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
