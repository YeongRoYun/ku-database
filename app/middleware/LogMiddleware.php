<?php

namespace app\middleware;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/View.php";

use app\ifs\Middleware;
use app\ifs\View;

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
