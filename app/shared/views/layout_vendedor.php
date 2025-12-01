<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'POS - Sistema Ventas'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/app.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/tailwind.css">
    <meta name="description" content="Sistema POS para Ferretería - Panel de Ventas">
</head>

<body>
    <div class="layout-vendedor">
        <!-- Sidebar simplificado para vendedor -->
        <aside class="sidebar">
            <?php include 'sidebar_vendedor.php'; ?>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Navbar superior -->
            <header class="navbar">
                <?php include 'navbar.php'; ?>
            </header>

            <!-- Contenido de la página -->
            <div class="content-wrapper">
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>

    <!-- Scripts globales -->
    <script src="<?php echo BASE_URL; ?>assets/js/app.js"></script>

    <!-- Scripts específicos de página -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo BASE_URL . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Configuración global para vendedor/POS
        document.addEventListener('DOMContentLoaded', function() {
            // TODO: Inicializar componentes de POS
            // Escáner, atajos de teclado, etc.
            console.log('Layout Vendedor/POS cargado');

            // Inicializar escáner si está disponible
            if (POS && POS.scanner) {
                POS.scanner.startListening();
            }
        });
    </script>
</body>

</html>
