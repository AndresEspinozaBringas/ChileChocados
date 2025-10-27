<?php
// La verificación de admin se hace en el controlador
layout('header');
layout('nav');
?>

<main class="container">

  <div class="h1">Panel de administración</div>
  
  <div class="row" style="gap:8px;margin:8px 0">
    <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/nueva-publicacion">+ Crear publicación</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/categorias">+ Gestionar categorías</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/mensajes">Centro de mensajería</a>
  </div>
  
  <div class="grid cols-4" style="gap: 16px; margin: 24px 0;">
    <div class="card" style="text-align: center; padding: 24px;">
      <div class="meta">Publicaciones activas</div>
      <div class="h1" style="margin-top: 8px;">128</div>
      <p class="meta" style="margin-top: 4px; font-size: 12px;">Incluye normales y destacadas</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
      <div class="meta">Pendientes por aprobar</div>
      <div class="h1" style="margin-top: 8px;">12</div>
      <p class="meta" style="margin-top: 4px; font-size: 12px;">Últimas 24h</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
      <div class="meta">Banners activos</div>
      <div class="h1" style="margin-top: 8px;">6</div>
      <p class="meta" style="margin-top: 4px; font-size: 12px;">Vencen en 15-30 días</p>
    </div>
    <div class="card" style="text-align: center; padding: 24px;">
      <div class="meta">Usuarios</div>
      <div class="h1" style="margin-top: 8px;">940</div>
      <p class="meta" style="margin-top: 4px; font-size: 12px;">Nuevos esta semana: 37</p>
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
            <a class="btn primary" href="#">Destacar ($5.000)</a>
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
            <a class="btn primary" href="#">Destacar ($5.000)</a>
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
    <div class="grid cols-4" style="gap: 16px; margin-top: 16px;">
      <div class="card" style="text-align: center; padding: 20px;">
        <div class="meta">Destacadas activas</div>
        <div class="h1" style="margin-top: 8px;">78</div>
        <p class="meta" style="margin-top: 4px; font-size: 12px;">Vigentes hoy</p>
      </div>
      <div class="card" style="text-align: center; padding: 20px;">
        <div class="meta">Nuevas (7 días)</div>
        <div class="h1" style="margin-top: 8px;">42</div>
        <p class="meta" style="margin-top: 4px; font-size: 12px;">Ingresos: $210.000</p>
      </div>
      <div class="card" style="text-align: center; padding: 20px;">
        <div class="meta">Nuevas (30 días)</div>
        <div class="h1" style="margin-top: 8px;">163</div>
        <p class="meta" style="margin-top: 4px; font-size: 12px;">Ingresos: $815.000</p>
      </div>
      <div class="card" style="text-align: center; padding: 20px;">
        <div class="meta">Tasa de aprobación</div>
        <div class="h1" style="margin-top: 8px;">96%</div>
        <p class="meta" style="margin-top: 4px; font-size: 12px;">Flow aprobado / total</p>
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
