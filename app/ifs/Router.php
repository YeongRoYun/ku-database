<?php

namespace app\ifs;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Middleware.php";

interface Router
{
    public function run();

    public function route(string $method, string $path, Controller $controller, string $func);

    public function register(Middleware $middleware);
}
