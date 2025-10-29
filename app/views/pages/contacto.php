<?php
/**
 * Vista: Página de Contacto
 * Formulario general para contactar con el equipo de ChileChocados
 * URL: /contacto
 */

// Variables del controlador
$pageTitle = $data['title'] ?? 'Contacto - ChileChocados';
$metaDescription = $data['meta_description'] ?? 'Contáctanos para dudas, sugerencias o soporte técnico';
$success = $data['success'] ?? null;
$error = $data['error'] ?? null;

// Cargar header
require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
/* ============================================================================
 * ESTILOS ESPECÍFICOS PARA PÁGINA DE CONTACTO
 * ============================================================================ */
.contacto-hero {
    background: linear-gradient(135deg, var(--cc-primary) 0%, var(--cc-primary-dark) 100%);
    color: var(--cc-white);
    padding: 48px 0;
    margin-bottom: 48px;
}

.contacto-hero-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.contacto-hero h1 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 16px;
}

.contacto-hero p {
    font-size: 18px;
    opacity: 0.95;
}

.contacto-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px 80px;
}

.contacto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: start;
}

/* Información de contacto */
.contacto-info {
    position: sticky;
    top: 100px;
}

.contacto-info-card {
    background: var(--cc-bg-surface);
    border-radius: var(--cc-radius-2xl);
    padding: 32px;
    box-shadow: var(--cc-shadow);
    border: 1px solid var(--cc-border-light);
}

.contacto-info h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin-bottom: 8px;
}

.contacto-info .subtitle {
    color: var(--cc-text-secondary);
    margin-bottom: 32px;
    line-height: 1.6;
}

.contacto-method {
    display: flex;
    gap: 16px;
    padding: 20px 0;
    border-bottom: 1px solid var(--cc-border-light);
}

.contacto-method:last-child {
    border-bottom: none;
}

.contacto-method-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: var(--cc-radius-lg);
    background: var(--cc-primary-pale);
    color: var(--cc-primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.contacto-method-content h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--cc-text-primary);
    margin-bottom: 4px;
}

.contacto-method-content p {
    font-size: 14px;
    color: var(--cc-text-secondary);
    margin-bottom: 8px;
}

.contacto-method-content a {
    color: var(--cc-primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
}

.contacto-method-content a:hover {
    text-decoration: underline;
}

/* Formulario */
.contacto-form-wrapper {
    background: var(--cc-bg-surface);
    border-radius: var(--cc-radius-2xl);
    padding: 32px;
    box-shadow: var(--cc-shadow);
    border: 1px solid var(--cc-border-light);
}

.contacto-form-wrapper h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--cc-text-primary);
    margin-bottom: 24px;
}

/* Alertas */
.alert {
    padding: 16px 20px;
    border-radius: var(--cc-radius-lg);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.alert-success {
    background: var(--cc-success-light);
    border: 1px solid var(--cc-success);
    color: #065f46;
}

.alert-error {
    background: var(--cc-danger-light);
    border: 1px solid var(--cc-danger);
    color: #991b1b;
}

.alert svg {
    flex-shrink: 0;
}

/* Form Group */
.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--cc-text-primary);
    margin-bottom: 8px;
}

.form-group label .required {
    color: var(--cc-danger);
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    font-size: 14px;
    font-family: inherit;
    color: var(--cc-text-primary);
    background: var(--cc-white);
    border: 1px solid var(--cc-border-default);
    border-radius: var(--cc-radius-lg);
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--cc-primary);
    box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.1);
}

.form-control::placeholder {
    color: var(--cc-text-tertiary);
}

textarea.form-control {
    min-height: 140px;
    resize: vertical;
}

select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 40px;
}

.form-help {
    font-size: 13px;
    color: var(--cc-text-tertiary);
    margin-top: 6px;
}

