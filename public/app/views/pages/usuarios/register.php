<?php
$pageTitle = 'Crear Cuenta';
layout('header');
layout('nav');
?>

<main class="container">

  <div class="h1">Crear cuenta</div>
  <div class="card">
    <form method="POST" action="<?php echo BASE_URL; ?>/registro" class="form two">
      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
      
      <label>Nombre
        <input type="text" name="nombre" placeholder="Nombre" required>
      </label>
      
      <label>Apellido
        <input type="text" name="apellido" placeholder="Apellido" required>
      </label>
      
      <label>Email
        <input type="email" name="email" placeholder="tu@correo.com" required>
      </label>
      
      <label>Teléfono
        <input type="text" name="telefono" placeholder="+56 9 ...">
      </label>
      
      <label>Contraseña
        <input type="password" name="password" placeholder="••••••••" required minlength="6">
      </label>
      
      <label>Confirmar contraseña
        <input type="password" name="password_confirm" placeholder="••••••••" required minlength="6">
      </label>
      
      <div class="row">
        <span class="tag">reCAPTCHA (placeholder)</span>
      </div>
      
      <div class="row" style="justify-content:flex-end">
        <button type="submit" class="btn primary">Registrarme</button>
      </div>
      
      <div class="breadcrumbs">
        <a href="<?php echo BASE_URL; ?>">← Volver al inicio</a>
      </div>
    </form>
  </div>

</main>

<?php layout('footer'); ?>
