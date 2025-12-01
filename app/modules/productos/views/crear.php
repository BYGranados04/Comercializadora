<?php
$errors     = $errors     ?? [];
$old        = $old        ?? [];
$categorias = $categorias ?? []; // opcional: lista de categorías desde el controlador
$marcas     = $marcas     ?? []; // opcional: lista de marcas desde el controlador
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

      <!-- Fila 3: categoría / marca -->
      <div class="grid-2">

        <!-- Categoría -->
        <div class="form-group">
          <label>Categoría *</label>

          <?php if (!empty($categorias)): ?>
            <!-- Si el controlador ya envía categorías, mostrar select por nombre -->
            <select name="categoria_id" class="input" required>
              <option value="">-- Seleccione categoría --</option>
              <?php foreach ($categorias as $cat): ?>
                <?php
                $sel = (string)($old['categoria_id'] ?? '') === (string)$cat['id']
                  ? 'selected'
                  : '';
                ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $sel ?>>
                  <?= htmlspecialchars($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <!-- Fallback actual por ID mientras no exista el catálogo -->
            <input
              type="number"
              name="categoria_id"
              class="input"
              required
              min="1"
              value="<?= htmlspecialchars($old['categoria_id'] ?? '1') ?>">
          <?php endif; ?>
        </div>

        <!-- Marca -->
        <div class="form-group">
          <label>Marca *</label>

          <?php if (!empty($marcas)): ?>
            <!-- Si el controlador ya envía marcas, mostrar select por nombre -->
            <select name="marca_id" class="input" required>
              <option value="">-- Seleccione marca --</option>
              <?php foreach ($marcas as $m): ?>
                <?php
                $sel = (string)($old['marca_id'] ?? '') === (string)$m['id']
                  ? 'selected'
                  : '';
                ?>
                <option value="<?= (int)$m['id'] ?>" <?= $sel ?>>
                  <?= htmlspecialchars($m['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <!-- Fallback por ID -->
            <input
              type="number"
              name="marca_id"
              class="input"
              required
              min="1"
              value="<?= htmlspecialchars($old['marca_id'] ?? '1') ?>">
          <?php endif; ?>
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
      <div class="form-group"
        style="margin-top: 8px; flex-direction: row; align-items: center; gap: 8px;">
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
