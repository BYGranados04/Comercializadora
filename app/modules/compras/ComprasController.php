<?php

require_once __DIR__ . "/ComprasModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";
require_once __DIR__ . "/../proveedores/ProveedoresModel.php";

class ComprasController extends Controller
{
    private ComprasModel $model;

    public function __construct()
    {
        $this->model = new ComprasModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        $compras = $this->model->listar();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"   => "Compras",
            "user"    => $_SESSION["user"],
            "content" => "compras/views/index",
            "compras" => $compras,
        ]);
    }

    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $productosModel   = new ProductosModel();
        $proveedoresModel = new ProveedoresModel();

        $productos   = $productosModel->listarActivos();   // catálogo de productos
        $proveedores = $proveedoresModel->listarActivos(); // catálogo de proveedores

        $errors = $_SESSION['compras_errors'] ?? [];
        $old    = $_SESSION['compras_old'] ?? [];

        unset($_SESSION['compras_errors'], $_SESSION['compras_old']);

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"       => "Registrar compra",
            "user"        => $_SESSION["user"],
            "content"     => "compras/views/crear",
            "productos"   => $productos,
            "proveedores" => $proveedores,
            "errors"      => $errors,
            "old"         => $old,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $input = $_POST;

        // Encabezado
        $proveedor_id = (int)($input['proveedor_id'] ?? 0);
        $fecha_compra = trim($input['fecha_compra'] ?? date('Y-m-d'));
        $numero_doc   = trim($input['numero_doc'] ?? '');
        $notas        = trim($input['notas'] ?? '');

        $productos_id   = $input['producto_id'] ?? [];
        $cantidades     = $input['cantidad'] ?? [];
        $costos_unit    = $input['costo_unitario'] ?? [];
        $descuentosFila = $input['descuento'] ?? [];

        $detalles   = [];
        $totalBruto = 0;
        $totalDesc  = 0;

        for ($i = 0; $i < count($productos_id); $i++) {
            $pid   = (int)$productos_id[$i];
            $cant  = (float)($cantidades[$i] ?? 0);
            $costo = (float)($costos_unit[$i] ?? 0);
            $desc  = (float)($descuentosFila[$i] ?? 0);

            if ($pid <= 0 || $cant <= 0 || $costo < 0) continue;

            $subtotal   = $cant * $costo - $desc;
            $totalBruto += $cant * $costo;
            $totalDesc  += $desc;

            $detalles[] = [
                'producto_id'    => $pid,
                'cantidad'       => $cant,
                'costo_unitario' => $costo,
                'descuento'      => $desc,
                'subtotal'       => $subtotal,
            ];
        }

        $errors = [];
        if ($proveedor_id <= 0) $errors[] = "El proveedor es obligatorio.";
        if (empty($detalles))  $errors[] = "Debe ingresar al menos un producto en la compra.";

        if ($errors) {
            // Si hay errores, volvemos a cargar catálogos para la vista
            $productosModel   = new ProductosModel();
            $proveedoresModel = new ProveedoresModel();

            $productos   = $productosModel->listarActivos();
            $proveedores = $proveedoresModel->listarActivos();

            $this->view("modules/dashboard/views/_admin_layout", [
                "title"       => "Registrar compra",
                "user"        => $_SESSION["user"],
                "content"     => "compras/views/crear",
                "productos"   => $productos,
                "proveedores" => $proveedores,
                "errors"      => $errors,
                "old"         => $input,
            ]);
            return;
        }

        $totalNeto = $totalBruto - $totalDesc;

        $header = [
            'proveedor_id' => $proveedor_id,
            'usuario_id'   => $_SESSION["user"]["id"] ?? 1,
            'fecha_compra' => $fecha_compra,
            'numero_doc'   => $numero_doc,
            'total_bruto'  => $totalBruto,
            'descuento'    => $totalDesc,
            'total_neto'   => $totalNeto,
            'estado'       => 'REGISTRADA',
            'notas'        => $notas,
        ];

        $this->model->crearCompra($header, $detalles);

        header("Location: /admin/compras?ok=creada");
        exit;
    }
}
