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

<style>
/* ============================================================================
 * ESTILOS GENERALES
 * ============================================================================ */

.hero {
  background: white;
  border: 2px solid #E5E5E5;
  border-radius: 12px;
  padding: 48px 32px;
  text-align: center;
  margin-bottom: 32px;
}

.hero .h1 {
  color: #2E2E2E;
  margin-bottom: 12px;
}

.hero .meta {
  color: #666666;
  margin-bottom: 24px;
}

.hero .row {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] .hero {
  background: #2C3E50 !important;
  border: 2px solid #34495E !important;
  border-radius: 12px;
  padding: 48px 32px;
  text-align: center;
}

:root[data-theme="dark"] .hero .h1 {
  color: white !important;
}

:root[data-theme="dark"] .hero .meta {
  color: rgba(255,255,255,0.9) !important;
}

:root[data-theme="dark"] .card {
  background: #1F2937 !important;
  border-color: #374151 !important;
}

:root[data-theme="dark"] .card .h3 {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .card .meta {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] .card p {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] .card ul {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] .card li {
  color: #D1D5DB !important;
}

:root[data-theme="dark"] .card strong {
  color: #F3F4F6;
}

:root[data-theme="dark"] .card em {
  color: #9CA3AF;
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
  background: #c72a22;
  border-color: #c72a22;
}
</style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
