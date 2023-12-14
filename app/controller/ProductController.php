<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductListView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductDetailView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductMutableView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";

use app\exception\BadRequestHttpException;
use app\exception\HttpException;
use app\exception\NotFoundHttpException;
use app\interface\Controller;
use app\view\ProductDetailView;
use app\view\ProductListView;
use app\view\ProductMutableView;
use app\view\RedirectView;
use function app\util\getDbConn;
use function app\util\safeMysqliQuery;

class ProductController implements Controller
{
    /**
     * @throws BadRequestHttpException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function getList(): ProductListView
    {
        // GET /products?categories=1,2,3&page=3
        if (!key_exists("categories", $_GET) || strlen($_GET["categories"]) == 0) {
            $categories = array();
        } else {
            /* @var $categories_query string */
            $categories_query = $_GET["categories"];
            $categories = explode(",", trim(trim($categories_query), ","));
        }
        if (!key_exists("page", $_GET)) {
            $page = 1;
        } else {
            $page = $_GET["page"];
        }
        // Validate queries
        foreach ($categories as $category) {
            if (!preg_match("/\d+/", $category)) {
                throw new BadRequestHttpException("카테고리 쿼리는 정수 ID를 보내야 합니다.");
            }
        }
        if (!preg_match("/\d+/", $page)) {
            throw new BadRequestHttpException("페이지 쿼리는 정수를 보내야 합니다.");
        } else {
            $page = intval($page);
        }
        // Find products
        $page_size = 50;
        $skip = $page_size * ($page - 1);
        if (!empty($categories)) {
            $categories_cond = "(" . implode(",", $categories) . ")";
            $query = <<<QUERY
SELECT products.id, products.name, products.image, products.price, products.good_count, products.view_count, categories.name AS category_name
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE categories.id IN $categories_cond
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
            $query = $query . " LIMIT $page_size OFFSET $skip;";
        } else {
            $query = $query . " LIMIT $page_size;";
        }

        $conn = getDbConn();
        $result = safeMysqliQuery($conn, $query);
        // Check data
        if (mysqli_num_rows($result) == 0) {
            throw new NotFoundHttpException("categories IN $categories_cond, page=$page, page_size=$page_size 에 일치하는 데이터가 없습니다.");
        }
        // Find meta data
        if (!empty($categories)) {
            $categories_cond = "(" . implode(",", $categories) . ")";
            $total_products = safeMysqliQuery($conn, "SELECT COUNT(*) FROM products WHERE category_id IN $categories_cond");
        } else {
            $total_products = safeMysqliQuery($conn, "SELECT COUNT(*) FROM products");
        }
        $total_cnt = mysqli_fetch_array($total_products)[0];
        $total_page = ceil($total_cnt / $page_size);

        // Convert data
        $view_columns = array("id", "image", "category", "name", "price", "good_count", "view_count");
        $constant_categories = safeMysqliQuery($conn, "SELECT id, name FROM categories");
        $category_map = array();
        for ($idx = 0; $idx < mysqli_num_rows($constant_categories); $idx += 1) {
            $row = mysqli_fetch_assoc($constant_categories);
            $category_map[$row["id"]] = $row["name"];
        }
        $view_data = array();
        for ($idx = 0; $idx < mysqli_num_rows($result); $idx += 1) {
            $row = mysqli_fetch_assoc($result);
            $view_data[] = array("id" => $row["id"], "image" => $row["image"], "category" => $row["category_name"],
                "name" => $row["name"], "price" => $row["price"], "good_count" => $row["good_count"],
                "view_count" => $row["view_count"]);
        }
        if (!empty($categories)) {
            $view_filter = implode(",", $categories);
        } else {
            $view_filter = "";
        }
        return new ProductListView(filter: $view_filter, page: $page, total: $total_cnt, beg_page: 1, end_page: $total_page, columns: $view_columns, data: $view_data, categories: $category_map);
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function getDetail(): ProductDetailView
    {
        // 1. get id
        $productId = $_REQUEST["PATH_VARIABLES"]["id"];
        if (!preg_match("/\d+/i", $productId)) {
            throw new BadRequestHttpException("상품 ID는 정수여야 합니다.");
        }
        $productId = intval($productId);

        // 2. 기본 정보
        $conn = getDbConn();
        $query = <<<QUERY
SELECT products.name, products.image, products.description, categories.name AS category_name, products.price, view_count, good_count
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE products.id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new NotFoundHttpException("상품이 없습니다: ID=$productId");
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
WHERE outer_bests.product_id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("최저가 정보가 없습니다. DB를 확인하세요: ID=$productId");
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
WHERE outer_brands.product_id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("편의점 정보가 없습니다. DB를 확인하세요: ID=$productId");
        }
        $brands = array();
        for ($idx = 0; $idx < mysqli_num_rows($queryResult); ++$idx) {
            $brands[] = mysqli_fetch_assoc($queryResult);
        }
        // 4. 조합
        $product["best"] = $best;
        $product["brands"] = $brands;
        $product["id"] = $productId;
        $view = new \app\view\ProductDetailView(data: $product);
        $conn->close();
        return $view;
    }

    public function getMutable(): ProductMutableView
    {
        // 1. get id
        $productId = $_REQUEST["PATH_VARIABLES"]["id"];
        if (!preg_match("/\d+/i", $productId)) {
            throw new BadRequestHttpException("상품 ID는 정수여야 합니다.");
        }
        $productId = intval($productId);

        // 2. 기본 정보
        $conn = getDbConn();
        $query = <<<QUERY
SELECT products.name, products.image, products.description, categories.name AS category_name, products.price, view_count, good_count
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE products.id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new NotFoundHttpException("상품이 없습니다: ID=$productId");
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
WHERE outer_bests.product_id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("최저가 정보가 없습니다. DB를 확인하세요: ID=$productId");
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
WHERE outer_brands.product_id = $productId;
QUERY;
        $queryResult = safeMysqliQuery($conn, $query);
        if (mysqli_num_rows($queryResult) == 0) {
            throw new HttpException("편의점 정보가 없습니다. DB를 확인하세요: ID=$productId");
        }
        $brands = array();
        for ($idx = 0; $idx < mysqli_num_rows($queryResult); ++$idx) {
            $brands[] = mysqli_fetch_assoc($queryResult);
        }
        // 4. 조합
        $product["best"] = $best;
        $product["brands"] = $brands;
        $product["id"] = $productId;

        // 5. 수정 사능한 속성 정보
        $mutableAttributes = array("categories" => array());
        $queryResult = safeMysqliQuery($conn, "SELECT id, name FROM categories");
        for ($idx = 0; $idx < mysqli_num_rows($queryResult); ++$idx) {
            $row = mysqli_fetch_assoc($queryResult);
            $mutableAttributes["categories"][$row["id"]] = $row["name"];
        }

        $view = new \app\view\ProductMutableView(data: $product, mutableAttributes: $mutableAttributes);
        $conn->close();
        return $view;
    }

    public function updateAttributes(): RedirectView
    {
        $productId = $_REQUEST["PATH_VARIABLES"]["id"];
        $updatedAttributes = array();
        if (key_exists("category", $_POST)) {
            $updatedAttributes["category"] = $_POST["category"];
        }
        // update
        $conn = getDbConn();
        safeMysqliQuery($conn, "SET autocommit=0;");
        safeMysqliQuery($conn, "SET session TRANSACTION ISOLATION LEVEL serializable;");
        safeMysqliQuery($conn, "begin;");
        safeMysqliQuery($conn, "UPDATE products SET category_id={$updatedAttributes["category"]} WHERE id=$productId");
        safeMysqliQuery($conn, "commit");
        $conn->close();
        return new RedirectView("/products/$productId");
    }
}
