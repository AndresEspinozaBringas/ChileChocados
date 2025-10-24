<?php
$pageTitle = 'Panel de Administración';
layout('header');
layout('nav');

// Verificar que el usuario sea administrador
if (!isAdmin()) {
    redirect('admin/login');
}
?>

<main class="container">

  <div class="h1">Panel de administración</div>
  
  <div class="row" style="gap:8px;margin:8px 0">
    <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/nueva-publicacion">+ Crear publicación</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/categorias">+ Gestionar categorías</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/mensajes">Centro de mensajería</a>
  </div>
  
  <div class="kpis">
    <div class="kpi">
      <div class="h3">Publicaciones activas</div>
      <div class="h1">128</div>
    </div>
    <div class="kpi">
      <div class="h3">Pendientes por aprobar</div>
      <div class="h1">12</div>
    </div>
    <div class="kpi">
      <div class="h3">Banners activos</div>
      <div class="h1">6</div>
    </div>
    <div class="kpi">
      <div class="h3">Usuarios</div>
      <div class="h1">940</div>
    </div>
  </div>

  <div class="card" style="margin-top:16px">
    <div class="h3">Moderación de publicaciones</div>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#123</td>
          <td><a href="<?php echo BASE_URL; ?>/detalle/123">SUV 2016</a></td>
          <td>Pendiente</td>
          <td>
            <a class="btn" href="#">Aprobar</a>
            <a class="btn" href="#">Rechazar</a>
            <a class="btn primary" href="#">Destacar (<?php echo formatPrice(PRECIO_DESTACADO_15_DIAS); ?>)</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/mensajes">Contactar</a>
          </td>
        </tr>
        <tr>
          <td>#124</td>
          <td><a href="<?php echo BASE_URL; ?>/detalle/124">Moto Enduro</a></td>
          <td>Pendiente</td>
          <td>
            <a class="btn" href="#">Aprobar</a>
            <a class="btn" href="#">Rechazar</a>
            <a class="btn primary" href="#">Destacar (<?php echo formatPrice(PRECIO_DESTACADO_15_DIAS); ?>)</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/mensajes">Contactar</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="card" style="margin-top:16px">
    <div class="h3">Gestión de banners</div>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Posición</th>
          <th>Cliente</th>
          <th>Vence</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#B01</td>
          <td>728x90</td>
          <td>Taller XX</td>
          <td>15 días</td>
          <td><a class="btn" href="#">Caducar</a></td>
        </tr>
        <tr>
          <td>#B02</td>
          <td>300x250</td>
          <td>Seguros YY</td>
          <td>30 días</td>
          <td><a class="btn" href="#">Caducar</a></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="card" style="margin-top:16px">
    <div class="h3">Estadísticas de Destacados (Flow)</div>
    <div class="grid cols-4" style="margin-top:8px">
      <div class="kpi">
        <div class="h3">Últimos 7 días</div>
        <div class="h1">42</div>
        <div class="meta"><?php echo formatPrice(210000); ?> CLP</div>
      </div>
      <div class="kpi">
        <div class="h3">Últimos 30 días</div>
        <div class="h1">163</div>
        <div class="meta"><?php echo formatPrice(815000); ?> CLP</div>
      </div>
      <div class="kpi">
        <div class="h3">Acumulado Año</div>
        <div class="h1">1.920</div>
        <div class="meta"><?php echo formatPrice(9600000); ?> CLP</div>
      </div>
      <div class="kpi">
        <div class="h3">Tasa de aprobación</div>
        <div class="h1">96%</div>
        <div class="meta">Flow aprobado / total</div>
      </div>
    </div>
    
    <div class="h3" style="margin-top:12px">Órdenes recientes (Flow)</div>
    <table class="table">
      <thead>
        <tr>
          <th>ID Orden</th>
          <th>Publicación</th>
          <th>Monto</th>
          <th>Estado</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>FL-98721</td>
          <td><a href="<?php echo BASE_URL; ?>/detalle/123">SUV 2016</a></td>
          <td><?php echo formatPrice(5000); ?></td>
          <td>Aprobado</td>
          <td>hoy</td>
        </tr>
        <tr>
          <td>FL-98718</td>
          <td><a href="<?php echo BASE_URL; ?>/detalle/124">Moto Enduro</a></td>
          <td><?php echo formatPrice(5000); ?></td>
          <td>Aprobado</td>
          <td>hoy</td>
        </tr>
      </tbody>
    </table>
  </div>

</main>

<?php layout('footer'); ?>
