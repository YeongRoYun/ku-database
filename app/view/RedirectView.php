<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/AbstractView.php";


class RedirectView extends AbstractView
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

     public function draw()
    {
        header("Location: " . $this->path);
    }
}
