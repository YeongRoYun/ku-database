<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/SimpleRouter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/LogMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/middleware/AuthMiddleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/SimpleController.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/AuthController.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/error.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/controller/ProductController.php";

use app\SimpleRouter;
use app\controller\SimpleController;

$router = new SimpleRouter();
$router->register(new \app\middleware\LogMiddleware($_SERVER["DOCUMENT_ROOT"] . "/app.log"));
$router->register(new \app\middleware\AuthMiddleware());

$authController = new \app\controller\AuthController();
$productController = new \app\controller\ProductController();

$router->route("POST", "/auth/login", $authController, "login");
$router->route("POST", "/auth/logout", $authController, "logout");
$router->route("GET", "/", $productController, "getList");
$router->route("GET", "/products", $productController, "getList");
$router->run();

//require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductDetailView.php";
//$view = new \app\view\ProductDetailView(data: array("name"=>"Test",
//    "image"=>"https://image.pyoniverse.kr/products/c281caa4c32491e9e1349e8f0d936c57fe3616e8.webp",
//    "description"=>"test description",
//    "category_name"=>"test",
//    "price"=>10.1,
//    "good_count"=>1,
//    "view_count"=>2,
//    "best"=>array("brand_name"=>"CU", "price"=>10.1, "events"=>"1+1,2+1", "event_price"=>null),
//    "brands"=>array(
//        array("brand_name"=>"CU", "price"=>10.1, "events"=>"1+1,2+1", "event_price"=>null),
//        array("brand_name"=>"CU", "price"=>10.1, "events"=>"1+1,2+1", "event_price"=>null),
//        array("brand_name"=>"CU", "price"=>10.1, "events"=>"1+1,2+1", "event_price"=>null),
//    )
//    ));
//$view->draw();
