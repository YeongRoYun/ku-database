<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

use app\interface\View;

class LoginView implements View
{

    #[\Override] public function draw(): void
    {
        // TODO: Implement draw() method.
        $html = <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head>
	<title>Login Page</title>
</head>
<body>
	<form action="/auth/login" method="post">
		<label for="id">ID:</label><br>
		<input type="text" id="id" name="id"><br>
		<label for="password">Password:</label><br>
		<input type="password" id="password" name="password"><br><br>
		<input type="submit" value="Submit">
	</form>
</body>
</html>

HTML;
        echo $html;
    }
}
