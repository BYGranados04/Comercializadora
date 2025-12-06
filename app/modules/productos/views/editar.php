<?php
$errors     = $errors ?? [];
$producto   = $producto ?? [];
$categorias = $categorias ?? [];
$marcas     = $marcas ?? [];

// Mapear ID → nombre
$mapCat = [];
foreach ($categorias as $c) {
    $mapCat[(int)$c['id']] = $c['nombre'];
}
$mapMar = [];
foreach ($marcas as $m) {
    $mapMar[(int)$m['id']] = $m['nombre'];
}

$catId   = (int)($producto['categoria_id'] ?? 0);
$marcaId = (int)($producto['marca_id'] ?? 0);

$catText   = $catId && isset($mapCat[$catId])   ? $mapCat[$catId]   : '';
$marcaText = $marcaId && isset($mapMar[$marcaId]) ? $mapMar[$marcaId] : '';
?>

<div class="card productos-form-card" style="max-width: 980px; margin: 18px auto;">

  <div class="card-header flex justify-between items-center">
    <h1 class="card-title">Editar producto</h1>
    <a href="/admin/productos" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card-body">
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul style="margin:0; padding-left:18px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <p class="text-muted" style="margin-bottom: 14px;">
      Actualice la información del producto maestro.
    </p>

    <form method="POST" action="/admin/productos/actualizar/<?= (int)$producto['id'] ?>">

      <!-- Fila 1: SKU / Código de barras -->
      <div class="grid-2">
        <div class="form-group">
          <label>SKU *</label>
          <input
            type="text"
            name="sku"
            class="input"
            required
            value="<?= htmlspecialchars($producto['sku'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Código de barras / QR</label>
          <input
            type="text"
            name="codigo_barra"
            class="input"
            value="<?= htmlspecialchars($producto['codigo_barra'] ?? '') ?>">
        </div>
      </div>

      <!-- Fila 2: Nombre -->
      <div class="grid-2">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Nombre *</label>
          <input
            type="text"
            name="nombre"
            class="input"
            required
            value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
        </div>
      </div>

      <!-- Fila 3: Categoría / Marca con lookup -->
      <div class="grid-2">

        <!-- CATEGORÍA -->
        <div class="form-group">
          <label>Categoría *</label>

          <div class="lookup-wrapper">
            <input type="hidden" name="categoria_id" id="categoria_id"
                   value="<?= $catId ?>">

            <input
              type="text"
              id="categoria_buscar"
              class="input lookup-input"
              placeholder="Escriba para buscar categoría..."
              autocomplete="off"
              value="<?= htmlspecialchars($catText) ?>"
            >

            <div class="lookup-results" id="categoria_results">
              <?php foreach ($categorias as $c): ?>
                <?php
                  $id   = (int)$c['id'];
                  $text = htmlspecialchars($c['nombre']);
                ?>
                <div
                  class="lookup-item"
                  data-id="<?= $id ?>"
                  data-label="<?= $text ?>"
                >
                  <?= $text ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- MARCA -->
        <div class="form-group">
          <label>Marca *</label>

          <div class="lookup-wrapper">
            <input type="hidden" name="marca_id" id="marca_id"
                   value="<?= $marcaId ?>">

            <input
              type="text"
              id="marca_buscar"
              class="input lookup-input"
              placeholder="Escriba para buscar marca..."
              autocomplete="off"
              value="<?= htmlspecialchars($marcaText) ?>"
            >

            <div class="lookup-results" id="marca_results">
              <?php foreach ($marcas as $m): ?>
                <?php
                  $id   = (int)$m['id'];
                  $text = htmlspecialchars($m['nombre']);
                ?>
                <div
                  class="lookup-item"
                  data-id="<?= $id ?>"
                  data-label="<?= $text ?>"
                >
                  <?= $text ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

      <!-- Fila 4: costos y precio -->
      <div class="grid-2">
        <div class="form-group">
          <label>Costo *</label>
          <input
            type="number"
            name="costo"
            class="input"
            required
            min="0"
            step="0.01"
            value="<?= htmlspecialchars($producto['costo_actual'] ?? '0') ?>">
        </div>

        <div class="form-group">
          <label>Precio de venta *</label>
          <input
            type="number"
            name="precio"
            class="input"
            required
            min="0"
            step="0.01"
            value="<?= htmlspecialchars($producto['precio_venta'] ?? '0') ?>">
        </div>
      </div>

      <!-- Fila 5: stock inicial / stock mínimo -->
      <div class="grid-2">
        <div class="form-group">
          <label>Stock</label>
          <input
            type="number"
            name="stock"
            class="input"
            min="0"
            value="<?= htmlspecialchars($producto['stock'] ?? '0') ?>">
        </div>

        <div class="form-group">
          <label>Stock mínimo</label>
          <input
            type="number"
            name="stock_minimo"
            class="input"
            min="0"
            value="<?= htmlspecialchars($producto['stock_minimo'] ?? '0') ?>">
        </div>
      </div>

      <!-- Acciones -->
      <div class="form-actions mt-4 flex justify-end gap-2">
        <a href="/admin/productos" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Actualizar producto</button>
      </div>

    </form>
  </div>
</div>

<script>
function initLookup(inputId, hiddenId, resultsId) {
  const input   = document.getElementById(inputId);
  const hidden  = document.getElementById(hiddenId);
  const results = document.getElementById(resultsId);

  if (!input || !hidden || !results) return;

  const items = Array.from(results.querySelectorAll('.lookup-item'));

  function filtrar() {
    const term = input.value.trim().toLowerCase();
    let visible = 0;

    items.forEach(item => {
      const label = item.dataset.label.toLowerCase();
      if (!term || label.includes(term)) {
        item.style.display = 'block';
        visible++;
      } else {
        item.style.display = 'none';
      }
    });

    results.style.display = visible > 0 ? 'block' : 'none';
  }

  input.addEventListener('focus', () => {
    filtrar();
  });

  input.addEventListener('input', () => {
    hidden.value = '';
    filtrar();
  });

  items.forEach(item => {
    item.addEventListener('click', () => {
      const id    = item.dataset.id;
      const label = item.dataset.label;

      hidden.value = id;
      input.value  = label;
      results.style.display = 'none';
    });
  });

  document.addEventListener('click', (e) => {
    if (!results.contains(e.target) && e.target !== input) {
      results.style.display = 'none';
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  initLookup('categoria_buscar', 'categoria_id', 'categoria_results');
  initLookup('marca_buscar',     'marca_id',     'marca_results');
});
</script>
