<?php
// APP_URL está en env.php y ya lo tiene así:
// define('APP_URL', '/Comercializadora/ferreteria-pos/public');
$base = APP_URL;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- RUTAS CORRECTAS PARA SUBCARPETA -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/tailwind.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/app.css">

  <title>Login - Comercializadora Sosa</title>
</head>

<body class="auth-page">

  <div class="auth-card">

    <div class="auth-logo-wrap">
      <img
        src="<?= $base ?>/assets/img/logo_sosa.png"
        alt="Comercializadora Sosa"
        class="auth-logo" />
    </div>

    <h1 class="auth-title">POS Ferretería</h1>
    <p class="auth-subtitle">Acceso para Administrador y Vendedor</p>

    <?php if (!empty($error)): ?>
      <div class="auth-error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="<?= $base ?>/login" method="POST">

      <div class="auth-field">
        <label>Correo</label>
        <input name="email" type="email" required class="auth-input" placeholder="email@email.com" />
      </div>

      <div class="auth-field" style="margin-top:12px;">
        <label>Contraseña</label>
        <input name="password" type="password" required class="auth-input" placeholder="********"/>
      </div>

      <button type="submit" class="auth-btn">Entrar</button>

      <div class="auth-footer">
        Comercializadora Sosa
        <div class="auth-badge">Sistema Interno</div>
      </div>

    </form>
  </div>

</body>

</html>
