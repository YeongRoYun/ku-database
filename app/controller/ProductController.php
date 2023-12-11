<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductListView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/util.php";

use app\exception\BadRequestHttpException;
use app\exception\HttpException;
use app\exception\NotFoundHttpException;
use app\interface\Controller;
use app\view\ProductListView;
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
SELECT products.id, products.name, products.description, products.image, products.price, products.good_count, products.view_count, categories.name AS category_name
FROM products JOIN categories ON (products.category_id = categories.id)
WHERE categories.id IN $categories_cond
ORDER BY products.id ASC

QUERY;
        } else {
            $query = <<<QUERY
SELECT products.id, products.name, products.description, products.image, products.price, products.good_count, products.view_count, categories.name AS category_name
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
        $view_columns = array("id", "image", "category", "name", "price", "good_count", "view_count", "description");
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
                "view_count" => $row["view_count"], "description" => $row["description"]);
        }
        if (!empty($categories)) {
            $view_filter = implode(",", $categories);
        } else {
            $view_filter = "";
        }
        return new ProductListView(filter: $view_filter, page: $page, total: $total_cnt, beg_page: 1, end_page: $total_page, columns: $view_columns, data: $view_data, categories: $category_map);
    }
}
