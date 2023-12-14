<?php

namespace app\view;
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/view/AbstractView.php";

class ProductListView extends AbstractView
{
    private string $filter;
    private int $page;
    private int $total;
    private int $beg_page;
    private int $end_page;
    private array $columns;
    private array $data;
    private array $categories;

    public function __construct(string $filter, int $page, int $total, int $begPage, int $endPage,
                                array  $columns, array $data, array $categories)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->columns = $columns;
        $this->data = $data;
        $this->total = $total;
        $this->beg_page = $begPage;
        $this->end_page = $endPage;
        $this->categories = $categories;
    }

    #[\Override] public function draw(): void
    {
        $columns = array_map(function(string $col): string {return "<th>".$col."</th>";}, $this->columns);
        $header = array_reduce($columns, function(string $acc, string $cur): string {return $acc.$cur;}, "");
        $header = "<thead><tr>".$header."</tr></thead>";
        /* @var $col string */
        $body = "<tbody>";
        foreach ($this->data as $row) {
            $row_info = "<tr onclick=\"window.location.href='/products/{$row["id"]}'\">";
            foreach ($this->columns as $col) {
                if ($col == "image") {
                    $row_info = $row_info . "<td><img src='$row[$col]' alt='$row[$col]' width='100' height='100'/></td>";
                } else {
                    $row_info = $row_info . "<td>" . $row[$col] . "</td>";
                }

            }
            $row_info = $row_info . "</tr>";
            $body = $body . $row_info;
        }
        $body = $body . "</tbody>";
        $prv_page = max($this->beg_page, $this->page - 1);
        $nxt_page = min($this->page + 1, $this->end_page);

        // Category Filter
        $filterForm = "<form id='filter-form' method=\"GET\" action=\"/products\">";
        foreach ($this->categories as $categoryId => $categoryName) {
            $filterForm = $filterForm . "<input type=\"checkbox\" name=\"category\" value=\"$categoryId\" /> $categoryName <br/>";
        }
        $filterForm = $filterForm . "<input type=\"submit\" value=\"Submit\" onclick=\"convertToString()\">";
        $filterForm = $filterForm . "</form>";

        $style = <<<STYLE
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}

th {
  background-color: #4CAF50;
  color: white;
}
.pagination {
    margin 0 auto;
}
STYLE;
        $script = <<<SCRIPT
function convertToString() {
  const selected = document.querySelectorAll('input[name="category"]:checked');
  const values = Array.from(selected).map(el => el.value);
  const result = values.join(",");
  const hiddenInput = document.createElement("input");
  hiddenInput.setAttribute("type", "hidden");
  hiddenInput.setAttribute("name", "categories");
  hiddenInput.setAttribute("value", result);
  document.querySelector("#filter-form").appendChild(hiddenInput);
}
SCRIPT;
        $body = <<<BODY
<div>
    $filterForm
    <p>전체 상품 수: $this->total 페이지: $this->page/$this->end_page</p>
    <table>
        $header
        $body
    </table>
    <nav class="pagination">
        <a href="/products?categories=$this->filter&page=$prv_page" class="prev">이전페이지</a>
        <a href="/products?categories=$this->filter&page=$this->page" class="selected">$this->page</a>
        <a href="/products?categories=$this->filter&page=$nxt_page" class="next">다음페이지</a>
    </nav>
</div>
BODY;
        echo $this->makeHtml(style: $style, script: $script, body: $body);
    }
}
