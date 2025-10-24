<?php
$pageTitle = 'Publicar Vehículo';
layout('header');
layout('nav');

// Verificar que el usuario esté logueado
if (!isLoggedIn()) {
    redirect('login');
}
?>

<main class="container">

<?php layout('icons'); ?>

  <div class="h1">Publicar vehículo</div>
  
  <form method="POST" action="<?php echo BASE_URL; ?>/publicar/procesar" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    
    <div class="card">
      <div class="h3">Paso 1: Tipificación</div>
      <div class="kit">
        <label class="tag">
          <input type="radio" name="tipificacion" value="chocado" required> Chocado / Siniestrado
        </label>
        <label class="tag">
          <input type="radio" name="tipificacion" value="mecanico"> Problemas mecánicos
        </label>
      </div>
    </div>

    <div class="card" id="step2">
      <div class="h3">Paso 2: Tipo de venta</div>
      <div class="kit">
        <label class="tag">
          <input type="radio" name="tipo_venta" value="completo" required> Vehículo completo (con precio)
        </label>
        <label class="tag">
          <input type="radio" name="tipo_venta" value="desarme"> En desarme (precio: "A convenir")
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 3: Datos del vehículo</div>
      <div class="form two">
        <label>Marca
          <input type="text" name="marca" placeholder="Marca" required>
        </label>
        
        <label>Modelo
          <input type="text" name="modelo" placeholder="Modelo" required>
        </label>
        
        <label>Año
          <input type="text" name="anio" placeholder="2018" required>
        </label>
        
        <label>Categoría padre
          <select name="categoria" required>
            <option value="">Seleccionar...</option>
            <option>Auto</option>
            <option>Moto</option>
            <option>Camión</option>
            <option>Bus</option>
            <option>Casa Rodante</option>
            <option>Náutica</option>
            <option>Maquinaria</option>
            <option>Aéreos</option>
          </select>
        </label>
        
        <label>Subcategoría
          <input type="text" name="subcategoria" placeholder="SUV / Sedán / ...">
        </label>
        
        <label>Región
          <select name="region" required>
            <option value="">Seleccionar...</option>
            <option>Región Metropolitana</option>
            <option>Valparaíso</option>
            <option>Biobío</option>
            <option>Los Lagos</option>
          </select>
        </label>
        
        <label>Comuna
          <input type="text" name="comuna" placeholder="Comuna" required>
        </label>
        
        <label style="grid-column:1/-1">Descripción
          <textarea name="descripcion" rows="4" placeholder="Daños principales, estado actual..." required></textarea>
        </label>
        
        <label id="precio-field">Precio
          <input type="text" name="precio" placeholder="$ / o 'A convenir con el vendedor'">
        </label>
      </div>
    </div>

    <div class="card">
      <div class="h3">Paso 4: Fotos (1 a 6) · Selecciona la <strong>foto principal</strong></div>
      <div class="gallery">
        <?php for($i = 1; $i <= 6; $i++): ?>
        <div class="slot">
          <input type="file" name="fotos[]" accept="image/*">
          <input type="radio" name="foto_principal" value="<?php echo $i; ?>" <?php echo $i === 1 ? 'checked' : ''; ?>>
        </div>
        <?php endfor; ?>
      </div>
      <p class="meta">La primera foto será la principal por defecto</p>
    </div>

    <div class="card">
      <div class="h3">Paso 5: Promoción</div>
      <div class="form two">
        <label class="tag">
          <input type="radio" name="promocion" value="normal" checked> Normal (gratis)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada15"> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_15_DIAS); ?> · 15 días)
        </label>
        <label class="tag">
          <input type="radio" name="promocion" value="destacada30"> Destacada (<?php echo formatPrice(PRECIO_DESTACADO_30_DIAS); ?> · 30 días)
        </label>
      </div>
    </div>

    <div class="sticky-actions">
      <button type="button" class="btn" onclick="guardarBorrador()">Guardar borrador</button>
      <button type="submit" class="btn primary">Enviar a revisión</button>
    </div>
  </form>

</main>

<script>
// Ocultar/mostrar campo precio según tipo de venta
document.querySelectorAll('input[name="tipo_venta"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const precioField = document.getElementById('precio-field');
    if (this.value === 'desarme') {
      precioField.style.display = 'none';
    } else {
      precioField.style.display = 'block';
    }
  });
});

function guardarBorrador() {
  alert('Funcionalidad de guardar borrador próximamente');
}
</script>

<?php layout('footer'); ?>
