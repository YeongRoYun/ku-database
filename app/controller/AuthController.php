<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/RedirectView.php";

use app\interface\Controller;
use app\interface\View;
use app\view\RedirectView;
use function app\exception\bad_request;
use function app\exception\server_error;
use function app\util\get_config;
use function app\util\get_db_conn;
use function app\util\safe_mysqli_query;

class AuthController implements Controller
{
    public function login(): View
    {
        $id = $_POST["id"];
        $pw = $_POST["password"];
        if (!$id) {
            bad_request("ID가 입력되지 않았습니다.");
        }
        if (!$pw) {
            bad_request("PW가 입력되지 않았습니다.");
        }
        $config = get_config();
        if (!key_exists("admin", $config)) {
            server_error("관리자 정보가 없습니다.");
        }
        $admin = $config["admin"];
        if ($id != $admin["id"] || $pw != $admin["password"]) {
            bad_request("ID 혹은 PW가 일치하지 않습니다.");
        }
        $conn = get_db_conn();
        if (!key_exists("secret", $config) || !key_exists("token", $config["secret"])) {
            server_error("비밀키 정보가 없습니다.");
        }
        $token = $config["secret"]["token"];
        $interval = \DateInterval::createFromDateString('30 minutes');
        $now = date_create();
        $expired_at = $now->add($interval);
        $session_id = md5($id . $now->format("Y-m-d H:i:s") . $token);
        $query = <<<QUERY
INSERT INTO sessions(id, expired_at) VALUES ("$session_id", "{$expired_at->format("Y-m-d H:i:s")}");
QUERY;
        safe_mysqli_query($conn, $query);

        if (!setcookie(name: "session_id", value: $session_id,
            expires_or_options: $expired_at->getTimestamp(), path: "/", httponly: true)) {
            $conn->close();
            server_error("세션을 쿠키에 할당할 수 없습니다.");
        }
        $conn->close();
        return new RedirectView("/");
    }

    public function logout(): View
    {
        if (!key_exists("session_id", $_COOKIE)) {
            return new RedirectView("/");
        }
        $session_id = $_COOKIE["session_id"];
        // Erase sessions
        $conn = get_db_conn();
        $query = <<<QUERY
DELETE FROM sessions
WHERE id="$session_id";
QUERY;
        safe_mysqli_query($conn, $query);
        // Set cookie
        if (!setcookie(name: "session_id", value: $session_id, expires_or_options: time() - 3600)) {
            $conn->close();
            server_error("세션을 쿠키에 할당할 수 없습니다.");
        }
        return new RedirectView("/");
    }
}
