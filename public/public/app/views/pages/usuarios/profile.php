<?php
$pageTitle = 'Mi Perfil';
layout('header');
layout('nav');

// Verificar que el usuario esté logueado
if (!isLoggedIn()) {
    redirect('login');
}

$user = currentUser();
?>

<main class="container">

  <div class="h1">Mi Perfil</div>
  
  <div class="grid cols-3">
    <div class="card">
      <div class="h3">Datos personales</div>
      <form method="POST" action="<?php echo BASE_URL; ?>/perfil/actualizar" class="form">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        
        <label>Nombre
          <input type="text" name="nombre" value="<?php echo e($user['nombre']); ?>" placeholder="John">
        </label>
        
        <label>Teléfono
          <input type="text" name="telefono" placeholder="+56 9 ...">
        </label>
        
        <label>Redes sociales
          <input type="text" name="redes" placeholder="@usuario">
        </label>
        
        <button type="submit" class="btn primary">Guardar cambios</button>
      </form>
    </div>
    
    <div class="card">
      <div class="h3">Publicaciones activas</div>
      <div class="list">
        <div class="row">
          <div class="tag">#123</div>
          <div>SUV 2016</div>
          <a class="btn" href="<?php echo BASE_URL; ?>/detalle/123">Ver</a>
        </div>
        <div class="row">
          <div class="tag">#124</div>
          <div>Moto Enduro</div>
          <a class="btn" href="<?php echo BASE_URL; ?>/detalle/124">Ver</a>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="h3">Publicaciones archivadas</div>
      <div class="list">
        <div class="row">
          <div class="tag">#099</div>
          <div>Sedán 2012 (Vendido)</div>
          <a class="btn" href="<?php echo BASE_URL; ?>/detalle/099">Ver</a>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row" style="justify-content:flex-end;margin-top:12px">
    <a class="btn primary" href="<?php echo BASE_URL; ?>/publicar">Nueva publicación</a>
  </div>

</main>

<?php layout('footer'); ?>
