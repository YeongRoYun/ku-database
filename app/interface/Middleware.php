<?php

namespace app\interface;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

interface Middleware
{
    public function intercept_request(): void;
    public function intercept_response(View $response): void;
}
