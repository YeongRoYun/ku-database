<?php

namespace app\view;

use app\interface\View;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

class ProductListView implements View
{
    private string $filter;
    private array $columns;
    private array $data;

    public function __construct(string $filter, array $columns, array $data)
    {
        $this->filter = $filter;
        $this->columns = $columns;
        $this->data = $data;
    }

    #[\Override] public function draw(): void
    {
        $columns = array_map(function(string $col): string {return "<th>".$col."</th>";}, $this->columns);
        $header = array_reduce($columns, function(string $acc, string $cur): string {return $acc.$cur;}, "");
        $header = "<thead><tr>".$header."</tr></thead>";
        /* @var $col string */
        $body = "<tbody>";
        foreach ($this->data as $row) {
            $row_info = "<tr>";
            foreach ($this->columns as $col) {
                $row_info = $row_info . "<td>" . $row[$col] . "</td>";
            }
            $row_info = $row_info . "</tr>";
            $body = $body . $row_info;
        }
        $body = $body . "</tbody>";
        $html = <<<HTML
<table>
  $header
  $body
</table>
<div class="pagination">
    <a href="/products?$this->filter&page=1" class="prev">이전페이지</a>
    <a href="/products?$this->filter&page=2" class="prev">2</a>
    <a href="/products?$this->filter&page=3" class="prev">다음페이지</a>
</div>
HTML;
        echo $html;
    }
}
