<?php
$productos = $productos ?? [];
?>

<style>
/* ===============================
   BUSCADOR ESTILO FACEBOOK
================================ */
.search-wrapper {
  position: relative;
  width: 100%;
}

.search-results {
  position: absolute;
  top: 38px;
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  max-height: 240px;
  overflow-y: auto;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
  z-index: 50;
  display: none;
}

.search-results div {
  padding: 8px 10px;
  cursor: pointer;
  transition: background 0.15s;
}

.search-results div:hover {
  background: #f3f4f6;
}

/* Tabla */
.venta-table input {
  width: 100%;
  padding: 6px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
}
</style>

<div class="card" style="margin-top: 18px;">
  <div class="card-header flex justify-between items-center">
    <h1 class="card-title">Registrar Venta</h1>
    <a href="/admin/dashboard" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card-body">

    <form method="POST" action="/admin/ventas/guardar">

      <!-- =========================
           CLIENTE
      ========================== -->
      <div class="form-group">
        <label>Cliente</label>
        <div class="search-wrapper">
          <input
            type="text"
            id="clienteBuscar"
            name="cliente"
            class="input"
            placeholder="Consumidor Final"
          >
          <div id="clienteResultados" class="search-results"></div>
        </div>

        <input type="hidden" name="cliente_id" id="clienteId">

        <p class="text-muted" style="margin-top:4px; font-size:12px;">
          Puede escribir el nombre o buscar por NIT / nombre y seleccionar de la lista.
        </p>
      </div>

      <hr>

      <!-- =========================
           PRODUCTOS
      ========================== -->
      <h2 class="section-title">Productos</h2>

      <table class="table venta-table" id="tablaVenta">
        <thead>
          <tr>
            <th style="width: 38%;">Producto</th>
            <th style="width: 12%;">Cant.</th>
            <th style="width: 20%;">Precio</th>
            <th style="width: 20%;">Subtotal</th>
            <th style="width: 10%;">Acción</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <button type="button" class="btn btn-primary" onclick="agregarFila()">+ Agregar producto</button>

      <hr>

      <div class="flex justify-end">
        <h2>Total: Q <span id="totalGeneral">0.00</span></h2>
      </div>

      <div class="form-actions mt-4 flex justify-end gap-2">
        <button type="submit" class="btn btn-primary">Guardar venta</button>
      </div>

    </form>
  </div>
</div>

<!-- =========================
     SCRIPT COMPLETO
========================== -->
<script>
/* =========================================
   PRODUCTOS DESDE PHP → JS
========================================= */
window.PRODUCTOS = <?= json_encode(array_map(function ($p) {
    return [
      "id"           => (int)$p["id"],
      "sku"          => $p["sku"],
      "nombre"       => $p["nombre"],
      "precio_venta" => (float)$p["precio_venta"],
    ];
}, $productos)); ?>;

/* =========================================
   AGREGAR FILA DE PRODUCTO
========================================= */
function agregarFila() {
  const tbody = document.querySelector("#tablaVenta tbody");
  const fila = document.createElement("tr");

  fila.innerHTML = `
    <td>
      <div class="search-wrapper">
        <input type="text" class="input buscador" placeholder="Escriba para buscar…">
        <div class="search-results"></div>
      </div>

      <input type="hidden" name="producto_id[]">
      <input type="hidden" name="nombre_producto[]">
    </td>

    <td>
      <input type="number" name="cantidad[]" class="cantidad"
             step="0.01" value="1" oninput="recalcularFila(this)">
    </td>

    <td>
      <input type="number" name="precio_unitario[]" class="precio"
             step="0.01" value="0" oninput="recalcularFila(this)">
    </td>

    <td>
      <input type="text" class="subtotal" value="0.00" readonly>
    </td>

    <td>
      <button type="button" class="btn btn-secondary"
        onclick="this.closest('tr').remove(); recalcularTotal();">X</button>
    </td>
  `;

  tbody.appendChild(fila);
  activarBuscadorProducto(fila);
}

