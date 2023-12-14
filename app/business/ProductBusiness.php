<?php

namespace app\business;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Business.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\exception\HttpException;
use app\exception\NotFoundHttpException;
use app\interface\Business;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class ProductBusiness implements Business
{
    // Return List Data
    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
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

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function getDetail(int $id): array
    {
        // 2. 기본 정보
        $conn = getDbConn();
        $query = <<<QUERY
SELECT products.name, products.image, products.description, categories.name AS category_name, products.price, view_count, good_count
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE products.id = $id;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new NotFoundHttpException("상품이 없습니다: ID=$id");
        }
        $product = mysqli_fetch_assoc($queryResult);
        // 3. 최저가 정보
        $query = <<<QUERY
SELECT brands.name AS brand_name, outer_bests.price, (
    SELECT GROUP_CONCAT(product_events.name SEPARATOR ",")
    FROM product_bests AS inner_bests
        NATURAL JOIN product_bests_product_events AS bridge
        JOIN product_events ON (bridge.event_id = product_events.id)
    GROUP BY inner_bests.product_id, inner_bests.brand_id
    HAVING inner_bests.product_id = outer_bests.product_id AND inner_bests.brand_id = outer_bests.brand_id
) AS events
FROM brands JOIN product_bests AS outer_bests ON (brands.id = outer_bests.brand_id)
WHERE outer_bests.product_id = $id;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("최저가 정보가 없습니다. DB를 확인하세요: ID=$id");
        }
        $best = mysqli_fetch_assoc($queryResult);
        // 4. 각 브랜드 정보
        $query = <<<QUERY
SELECT brands.name AS brand_name, outer_brands.price, outer_brands.event_price, (
    SELECT GROUP_CONCAT(product_events.name SEPARATOR ",")
    FROM product_brands AS inner_brands
        NATURAL JOIN product_brands_product_events AS bridge
        JOIN product_events ON (bridge.event_id = product_events.id)
    GROUP BY inner_brands.product_id, inner_brands.brand_id
    HAVING inner_brands.product_id = outer_brands.product_id AND inner_brands.brand_id = outer_brands.brand_id
) AS events
FROM brands JOIN product_brands AS outer_brands ON (brands.id = outer_brands.brand_id)
WHERE outer_brands.product_id = $id;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("편의점 정보가 없습니다. DB를 확인하세요: ID=$id");
        }
        $brands = array();
        for ($idx = 0; $idx < mysqli_num_rows($queryResult); ++$idx) {
            $brands[] = mysqli_fetch_assoc($queryResult);
        }
        // 4. 조합
        $product["best"] = $best;
        $product["brands"] = $brands;
        $product["id"] = $id;
        $conn->close();
        return $product;
    }

    /**
     * @throws HttpException
     */
    public function getMutableAttributes(): array
    {
        $conn = getDbConn();
        $mutableAttributes = array("categories" => array());
        $queryResult = safeMysqliQuery($conn, "SELECT id, name FROM categories");
        for ($idx = 0; $idx < mysqli_num_rows($queryResult); ++$idx) {
            $row = mysqli_fetch_assoc($queryResult);
            $mutableAttributes["categories"][$row["id"]] = $row["name"];
        }
        $conn->close();
        return $mutableAttributes;
    }

    /**
     * @throws HttpException
     */
    public function updateAttributes(int $id, array $updatedAttributes): void
    {
        $conn = getDbConn();
        safeMysqliQuery($conn, "SET autocommit=0;");
        safeMysqliQuery($conn, "SET session TRANSACTION ISOLATION LEVEL serializable;");
        safeMysqliQuery($conn, "begin;");
        safeMysqliQuery($conn, "UPDATE products SET category_id={$updatedAttributes["category"]} WHERE id=$id");
        safeMysqliQuery($conn, "commit");
        $conn->close();
    }
}
