<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/SimpleRouter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/LogMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/AuthMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/SimpleController.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/AuthController.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/error.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use app\SimpleRouter;
use app\controller\SimpleController;

$router = new SimpleRouter();
$router->register(new \app\middleware\LogMiddleware($_SERVER["DOCUMENT_ROOT"] . "/app.log"));
$router->register(new \app\middleware\AuthMiddleware());
$router->route("GET", "/", new SimpleController(), "get");
$router->route("POST", "/auth/login", new \app\controller\AuthController(), "login");
$router->route("POST", "/auth/logout", new \app\controller\AuthController(), "logout");
$router->run();
