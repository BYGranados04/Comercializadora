<div class="card compras-card">
  <!-- HEADER -->
  <div class="card-header compras-header">
    <div>
      <h1 class="card-title">Compras</h1>
      <p class="card-subtitle">Historial de compras a proveedores</p>
    </div>
    <a href="/admin/compras/crear" class="btn btn-primary">
      + Nueva compra
    </a>
  </div>

  <!-- FILTROS (por ahora visuales, luego los conectamos) -->
  <div class="compras-filters">
    <input
      type="text"
      class="input"
      placeholder="Buscar por número, proveedor o fecha (visual)"
    >
    <select class="input">
      <option value="">Todas las compras</option>
      <option value="PENDIENTE">Pendientes</option>
      <option value="CERRADA">Cerradas</option>
      <option value="ANULADA">Anuladas</option>
    </select>
  </div>

  <!-- TABLA -->
  <div class="card-body">
    <table class="table compras-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Número doc</th>
          <th>Total</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($compras)): ?>
          <?php foreach ($compras as $c): ?>
            <?php
              // Tratamos de adivinar las columnas según existan en tu tabla
              $fechaRaw = $c['fecha_compra'] ?? ($c['fecha'] ?? ($c['created_at'] ?? ''));
              $numero   = $c['numero_doc']   ?? ($c['documento'] ?? '');
              $total    = $c['total_neto']   ?? ($c['total'] ?? 0);
              $estado   = strtoupper(trim($c['estado'] ?? ($c['estatus'] ?? '')));

              // Formato de fecha simple (si viene con timestamp)
              $fecha = $fechaRaw;
              if ($fechaRaw && strlen($fechaRaw) >= 10) {
                  $fecha = substr($fechaRaw, 0, 10);
              }

              // Clase del badge según estado
              $badgeClass = 'badge-status';
              if ($estado === 'PENDIENTE') {
                  $badgeClass .= ' badge-status--pendiente';
              } elseif ($estado === 'CERRADA') {
                  $badgeClass .= ' badge-status--cerrada';
              } elseif ($estado === 'ANULADA') {
                  $badgeClass .= ' badge-status--anulada';
              }
            ?>
            <tr>
              <td><?= htmlspecialchars($c['id']) ?></td>
              <td><?= htmlspecialchars($fecha) ?></td>
              <td><?= htmlspecialchars($numero) ?></td>
              <td class="compras-total">Q <?= number_format((float)$total, 2) ?></td>
              <td>
                <span class="<?= $badgeClass ?>">
                  <?= htmlspecialchars($estado ?: 'SIN ESTADO') ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">No hay compras registradas.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
