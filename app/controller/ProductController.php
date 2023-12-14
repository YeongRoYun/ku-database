<?php

namespace app\controller;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/Controller.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductListView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductDetailView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/ProductMutableView.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/exception/http.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/business/ProductBusiness.php";

use app\business\ProductBusiness;
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
    private ProductBusiness $productBusiness;

    public function __construct()
    {
        $this->productBusiness = new ProductBusiness();
    }

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
        $res = $this->productBusiness->getList(categories: $categories, page: $page);
        return new ProductListView(filter: $res["filter"], page: $res["page"], total: $res["total"],
            begPage: $res["begPage"], endPage: $res["endPage"], columns: $res["columns"], data: $res["data"],
            categories: $res["categories"]);
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

        $product = $this->productBusiness->getDetail(id: $productId);
        return new \app\view\ProductDetailView(data: $product);
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function getMutable(): ProductMutableView
    {
        // 1. get id
        $productId = $_REQUEST["PATH_VARIABLES"]["id"];
        if (!preg_match("/\d+/i", $productId)) {
            throw new BadRequestHttpException("상품 ID는 정수여야 합니다.");
        }
        $productId = intval($productId);

        $product = $this->productBusiness->getDetail(id: $productId);

        // 5. 수정 사능한 속성 정보
        $mutableAttributes = $this->productBusiness->getMutableAttributes();
        return new \app\view\ProductMutableView(data: $product, mutableAttributes: $mutableAttributes);
    }

    /**
     * @throws HttpException
     */
    public function updateAttributes(): RedirectView
    {
        $productId = $_REQUEST["PATH_VARIABLES"]["id"];
        $updatedAttributes = array();
        if (key_exists("category", $_POST)) {
            $updatedAttributes["category"] = $_POST["category"];
        }
        // update
        $this->productBusiness->updateAttributes(id: $productId, updatedAttributes: $updatedAttributes);
        return new RedirectView("/products/$productId");
    }
}
