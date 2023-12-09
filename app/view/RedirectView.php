<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

use app\interface\View;

class RedirectView implements View
{
    private string $path;
    public function __construct(string $path) {
        $this->path = $path;
    }

    #[\Override] public function draw(): void
    {
        header( "Location: " . $this->path );
    }
}
