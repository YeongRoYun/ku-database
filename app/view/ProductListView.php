<?php

namespace app\view;

use app\interface\View;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/interface/View.php";

class ProductListView implements View
{
    private string $filter;
    private int $page;
    private int $total;
    private int $beg_page;
    private int $end_page;
    private array $columns;
    private array $data;

    public function __construct(string $filter, int $page, int $total, int $beg_page, int $end_page, array $columns, array $data)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->columns = $columns;
        $this->data = $data;
        $this->total = $total;
        $this->beg_page = $beg_page;
        $this->end_page = $end_page;
    }

    #[\Override] public function draw(): void
    {
//        $arr_size = count($this->data);
//        var_dump($arr_size);
        $columns = array_map(function(string $col): string {return "<th>".$col."</th>";}, $this->columns);
        $header = array_reduce($columns, function(string $acc, string $cur): string {return $acc.$cur;}, "");
        $header = "<thead><tr>".$header."</tr></thead>";
        /* @var $col string */
        $body = "<tbody>";
        foreach ($this->data as $row) {
            $row_info = "<tr>";
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
        $html = <<<HTML
<table>
  $header
  $body
</table>
<div class="pagination">
    <a href="/products?categories=$this->filter&page=$prv_page" class="prev">이전페이지</a>
    <a href="/products?categories=$this->filter&page=$this->page" class="selected">$this->page</a>
    <a href="/products?categories=$this->filter&page=$nxt_page" class="next">다음페이지</a>
</div>
HTML;
        echo $html;
    }
}
