<?php
/**
 * Vista: Configuración del Sistema - Panel Admin
 * Gestión de parámetros generales de la plataforma
 */
layout('header');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin-layout.css">

<main class="container admin-container">
    
    <div style="margin-bottom: 32px;">
        <h1 class="h1" style="margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
            <?php echo icon('settings', 32); ?>
            Configuración del Sistema
        </h1>
        <p class="meta" style="font-size: 15px;">
            Gestiona los parámetros generales de la plataforma
        </p>
    </div>
                

    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <?php echo icon('check-circle', 20); ?>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 24px;">
            <?php echo icon('alert-circle', 20); ?>
            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>
    
    <!-- Formulario de Configuración -->
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/configuracion/guardar" class="config-form">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        
        <!-- Sección: Información de la Empresa -->
        <div class="config-section">
            <h2><?php echo icon('building', 24); ?> Información de la Empresa</h2>
            <p class="section-description">Datos generales de contacto y ubicación</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sitio_nombre">
                        <?php echo icon('tag', 18); ?> Nombre del Sitio
                    </label>
                    <input 
                        type="text" 
                        id="sitio_nombre" 
                        name="sitio_nombre" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['sitio_nombre'] ?? 'ChileChocados'); ?>"
                        required>
                    <small class="form-text">Nombre de la empresa o sitio web</small>
                </div>
                
                <div class="form-group">
                    <label for="sitio_email">
                        <?php echo icon('mail', 18); ?> Email de Contacto
                    </label>
                    <input 
                        type="email" 
                        id="sitio_email" 
                        name="sitio_email" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['sitio_email'] ?? 'contacto@chilechocados.cl'); ?>"
                        required>
                    <small class="form-text">Email principal de contacto</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sitio_telefono">
                        <?php echo icon('phone', 18); ?> Teléfono de Contacto
                    </label>
                    <input 
                        type="text" 
                        id="sitio_telefono" 
                        name="sitio_telefono" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['sitio_telefono'] ?? '+56 9 1234 5678'); ?>"
                        required>
                    <small class="form-text">Teléfono principal de contacto</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_numero">
                        <?php echo icon('message-circle', 18); ?> Número de WhatsApp
                    </label>
                    <input 
                        type="text" 
                        id="whatsapp_numero" 
                        name="whatsapp_numero" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['whatsapp_numero'] ?? '+56912345678'); ?>"
                        placeholder="+56912345678">
                    <small class="form-text">Número de WhatsApp (sin espacios ni guiones)</small>
                </div>
            </div>
        </div>
        
        <!-- Sección: Redes Sociales -->
        <div class="config-section">
            <h2><?php echo icon('share-2', 24); ?> Redes Sociales</h2>
            <p class="section-description">Enlaces a las redes sociales de la empresa</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="facebook_url">
                        <?php echo icon('facebook', 18); ?> Facebook
                    </label>
                    <input 
                        type="url" 
                        id="facebook_url" 
                        name="facebook_url" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['facebook_url'] ?? 'https://facebook.com/chilechocados'); ?>"
                        placeholder="https://facebook.com/tuempresa">
                    <small class="form-text">URL de la página de Facebook</small>
                </div>
                
                <div class="form-group">
                    <label for="instagram_url">
                        <?php echo icon('instagram', 18); ?> Instagram
                    </label>
                    <input 
                        type="url" 
                        id="instagram_url" 
                        name="instagram_url" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['instagram_url'] ?? 'https://instagram.com/chilechocados'); ?>"
                        placeholder="https://instagram.com/tuempresa">
                    <small class="form-text">URL de la cuenta de Instagram</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="youtube_url">
                        <?php echo icon('youtube', 18); ?> YouTube
                    </label>
                    <input 
                        type="url" 
                        id="youtube_url" 
                        name="youtube_url" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['youtube_url'] ?? ''); ?>"
                        placeholder="https://youtube.com/@tuempresa">
                    <small class="form-text">URL del canal de YouTube</small>
                </div>
                
                <div class="form-group">
                    <label for="twitter_url">
                        <?php echo icon('twitter', 18); ?> Twitter / X
                    </label>
                    <input 
                        type="url" 
                        id="twitter_url" 
                        name="twitter_url" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['twitter_url'] ?? ''); ?>"
                        placeholder="https://twitter.com/tuempresa">
                    <small class="form-text">URL de la cuenta de Twitter/X</small>
                </div>
            </div>
        </div>
        
        <!-- Sección: Precios de Publicaciones Destacadas -->
        <div class="config-section">
            <h2><?php echo icon('dollar-sign', 24); ?> Precios de Publicaciones Destacadas</h2>
            <p class="section-description">Define los precios para las publicaciones destacadas</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="precio_destacado_15_dias">
                        <?php echo icon('star', 18); ?> Precio Destacado 15 días (CLP)
                    </label>
                    <input 
                        type="number" 
                        id="precio_destacado_15_dias" 
                        name="precio_destacado_15_dias" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['precio_destacado_15_dias'] ?? '15000'); ?>"
                        min="0"
                        step="1000"
                        required>
                    <small class="form-text">Precio en pesos chilenos para destacar una publicación por 15 días</small>
                </div>
                
                <div class="form-group">
                    <label for="precio_destacado_30_dias">
                        <?php echo icon('star', 18); ?> Precio Destacado 30 días (CLP)
                    </label>
                    <input 
                        type="number" 
                        id="precio_destacado_30_dias" 
                        name="precio_destacado_30_dias" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['precio_destacado_30_dias'] ?? '25000'); ?>"
                        min="0"
                        step="1000"
                        required>
                    <small class="form-text">Precio en pesos chilenos para destacar una publicación por 30 días</small>
                </div>
            </div>
        </div>
        
        <!-- Sección: Límites de Fotos -->
        <div class="config-section">
            <h2><?php echo icon('images', 24); ?> Límites de Fotos</h2>
            <p class="section-description">Configura las restricciones para las fotos de publicaciones</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="minimo_fotos">
                        <?php echo icon('image', 18); ?> Mínimo de Fotos
                    </label>
                    <input 
                        type="number" 
                        id="minimo_fotos" 
                        name="minimo_fotos" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['minimo_fotos'] ?? '1'); ?>"
                        min="1"
                        max="10"
                        required>
                    <small class="form-text">Cantidad mínima de fotos requeridas por publicación</small>
                </div>
                
                <div class="form-group">
                    <label for="maximo_fotos">
                        <?php echo icon('images', 18); ?> Máximo de Fotos
                    </label>
                    <input 
                        type="number" 
                        id="maximo_fotos" 
                        name="maximo_fotos" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['maximo_fotos'] ?? '6'); ?>"
                        min="1"
                        max="20"
                        required>
                    <small class="form-text">Cantidad máxima de fotos permitidas por publicación</small>
                </div>
            </div>
        </div>
        
        <!-- Sección: Tamaños de Archivos -->
        <div class="config-section">
            <h2><?php echo icon('upload', 24); ?> Tamaños Máximos de Archivos</h2>
            <p class="section-description">Define los límites de tamaño para archivos subidos</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tamano_maximo_imagen_mb">
                        <?php echo icon('file-image', 18); ?> Tamaño Máximo de Imagen (MB)
                    </label>
                    <input 
                        type="number" 
                        id="tamano_maximo_imagen_mb" 
                        name="tamano_maximo_imagen_mb" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['tamano_maximo_imagen_mb'] ?? '5'); ?>"
                        min="1"
                        max="50"
                        step="0.5"
                        required>
                    <small class="form-text">Tamaño máximo en megabytes para cada imagen</small>
                </div>
                
                <div class="form-group">
                    <label for="tamano_maximo_adjunto_mb">
                        <?php echo icon('paperclip', 18); ?> Tamaño Máximo de Adjunto (MB)
                    </label>
                    <input 
                        type="number" 
                        id="tamano_maximo_adjunto_mb" 
                        name="tamano_maximo_adjunto_mb" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($config['tamano_maximo_adjunto_mb'] ?? '10'); ?>"
                        min="1"
                        max="100"
                        step="0.5"
                        required>
                    <small class="form-text">Tamaño máximo en megabytes para archivos adjuntos en mensajes</small>
                </div>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="sticky-actions">
            <a href="<?php echo BASE_URL; ?>/admin" class="btn">Cancelar</a>
            <button type="submit" class="btn primary">Guardar Configuración</button>
        </div>
        
    </form>

