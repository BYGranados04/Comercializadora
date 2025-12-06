<?php
session_start();

require_once __DIR__ . "/../app/config/env.php";
require_once __DIR__ . "/../app/config/database.php";

require_once __DIR__ . "/../app/core/Router.php";
require_once __DIR__ . "/../app/core/Controller.php";
require_once __DIR__ . "/../app/core/Model.php";
require_once __DIR__ . "/../app/core/View.php";

require_once __DIR__ . "/../app/middleware/AuthMiddleware.php";
require_once __DIR__ . "/../app/middleware/RoleMiddleware.php";

require_once __DIR__ . "/../app/modules/auth/User.php";
require_once __DIR__ . "/../app/modules/auth/AuthController.php";
require_once __DIR__ . "/../app/modules/dashboard/DashboardController.php";
require_once __DIR__ . "/../app/modules/productos/ProductosController.php";
require_once __DIR__ . "/../app/modules/compras/ComprasController.php";
require_once __DIR__ . "/../app/modules/proveedores/ProveedoresController.php";

require_once __DIR__ . "/../app/modules/catalogos/CategoriasMarcasController.php";
require_once __DIR__ . "/../app/modules/categorias/CategoriasModel.php";
require_once __DIR__ . "/../app/modules/marcas/MarcasModel.php";

require_once __DIR__ . "/../app/modules/ventas/VentasController.php";

require_once __DIR__ . "/../app/modules/clientes/ClientesController.php";


$router = new Router();


$router->get("/", "AuthController@loginForm");
$router->get("/login", "AuthController@loginForm");
$router->post("/login", "AuthController@login");
$router->get("/logout", "AuthController@logout");

// Dashboard
$router->get("/admin/dashboard", "DashboardController@index");

// PRODUCTOS
$router->get("/admin/productos",                "ProductosController@index");
$router->get("/admin/productos/crear",          "ProductosController@crear");
$router->post("/admin/productos/guardar",       "ProductosController@guardar");
$router->get("/admin/productos/editar/{id}",    "ProductosController@editar");
$router->post("/admin/productos/actualizar/{id}","ProductosController@actualizar");
$router->post("/admin/productos/eliminar/{id}", "ProductosController@eliminar");

// COMPRAS
$router->get('/admin/compras',          'ComprasController@index');
$router->get('/admin/compras/crear',    'ComprasController@crear');
$router->post('/admin/compras/guardar', 'ComprasController@guardar');

// PROVEEDORES
$router->get('/admin/proveedores',          'ProveedoresController@index');
$router->get('/admin/proveedores/crear',    'ProveedoresController@crear');
$router->post('/admin/proveedores/guardar', 'ProveedoresController@guardar');
$router->post('/admin/proveedores/cambiar-estado/{id}', 'ProveedoresController@cambiarEstado');

// CATEGORÍAS & MARCAS
$router->get('/admin/catalogos',                          'CategoriasMarcasController@index');
$router->post('/admin/catalogos/categorias/guardar',      'CategoriasMarcasController@guardarCategoria');
$router->post('/admin/catalogos/marcas/guardar',          'CategoriasMarcasController@guardarMarca');
$router->post('/admin/catalogos/categorias/cambiar-estado/{id}', 'CategoriasMarcasController@cambiarEstadoCategoria');
$router->post('/admin/catalogos/marcas/cambiar-estado/{id}',     'CategoriasMarcasController@cambiarEstadoMarca');

// VENTAS
$router->get("/admin/ventas",         "VentasController@index");
$router->get("/admin/ventas/crear",   "VentasController@crear");
$router->post("/admin/ventas/guardar","VentasController@guardar");

// CLIENTES (para ventas)
$router->get("/admin/clientes/buscar", "ClientesController@buscar");
$router->post("/admin/clientes/crear-rapido", "ClientesController@crearRapido");

// CLIENTES – buscador para ventas
$router->get('/admin/clientes/buscar', 'ClientesController@buscar');


$router->dispatch();
