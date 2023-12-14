<?php

namespace app\business;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Business.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\exception\NotFoundHttpException;
use app\interface\Business;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class ProductBusiness implements Business
{
    // Return List Data
    public function getList(array $categories, int $page): array
    {
        $pageSize = 50;
        $skip = $pageSize * ($page - 1);
        if (!empty($categories)) {
            $categoriesCond = "(" . implode(",", $categories) . ")";
            $query = <<<QUERY
SELECT products.id, products.name, products.image, products.price, products.good_count, products.view_count, categories.name AS category_name
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE categories.id IN $categoriesCond
ORDER BY products.id ASC

QUERY;
        } else {
            $query = <<<QUERY
SELECT products.id, products.name, products.image, products.price, products.good_count, products.view_count, categories.name AS category_name
FROM products JOIN categories ON (products.category_id = categories.id)
ORDER BY products.id ASC
QUERY;
        }
        if ($skip > 0) {
            $query = $query . " LIMIT $pageSize OFFSET $skip;";
        } else {
            $query = $query . " LIMIT $pageSize;";
        }

        $conn = getDbConn();
        $result = safeMysqliQuery($conn, $query);
        // Check data
        if (mysqli_num_rows($result) == 0) {
            throw new NotFoundHttpException("categories IN $categoriesCond, page=$page, page_size=$pageSize 에 일치하는 데이터가 없습니다.");
        }
        // Find meta data
        if (!empty($categories)) {
            $categoriesCond = "(" . implode(",", $categories) . ")";
            $totalProducts = safeMysqliQuery($conn, "SELECT COUNT(*) FROM products WHERE category_id IN $categoriesCond");
        } else {
            $totalProducts = safeMysqliQuery($conn, "SELECT COUNT(*) FROM products");
        }
        $totalCnt = mysqli_fetch_array($totalProducts)[0];
        $totalPage = ceil($totalCnt / $pageSize);

        // Convert data
        $viewColumns = array("id", "image", "category", "name", "price", "good_count", "view_count");
        $constantCategories = safeMysqliQuery($conn, "SELECT id, name FROM categories");
        $categoryMap = array();
        for ($idx = 0; $idx < mysqli_num_rows($constantCategories); $idx += 1) {
            $row = mysqli_fetch_assoc($constantCategories);
            $categoryMap[$row["id"]] = $row["name"];
        }
        $viewData = array();
        for ($idx = 0; $idx < mysqli_num_rows($result); $idx += 1) {
            $row = mysqli_fetch_assoc($result);
            $viewData[] = array("id" => $row["id"], "image" => $row["image"], "category" => $row["category_name"],
                "name" => $row["name"], "price" => $row["price"], "good_count" => $row["good_count"],
                "view_count" => $row["view_count"]);
        }
        if (!empty($categories)) {
            $viewFilter = implode(",", $categories);
        } else {
            $viewFilter = "";
        }
        $res = array(
            "filter" => $viewFilter,
            "page" => $page,
            "total" => $totalCnt,
            "begPage" => 1,
            "endPage" => $totalPage,
            "columns" => $viewColumns,
            "data" => $viewData,
            "categories" => $categoryMap
        );
        $conn->close();
        return $res;
    }
}