</main>

<style>
        .config-form {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .config-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .config-section:last-of-type {
            border-bottom: none;
        }
        
        .config-section h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .config-section h2 i {
            color: #007bff;
            margin-right: 10px;
        }
        
        .section-description {
            color: #666;
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group label i {
            color: #007bff;
            margin-right: 5px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background: white;
            color: #333;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        /* Forzar estilos consistentes para todos los tipos de input */
        input[type="text"].form-control,
        input[type="email"].form-control,
        input[type="url"].form-control,
        input[type="number"].form-control {
            background: white !important;
            color: #333 !important;
        }
        
        /* Prevenir estilos de autocompletado del navegador */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
            -webkit-text-fill-color: #333 !important;
            box-shadow: 0 0 0 1000px white inset !important;
        }
        
        .form-text {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 0.875rem;
        }
        
        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            z-index: 10;
        }
        
        .btn {
            padding: 12px 24px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: #333;
        }
        
        .btn:hover {
            border-color: #E6332A;
            background: #fff5f5;
        }
        
        .btn.primary {
            background: #E6332A;
            color: white;
            border-color: #E6332A;
        }
        
        .btn.primary:hover {
            background: #c72a22;
            border-color: #c72a22;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* ============================================================================
         * DARK MODE
         * ============================================================================ */
        
        :root[data-theme="dark"] .config-form {
            background: #1F2937;
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        
        :root[data-theme="dark"] .config-section {
            border-bottom-color: #374151;
        }
        
        :root[data-theme="dark"] .config-section h2 {
            color: #F3F4F6;
        }
        
        :root[data-theme="dark"] .config-section h2 i,
        :root[data-theme="dark"] .config-section h2 svg {
            color: var(--cc-primary);
        }
        
        :root[data-theme="dark"] .section-description {
            color: #9CA3AF;
        }
        
        :root[data-theme="dark"] .form-group label {
            color: #D1D5DB;
        }
        
        :root[data-theme="dark"] .form-group label i,
        :root[data-theme="dark"] .form-group label svg {
            color: var(--cc-primary);
        }
        
        :root[data-theme="dark"] .form-control {
            background: #374151 !important;
            border-color: #4B5563;
            color: #F3F4F6 !important;
        }
        
        :root[data-theme="dark"] .form-control:focus {
            border-color: var(--cc-primary);
            background: #1F2937 !important;
            box-shadow: 0 0 0 3px rgba(230, 51, 42, 0.2);
        }
        
        :root[data-theme="dark"] .form-control::placeholder {
            color: #9CA3AF;
        }
        
        /* Forzar estilos consistentes en modo oscuro para todos los tipos de input */
        :root[data-theme="dark"] input[type="text"].form-control,
        :root[data-theme="dark"] input[type="email"].form-control,
        :root[data-theme="dark"] input[type="url"].form-control,
        :root[data-theme="dark"] input[type="number"].form-control {
            background: #374151 !important;
            color: #F3F4F6 !important;
        }
        
        /* Prevenir estilos de autocompletado del navegador en modo oscuro */
        :root[data-theme="dark"] .form-control:-webkit-autofill,
        :root[data-theme="dark"] .form-control:-webkit-autofill:hover,
        :root[data-theme="dark"] .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #374151 inset !important;
            -webkit-text-fill-color: #F3F4F6 !important;
            box-shadow: 0 0 0 1000px #374151 inset !important;
        }
        
        :root[data-theme="dark"] .form-text {
            color: #9CA3AF;
        }
        
        :root[data-theme="dark"] .sticky-actions {
            background: #1F2937;
            border-top-color: #374151;
        }
        
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
        
        :root[data-theme="dark"] .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6EE7B7;
            border-color: #10B981;
        }
        
        :root[data-theme="dark"] .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #FCA5A5;
            border-color: #EF4444;
        }
        
        :root[data-theme="dark"] .h1,
        :root[data-theme="dark"] h1 {
            color: #F3F4F6;
        }
        
        :root[data-theme="dark"] .meta {
            color: #9CA3AF;
        }
        
        /* Iconos en dark mode */
        :root[data-theme="dark"] svg {
            color: inherit;
        }
</style>

<script>
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

<?php layout('footer'); ?>
