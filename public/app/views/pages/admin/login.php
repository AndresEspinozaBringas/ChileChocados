<?php
$pageTitle = 'Admin - Iniciar Sesión';
layout('header');
?>

<header>
  <div class="topbar container">
    <div class="row" style="gap:10px">
      <div class="logo">
        <a href="<?php echo BASE_URL; ?>">
          <img src="<?php echo asset('images/logo.jpeg'); ?>" alt="<?php echo APP_NAME; ?>" style="height:36px;vertical-align:middle">
        </a>
      </div>
    </div>
    <div class="row">
      <button id="theme-toggle" class="btn">Oscuro</button>
    </div>
  </div>
</header>

<main class="container">

  <div class="h1">Administrador · Iniciar sesión</div>
  <div class="card">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/login/procesar" class="form">
      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
      
      <label>Email
        <input type="email" name="email" placeholder="admin@chilechocados.cl" required>
      </label>
      
      <label>Contraseña
        <input type="password" name="password" placeholder="••••••••" required>
      </label>
      
      <div class="row" style="justify-content:flex-end;gap:8px">
        <a class="btn" href="<?php echo BASE_URL; ?>/admin/recuperar">¿Olvidaste tu contraseña?</a>
        <button type="submit" class="btn primary">Entrar</button>
      </div>
    </form>
  </div>
    
</main>

<?php layout('footer'); ?>
