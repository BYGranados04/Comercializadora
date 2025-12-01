<div class="bg-white rounded-2xl shadow p-6">
  <h1 class="text-2xl font-bold text-primary">
    Bienvenido, <?= htmlspecialchars($user["nombre"]) ?>
  </h1>
  <p class="text-textMuted mt-2">
    Panel administrativo de Comercializadora Sosa.
  </p>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-surface rounded-xl p-4">
      <div class="text-sm text-textMuted">Ventas hoy</div>
      <div class="text-2xl font-extrabold">Q 0.00</div>
    </div>
    <div class="bg-surface rounded-xl p-4">
      <div class="text-sm text-textMuted">Productos bajos</div>
      <div class="text-2xl font-extrabold">0</div>
    </div>
    <div class="bg-surface rounded-xl p-4">
      <div class="text-sm text-textMuted">Compras del mes</div>
      <div class="text-2xl font-extrabold">Q 0.00</div>
    </div>
  </div>
</div>
