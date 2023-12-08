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



    #[\Override] public function intercept_request(): void
    {
        // TODO: Implement intercept_request() method.
        error_log("log  request test\n", "3", $this->logfile);
    }

    #[\Override] public function intercept_response(View $response): void
    {
        // TODO: Implement intercept_response() method.
        error_log("log response test\n", "3", $this->logfile);
    }
}
