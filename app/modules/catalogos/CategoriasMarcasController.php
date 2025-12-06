<?php

require_once __DIR__ . '/../categorias/CategoriasModel.php';
require_once __DIR__ . '/../marcas/MarcasModel.php';

class CategoriasMarcasController extends Controller
{
    private CategoriasModel $categoriasModel;
    private MarcasModel $marcasModel;

    public function __construct()
    {
        $this->categoriasModel = new CategoriasModel();
        $this->marcasModel     = new MarcasModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        $categorias = $this->categoriasModel->listarTodos();
        $marcas     = $this->marcasModel->listarTodos();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"      => "Categorías & marcas",
            "user"       => $_SESSION["user"],
            "content"    => "catalogos/views/index",
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    public function guardarCategoria()
    {
        RoleMiddleware::requireAdmin();

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->categoriasModel->crear($nombre);
        }

        header("Location: /admin/catalogos");
        exit;
    }

    public function guardarMarca()
    {
        RoleMiddleware::requireAdmin();

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->marcasModel->crear($nombre);
        }

        header("Location: /admin/catalogos");
        exit;
    }

    public function cambiarEstadoCategoria($id)
    {
        RoleMiddleware::requireAdmin();

        $id     = (int)$id;
        $activo = (int)($_POST['activo'] ?? 0);

        $this->categoriasModel->cambiarEstado($id, $activo);

        header("Location: /admin/catalogos");
        exit;
    }

    public function cambiarEstadoMarca($id)
    {
        RoleMiddleware::requireAdmin();

        $id     = (int)$id;
        $activo = (int)($_POST['activo'] ?? 0);

        $this->marcasModel->cambiarEstado($id, $activo);

        header("Location: /admin/catalogos");
        exit;
    }
}
