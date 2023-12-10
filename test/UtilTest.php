<?php

namespace test;

use PHPUnit\Framework\TestCase;
use function app\util\getConfig;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class UtilTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);
    }

    public function test_get_config()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
        $config = getConfig();
        $this->assertTrue($config != false);
    }

    public function test_get_conn()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
        $conn = getDbConn();
        $this->assertTrue($conn != false);
    }

    public function test_session()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
        $datetime = date("Y-m-d H:i:s");
        $conn = getDbConn();
        $query = <<<QUERY
SELECT expired_at
FROM sessions
WHERE id="test";
QUERY;
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            $this->alert_login();
        }
        $row = mysqli_fetch_row($result);
        $expired_at = $row[0];
        var_dump($datetime, $expired_at);
        $this->assertTrue(true);
        var_dump(strtotime($expired_at));
        var_dump(strtotime("now"));

    }

    public function test_date()
    {
        $now = date_create();
        $interval = \DateInterval::createFromDateString('30 minutes');
        $after_10_min = $now->add($interval);
        var_dump($now, $after_10_min);
        $this->assertEquals($after_10_min->sub($interval), $now);
    }

    public function test_error_handler()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/error.php";
        $this->expectException(\ErrorException::class);
        trigger_error("Some Test Error");
    }

    public function test_constant_categories()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
        $conn = getDbConn();
        $constant_categories = safeMysqliQuery($conn, "SELECT id, name FROM categories");
        $category_map = array();
        for ($idx = 0; $idx < mysqli_num_rows($constant_categories); $idx += 1) {
            $row = mysqli_fetch_assoc($constant_categories);
            $category_map[$row["id"]] = $row["name"];
        }
        var_dump($category_map);
        $this->assertNotEmpty($category_map);
    }
}
