<?php
$pageTitle = '404 - Página no encontrada';
require_once APP_PATH . '/views/layouts/header.php';
?>

<div class="hero">
    <div class="h1">404 - Página no encontrada</div>
    <p class="meta">Lo sentimos, la página que buscas no existe.</p>
    <div class="row" style="gap:8px;margin-top:16px">
        <a class="btn primary" href="<?php echo url(); ?>">Volver al inicio</a>
        <a class="btn" href="<?php echo url('listado'); ?>">Ver publicaciones</a>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
