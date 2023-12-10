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
        $path = explode("?", $_SERVER["REQUEST_URI"])[0];
        if ($path == "/auth/login") {
            return;
        }
        if (!key_exists("session_id", $_COOKIE)) {
            $this->alert_login();
        }
        $conn = get_db_conn();
        $query = <<<QUERY
SELECT expired_at
FROM sessions
WHERE id="{$_COOKIE["session_id"]}";
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
        // 세션 유지시간 다시 30분
        if (!key_exists("session_id", $_COOKIE)) {
            return;
        }
        $conn = get_db_conn();
        $expired_at = date_create();
        $interval = \DateInterval::createFromDateString('30 minutes');
        $expired_at = $expired_at->add($interval);
        $query = <<<QUERY
UPDATE sessions SET expired_at="{$expired_at->format("Y-m-d H:i:s")}"
WHERE id="{$_COOKIE["session_id"]}";
QUERY;

        $result = mysqli_query($conn, $query);
        if (!$result) {
            $conn->close();
            server_error("Database에서 Query를 실행할 수 없습니다." . "Query: " . $query);
        }
        if (!setcookie(name: "session_id", value: $_COOKIE["session_id"],
            expires_or_options: $expired_at->getTimestamp(), path: "/", httponly: true)) {
            $conn->close();
            server_error("로그인 세션을 쿠키에 할당할 수 없습니다.");
        }
        $conn->close();
    }

    #[NoReturn] private function alert_login(): void
    {
        // 기존 쿠키 지우기
        if (key_exists("session_id", $_COOKIE)) {
            setcookie(name: "session_id", value: $_COOKIE["session_id"], expires_or_options: time() - 3600);
        }
        $login_view = new LoginView();
        $login_view->draw();
        die("<script>alert('로그인이 필요합니다.')</script>");
    }
}
