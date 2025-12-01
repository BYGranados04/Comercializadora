<?php
$errors      = $errors      ?? [];
$old         = $old         ?? [];
$productos   = $productos   ?? [];
$proveedores = $proveedores ?? [];
?>

<div class="card compras-form-card">
  <div class="card-header flex justify-between items-center">
    <h1 class="card-title">Registrar compra</h1>
    <a href="/admin/compras" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card-body">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="/admin/compras/guardar">
      <div class="grid-2">
        <div class="form-group">
          <label>Proveedor *</label>
          <select name="proveedor_id" class="input" required>
            <option value="">-- Seleccione proveedor --</option>
            <?php foreach ($proveedores as $prov): ?>
              <?php
              $selected = ((string)($old['proveedor_id'] ?? '') === (string)$prov['id'])
                ? 'selected'
                : '';
              ?>
              <option value="<?= (int)$prov['id'] ?>" <?= $selected ?>>
                <?= htmlspecialchars($prov['nombre']) ?> (<?= htmlspecialchars($prov['nit']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Fecha compra *</label>
          <input
            type="date"
            name="fecha_compra"
            class="input"
            value="<?= htmlspecialchars($old['fecha_compra'] ?? date('Y-m-d')) ?>"
            required>
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label>Número de documento</label>
          <input
            type="text"
            name="numero_doc"
            class="input"
            placeholder="Factura, recibo, etc."
            value="<?= htmlspecialchars($old['numero_doc'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Notas</label>
          <input
            type="text"
            name="notas"
            class="input"
            value="<?= htmlspecialchars($old['notas'] ?? '') ?>">
        </div>
      </div>

      <hr>

      <h2 class="section-title">Detalle de compra</h2>
      <p class="text-muted">Por ahora se permiten 3 renglones fijos. Luego lo hacemos dinámico.</p>

      <table class="table table-sm">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Costo unitario</th>
            <th>Descuento</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; $i < 3; $i++): ?>
            <tr>
              <td>
                <!-- ID real que se envía al backend -->
                <input type="hidden" name="producto_id[]" class="producto-id-hidden">

                <!-- Buscador de producto -->
                <div class="producto-search-wrapper">
                  <input
                    type="text"
                    class="input input-sm producto-search-input"
                    placeholder="Buscar producto por nombre o SKU"
                    autocomplete="off"
                    data-index="<?= $i ?>">
                  <div class="producto-search-results" data-index="<?= $i ?>"></div>
                </div>
              </td>

              <td>
                <input
                  type="number"
                  step="0.01"
                  name="cantidad[]"
                  class="input input-sm">
              </td>
              <td>
                <input
                  type="number"
                  step="0.0001"
                  name="costo_unitario[]"
                  class="input input-sm">
              </td>
              <td>
                <input
                  type="number"
                  step="0.01"
                  name="descuento[]"
                  class="input input-sm"
                  value="0">
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>

      <div class="form-actions mt-4 flex justify-end gap-2">
        <a href="/admin/compras" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar compra</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Catálogo de productos desde PHP hacia JS
  const PRODUCTOS_CATALOGO = <?=
                              json_encode(array_map(function ($p) {
                                return [
                                  'id'     => (int)$p['id'],
                                  'sku'    => $p['sku'] ?? '',
                                  'nombre' => $p['nombre'] ?? '',
                                ];
                              }, $productos), JSON_UNESCAPED_UNICODE);
                              ?>;

  function filtrarProductos(term) {
    term = term.toLowerCase().trim();
    if (!term) return [];
    return PRODUCTOS_CATALOGO
      .filter(p => (p.nombre + ' ' + p.sku).toLowerCase().includes(term))
      .slice(0, 10);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const inputsBusqueda = document.querySelectorAll('.producto-search-input');

    inputsBusqueda.forEach(input => {
      const index = input.dataset.index;
      const resultsBox = document.querySelector(
        '.producto-search-results[data-index="' + index + '"]'
      );
      const hiddenId = input.closest('td').querySelector('.producto-id-hidden');

      input.addEventListener('input', () => {
        const term = input.value;
        const results = filtrarProductos(term);

        if (!term || results.length === 0) {
          resultsBox.style.display = 'none';
          resultsBox.innerHTML = '';
          hiddenId.value = '';
          return;
        }

        resultsBox.innerHTML = '';
        results.forEach(p => {
          const div = document.createElement('div');
          div.className = 'producto-search-item';
          div.innerHTML = `
            <span class="nombre">${p.nombre}</span>
            <span class="sku">SKU: ${p.sku || 'N/A'} · ID: ${p.id}</span>
          `;
          div.addEventListener('click', () => {
            input.value = p.nombre + (p.sku ? ' (SKU: ' + p.sku + ')' : '');
            hiddenId.value = p.id;
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
          });
          resultsBox.appendChild(div);
        });

        resultsBox.style.display = 'block';
      });

      input.addEventListener('blur', () => {
        setTimeout(() => {
          resultsBox.style.display = 'none';
        }, 200);
      });

      input.addEventListener('focus', () => {
        if (input.value.trim()) {
          input.dispatchEvent(new Event('input'));
        }
      });
    });
  });
</script>
