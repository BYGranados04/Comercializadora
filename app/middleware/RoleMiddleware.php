<?php
class RoleMiddleware {
  public static function requireAdmin() {
    AuthMiddleware::requireLogin();
    if ($_SESSION["user"]["rol"] !== "ADMIN") {
      header("Location: /pos");
      exit;
    }
  }

  public static function requireVendedor() {
    AuthMiddleware::requireLogin();
    if ($_SESSION["user"]["rol"] !== "VENDEDOR") {
      header("Location: /admin/dashboard");
      exit;
    }
  }
}