/* =========================================
   BUSCADOR DE PRODUCTOS (LOCAL)
========================================= */
function activarBuscadorProducto(fila) {
  const input = fila.querySelector(".buscador");
  const results = fila.querySelector(".search-results");

  input.addEventListener("input", () => {
    const q = input.value.toLowerCase().trim();
    if (!q) {
      results.style.display = "none";
      return;
    }

    results.innerHTML = "";

    const encontrados = window.PRODUCTOS.filter(
      p => p.nombre.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
    );

    if (encontrados.length === 0) {
      results.innerHTML = "<div>Sin resultados</div>";
    } else {
      encontrados.forEach(p => {
        const item = document.createElement("div");
        item.textContent = `${p.nombre} (SKU: ${p.sku})`;

        item.onclick = () => {
          fila.querySelector("input[name='producto_id[]']").value = p.id;
          fila.querySelector("input[name='nombre_producto[]']").value = p.nombre;
          fila.querySelector(".precio").value = p.precio_venta;

          input.value = p.nombre;

          results.style.display = "none";
          recalcularFila(fila.querySelector(".cantidad"));
        };

        results.appendChild(item);
      });
    }

    results.style.display = "block";
  });
}

/* =========================================
   CÁLCULO DE SUBTOTAL Y TOTAL
========================================= */
function recalcularFila(el) {
  const tr = el.closest("tr");
  const cant = parseFloat(tr.querySelector(".cantidad").value || 0);
  const precio = parseFloat(tr.querySelector(".precio").value || 0);

  const sub = cant * precio;
  tr.querySelector(".subtotal").value = sub.toFixed(2);

  recalcularTotal();
}

function recalcularTotal() {
  let total = 0;
  document.querySelectorAll(".subtotal").forEach(s => {
    total += parseFloat(s.value || 0);
  });

  document.getElementById("totalGeneral").textContent = total.toFixed(2);
}

/* =========================================
   BUSCADOR DE CLIENTES (AJAX + TIPO FB)
========================================= */
document.addEventListener("DOMContentLoaded", () => {
  // Arrancamos con una fila de producto
  agregarFila();

  const input   = document.getElementById("clienteBuscar");
  const results = document.getElementById("clienteResultados");
  const hidden  = document.getElementById("clienteId");

  if (!input || !results || !hidden) return;

  let abortController = null;

  input.addEventListener("input", async () => {
    const q = input.value.trim();

    // Siempre que cambia el texto, limpiamos el id
    hidden.value = "";

    if (q.length < 2) {
      results.style.display = "none";
      results.innerHTML = "";
      return;
    }

    // Cancelar petición anterior si sigue viva
    if (abortController) {
      abortController.abort();
    }
    abortController = new AbortController();

    try {
      const res = await fetch(
        `/admin/clientes/buscar?q=${encodeURIComponent(q)}`,
        { signal: abortController.signal }
      );

      if (!res.ok) {
        console.error("Error HTTP buscando clientes:", res.status);
        results.style.display = "none";
        return;
      }

      const data = await res.json();
      results.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        const div = document.createElement("div");
        div.innerHTML = `
          <strong>Sin coincidencias</strong><br>
          <small>Se usará el texto escrito.</small>
        `;
        div.onclick = () => {
          results.style.display = "none";
        };
        results.appendChild(div);
      } else {
        data.forEach(c => {
          const div = document.createElement("div");
          div.innerHTML = `
            <strong>${c.nombre}</strong><br>
            <small>NIT: ${c.nit || 'CF'}${c.direccion ? ' · ' + c.direccion : ''}</small>
          `;

          div.onclick = () => {
            input.value = `${c.nombre}${c.nit ? ' (NIT: ' + c.nit + ')' : ''}`;
            hidden.value = c.id;
            results.style.display = "none";
          };

          results.appendChild(div);
        });
      }

      results.style.display = "block";
    } catch (err) {
      if (err.name === "AbortError") return; // se canceló, no pasa nada
      console.error("Error buscando clientes:", err);
      results.style.display = "none";
    }
  });

  // Cerrar dropdown si hace click fuera
  document.addEventListener("click", (ev) => {
    if (!results.contains(ev.target) && ev.target !== input) {
      results.style.display = "none";
    }
  });
});
</script>
