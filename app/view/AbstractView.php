<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/ifs/View.php";

use app\ifs\View;

abstract class AbstractView implements View
{
    protected function makeHtml(string $style = "", string $script = "", string $body = ""): string {
        $menuStyle = $this->getMenuStyle();
        $menu = $this->getMenu();
        $html = <<<HTML
<head lang="ko">
    <title>Pyoniverse Dashboard</title>
    <style>
        $menuStyle
        $style
    </style>
    <script>
        $script
    </script>
</head>
<body>
    $menu
    $body
</body>
HTML;
        return $html;
    }
    private function getMenuStyle(): string
    {
        $style = <<<STYLE
.menu {
    list-style: none;
}
.menu > li {
    float: left;
    margin-right: 20px;
}
STYLE;
    return $style;
    }
    private function getMenu(): string
    {
        $body = <<<BODY
<div>
    <ul class="menu">
        <li>
            <form action="/~2018320135/ku-database/auth/logout.php" method="post">
                <input type="submit" value="로그아웃">
            </form>
        </li>
        <li><button onclick="location.href='/~2018320135/ku-database/products'">상품리스트</button></li>
    </ul>
</div>
<br/>
<br/>
BODY;
    return $body;
    }
}
