<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $pageTitle ?? 'ChileChocados – Marketplace de bienes siniestrados'; ?></title>
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <link rel="icon" type="image/png" href="<?php echo url('assets/images/icon.png'); ?>">
    <?php if (isset($includeJQueryUI) && $includeJQueryUI): ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <?php endif; ?>
</head>
<body>
<header>
    <div class="topbar container">
        <div class="row" style="gap:10px">
            <button id="burger-toggle" class="icon-btn mobile-only" aria-label="Abrir menú">
                <span class="icon hamburger"><span></span></span>
            </button>
            <div class="logo">
                <a href="<?php echo url(); ?>">
                    <img src="<?php echo url('assets/images/logo.jpeg'); ?>" alt="ChileChocados" style="height:36px;vertical-align:middle">
                </a>
            </div>
        </div>
        
        <!-- Navegación Desktop -->
        <nav class="desktop-only">
            <a href="<?php echo url(); ?>">Inicio</a>
            <a href="<?php echo url('categorias'); ?>">Categorías</a>
            <a href="<?php echo url('listado'); ?>">Listado</a>
            <a href="<?php echo url('favoritos'); ?>">Favoritos</a>
            <a href="<?php echo url('publicar'); ?>">Publicar</a>
            <a href="<?php echo url('vender'); ?>" class="btn primary">Vender mi siniestrado</a>
            <a href="<?php echo url('mensajes'); ?>">Mensajes</a>
            
            <?php if (isAuthenticated()): ?>
                <a href="<?php echo url('perfil'); ?>" class="btn ghost">
                    <?php echo sanitize(currentUser()['nombre']); ?>
                </a>
                <a href="<?php echo url('logout'); ?>" class="btn">Salir</a>
            <?php else: ?>
                <a href="<?php echo url('login'); ?>" class="btn ghost">Login</a>
                <a href="<?php echo url('registro'); ?>" class="btn primary">Registro</a>
            <?php endif; ?>
        </nav>
        
        <div class="row">
            <button id="theme-toggle" class="btn">Oscuro</button>
        </div>
    </div>
    
    <!-- Navegación Mobile -->
    <nav class="mobile-nav container" aria-hidden="true" style="display:none">
        <a href="<?php echo url(); ?>">Inicio</a>
        <a href="<?php echo url('categorias'); ?>">Categorías</a>
        <a href="<?php echo url('listado'); ?>">Listado</a>
        <a href="<?php echo url('favoritos'); ?>">Favoritos</a>
        <a href="<?php echo url('publicar'); ?>">Publicar</a>
        <a href="<?php echo url('vender'); ?>" class="btn primary">Vender mi siniestrado</a>
        <a href="<?php echo url('mensajes'); ?>">Mensajes</a>
        
        <?php if (isAuthenticated()): ?>
            <a href="<?php echo url('perfil'); ?>"><?php echo sanitize(currentUser()['nombre']); ?></a>
            <a href="<?php echo url('logout'); ?>">Salir</a>
        <?php else: ?>
            <a href="<?php echo url('login'); ?>">Login</a>
            <a href="<?php echo url('registro'); ?>">Registro</a>
        <?php endif; ?>
    </nav>
</header>

<?php
// Mostrar mensaje flash si existe
$flash = getFlash();
if ($flash):
?>
<div class="container">
    <div class="notice <?php echo $flash['type']; ?>" style="margin-top:16px">
        <?php echo sanitize($flash['message']); ?>
    </div>
</div>
<?php endif; ?>

<main class="container">
