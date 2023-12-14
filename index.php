<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/SimpleRouter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/LogMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/AuthMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/AuthController.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/error.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/ProductController.php";

use app\SimpleRouter;

$router = new SimpleRouter();
$router->register(new \app\middleware\LogMiddleware($_SERVER["DOCUMENT_ROOT"] . "/app.log"));
$router->register(new \app\middleware\AuthMiddleware());

$authController = new \app\controller\AuthController();
$productController = new \app\controller\ProductController();

$router->route("POST", "/auth/login", $authController, "login");
$router->route("POST", "/auth/logout", $authController, "logout");
$router->route("GET", "/", $productController, "getList");
$router->route("GET", "/products", $productController, "getList");
$router->route("GET", "/products/{id}", $productController, "getDetail");
$router->route("GET", "/products/mutable/{id}", $productController, "getMutable");
$router->route("POST", "/products/{id}", $productController, "updateAttributes");
$router->run();
