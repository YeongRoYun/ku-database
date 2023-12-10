<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/RedirectView.php";

use app\exception\BadRequestHttpException;
use app\exception\HttpException;
use app\interface\Controller;
use app\interface\View;
use app\view\RedirectView;
use function app\util\getConfig;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class AuthController implements Controller
{
    /**
     * @throws HttpException
     */
    public function login(): View
    {
        $id = $_POST["id"];
        $pw = $_POST["password"];
        if (!$id) {
            throw new BadRequestHttpException("ID가 입력되지 않았습니다.");
        }
        if (!$pw) {
            throw new BadRequestHttpException("PW가 입력되지 않았습니다.");
        }
        $config = getConfig();
        if (!key_exists("admin", $config)) {
            throw new HttpException("관리자 정보가 없습니다.");
        }
        $admin = $config["admin"];
        if ($id != $admin["id"] || $pw != $admin["password"]) {
            throw new BadRequestHttpException("ID 혹은 PW가 일치하지 않습니다.");
        }
        $conn = getDbConn();
        if (!key_exists("secret", $config) || !key_exists("token", $config["secret"])) {
            throw new HttpException("비밀키 정보가 없습니다.");
        }
        $token = $config["secret"]["token"];
        $interval = \DateInterval::createFromDateString('30 minutes');
        $now = date_create();
        $expired_at = $now->add($interval);
        $session_id = md5($id . $now->format("Y-m-d H:i:s") . $token);
        $query = <<<QUERY
INSERT INTO sessions(id, expired_at) VALUES ("$session_id", "{$expired_at->format("Y-m-d H:i:s")}");
QUERY;
        safeMysqliQuery($conn, $query);

        if (!setcookie(name: "session_id", value: $session_id,
            expires_or_options: $expired_at->getTimestamp(), path: "/", httponly: true)) {
            $conn->close();
            throw new HttpException("세션을 쿠키에 할당할 수 없습니다.");
        }
        $conn->close();
        return new RedirectView("/");
    }

    /**
     * @throws HttpException
     */
    public function logout(): View
    {
        if (!key_exists("session_id", $_COOKIE)) {
            return new RedirectView("/");
        }
        $session_id = $_COOKIE["session_id"];
        // Erase sessions
        $conn = getDbConn();
        $query = <<<QUERY
DELETE FROM sessions
WHERE id="$session_id";
QUERY;
        safeMysqliQuery($conn, $query);
        // Set cookie
        if (!setcookie(name: "session_id", value: $session_id, expires_or_options: time() - 3600)) {
            $conn->close();
            throw new HttpException("세션을 쿠키에 할당할 수 없습니다.");
        }
        return new RedirectView("/");
    }
}
