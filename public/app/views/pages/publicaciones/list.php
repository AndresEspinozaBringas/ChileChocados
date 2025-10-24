<?php
$pageTitle = 'Listado de Publicaciones';
$includeJQueryUI = true;
layout('header');
layout('nav');
?>

<main class="container">

<?php layout('icons'); ?>

  <div class="h1">Listado por categoría</div>
  
  <form id="list-search" class="search simplified" action="<?php echo BASE_URL; ?>/listado" method="get">
    <div class="select">
      <span class="iconify"><svg><use href="#category"/></svg></span>
      <select name="cat">
        <option value="">Categoría</option>
        <option <?php echo (isset($_GET['cat']) && $_GET['cat'] == 'auto') ? 'selected' : ''; ?>>Auto</option>
        <option <?php echo (isset($_GET['cat']) && $_GET['cat'] == 'moto') ? 'selected' : ''; ?>>Moto</option>
        <option <?php echo (isset($_GET['cat']) && $_GET['cat'] == 'camion') ? 'selected' : ''; ?>>Camión</option>
        <option>Bus</option>
        <option>Casa Rodante</option>
        <option>Náutica</option>
        <option>Maquinaria</option>
        <option>Aéreos</option>
      </select>
    </div>
    <div>
      <input id="q2" name="q" placeholder="Palabra clave (marca, modelo, año)" value="<?php echo isset($_GET['q']) ? e($_GET['q']) : ''; ?>">
    </div>
    <div class="select">
      <span class="iconify"><svg><use href="#pin"/></svg></span>
      <select name="region">
        <option value="">Región</option>
        <option <?php echo (isset($_GET['region']) && $_GET['region'] == 'Metropolitana') ? 'selected' : ''; ?>>Metropolitana</option>
        <option>Valparaíso</option>
        <option>Biobío</option>
        <option>Los Lagos</option>
        <option>Antofagasta</option>
        <option>Coquimbo</option>
      </select>
    </div>
    <button class="btn primary" type="submit">Buscar</button>
  </form>

  <?php
  $regionText = isset($_GET['region']) && !empty($_GET['region']) ? e($_GET['region']) : 'todas las regiones';
  ?>
  <div id="result-indicator" class="result-indicator">
    Mostrando 12 publicaciones en <span id="region-title"><?php echo $regionText; ?></span>
  </div>
  
  <div id="quick-filters" class="filters">
    <button class="active" data-type="all">Todos</button>
    <button data-type="sin">Siniestrado</button>
    <button data-type="des">En desarme</button>
    <button data-type="precio">Con precio</button>
    <button data-type="conv">A convenir</button>
  </div>

  <div class="grid cols-4" style="margin-top:12px">
    <?php for($i = 1; $i <= 12; $i++): ?>
    <a class="card" href="<?php echo BASE_URL; ?>/detalle/<?php echo $i; ?>">
      <div class="ph"></div>
      <div class="h3">Publicación <?php echo $i; ?></div>
      <div class="row price-row" style="justify-content:space-between">
        <span class="badge"><?php echo $i % 3 == 0 ? 'En desarme' : 'Siniestrado'; ?></span>
        <span class="<?php echo $i % 3 == 0 ? 'meta' : 'h3'; ?>" style="<?php echo $i % 3 != 0 ? 'font-weight:800' : ''; ?>">
          <?php echo $i % 3 == 0 ? 'A convenir' : formatPrice(4000000 + ($i * 100000)); ?>
        </span>
      </div>
    </a>
    <?php endfor; ?>
  </div>
  
  <div class="pagination">
    <a href="#" class="active">1</a>
    <a href="#">2</a>
    <a href="#">3</a>
    <a href="#">Siguiente »</a>
  </div>
</main>

<?php layout('footer'); ?>
