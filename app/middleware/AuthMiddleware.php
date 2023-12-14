<?php

namespace app\middleware;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Middleware.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/LoginView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\exception\HttpException;
use app\interface\Middleware;
use app\interface\View;
use app\view\LoginView;
use JetBrains\PhpStorm\NoReturn;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class AuthMiddleware implements Middleware
{

    /**
     * @throws HttpException
     */
    #[\Override] public function interceptRequest(): void
    {
        $path = explode("?", $_SERVER["REQUEST_URI"])[0];
        if ($path == "/auth/login") {
            return;
        }
        if (!key_exists("session_id", $_COOKIE)) {
            $this->alertLogin();
        }
        $conn = getDbConn();
        $query = <<<QUERY
SELECT expired_at
FROM sessions
WHERE id="{$_COOKIE["session_id"]}";
QUERY;
        $result = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            $conn->close();
            $this->alertLogin();
        }
        $expiredAt = mysqli_fetch_row($result)[0];
        if (strtotime($expiredAt) <= strtotime("now")) {
            $query = <<<QUERY
DELETE FROM sessions
WHERE id={$_COOKIE["session_id"]};
QUERY;
            safeMysqliQuery($conn, $query);
            $conn->close();
            $this->alertLogin();
        }
    }

    /**
     * @throws HttpException
     */
    #[\Override] public function interceptResponse(View $response): void
    {
        // 세션 유지시간 다시 30분
        if (!key_exists("session_id", $_COOKIE)) {
            return;
        }
        $conn = getDbConn();
        $expiredAt = date_create();
        $interval = \DateInterval::createFromDateString('30 minutes');
        $expiredAt = $expiredAt->add($interval);
        $query = <<<QUERY
UPDATE sessions SET expired_at="{$expiredAt->format("Y-m-d H:i:s")}"
WHERE id="{$_COOKIE["session_id"]}";
QUERY;
        safeMysqliQuery($conn, $query);
        if (!setcookie(name: "session_id", value: $_COOKIE["session_id"],
            expires_or_options: $expiredAt->getTimestamp(), path: "/", httponly: true)) {
            $conn->close();
            throw new HttpException("로그인 세션을 쿠키에 할당할 수 없습니다.");
        }
        $conn->close();
    }

    #[NoReturn] private function alertLogin(): void
    {
        // 기존 쿠키 지우기
        if (key_exists("session_id", $_COOKIE)) {
            setcookie(name: "session_id", value: $_COOKIE["session_id"], expires_or_options: time() - 3600);
        }
        $loginView = new LoginView();
        $loginView->draw();
        die("<script>alert('로그인이 필요합니다.')</script>");
    }
}
