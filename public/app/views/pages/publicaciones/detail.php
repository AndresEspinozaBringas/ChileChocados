<?php
$pageTitle = 'Detalle de Publicación';
layout('header');
layout('nav');
?>

<main class="container">

<?php layout('icons'); ?>

  <div class="breadcrumbs">
    <a href="<?php echo BASE_URL; ?>/listado">← Volver al listado</a>
  </div>
  
  <div class="grid cols-3">
    <div class="card" style="grid-column:1/3">
      <div class="ph" style="height:340px"></div>
      <div class="gallery" style="grid-template-columns:repeat(6,1fr)">
        <?php for($i = 1; $i <= 6; $i++): ?>
        <div class="slot"><?php echo $i; ?></div>
        <?php endfor; ?>
      </div>
    </div>
    
    <aside class="card">
      <div class="h2">Título del vehículo</div>
      <div class="meta">
        <span class="iconify"><svg><use href="#category"/></svg></span> 
        Categoría · Modelo · 
        <span class="iconify"><svg><use href="#pin"/></svg></span> 
        Ubicación
      </div>
      <div class="row" style="align-items:center;gap:10px;margin-top:12px">
        <span class="badge">Siniestrado</span>
        <div class="h3" style="margin:0"><?php echo formatPrice(3500000); ?></div>
      </div>
      <p class="meta">Si fuera <strong>En desarme</strong> aquí diría: <em>A convenir</em>.</p>
      
      <div class="row" style="gap:8px">
        <a class="btn primary" href="<?php echo BASE_URL; ?>/mensajes">Contactar vendedor</a>
        <button id="fav-toggle" data-pid="123" class="fav-btn">
          <span class="heart"></span> Favorito
        </button>
        <a class="btn" href="#" data-share-modal>Compartir</a>
      </div>
    </aside>
  </div>

  <div class="grid cols-3" style="margin-top:16px">
    <div class="card" style="grid-column:1/3">
      <div class="h3">Descripción</div>
      <p class="meta">Daños principales, estado actual, observaciones...</p>
    </div>
    <div class="card">
      <div class="h3">Vendedor</div>
      <p class="meta">Nombre · teléfono · redes</p>
    </div>
  </div>

  <!-- Modal de compartir -->
  <div id="share-backdrop" class="modal-backdrop">
    <div class="modal">
      <div class="row" style="justify-content:space-between;align-items:center">
        <div class="h3" style="margin:0">Compartir publicación</div>
        <a href="#" data-close class="btn">Cerrar</a>
      </div>
      <div class="preview" style="margin-top:10px"></div>
      <p class="meta">Se generará un post con branding ChileChocados y la foto principal de tu publicación.</p>
      <div class="modal-actions">
        <a class="btn" href="#" onclick="event.preventDefault()">Facebook</a>
        <a class="btn" href="#" onclick="event.preventDefault()">Instagram</a>
        <a class="btn" href="#" onclick="event.preventDefault()">WhatsApp</a>
        <a class="btn" href="#" onclick="event.preventDefault()">X (Twitter)</a>
        <a id="copy-link" class="btn primary" href="#" onclick="event.preventDefault()">Copiar link</a>
      </div>
    </div>
  </div>

</main>

<?php layout('footer'); ?>
