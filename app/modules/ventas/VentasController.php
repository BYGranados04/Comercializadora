<?php

require_once __DIR__ . "/VentasModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";

class VentasController extends Controller
{
    private VentasModel $model;

    public function __construct()
    {
        $this->model = new VentasModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdmin();

        $ventas = $this->model->listar();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"   => "Ventas",
            "user"    => $_SESSION["user"],
            "content" => "ventas/views/index",
            "ventas"  => $ventas,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $productosModel = new ProductosModel();
        $productos      = $productosModel->listarActivos();

        $errors = $_SESSION['ventas_errors'] ?? [];
        $old    = $_SESSION['ventas_old']    ?? [];

        unset($_SESSION['ventas_errors'], $_SESSION['ventas_old']);

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"     => "Registrar venta",
            "user"      => $_SESSION["user"],
            "content"   => "ventas/views/crear",
            "productos" => $productos,
            "errors"    => $errors,
            "old"       => $old,
        ]);
    }

    // =========================
    // GUARDAR
    // =========================
    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $input   = $_POST;
        $cliente = trim($input["cliente"] ?? "Consumidor Final");

        $productos_id = $input["producto_id"]      ?? [];
        $cantidades   = $input["cantidad"]         ?? [];
        $precios      = $input["precio_unitario"]  ?? [];

        $detalles   = [];
        $totalBruto = 0;
        $totalDesc  = 0; // descuento por ahora 0

        for ($i = 0; $i < count($productos_id); $i++) {
            $pid    = (int)($productos_id[$i] ?? 0);
            $cant   = (float)($cantidades[$i] ?? 0);
            $precio = (float)($precios[$i] ?? 0);

            if ($pid <= 0 || $cant <= 0 || $precio < 0) {
                continue;
            }

            $subtotal   = $cant * $precio;
            $totalBruto += $subtotal;

            $detalles[] = [
                "producto_id"     => $pid,
                "cantidad"        => $cant,
                "precio_unitario" => $precio,
                "descuento"       => 0,
                "subtotal"        => $subtotal,
            ];
        }

        $errors = [];
        if (empty($detalles)) {
            $errors[] = "Debe agregar al menos un producto a la venta.";
        }

        if ($errors) {
            $_SESSION["ventas_errors"] = $errors;
            $_SESSION["ventas_old"]    = $input;
            header("Location: /admin/ventas/crear");
            exit;
        }

        $totalNeto = $totalBruto - $totalDesc;

        $header = [
            "cliente"     => $cliente,
            "usuario_id"  => $_SESSION["user"]["id"] ?? 1,
            "fecha_venta" => date("Y-m-d H:i:s"),
            "numero_doc"  => null, // luego lo usamos para FEL / correlativo
            "total_bruto" => $totalBruto,
            "descuento"   => $totalDesc,
            "total_neto"  => $totalNeto,
            "estado"      => "EMITIDA",
            "notas"       => "",
        ];

        $ventaId = $this->model->crearVenta($header, $detalles);

        header("Location: /admin/ventas?ok=creada&id=" . $ventaId);
        exit;
    }
}
