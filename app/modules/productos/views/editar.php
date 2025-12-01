<?php
$errors   = $errors ?? [];
$producto = $producto ?? [];
?>

<div class="productos-wrap">
  <div class="productos-header">
    <div>
      <h2 class="productos-title">Editar Producto</h2>
      <p class="productos-subtitle">Actualice los datos del producto maestro.</p>
    </div>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-err">
      <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form class="productos-form"
        method="POST"
        action="/admin/productos/actualizar/<?= (int)$producto['id'] ?>">

    <div>
      <label>SKU *</label>
      <input type="text" name="sku" required
             value="<?= htmlspecialchars($producto['sku'] ?? '') ?>">
    </div>

    <div>
      <label>Código de barras / QR</label>
      <input type="text" name="codigo_barra"
             value="<?= htmlspecialchars($producto['codigo_barra'] ?? '') ?>">
    </div>

    <div class="full">
      <label>Nombre *</label>
      <input type="text" name="nombre" required
             value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
    </div>

    <div>
      <label>Categoría ID *</label>
      <input type="number" name="categoria_id" required min="1"
             value="<?= htmlspecialchars($producto['categoria_id'] ?? '1') ?>">
    </div>

    <div>
      <label>Marca ID *</label>
      <input type="number" name="marca_id" required min="1"
             value="<?= htmlspecialchars($producto['marca_id'] ?? '1') ?>">
    </div>

    <div>
      <label>Costo *</label>
      <input type="number" name="costo" required min="0" step="0.01"
             value="<?= htmlspecialchars($producto['costo_actual'] ?? '0') ?>">
    </div>

    <div>
      <label>Precio de venta *</label>
      <input type="number" name="precio" required min="0" step="0.01"
             value="<?= htmlspecialchars($producto['precio_venta'] ?? '0') ?>">
    </div>

    <div>
      <label>Stock</label>
      <input type="number" name="stock" min="0"
             value="<?= htmlspecialchars($producto['stock'] ?? '0') ?>">
    </div>

    <div>
      <label>Stock mínimo</label>
      <input type="number" name="stock_minimo" min="0"
             value="<?= htmlspecialchars($producto['stock_minimo'] ?? '0') ?>">
    </div>

    <div class="productos-form-actions">
      <a class="btn-ghost" href="/admin/productos">Cancelar</a>
      <button class="btn-primary" type="submit">Guardar cambios</button>
    </div>

  </form>
</div>
