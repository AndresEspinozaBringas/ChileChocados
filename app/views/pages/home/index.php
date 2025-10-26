<?php
$pageTitle = 'Inicio';
layout('header');
layout('nav');
?>

<main class="container">
  
<?php layout('icons'); ?>

  <div class="hero">
    <div class="h1">Marketplace de bienes siniestrados</div>
    <p class="meta">Encuentra la oportunidad perfecta después del impacto.</p>
    <div class="badge">
      <span class="iconify">
        <svg><use href="#pin"/></svg>
      </span>
      Busca por <strong>categoría</strong> y <strong>región</strong> para resultados más precisos.
    </div>
  </div>
  
  <div class="banner-below"><div class="ph"></div></div>

  <section>
    <div class="h2">Publicaciones destacadas</div>
    <div class="grid cols-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <a class="card" href="<?php echo BASE_URL; ?>/detalle/<?php echo $i; ?>">
        <div class="ph"></div>
        <div class="h3">Título publicación <?php echo $i; ?></div>
        <div class="row" style="justify-content:space-between">
          <span class="badge"><?php echo $i % 2 == 0 ? 'Siniestrado' : 'En desarme'; ?></span>
          <span class="meta"><?php echo $i % 2 == 0 ? formatPrice(3100000 + ($i * 100000)) : 'A convenir'; ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
  </section>

  <section>
    <div class="section-title">
      <span class="iconify"><svg><use href="#category"/></svg></span>
      <div class="h2">Explora publicaciones</div>
    </div>
    <div class="grid cols-4">
      <?php for ($i = 1; $i <= 8; $i++): ?>
      <a class="card" href="<?php echo BASE_URL; ?>/detalle/<?php echo $i; ?>">
        <div class="ph"></div>
        <div class="h3">Publicación aleatoria <?php echo $i; ?></div>
        <div class="row price-row" style="justify-content:space-between">
          <span class="badge"><?php echo $i % 3 == 0 ? 'En desarme' : 'Siniestrado'; ?></span>
          <span class="<?php echo $i % 3 == 0 ? 'meta' : 'h3'; ?>" style="<?php echo $i % 3 != 0 ? 'font-weight:800' : ''; ?>">
            <?php echo $i % 3 == 0 ? 'A convenir' : formatPrice(3000000 + ($i * 500000)); ?>
          </span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
  </section>

  <section>
    <div class="h2">Categorías padre</div>
    <div class="grid cols-4">
      <?php
      $categorias = [
        ['id' => 'car', 'nombre' => 'Auto', 'sub' => 'Sedán • SUV • Deportivo', 'count' => 230],
        ['id' => 'bike', 'nombre' => 'Moto', 'sub' => 'Scooter • Enduro • Touring', 'count' => 84],
        ['id' => 'truck', 'nombre' => 'Camión', 'sub' => 'Ligero • Pesado', 'count' => 45],
        ['id' => 'rv', 'nombre' => 'Casa Rodante', 'sub' => 'RV • Camper', 'count' => 12],
        ['id' => 'boat', 'nombre' => 'Náutica', 'sub' => 'Lanchas • Yates', 'count' => 9],
        ['id' => 'bus', 'nombre' => 'Bus', 'sub' => 'Urbano • Interurbano', 'count' => 21],
        ['id' => 'gear', 'nombre' => 'Maquinaria', 'sub' => 'Retro • Grúa', 'count' => 33],
        ['id' => 'plane', 'nombre' => 'Aéreos', 'sub' => 'Ligera • Drones', 'count' => 5]
      ];

      foreach ($categorias as $cat):
        ?>
      <a class="card cat-card" href="<?php echo BASE_URL; ?>/listado?cat=<?php echo e($cat['id']); ?>">
        <div class="left">
          <span class="iconify"><svg><use href="#<?php echo $cat['id']; ?>"/></svg></span>
          <div>
            <div class="h3"><?php echo e($cat['nombre']); ?></div>
            <p class="meta"><?php echo e($cat['sub']); ?></p>
          </div>
        </div>
        <span class="cat-count"><?php echo $cat['count']; ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section>
    <div class="sell-cta" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
      <div>
        <div class="h2">Quiero vender mi siniestrado</div>
        <p class="meta">Sube tus fotos, define si es siniestrado o en desarme y publícalo hoy.</p>
      </div>
      <div class="row" style="gap:8px">
        <a class="btn primary" href="<?php echo BASE_URL; ?>/vender">Vender mi siniestrado</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/publicar">Publicar ahora</a>
      </div>
    </div>
  </section>

</main>

<?php layout('footer'); ?>
