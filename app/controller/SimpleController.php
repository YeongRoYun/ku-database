<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/SimpleView.php";

use app\interface\Controller;
use app\interface\View;
use app\view\SimpleView;

class SimpleController implements Controller
{
    public function get(): View {
        return new SimpleView("hello, world");
    }

}
