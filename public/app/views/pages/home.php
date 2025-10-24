<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="hero">
    <div class="h1">Marketplace de bienes siniestrados</div>
    <p class="meta">Encuentra vehículos y bienes siniestrados con la mejor relación calidad-precio.</p>
    <div class="badge">
        <span class="iconify"><svg><use href="<?php echo url('assets/icons/icons.svg#pin'); ?>"/></svg></span>
        Busca por <strong>categoría</strong> y <strong>región</strong> para resultados más precisos.
    </div>
</div>

<div class="banner-below"><div class="ph"></div></div>

<!-- Publicaciones destacadas -->
<section>
    <div class="h2">Publicaciones destacadas</div>
    <div class="grid cols-4">
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <a class="card" href="<?php echo url('detalle/' . $i); ?>">
            <div class="ph"></div>
            <div class="h3">Título publicación <?php echo $i; ?></div>
            <div class="row" style="justify-content:space-between">
                <span class="badge">En desarme</span>
                <span class="meta">A convenir</span>
            </div>
        </a>
        <?php endfor; ?>
    </div>
</section>

<!-- Explora publicaciones -->
<section>
    <div class="section-title">
        <span class="iconify"><svg><use href="<?php echo url('assets/icons/icons.svg#category'); ?>"/></svg></span>
        <div class="h2">Explora publicaciones</div>
    </div>
    <div class="grid cols-4">
        <?php for ($i = 1; $i <= 8; $i++): ?>
        <a class="card" href="<?php echo url('detalle/' . $i); ?>">
            <div class="ph"></div>
            <div class="h3">Publicación aleatoria <?php echo $i; ?></div>
            <div class="row price-row" style="justify-content:space-between">
                <span class="badge"><?php echo $i % 2 ? 'Siniestrado' : 'En desarme'; ?></span>
                <span class="<?php echo $i % 2 ? 'h3' : 'meta'; ?>" style="font-weight:<?php echo $i % 2 ? '800' : 'normal'; ?>">
                    <?php echo $i % 2 ? formatPrice(rand(3000000, 10000000)) : 'A convenir'; ?>
                </span>
            </div>
        </a>
        <?php endfor; ?>
    </div>
</section>

<!-- Categorías padre -->
<section>
    <div class="h2">Categorías padre</div>
    <div class="grid cols-4">
        <?php foreach ($categorias as $cat): ?>
        <a class="card cat-card" href="<?php echo url('listado?categoria=' . $cat['id']); ?>">
            <div class="left">
                <span class="iconify"><svg><use href="<?php echo url('assets/icons/icons.svg#' . $cat['icon']); ?>"/></svg></span>
                <div>
                    <div class="h3"><?php echo sanitize($cat['nombre']); ?></div>
                    <p class="meta"><?php echo sanitize($cat['subcategorias']); ?></p>
                </div>
            </div>
            <span class="cat-count"><?php echo $cat['count']; ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA Vender -->
<section>
    <div class="sell-cta" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <div class="h2">Quiero vender mi siniestrado</div>
            <p class="meta">Sube tus fotos, define si es siniestrado o en desarme y publícalo hoy.</p>
        </div>
        <div class="row" style="gap:8px">
            <a class="btn primary" href="<?php echo url('vender'); ?>">Vender mi siniestrado</a>
            <a class="btn" href="<?php echo url('publicar'); ?>">Publicar ahora</a>
        </div>
    </div>
</section>

<!-- Espacios de banner -->
<section>
    <div class="h2">Espacios de banner</div>
    <div class="grid cols-3">
        <a class="card" href="<?php echo url('admin'); ?>">
            <div class="ph"></div>
            <div class="meta">Banner 728x90</div>
        </a>
        <a class="card" href="<?php echo url('admin'); ?>">
            <div class="ph"></div>
            <div class="meta">Banner 300x250</div>
        </a>
        <a class="card" href="<?php echo url('admin'); ?>">
            <div class="ph"></div>
            <div class="meta">Banner 160x600</div>
        </a>
    </div>
</section>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
