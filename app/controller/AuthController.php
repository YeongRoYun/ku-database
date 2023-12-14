<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/RedirectView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/business/AuthBusiness.php";

use app\business\AuthBusiness;
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
    private AuthBusiness $authBusiness;

    public function __construct()
    {
        $this->authBusiness = new AuthBusiness();
    }

    /**
     * @throws HttpException
     * @throws BadRequestHttpException
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
        $sessionInfo = $this->authBusiness->login(id: $id, pw: $pw);
        if (!setcookie(name: "session_id", value: $sessionInfo["sessionId"],
            expires_or_options: $sessionInfo["expiredAt"]->getTimestamp(), path: "/", httponly: true)) {
            throw new HttpException("세션을 쿠키에 할당할 수 없습니다.");
        }
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
        $sessionId = $_COOKIE["session_id"];
        $this->authBusiness->logout(sessionId: $sessionId);
        // Set cookie
        if (!setcookie(name: "session_id", value: $sessionId, expires_or_options: time() - 3600)) {
            throw new HttpException("세션을 쿠키에 할당할 수 없습니다.");
        }
        return new RedirectView("/");
    }
}
