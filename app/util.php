<?php

namespace app\util;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use function app\exception\server_error;

function get_config(): array
{
    $config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/resource/dev.ini", true);
    if (!$config) {
        server_error("설정 파일을 찾을 수 없습니다.");
    }
    return $config;
}

function get_db_conn(): \mysqli
{
    $config = get_config();
    if (!array_key_exists("database", $config)) {
        server_error("Database 설정을 찾을 수 없습니다.");
    }
    $database = $config["database"];
    $conn = mysqli_connect(hostname: $database["host"], username: $database["user"],
        password: $database["password"], port: $database["port"]);
    if (!$conn) {
        server_error("DB에 연결할 수 없습니다.");
    } else {
        return $conn;
    }
}
