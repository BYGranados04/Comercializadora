<div class="productos-wrap">

  <!-- Encabezado -->
  <div class="productos-header">
    <div>
      <h2 class="productos-title">Productos</h2>
      <p class="productos-subtitle">Catálogo maestro para compras, ventas y scanner.</p>
    </div>
    <a class="btn-primary" href="/admin/productos/crear">+ Nuevo Producto</a>
  </div>

  <!-- Alertas (si aplica) -->
  <?php if (!empty($ok)): ?>
    <div class="alert alert-ok">✔ <?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

  <?php if (!empty($err)): ?>
    <div class="alert alert-err">⚠️ <?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <!-- Buscador / Scanner -->
  <form class="productos-search" method="GET" action="/admin/productos">
    <input name="q"
           autofocus
           value="<?= htmlspecialchars($q ?? '') ?>"
           placeholder="Escanee o busque por SKU / Código / Nombre">
    <button class="btn-secondary">Buscar</button>
  </form>

  <!-- Tabla -->
  <div class="productos-table-wrap">
    <table class="productos-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>SKU</th>
          <th>Nombre</th>
          <th>Código</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Serie</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php if (empty($productos)): ?>
          <tr>
            <td colspan="8" style="text-align:center; padding:20px;">
              No hay productos registrados.
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($productos as $p): ?>
          <tr>
            <td><?= $p["id"] ?></td>
            <td class="productos-sku"><?= htmlspecialchars($p["sku"]) ?></td>
            <td><?= htmlspecialchars($p["nombre"]) ?></td>
            <td><?= htmlspecialchars($p["codigo_barra"]) ?></td>
            <td class="productos-precio">Q <?= number_format($p["precio_venta"], 2) ?></td>
            <td><?= $p["stock"] ?></td>

            <!-- Serie Badge -->
            <td>
              <?php if (!empty($p["requiere_serie"] ?? null)): ?>
                  Sí
              <?php else: ?>
                  No
              <?php endif; ?>
            </td>

            <td style="display:flex; gap:6px;">
              <a href="/admin/productos/editar/<?= $p['id'] ?>"
                 class="btn-xs btn-edit">Editar</a>

              <form method="POST" action="/admin/productos/eliminar/<?= $p['id'] ?>"
                    onsubmit="return confirm('¿Seguro que desea desactivar este producto?');">
                <button class="btn-xs btn-off">Desactivar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>

      </tbody>
    </table>
  </div>

</div>
