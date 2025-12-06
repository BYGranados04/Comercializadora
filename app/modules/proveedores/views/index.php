<?php
$proveedores = $proveedores ?? [];
?>

<div class="card" style="margin-top: 18px;">
  <div class="card-header flex justify-between items-center">
    <div>
      <h1 class="card-title">Proveedores</h1>
      <p class="text-muted">Catálogo de proveedores para compras.</p>
    </div>
    <a href="/admin/proveedores/crear" class="btn btn-primary">
      + Nuevo proveedor
    </a>
  </div>

  <div class="card-body">
    <?php if (isset($_GET['ok']) && $_GET['ok'] === 'creado'): ?>
      <div class="alert alert-success">
        Proveedor creado correctamente.
      </div>
    <?php endif; ?>

    <table class="table compras-table proveedores-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>NIT</th>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($proveedores)): ?>
        <tr>
          <td colspan="7">No hay proveedores registrados.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($proveedores as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= htmlspecialchars($p['nit']) ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['telefono']) ?></td>
            <td><?= htmlspecialchars($p['correo']) ?></td>
            <td>
              <?php if (!empty($p['activo'])): ?>
                <span class="badge-status-proveedor badge-status-proveedor--activo">
                  ACTIVO
                </span>
              <?php else: ?>
                <span class="badge-status-proveedor badge-status-proveedor--inactivo">
                  INACTIVO
                </span>
              <?php endif; ?>
            </td>
            <td>
              <form method="POST"
                    action="/admin/proveedores/cambiar-estado/<?= (int)$p['id'] ?>"
                    style="display:inline;">
                <?php if (!empty($p['activo'])): ?>
                  <button type="submit" class="btn-estado">
                    Desactivar
                  </button>
                <?php else: ?>
                  <button type="submit" class="btn-estado btn-estado--activar">
                    Activar
                  </button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
