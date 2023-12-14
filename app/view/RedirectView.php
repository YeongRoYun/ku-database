<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/AbstractView.php";


class RedirectView extends AbstractView
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    #[\Override] public function draw(): void
    {
        header("Location: " . $this->path);
    }
}