/* Botones */
.btn-submit {
    width: 100%;
    padding: 14px 24px;
    font-size: 16px;
    font-weight: 600;
    color: var(--cc-white);
    background: var(--cc-primary);
    border: none;
    border-radius: var(--cc-radius-lg);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    background: var(--cc-primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(230, 51, 42, 0.3);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-submit:disabled {
    background: var(--cc-gray-400);
    cursor: not-allowed;
    transform: none;
}

/* Responsive */
@media (max-width: 968px) {
    .contacto-hero h1 {
        font-size: 28px;
    }
    
    .contacto-hero p {
        font-size: 16px;
    }
    
    .contacto-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }
    
    .contacto-info {
        position: static;
    }
}

@media (max-width: 640px) {
    .contacto-hero {
        padding: 32px 0;
        margin-bottom: 32px;
    }
    
    .contacto-container {
        padding-bottom: 48px;
    }
    
    .contacto-info-card,
    .contacto-form-wrapper {
        padding: 24px;
    }
}
</style>

<!-- Hero Section -->
<section class="contacto-hero">
    <div class="contacto-hero-content">
        <h1>¿Necesitas ayuda?</h1>
        <p>Estamos aquí para ayudarte. Envíanos tu consulta y te responderemos lo antes posible.</p>
    </div>
</section>

<!-- Main Content -->
<main class="contacto-container">
    <div class="contacto-grid">
        
        <!-- Información de Contacto -->
        <aside class="contacto-info">
            <div class="contacto-info-card">
                <h2>Información de Contacto</h2>
                <p class="subtitle">Elige el método que prefieras para comunicarte con nosotros.</p>
                
                <!-- Email -->
                <div class="contacto-method">
                    <div class="contacto-method-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m2 7 10 8 10-8"/>
                        </svg>
                    </div>
                    <div class="contacto-method-content">
                        <h3>Email</h3>
                        <p>Escríbenos y te responderemos en menos de 24 horas</p>
                        <a href="mailto:soporte@chilechocados.cl">soporte@chilechocados.cl</a>
                    </div>
                </div>
                
                <!-- Teléfono -->
                <div class="contacto-method">
                    <div class="contacto-method-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="contacto-method-content">
                        <h3>Teléfono</h3>
                        <p>Lunes a Viernes de 9:00 a 18:00 hrs</p>
                        <a href="tel:+56912345678">+569 1234 5678</a>
                    </div>
                </div>
                
                <!-- Horario -->
                <div class="contacto-method">
                    <div class="contacto-method-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="contacto-method-content">
                        <h3>Horario de Atención</h3>
                        <p>Lunes a Viernes: 9:00 - 18:00 hrs</p>
                        <p>Sábados: 10:00 - 14:00 hrs</p>
                    </div>
                </div>
                
                <!-- Ubicación -->
                <div class="contacto-method">
                    <div class="contacto-method-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="contacto-method-content">
                        <h3>Ubicación</h3>
                        <p>Santiago, Chile</p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Formulario de Contacto -->
        <section class="contacto-form-wrapper">
            <h2>Envíanos un Mensaje</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form action="/contacto/enviar" method="POST" id="contactoForm">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">
                        Nombre completo
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-control"
                        placeholder="Ej: Juan Pérez"
                        required
                        minlength="3"
                        maxlength="100"
                    >
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Correo electrónico
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control"
                        placeholder="tu@email.com"
                        required
                    >
                </div>
                
                <!-- Asunto/Categoría -->
                <div class="form-group">
                    <label for="asunto">
                        Asunto
                        <span class="required">*</span>
                    </label>
                    <select id="asunto" name="asunto" class="form-control" required>
                        <option value="">Selecciona una categoría</option>
                        <option value="Consulta General">Consulta General</option>
                        <option value="Soporte Técnico">Soporte Técnico</option>
                        <option value="Problema con Publicación">Problema con Publicación</option>
                        <option value="Problema con Pago">Problema con Pago</option>
                        <option value="Denunciar Usuario">Denunciar Usuario</option>
                        <option value="Sugerencia">Sugerencia</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                
                <!-- Mensaje -->
                <div class="form-group">
                    <label for="mensaje">
                        Mensaje
                        <span class="required">*</span>
                    </label>
                    <textarea 
                        id="mensaje" 
                        name="mensaje" 
                        class="form-control"
                        placeholder="Escribe tu mensaje aquí..."
                        required
                        minlength="10"
                        maxlength="1000"
                    ></textarea>
                    <p class="form-help">Mínimo 10 caracteres, máximo 1000</p>
                </div>
                
                <!-- Botón Enviar -->
                <button type="submit" class="btn-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Enviar Mensaje
                </button>
            </form>
        </section>
        
    </div>
</main>

<script>
// Validación adicional del formulario
document.getElementById('contactoForm')?.addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const asunto = document.getElementById('asunto').value;
    const mensaje = document.getElementById('mensaje').value.trim();
    
    if (nombre.length < 3) {
        e.preventDefault();
        alert('El nombre debe tener al menos 3 caracteres');
        return false;
    }
    
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        e.preventDefault();
        alert('Por favor ingresa un email válido');
        return false;
    }
    
    if (!asunto) {
        e.preventDefault();
        alert('Por favor selecciona un asunto');
        return false;
    }
    
    if (mensaje.length < 10) {
        e.preventDefault();
        alert('El mensaje debe tener al menos 10 caracteres');
        return false;
    }
    
    return true;
});
</script>

<?php
// Cargar footer
require_once __DIR__ . '/../../layouts/footer.php';
?>
