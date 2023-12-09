<?php

namespace app\middleware;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/LoginView.php";

use app\interface\Middleware;
use app\interface\View;
use app\view\LoginView;

class AuthMiddleware implements Middleware
{

    #[\Override] public function intercept_request(): void
    {
        // TODO: Implement intercept_request() method.
        $login_view = new LoginView();
        $login_view->draw();
        die();
    }

    #[\Override] public function intercept_response(View $response): void
    {
        // TODO: Implement intercept_response() method.
    }
}
