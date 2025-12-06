<?php

require_once __DIR__ . "/ClientesModel.php";

class ClientesController extends Controller
{
    private ClientesModel $model;

    public function __construct()
    {
        $this->model = new ClientesModel();
    }

    /**
     * Endpoint AJAX para el buscador de clientes en ventas
     * GET /admin/clientes/buscar?q=texto
     */
    public function buscar()
    {
        RoleMiddleware::requireAdmin(); // solo dentro del panel

        $q = $_GET["q"] ?? "";

        $clientes = $this->model->buscar($q);

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($clientes);
    }

    // Más adelante si quiere:
    // index(), crear(), guardar(), etc.
}
