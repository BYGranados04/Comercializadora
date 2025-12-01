<?php
// Recibe: $title, $user, $content (ruta de vista interna)
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? "Admin") ?></title>
  <link rel="stylesheet" href="/assets/css/tailwind.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>

<body class="admin-root">

  <!-- SIDEBAR FIJA -->
  <?php require __DIR__ . "/_admin_sidebar.php"; ?>

  <!-- CONTENIDO -->
  <main class="admin-main">
    <!-- Topbar simple -->
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <span class="admin-topbar-title"><?= htmlspecialchars($title ?? "Panel Admin") ?></span>
      </div>
      <div class="admin-topbar-right">
        <span class="admin-user"><?= htmlspecialchars($user["nombre"]) ?></span>
        <a class="admin-logout" href="/logout">Salir</a>
      </div>
    </header>

    <section class="admin-content">
  <?php
    // Base real a /app/modules
    $modulesBase = dirname(__DIR__, 2); // sube desde dashboard/views -> app/modules
    $viewPath = $modulesBase . "/" . $content . ".php";

    if (!file_exists($viewPath)) {
        die("Vista no encontrada: " . $viewPath);
    }

    require $viewPath;
  ?>
</section>
  </main>

</body>

</html>
