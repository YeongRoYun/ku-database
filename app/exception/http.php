<?php

namespace app\exception;

use JetBrains\PhpStorm\NoReturn;

class HttpException extends \Exception
{
    // Redefine the exception so message isn't optional
    protected int $httpStatusCode;

    public function __construct(string $message, int $code = 0, \Throwable $previous = null, int $httpStatusCode = 500)
    {
        // some code
        $this->httpStatusCode = $httpStatusCode;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->httpStatusCode}]: {$this->message}\n";
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 404);
    }
}

class NotAllowHttpException extends HttpException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 405);
    }
}

class BadRequestHttpException extends HttpException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 400);
    }
}

function exception_handler(\Throwable $exception): void
{
    if (is_subclass_of($exception, '\app\exception\HttpException')) {
        $status_code = $exception->getHttpStatusCode();
        $message = $exception->getMessage();
    } else {
        $status_code = 500;
        $message = "예상하지 못한 예외가 발생했습니다.: " . $exception->getMessage();
    }
    $html = <<<HTML
<script>alert("$message")</script>
HTML;
    if ($status_code == 500) {
        var_dump($exception);
        die($html);
    } else {
        header("Location: " . "/");
        die($html);
    }

}

set_exception_handler('\app\exception\exception_handler');
