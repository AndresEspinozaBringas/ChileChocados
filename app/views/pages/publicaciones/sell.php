<?php
use App\Helpers\Auth;

$pageTitle = 'Vender mi Siniestrado';

require_once APP_PATH . '/views/layouts/header.php';
?>

<main class="container">
  <div class="hero">
    <div class="h1">¿Quieres vender tu siniestrado?</div>
    <p class="meta">Publica en minutos y comparte en tus redes con branding ChileChocados.</p>
    <div class="row" style="gap:8px">
      <a class="btn primary" href="<?= BASE_URL ?>/publicar">Comenzar publicación</a>
      <?php if (Auth::check()): ?>
        <a class="btn" href="<?= BASE_URL ?>/perfil">Ir a mi perfil</a>
      <?php else: ?>
        <a class="btn" href="<?= BASE_URL ?>/login">Iniciar sesión</a>
      <?php endif; ?>
    </div>
  </div>

  <section class="grid cols-3">
    <div class="card">
      <div class="h3">1) Define la tipificación</div>
      <p class="meta">Elige entre <strong>Siniestrado</strong> o <strong>En desarme</strong>. Si es en desarme, el precio será <em>A convenir</em>.</p>
    </div>
    <div class="card">
      <div class="h3">2) Sube tus fotos (1 a 6)</div>
      <p class="meta">Foto principal clara del frente/lateral. Puedes editar o agregar más luego.</p>
    </div>
    <div class="card">
      <div class="h3">3) Ubicación y contacto</div>
      <p class="meta">Selecciona <strong>Región</strong> y <strong>Comuna</strong> para mejorar los resultados de búsqueda.</p>
    </div>
  </section>

  <section style="margin-top:12px">
    <div class="grid cols-3">
      <div class="card">
        <div class="h3">Beneficios</div>
        <ul class="meta">
          <li>Publicación gratuita</li>
          <li>Opción de <strong>Destacado ($5.000)</strong> con pago Flow</li>
          <li>Post corporativo automático para redes</li>
        </ul>
      </div>
      <div class="card">
        <div class="h3">Requisitos</div>
        <ul class="meta">
          <li>1 a 6 fotos</li>
          <li>Descripción breve del estado</li>
          <li>Región y comuna</li>
        </ul>
      </div>
      <div class="card">
        <div class="h3">Ayuda</div>
        <p class="meta">¿Dudas? Escríbenos desde la mensajería o correo soporte.</p>
      </div>
    </div>
  </section>
</main>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
