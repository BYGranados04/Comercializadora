<?php
class DashboardController extends Controller
{
  public function index()
  {
    RoleMiddleware::requireAdmin();

    $this->view("modules/dashboard/views/_admin_layout", [
      "title" => "Dashboard Ejecutivo",
      "user" => $_SESSION["user"],
      "content" => "dashboard/views/dashboard" // apunta a dashboard.php interno
    ]);
  }
}
