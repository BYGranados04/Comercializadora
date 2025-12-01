<!-- Sidebar para Administrador -->
<div class="sidebar-header p-4 border-b border-surface-tertiary">
    <h2 class="text-lg font-bold text-primary">Admin Panel</h2>
    <p class="text-sm text-text-secondary">Sistema POS/ERP</p>
</div>

<nav class="sidebar-nav p-4">
    <ul class="space-y-2">
        <!-- Dashboard -->
        <li>
            <a href="/dashboard" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">📊</span>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Ventas -->
        <li>
            <a href="/pos" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">💰</span>
                <span>Punto de Venta</span>
            </a>
        </li>

        <li>
            <a href="/ventas" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">🧾</span>
                <span>Historial Ventas</span>
            </a>
        </li>

        <!-- Productos e Inventario -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Inventario</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/productos" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📦</span>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/inventario" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📋</span>
                            <span>Control Stock</span>
                        </a>
                    </li>
                    <li>
                        <a href="/inventario/kardex" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📈</span>
                            <span>Kardex</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Compras -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Compras</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/compras" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">🛒</span>
                            <span>Órdenes de Compra</span>
                        </a>
                    </li>
                    <li>
                        <a href="/proveedores" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">🏢</span>
                            <span>Proveedores</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Clientes y Cotizaciones -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Clientes</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/clientes" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">👥</span>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cotizaciones" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📄</span>
                            <span>Cotizaciones</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- FEL -->
        <li>
            <a href="/fel" class="sidebar-link flex items-center p-3 rounded hover:bg-surface-secondary">
                <span class="icon mr-3">🧾</span>
                <span>FEL</span>
            </a>
        </li>

        <!-- Reportes -->
        <li>
            <div class="sidebar-section">
                <p class="text-sm font-semibold text-text-secondary mb-2 uppercase tracking-wider">Reportes</p>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="/reportes/ventas" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📊</span>
                            <span>Ventas</span>
                        </a>
                    </li>
                    <li>
                        <a href="/reportes/inventario" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">📦</span>
                            <span>Inventario</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cierrecaja" class="sidebar-link flex items-center p-2 rounded hover:bg-surface-secondary text-sm">
                            <span class="icon mr-2">💼</span>
                            <span>Cierre de Caja</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

<!-- Usuario actual -->
<div class="sidebar-footer p-4 border-t border-surface-tertiary mt-auto">
    <div class="flex items-center">
        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
            A
        </div>
        <div>
            <p class="text-sm font-medium">Administrador</p>
            <p class="text-xs text-text-secondary">admin@ferreteria.com</p>
        </div>
    </div>
    <a href="/auth/logout" class="btn btn-secondary w-full mt-3 text-sm">
        Cerrar Sesión
    </a>
</div>
