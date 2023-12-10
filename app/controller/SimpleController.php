<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/RedirectView.php";

use app\interface\Controller;
use app\interface\View;
use app\view\RedirectView;

class SimpleController implements Controller
{
    public function get(): View
    {
        return new RedirectView("/products");
    }

}
