<?php
class AuthController extends Controller
{

  public function loginForm()
  {
    $this->view("modules/auth/views/login");
  }

  public function login()
  {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
      return $this->view("modules/auth/views/login", [
        "error" => "Debe ingresar correo y contraseña."
      ]);
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if (!$user || !password_verify($password, $user["password_hash"])) {
      return $this->view("modules/auth/views/login", [
        "error" => "Credenciales inválidas."
      ]);
    }

    $_SESSION["user"] = [
      "id" => $user["id"],
      "nombre" => $user["nombre"],
      "email" => $user["email"],
      "rol" => $user["rol"]
    ];

    // Redirect según rol
    if ($user["rol"] === "ADMIN") {
      header("Location: /admin/dashboard");
    } else {
      header("Location: /pos");
    }
    exit;
  }

  public function logout()
  {
    session_destroy();
    header("Location: /login");
    exit;
  }
}
