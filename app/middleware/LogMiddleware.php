<?php

namespace app\middleware;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

use app\interface\Middleware;
use app\interface\View;

class LogMiddleware implements Middleware
{
    private string $logfile;

    public function __construct(string $logfile) {
        $this->logfile = $logfile;
    }



    #[\Override] public function interceptRequest(): void
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $requestUrl = $_SERVER["REQUEST_URI"];
        $timestamp = date_create()->format("Y-m-dTH:i:s");
        $log = "[$timestamp] Request: [$requestMethod] [$requestUrl]\n";
        error_log($log, "3", $this->logfile);
    }

    #[\Override] public function interceptResponse(View $response): void
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $requestUrl = $_SERVER["REQUEST_URI"];
        $timestamp = date_create()->format("Y-m-dTH:i:s");
        $log = "[$timestamp] Response: [$requestMethod] [$requestUrl]\n";
        error_log($log, "3", $this->logfile);
    }
}
