<?php
$errors     = $errors ?? [];
$old        = $old ?? [];
$productos  = $productos ?? [];
$proveedores = $proveedores ?? [];

// Para JS: lista de productos (solo lo necesario)
$productosJs = [];
foreach ($productos as $p) {
    $productosJs[] = [
        'id'    => (int)$p['id'],
        'nombre'=> $p['nombre'],
        'sku'   => $p['sku'] ?? '',
        // costo sugerido para la compra (puede ser costo_actual o precio_venta, usted manda)
        'costo' => isset($p['costo_actual']) ? (float)$p['costo_actual'] : (float)($p['precio_venta'] ?? 0),
    ];
}
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

    <form method="POST" action="/admin/compras/guardar" id="form-compra">
      <!-- Encabezado -->
      <div class="grid-2">
        <div class="form-group">
          <label>Proveedor *</label>

          <div class="lookup-wrapper">
            <input type="hidden" name="proveedor_id" id="proveedor_id"
                   value="<?= htmlspecialchars($old['proveedor_id'] ?? '') ?>">

            <input
              type="text"
              id="proveedor_buscar"
              class="input lookup-input"
              placeholder="Escriba para buscar proveedor..."
              autocomplete="off"
              value="<?= htmlspecialchars($old['proveedor_nombre'] ?? '') ?>"
            >

            <div class="lookup-results" id="proveedor_results">
              <?php foreach ($proveedores as $prov): ?>
                <?php
                  $pid   = (int)$prov['id'];
                  $label = htmlspecialchars($prov['nombre']);
                  $nit   = htmlspecialchars($prov['nit']);
                ?>
                <div
                  class="lookup-item"
                  data-id="<?= $pid ?>"
                  data-label="<?= $label ?> (NIT: <?= $nit ?>)"
                >
                  <?= $label ?> (NIT: <?= $nit ?>)
                </div>
              <?php endforeach; ?>
            </div>
          </div>
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
      <p class="text-muted">
        Por ahora se permiten 3 renglones fijos. Luego lo hacemos dinámico.
      </p>

      <table class="table table-sm" id="tabla-detalle">
        <thead>
          <tr>
            <th style="width: 38%;">Producto</th>
            <th style="width: 14%;">Cantidad</th>
            <th style="width: 18%;">Costo unitario</th>
            <th style="width: 15%;">Descuento</th>
            <th style="width: 15%;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < 3; $i++): ?>
          <tr>
            <td>
              <div class="lookup-wrapper">
                <input type="hidden" name="producto_id[]" class="producto-id">

                <input
                  type="text"
                  class="input input-sm lookup-input producto-buscar"
                  placeholder="Escriba nombre o SKU..."
                  autocomplete="off"
                >

                <div class="lookup-results producto-results">
                  <!-- JS llena esto -->
                </div>
              </div>
            </td>
            <td>
              <input
                type="number"
                step="0.01"
                name="cantidad[]"
                class="input input-sm campo-cantidad"
                min="0">
            </td>
            <td>
              <input
                type="number"
                step="0.0001"
                name="costo_unitario[]"
                class="input input-sm campo-costo"
                min="0">
            </td>
            <td>
              <input
                type="number"
                step="0.01"
                name="descuento[]"
                class="input input-sm campo-descuento"
                value="0"
                min="0">
            </td>
            <td>
              <input
                type="text"
                class="input input-sm campo-subtotal"
                readonly
                value="0.00">
            </td>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>

      <!-- Totales -->
      <div class="grid-2" style="margin-top: 10px;">
        <div></div>
        <div>
          <div class="form-group">
            <label>Total bruto</label>
            <input type="text" class="input" id="total_bruto_view" readonly value="0.00">
          </div>
          <div class="form-group">
            <label>Descuento total</label>
            <input type="text" class="input" id="total_desc_view" readonly value="0.00">
          </div>
          <div class="form-group">
            <label>Total neto</label>
            <input type="text" class="input" id="total_neto_view" readonly value="0.00">
          </div>
        </div>
      </div>

      <!-- Estos se llenan con JS antes del submit -->
      <input type="hidden" name="total_bruto" id="total_bruto">
      <input type="hidden" name="total_descuento" id="total_descuento">
      <input type="hidden" name="total_neto" id="total_neto">

      <div class="form-actions mt-4 flex justify-end gap-2">
        <a href="/admin/compras" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar compra</button>
      </div>
    </form>
  </div>
</div>

