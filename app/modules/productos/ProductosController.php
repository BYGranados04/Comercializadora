<?php

require_once __DIR__ . "/ProductosModel.php";
require_once __DIR__ . "/../categorias/CategoriasModel.php";
require_once __DIR__ . "/../marcas/MarcasModel.php";

class ProductosController extends Controller
{
    private ProductosModel $model;

    public function __construct()
    {
        $this->model = new ProductosModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdmin();

        $q         = $_GET["q"] ?? "";
        $productos = $this->model->buscar(trim($q));

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"      => "Productos",
            "user"       => $_SESSION["user"],
            "content"    => "productos/views/index",
            "productos"  => $productos,
            "q"          => $q,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $categorias = $categoriasModel->listarActivas();
        $marcas     = $marcasModel->listarActivas();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"      => "Crear Producto",
            "user"       => $_SESSION["user"],
            "content"    => "productos/views/crear",
            "errors"     => [],
            "old"        => [],
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $data   = $this->sanitizar($_POST);
        $errors = $this->validar($data);

        // Unicidad
        if ($this->model->skuExiste($data["sku"])) {
            $errors[] = "El SKU ya existe.";
        }
        if (!empty($data["codigo_barra"]) && $this->model->codigoExiste($data["codigo_barra"])) {
            $errors[] = "El código de barras/QR ya existe.";
        }

        if ($errors) {
            // Recargar catálogos
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            $categorias = $categoriasModel->listarActivas();
            $marcas     = $marcasModel->listarActivas();

            $this->view("modules/dashboard/views/_admin_layout", [
                "title"      => "Crear Producto",
                "user"       => $_SESSION["user"],
                "content"    => "productos/views/crear",
                "errors"     => $errors,
                "old"        => $data,
                "categorias" => $categorias,
                "marcas"     => $marcas,
            ]);
            return;
        }

        // Payload alineado con columnas de BD
        $payload = [
            "sku"              => trim($data["sku"] ?? ""),
            "codigo_barra"     => trim($data["codigo_barra"] ?? ""),
            "nombre"           => trim($data["nombre"] ?? ""),
            "categoria_id"     => (int)($data["categoria_id"] ?? 1),
            "marca_id"         => (int)($data["marca_id"] ?? 1),
            "unidad_medida_id" => 1, // por ahora fija

            "precio_venta"     => (float)($data["precio"] ?? 0),
            "costo_actual"     => (float)($data["costo"] ?? 0),

            "stock"            => (int)($data["stock"] ?? 0),
            "stock_minimo"     => (int)($data["stock_minimo"] ?? 0),

            "descripcion"      => "",
            "activo"           => 1,
            "estado"           => "ACTIVO",
        ];

        $this->model->crear($payload);

        header("Location: /admin/productos?ok=creado");
        exit;
    }

    // =========================
    // EDITAR
    // =========================
    public function editar($id)
    {
        RoleMiddleware::requireAdmin();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            header("Location: /admin/productos?err=noexiste");
            exit;
        }

        // Cargar catálogos para los selects / lookups
        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $categorias = $categoriasModel->listarActivas();
        $marcas     = $marcasModel->listarActivas();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"      => "Editar Producto",
            "user"       => $_SESSION["user"],
            "content"    => "productos/views/editar",
            "errors"     => [],
            "producto"   => $producto,
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdmin();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            header("Location: /admin/productos?err=noexiste");
            exit;
        }

        $data   = $this->sanitizar($_POST);
        $errors = $this->validar($data);

        // Validar unicidad ignorando el propio ID
        if ($this->model->skuExiste($data["sku"], (int)$id)) {
            $errors[] = "El SKU ya existe en otro producto.";
        }
        if (!empty($data["codigo_barra"]) && $this->model->codigoExiste($data["codigo_barra"], (int)$id)) {
            $errors[] = "El código de barras/QR ya existe en otro producto.";
        }

        if ($errors) {
            // Recargar catálogos
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            $categorias = $categoriasModel->listarActivas();
            $marcas     = $marcasModel->listarActivas();

            $this->view("modules/dashboard/views/_admin_layout", [
                "title"      => "Editar Producto",
                "user"       => $_SESSION["user"],
                "content"    => "productos/views/editar",
                "errors"     => $errors,
                "producto"   => array_merge($producto, $data),
                "categorias" => $categorias,
                "marcas"     => $marcas,
            ]);
            return;
        }

        $payload = [
            "sku"              => $data["sku"],
            "codigo_barra"     => $data["codigo_barra"],
            "nombre"           => $data["nombre"],
            "categoria_id"     => $data["categoria_id"],
            "marca_id"         => $data["marca_id"],
            "unidad_medida_id" => 1,
            "costo_actual"     => $data["costo"],
            "precio_venta"     => $data["precio"],
            "stock"            => $data["stock"],
            "stock_minimo"     => $data["stock_minimo"],
            "descripcion"      => $producto["descripcion"] ?? "",
            "estado"           => $producto["estado"] ?? "ACTIVO",
        ];

        $this->model->actualizar((int)$id, $payload);

        header("Location: /admin/productos?ok=actualizado");
        exit;
    }

    // =========================
    // ELIMINAR (desactivar)
    // =========================
    public function eliminar($id)
    {
        RoleMiddleware::requireAdmin();

        $this->model->desactivar((int)$id);
        header("Location: /admin/productos?ok=eliminado");
        exit;
    }

    // =========================
    // HELPERS
    // =========================
    private function sanitizar(array $input): array
    {
        return [
            "sku"           => strtoupper(trim($input["sku"] ?? "")),
            "codigo_barra"  => trim($input["codigo_barra"] ?? ""),
            "nombre"        => trim($input["nombre"] ?? ""),
            "categoria_id"  => (int)($input["categoria_id"] ?? 0),
            "marca_id"      => (int)($input["marca_id"] ?? 0),
            "costo"         => (float)($input["costo"] ?? 0),
            "precio"        => (float)($input["precio"] ?? 0),
            "stock"         => (int)($input["stock"] ?? 0),
            "stock_minimo"  => (int)($input["stock_minimo"] ?? 0),
            // reservado para futuro (columna todavía no existe)
            "requiere_serie"=> isset($input["requiere_serie"]) ? 1 : 0,
        ];
    }

    private function validar(array $data): array
    {
        $errors = [];

        if ($data["sku"] === "") {
            $errors[] = "SKU es obligatorio.";
        }
        if ($data["nombre"] === "") {
            $errors[] = "Nombre es obligatorio.";
        }
        if ($data["precio"] < $data["costo"]) {
            $errors[] = "Precio no puede ser menor al costo.";
        }
        if ($data["categoria_id"] <= 0) {
            $errors[] = "Categoría es obligatoria.";
        }
        if ($data["marca_id"] <= 0) {
            $errors[] = "Marca es obligatoria.";
        }
        if ($data["stock"] < 0) {
            $errors[] = "Stock no puede ser negativo.";
        }
        if ($data["stock_minimo"] < 0) {
            $errors[] = "Stock mínimo no puede ser negativo.";
        }

        return $errors;
    }
}
