<?php

namespace app\view;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

use app\interface\View;

class SimpleView implements View
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    #[\Override] public function draw(): void
    {
        // TODO: Implement draw() method.
        echo "<h1>" . $this->text . "</h1>";
    }
}
