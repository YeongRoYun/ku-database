<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/AbstractView.php";

class ProductMutableView extends AbstractView
{
    private array $data;
    private array $mutableAttributes;

    public function __construct(array $data, array $mutableAttributes)
    {
        $this->data = $data;
        $this->mutableAttributes = $mutableAttributes;
    }

    #[\Override] public function draw(): void
    {
        $style = <<<STYLE
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
STYLE;

        $brand_rows = "";
        foreach ($this->data["brands"] as $brand) {
            $brand_rows = $brand_rows . "<tr>
                        <td>{$brand["brand_name"]}</td>
                        <td>{$brand["events"]}</td>
                        <td>{$brand["price"]}</td>
                        <td>{$brand["event_price"]}</td>
                    </tr>";
        }
        // mutable fields
        $categorySelector = "<select name=\"category\" id=\"category\">";
        foreach ($this->mutableAttributes["categories"] as $categoryId => $categoryName) {
            if ($categoryName != $this->data["category_name"]) {
                $categorySelector = $categorySelector . "<option value='$categoryId'>$categoryName</option>";
            } else {
                $categorySelector = $categorySelector . "<option value='$categoryId' selected>$categoryName</option>";
            }
        }

        $body = <<<BODY
<h1>{$this->data["name"]}</h1>
<div style="text-align: center;">
    <img src="{$this->data["image"]}" alt="{$this->data["name"]}"/>
</div>
<div>
    <form action="/products/{$this->data["id"]}" method="POST">
        <input type="submit" value="수정하기" />
        <h2>설명</h2>
        <p>{$this->data["description"]}</p>
        <h2>기본 정보</h2>
        <p>
            <table>
                <thead>
                    <tr>
                        <th>카테고리</th>
                        <th>가격</th>
                        <th>Good Count</th>
                        <th>View Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            $categorySelector
                        </td>
                        <td>{$this->data["price"]}</td>
                        <td>{$this->data["good_count"]}</td>
                        <td>{$this->data["view_count"]}</td>
                    </tr>
                </tbody>
            </table>
        </p>
        <h2>최저 가격 정보</h2>
        <p>
            <table>
                <thead>
                    <tr>
                        <th>편의점</th>
                        <th>행사정보</th>
                        <th>최저가격</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$this->data["best"]["brand_name"]}</td>
                        <td>{$this->data["best"]["events"]}</td>
                        <td>{$this->data["best"]["price"]}</td>
                    </tr>
                </tbody>
            </table>
        </p>
        <h2>판매중인 편의점 정보</h2>
        <p>
            <table>
                <thead>
                    <tr>
                        <th>편의점</th>
                        <th>행사정보</th>
                        <th>가격</th>
                        <th>행사가격</th>
                    </tr>
                </thead>
                <tbody>
                    $brand_rows
                </tbody>
            </table>
        </p>
    </form>
</div>
BODY;
        echo $this->makeHtml(style: $style, body: $body);
    }
}