<script>
// ---------- LOOKUP PROVEEDOR (ya lo tiene genérico, lo reciclamos) ----------
function initLookup(input, hidden, results) {
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

  input.addEventListener('focus', filtrar);
  input.addEventListener('input', () => {
    hidden.value = '';
    filtrar();
  });

  items.forEach(item => {
    item.addEventListener('click', () => {
      hidden.value = item.dataset.id;
      input.value  = item.dataset.label;
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
  const inputProv   = document.getElementById('proveedor_buscar');
  const hiddenProv  = document.getElementById('proveedor_id');
  const resultsProv = document.getElementById('proveedor_results');
  initLookup(inputProv, hiddenProv, resultsProv);

  // ---------- LOOKUP PRODUCTO POR FILA ----------
  const productos = <?= json_encode($productosJs, JSON_UNESCAPED_UNICODE) ?>;

  function initLookupProducto(row) {
    const input   = row.querySelector('.producto-buscar');
    const hidden  = row.querySelector('.producto-id');
    const results = row.querySelector('.producto-results');

    if (!input || !hidden || !results) return;

    function renderLista(term) {
      const t = term.trim().toLowerCase();
      results.innerHTML = '';

      const filtrados = productos.filter(p => {
        const txt = (p.nombre + ' ' + p.sku).toLowerCase();
        return !t || txt.includes(t);
      }).slice(0, 20); // límite de resultados

      if (!filtrados.length) {
        results.style.display = 'none';
        return;
      }

      filtrados.forEach(p => {
        const div = document.createElement('div');
        div.className = 'lookup-item';
        div.dataset.id    = p.id;
        div.dataset.label = p.nombre + (p.sku ? ' (SKU: ' + p.sku + ')' : '');
        div.dataset.costo = p.costo;
        div.textContent   = div.dataset.label;
        results.appendChild(div);

        div.addEventListener('click', () => {
          hidden.value      = p.id;
          input.value       = div.dataset.label;

          const costoInput  = row.querySelector('.campo-costo');
          if (costoInput && !costoInput.value) {
            costoInput.value = p.costo;
          }

          results.style.display = 'none';
          recalcularFila(row);
          recalcularTotales();
        });
      });

      results.style.display = 'block';
    }

    input.addEventListener('focus', () => renderLista(input.value));
    input.addEventListener('input', () => {
      hidden.value = '';
      renderLista(input.value);
    });

    document.addEventListener('click', (e) => {
      if (!results.contains(e.target) && e.target !== input) {
        results.style.display = 'none';
      }
    });
  }

  // ---------- CÁLCULOS ----------
  function recalcularFila(row) {
    const cantInput  = row.querySelector('.campo-cantidad');
    const costoInput = row.querySelector('.campo-costo');
    const descInput  = row.querySelector('.campo-descuento');
    const subInput   = row.querySelector('.campo-subtotal');

    const cant  = parseFloat(cantInput?.value || 0);
    const costo = parseFloat(costoInput?.value || 0);
    const desc  = parseFloat(descInput?.value || 0);

    let subtotal = cant * costo - desc;
    if (subtotal < 0) subtotal = 0;

    if (subInput) {
      subInput.value = subtotal.toFixed(2);
    }
  }

  function recalcularTotales() {
    let totalBruto = 0;
    let totalDesc  = 0;

    document.querySelectorAll('#tabla-detalle tbody tr').forEach(row => {
      const cantInput  = row.querySelector('.campo-cantidad');
      const costoInput = row.querySelector('.campo-costo');
      const descInput  = row.querySelector('.campo-descuento');

      const cant  = parseFloat(cantInput?.value || 0);
      const costo = parseFloat(costoInput?.value || 0);
      const desc  = parseFloat(descInput?.value || 0);

      if (cant > 0 && costo >= 0) {
        totalBruto += cant * costo;
        totalDesc  += desc > 0 ? desc : 0;
      }
    });

    const totalNeto = totalBruto - totalDesc;

    document.getElementById('total_bruto_view').value = totalBruto.toFixed(2);
    document.getElementById('total_desc_view').value  = totalDesc.toFixed(2);
    document.getElementById('total_neto_view').value  = totalNeto.toFixed(2);

    document.getElementById('total_bruto').value      = totalBruto.toFixed(2);
    document.getElementById('total_descuento').value  = totalDesc.toFixed(2);
    document.getElementById('total_neto').value       = totalNeto.toFixed(2);
  }

  // Inicializar filas
  document.querySelectorAll('#tabla-detalle tbody tr').forEach(row => {
    initLookupProducto(row);

    ['.campo-cantidad', '.campo-costo', '.campo-descuento'].forEach(sel => {
      const input = row.querySelector(sel);
      if (input) {
        input.addEventListener('input', () => {
          recalcularFila(row);
          recalcularTotales();
        });
      }
    });
  });

  // Antes de enviar, aseguramos totales frescos
  const form = document.getElementById('form-compra');
  form.addEventListener('submit', () => {
    recalcularTotales();
  });
});
</script>
