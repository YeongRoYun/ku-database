<?php

namespace app\business;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Business.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\exception\BadRequestHttpException;
use app\exception\HttpException;
use app\interface\Business;
use function app\util\getConfig;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class AuthBusiness implements Business
{
    // Return sessionInfo
    /**
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function login(string $id, string $pw): array
    {
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
        $expiredAt = $now->add($interval);
        $sessionId = md5($id . $now->format("Y-m-d H:i:s") . $token);
        $query = <<<QUERY
INSERT INTO sessions(id, expired_at) VALUES ("$sessionId", "{$expiredAt->format("Y-m-d H:i:s")}");
QUERY;
        safeMysqliQuery($conn, $query);
        $conn->close();
        return array("sessionId" => $sessionId, "expiredAt" => $expiredAt);
    }

    public function logout(string $sessionId): void
    {
        // Erase sessions
        $conn = getDbConn();
        $query = <<<QUERY
DELETE FROM sessions
WHERE id="$sessionId";
QUERY;
        safeMysqliQuery($conn, $query);
        $conn->close();
    }
}
