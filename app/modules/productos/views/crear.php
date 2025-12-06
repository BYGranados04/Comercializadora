<?php
$errors     = $errors ?? [];
$old        = $old ?? [];
$categorias = $categorias ?? [];
$marcas     = $marcas ?? [];

// Mapear ID → nombre para rellenar el texto si viene "old"
$mapCat = [];
foreach ($categorias as $c) {
    $mapCat[(int)$c['id']] = $c['nombre'];
}
$mapMar = [];
foreach ($marcas as $m) {
    $mapMar[(int)$m['id']] = $m['nombre'];
}

$oldCatId   = (int)($old['categoria_id'] ?? 0);
$oldMarcaId = (int)($old['marca_id'] ?? 0);

$oldCatText   = $oldCatId && isset($mapCat[$oldCatId])   ? $mapCat[$oldCatId]   : '';
$oldMarcaText = $oldMarcaId && isset($mapMar[$oldMarcaId]) ? $mapMar[$oldMarcaId] : '';
?>

<div class="card productos-form-card" style="max-width: 980px; margin: 18px auto;">

  <div class="card-header flex justify-between items-center">
    <h1 class="card-title">Crear producto</h1>
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
      Registre el producto maestro para compras, ventas e inventario.
    </p>

    <form method="POST" action="/admin/productos/guardar">

      <!-- Fila 1: SKU / Código de barras -->
      <div class="grid-2">
        <div class="form-group">
          <label>SKU *</label>
          <input
            type="text"
            name="sku"
            class="input"
            required
            placeholder="Ej: MART-16OZ"
            value="<?= htmlspecialchars($old['sku'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Código de barras / QR</label>
          <input
            type="text"
            name="codigo_barra"
            class="input"
            placeholder="Escanee aquí si ya tiene código"
            value="<?= htmlspecialchars($old['codigo_barra'] ?? '') ?>">
        </div>
      </div>

      <!-- Fila 2: Nombre (full) -->
      <div class="grid-2">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Nombre *</label>
          <input
            type="text"
            name="nombre"
            class="input"
            required
            placeholder="Ej: Martillo 16oz Mango Fibra"
            value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
        </div>
      </div>

      <!-- Fila 3: CATEGORÍA / MARCA con buscador tipo proveedor -->
      <div class="grid-2">

        <!-- CATEGORÍA -->
        <div class="form-group">
          <label>Categoría *</label>

          <div class="lookup-wrapper">
            <!-- ID real que se envía al backend -->
            <input type="hidden" name="categoria_id" id="categoria_id"
                   value="<?= $oldCatId ?>">

            <!-- Input visible para buscar / mostrar nombre -->
            <input
              type="text"
              id="categoria_buscar"
              class="input lookup-input"
              placeholder="Escriba para buscar categoría..."
              autocomplete="off"
              value="<?= htmlspecialchars($oldCatText) ?>"
            >

            <!-- Lista de resultados -->
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
                   value="<?= $oldMarcaId ?>">

            <input
              type="text"
              id="marca_buscar"
              class="input lookup-input"
              placeholder="Escriba para buscar marca..."
              autocomplete="off"
              value="<?= htmlspecialchars($oldMarcaText) ?>"
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
            value="<?= htmlspecialchars($old['costo'] ?? '0') ?>">
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
            value="<?= htmlspecialchars($old['precio'] ?? '0') ?>">
        </div>
      </div>

      <!-- Fila 5: stock inicial / stock mínimo -->
      <div class="grid-2">
        <div class="form-group">
          <label>Stock inicial</label>
          <input
            type="number"
            name="stock"
            class="input"
            min="0"
            value="<?= htmlspecialchars($old['stock'] ?? '0') ?>">
        </div>

        <div class="form-group">
          <label>Stock mínimo</label>
          <input
            type="number"
            name="stock_minimo"
            class="input"
            min="0"
            value="<?= htmlspecialchars($old['stock_minimo'] ?? '0') ?>">
        </div>
      </div>

      <!-- Checkbox requiere serie -->
      <div class="form-group" style="margin-top: 8px; flex-direction: row; align-items: center; gap: 8px;">
        <input
          id="requiere_serie"
          type="checkbox"
          name="requiere_serie"
          <?= !empty($old['requiere_serie']) ? 'checked' : '' ?>>
        <label for="requiere_serie" style="margin:0; font-weight: 500;">
          Requiere número de serie (equipos de alto valor / garantía)
        </label>
      </div>

      <!-- Acciones -->
      <div class="form-actions mt-4 flex justify-end gap-2">
        <a href="/admin/productos" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar producto</button>
      </div>

    </form>
  </div>
</div>

<script>
// Lookup genérico (mismo UX que proveedor)
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
    hidden.value = ''; // si cambia texto, invalidamos selección
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
  initLookup('marca_buscar', 'marca_id', 'marca_results');
});
</script>
