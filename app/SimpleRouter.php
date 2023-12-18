<?php

namespace app;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Router.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use app\exception\NotAllowHttpException;
use app\exception\NotFoundHttpException;
use app\ifs\Controller;
use app\ifs\Middleware;
use app\ifs\Router;
use app\ifs\View;

class SimpleRouter implements Router
{
    private $middlewares = array();
    private $routeTable = array();


    /**
     * @throws NotFoundHttpException
     * @throws NotAllowHttpException
     */
     public function run()
    {
        /* @var $middleware Middleware */
        foreach ($this->middlewares as $middleware) {
            $middleware->interceptRequest();
        }
        $curPath = explode("?", $_SERVER["REQUEST_URI"])[0];
        $curMethod = $_SERVER["REQUEST_METHOD"];
        
        $hasPath = false;
        $hasMethod = false;
        $controller = null;
        $func = null;
        $pathVariables = array();
        foreach ($this->routeTable as $pathPattern => $values) {
            if ($this->checkPath($curPath, $pathPattern, $pathVariables)) {
                $hasPath = true;
                foreach ($values as $val) {
                    if ($curMethod == $val["method"]) {
                        $hasMethod = true;
                        $controller = $val["controller"];
                        $func = $val["func"];
                    }
                }
            }
            if ($hasPath && $hasMethod) {
                break;
            }
            $pathVariables = array();
        }
        $_REQUEST["PATH_VARIABLES"] = $pathVariables;
        if (!$hasPath) {
            throw new NotFoundHttpException($curPath . "은(는) 잘못된 경로입니다.");
        } elseif (!$hasMethod) {
            throw new NotAllowHttpException($curMethod . "은(는) 허용되지 않는 요청입니다.");
        } else {
            /* @var $response View */
            $response = $controller->$func();
        }
        /* @var $middleware Middleware */
        foreach ($this->middlewares as $middleware) {
            $middleware->interceptResponse($response);
        }
        $response->draw();
    }


     public function route(string $method, string $path, Controller $controller, string $func)
    {
        if (!array_key_exists($path, $this->routeTable)) {
            $this->routeTable[$path] = array();
        }
        $this->routeTable[$path][] = array("method" => $method, "controller" => $controller, "func" => $func);
    }

     public function register(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    private function checkPath($path, $pathPattern, &$pathVariables): bool {
        $pathChunks = explode("/", trim(trim($path), "/"));
        $pathPatternChunks = explode("/", trim(trim($pathPattern), "/"));
        if (count($pathChunks) != count($pathPatternChunks)) {
            return false;
        }
        $res = true;
        for ($idx = 0; $idx < count($pathChunks); ++$idx) {
            $pathChunk = $pathChunks[$idx];
            $pathPatternChunk = $pathPatternChunks[$idx];
            $res = $this->checkPathChunk($pathChunk, $pathPatternChunk, $pathVariables);
            if (!$res) {
                break;
            }
        }
        return $res;
    }

    private function checkPathChunk($pathChunk, $pathPatternChunk, &$pathVariables): bool {
        $varRegex = "/^\{(.+)}$/i";
        $res = true;
        if (preg_match($varRegex, $pathPatternChunk, $matches)) {
            $varName = $matches[1];
            $var = $pathChunk;
            $pathVariables[$varName] = $var;
        } elseif ($pathChunk != $pathPatternChunk) {
            $res = false;
        }
        return $res;
    }
}
