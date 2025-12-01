<!-- Sidebar para Vendedor - Simplificado -->
<div class="sidebar-header p-4 border-b border-surface-tertiary">
    <h2 class="text-lg font-bold text-primary">POS Ventas</h2>
    <p class="text-sm text-text-secondary">Ferretería</p>
</div>

<nav class="sidebar-nav p-4">
    <ul class="space-y-2">
        <!-- POS Principal -->
        <li>
            <a href="/pos" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary bg-primary text-white">
                <span class="icon mr-3">💰</span>
                <span>Punto de Venta</span>
            </a>
        </li>

        <!-- Productos (solo consulta) -->
        <li>
            <a href="/productos" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">📦</span>
                <span>Consultar Productos</span>
            </a>
        </li>

        <!-- Ventas del día -->
        <li>
            <a href="/ventas/hoy" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">🧾</span>
                <span>Ventas del Día</span>
            </a>
        </li>

        <!-- Clientes -->
        <li>
            <a href="/clientes" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">👥</span>
                <span>Clientes</span>
            </a>
        </li>

        <!-- Cotizaciones -->
        <li>
            <a href="/cotizaciones" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">📄</span>
                <span>Cotizaciones</span>
            </a>
        </li>

        <!-- Separador -->
        <li class="pt-4">
            <hr class="border-surface-tertiary">
        </li>

        <!-- Mi turno -->
        <li>
            <div class="p-3 bg-surface-secondary rounded">
                <p class="text-sm font-medium text-text-primary">Mi Turno</p>
                <p class="text-xs text-text-secondary">Iniciado: 08:00 AM</p>
                <p class="text-xs text-success">Estado: Activo</p>
            </div>
        </li>

        <!-- Atajos rápidos -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Atajos</p>
                <ul class="space-y-1">
                    <li>
                        <button class="w-full text-left p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">🔍</span>
                            <span>Buscar Producto (F2)</span>
                        </button>
                    </li>
                    <li>
                        <button class="w-full text-left p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">💳</span>
                            <span>Procesar Pago (F9)</span>
                        </button>
                    </li>
                    <li>
                        <button class="w-full text-left p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">🖨️</span>
                            <span>Reimprimir (F10)</span>
                        </button>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

<!-- Usuario actual -->
<div class="sidebar-footer p-4 border-t border-surface-tertiary mt-auto">
    <div class="flex items-center">
        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
            V
        </div>
        <div>
            <p class="text-sm font-medium">Vendedor</p>
            <p class="text-xs text-text-secondary">Caja #1</p>
        </div>
    </div>
    <a href="/auth/logout" class="btn btn-secondary w-full mt-3 text-sm">
        Cerrar Sesión
    </a>
</div>
