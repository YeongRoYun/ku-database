<?php

namespace app\view;

function logoutButton(): string
{
    $button = <<<HTML
<form action="/auth/logout" method="post">
  <input type="submit" value="로그아웃">
</form>
HTML;
    return $button;
}
