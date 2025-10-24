<?php
$pageTitle = 'Iniciar Sesión';
layout('header');
layout('nav');
?>

<main class="container">

  <div class="h1">Iniciar sesión</div>
  <div class="card">
    <form method="POST" action="<?php echo BASE_URL; ?>/login" class="form">
      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
      
      <label>Email
        <input type="email" name="email" placeholder="tu@correo.com" required>
      </label>
      
      <label>Contraseña
        <input type="password" name="password" placeholder="••••••••" required>
      </label>
      
      <div class="row">
        <span class="tag">reCAPTCHA (placeholder)</span>
      </div>
      
      <div class="row" style="justify-content:flex-end;gap:8px">
        <a class="btn" href="<?php echo BASE_URL; ?>/registro">Crear cuenta</a>
        <button type="submit" class="btn primary">Entrar</button>
      </div>
      
      <div class="breadcrumbs">
        <a href="<?php echo BASE_URL; ?>">← Volver al inicio</a>
      </div>
    </form>
  </div>

</main>

<?php layout('footer'); ?>
