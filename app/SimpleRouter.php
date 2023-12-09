<?php

namespace app;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Router.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use app\interface\Controller;
use app\interface\Middleware;
use app\interface\Router;
use app\interface\View;
use function app\exception\not_found;
use function app\exception\not_allow;

class SimpleRouter implements Router
{
    private array $middlewares = array();
    private array $route_table = array();


    #[\Override] public function run(): void
    {
        /* @var $middleware Middleware */
        foreach ($this->middlewares as $middleware) {
            $middleware->intercept_request();
        }

        $cur_path = explode("?", $_SERVER["REQUEST_URI"])[0];
        $cur_method = $_SERVER["REQUEST_METHOD"];
        $has_path = false;
        $has_method = false;
        $controller = null;
        $func = null;

        foreach ($this->route_table as $path => $values) {
            foreach ($values as $val) {
                if ($cur_path == $path) {
                    $has_path = true;
                    if ($cur_method == $val["method"]) {
                        $has_method = true;
                        $controller = $val["controller"];
                        $func = $val["func"];
                    }
                }
            }
            if ($has_path && $has_method) {
                break;
            }
        }
        if (!$has_path) {
            not_found($cur_path . "은(는) 잘못된 경로입니다.");
        } elseif (!$has_method) {
            not_allow($cur_method . "은(는) 허용되지 않는 요청입니다.");
        } else {
            /* @var $response View */
            $response = $controller->$func();
        }
        /* @var $middleware Middleware */
        foreach ($this->middlewares as $middleware) {
            $middleware->intercept_response($response);
        }
        $response->draw();
    }


    #[\Override] public function route(string $method, string $path, Controller $controller, string $func): void
    {
        if (!array_key_exists($path, $this->route_table)) {
            $this->route_table[$path] = array();
        }
        $this->route_table[$path][] = array("method" => $method, "controller" => $controller, "func" => $func);
    }

    #[\Override] public function register(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}
