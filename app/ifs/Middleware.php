<?php

namespace app\ifs;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/View.php";

interface Middleware
{
    public function interceptRequest();
    public function interceptResponse(View $response);
}
