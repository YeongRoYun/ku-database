<?php

namespace app\interface;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";

interface Router
{
    public function run(): void;

    public function route(string $method, string $path, Controller $controller, string $func): void;

    public function register(Middleware $middleware): void;
}
