<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/AbstractView.php";

class LoginView extends AbstractView
{

     public function draw()
    {
    	$loginUrl = "/~2018320135/ku-database/auth/login.php";
        $body = <<<BODY
<form action="$loginUrl" method="post">
    <label for="id">ID:</label><br>
    <input type="text" id="id" name="id"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Submit">
</form>
BODY;
        echo $this->makeHtml("", "", $body);
    }
}
