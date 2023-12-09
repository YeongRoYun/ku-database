<?php

namespace app\middleware;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/LoginView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\interface\Middleware;
use app\interface\View;
use app\view\LoginView;
use JetBrains\PhpStorm\NoReturn;
use function app\exception\server_error;
use function app\util\get_db_conn;

class AuthMiddleware implements Middleware
{

    #[\Override] public function intercept_request(): void
    {
        // TODO: Implement intercept_request() method.
        if (!key_exists("session_id", $_COOKIE)) {
            $this->alert_login();
        }
        $conn = get_db_conn();
        $query = <<<QUERY
SELECT expired_at
FROM sessions
WHERE id={$_COOKIE["session_id"]};
QUERY;
        $result = mysqli_query($conn, $query);
        if (!$result) {
            $conn->close();
            server_error("Database에서 Query를 실행할 수 없습니다." . "Query: " . $query);
        }
        if (mysqli_num_rows($result) == 0) {
            $conn->close();
            $this->alert_login();
        }
        $expired_at = mysqli_fetch_row($result)[0];
        if (strtotime($expired_at) <= strtotime("now")) {
            $query = <<<QUERY
DELETE FROM sessions
WHERE id={$_COOKIE["session_id"]};
commit;
QUERY;
            $result = mysqli_query($conn, $query);
            if (!$result) {
                $conn->close();
                server_error("Database에서 Query를 실행할 수 없습니다." . "Query: " . $query);
            } else {
                $conn->close();
                $this->alert_login();
            }
        }
    }

    #[\Override] public function intercept_response(View $response): void
    {
        // TODO: Implement intercept_response() method.
    }

    #[NoReturn] private function alert_login(): void
    {
        $login_view = new LoginView();
        $login_view->draw();
        die("<script>alert('로그인이 필요합니다.')</script>");
    }
}
