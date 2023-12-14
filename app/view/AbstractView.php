<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

use app\interface\View;

abstract class AbstractView implements View
{
    protected function makeHtml(string $css = "", string $js = "", string $body = ""): string {
        $html = <<<HTML
<head lang="ko">
    <title>Pyoniverse Dashboard</title>
    <style>
        $css
    </style>
    <script>
        $js
    </script>
</head>
<body>
    $body
</body>
HTML;
        return $html;
    }
}
