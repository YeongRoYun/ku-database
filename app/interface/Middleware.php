<?php

namespace app\interface;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

interface Middleware
{
    public function interceptRequest(): void;
    public function interceptResponse(View $response): void;
}
