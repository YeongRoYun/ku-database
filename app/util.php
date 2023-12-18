<?php

namespace app\util;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use app\exception\HttpException;

/**
 * @throws HttpException
 */
function getConfig(): array
{
    $config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/resource/dev.ini", true);
    if (!$config) {
        throw new HttpException("설정 파일을 찾을 수 없습니다.");
    }
    return $config;
}

/**
 * @throws HttpException
 */
function getDbConn(): \mysqli
{
    $config = getConfig();
    if (!array_key_exists("database", $config)) {
        throw new HttpException("Database 설정을 찾을 수 없습니다.");
    }
    $database = $config["database"];
    $conn = mysqli_connect($database["host"], $database["user"],
        $database["password"], $database["db"], $database["port"]);
    if (!$conn) {
        throw new HttpException("DB에 연결할 수 없습니다.");
    } else {
        return $conn;
    }
}

/**
 * @throws HttpException
 */
function safeMysqliQuery(\mysqli $conn, string $query)
{
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $conn->close();
        throw new HttpException("Database에서 Query를 실행할 수 없습니다." . "Query: " . $query);
    }
    return $result;
}
