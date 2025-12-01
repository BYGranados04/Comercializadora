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
$router = new Router();

// Auth
$router->get("/", "AuthController@loginForm");
$router->get("/login", "AuthController@loginForm");
$router->post("/login", "AuthController@login");
$router->get("/logout", "AuthController@logout");

// Rutas dummy por ahora (luego las hacemos reales)
$router->get("/admin/dashboard", "DashboardController@index");

$router->get("/admin/productos", "ProductosController@index");
$router->get("/admin/productos/crear", "ProductosController@crear");
$router->post("/admin/productos/guardar", "ProductosController@guardar");

$router->get("/admin/productos/editar/{id}", "ProductosController@editar");
$router->post("/admin/productos/actualizar/{id}", "ProductosController@actualizar");

$router->post("/admin/productos/eliminar/{id}", "ProductosController@eliminar");$router->get("/admin/productos", "ProductosController@index");
$router->get("/admin/productos/crear", "ProductosController@crear");
$router->post("/admin/productos/guardar", "ProductosController@guardar");

$router->get("/admin/productos/editar/{id}", "ProductosController@editar");
$router->post("/admin/productos/actualizar/{id}", "ProductosController@actualizar");

$router->post("/admin/productos/eliminar/{id}", "ProductosController@eliminar");

// COMPRAS
$router->get('/admin/compras',         'ComprasController@index');
$router->get('/admin/compras/crear',   'ComprasController@crear');
$router->post('/admin/compras/guardar','ComprasController@guardar');


$router->dispatch();


