<?php
class AuthMiddleware {
  public static function requireLogin() {
    if (empty($_SESSION["user"])) {
      header("Location: /login");
      exit;
    }
  }
}
