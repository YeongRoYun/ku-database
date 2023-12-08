<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/SimpleRouter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/LogMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/SimpleController.php";

use app\SimpleRouter;
use app\middleware\LogMiddleware;
use app\controller\SimpleController;


$router = new SimpleRouter();
$router->register(new LogMiddleware($_SERVER["DOCUMENT_ROOT"] . "/app.log"));
$router->route("GET", "/", new SimpleController(), "get");
$router->run();
