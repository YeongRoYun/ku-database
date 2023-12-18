<?php
$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);
$_SERVER["REQUEST_URI"] = str_replace("/~2018320135/ku-database", "", $_SERVER["REQUEST_URI"]);
$_SERVER["REQUEST_URI"] = str_replace(".php", "", $_SERVER["REQUEST_URI"]);
require_once $_SERVER["DOCUMENT_ROOT"] . "/index.php";