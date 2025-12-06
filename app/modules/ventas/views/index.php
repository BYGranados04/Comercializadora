<?php
$ventas = $ventas ?? [];
?>

<div class="card" style="margin-top: 18px;">
  <div class="card-header flex justify-between items-center">
    <div>
      <h1 class="card-title">Ventas</h1>
      <p class="text-muted">Listado de ventas recientes.</p>
    </div>
    <a href="/admin/ventas/crear" class="btn btn-primary">
      + Nueva venta
    </a>
  </div>

  <div class="card-body">
    <?php if (isset($_GET["ok"]) && $_GET["ok"] === "creada"): ?>
      <div class="alert alert-success">
        Venta registrada correctamente.
      </div>
    <?php endif; ?>

    <table class="table compras-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Cliente</th>
          <th>Total</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($ventas)): ?>
        <tr>
          <td colspan="5">No hay ventas registradas.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($ventas as $v): ?>
          <tr>
            <td><?= (int)$v["id"] ?></td>
            <td><?= htmlspecialchars($v["fecha_venta"] ?? "") ?></td>
            <td><?= htmlspecialchars($v["cliente"] ?? "") ?></td>
            <td class="compras-total">
              Q <?= number_format((float)($v["total_neto"] ?? 0), 2) ?>
            </td>
            <td><?= htmlspecialchars($v["estado"] ?? "") ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
