<?php

namespace app\exception;

use JetBrains\PhpStorm\NoReturn;


#[NoReturn] function not_found(string $msg): void
{
    http_response_code(404);
    die($msg);
}

#[NoReturn] function not_allow(string $msg): void
{
    http_response_code(405);
    die($msg);
}

#[NoReturn] function server_error(string $msg): void {
    http_response_code(500);
    die($msg);
}

#[NoReturn] function bad_request(string $msg): void
{
    http_response_code(400);
    die($msg);
}
