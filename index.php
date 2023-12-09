<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/SimpleRouter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/LogMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/AuthMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/SimpleController.php";

use app\SimpleRouter;
use app\controller\SimpleController;


$router = new SimpleRouter();
$router->register(new \app\middleware\LogMiddleware($_SERVER["DOCUMENT_ROOT"] . "/app.log"));
$router->register(new \app\middleware\AuthMiddleware());
$router->route("GET", "/", new SimpleController(), "get");
$router->run();
