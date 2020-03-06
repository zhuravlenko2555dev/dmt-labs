<?php


namespace App\DMT;


class Lab6 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function priority_setting_calculate($data) {
        $result_data = array();
        $adjacency_matrix = array();
        $sum_by_row = array();
        $p = array();
        foreach ($data as $index => $column) {
            $adjacency_matrix[$index] = array();
            foreach ($data as $elem) {
                if ($column > $elem) {
                    array_push($adjacency_matrix[$index], 2);
                } else if ($column < $elem) {
                    array_push($adjacency_matrix[$index], 0);
                } else if ($column == $elem) {
                    array_push($adjacency_matrix[$index], 1);
                }
            }
        }

        foreach ($adjacency_matrix as $row) {
            array_push($sum_by_row, array_sum($row));
        }

        $p[0] = array();
        $sum = array_sum($sum_by_row);
        foreach ($sum_by_row as $row) {
            array_push($p[0], $row / $sum);
        }

        $p[1] = array();
        foreach ($adjacency_matrix as $row) {
            $s = 0;
            foreach ($row as $index => $column) {
                $s += $column *  $sum_by_row[$index];
            }
            array_push($p[1], $s);
        }

        $p[2] = array();
        $sum2 = array_sum($p[1]);
        foreach ($p[1] as $row) {
            array_push($p[2], $row / $sum2);
        }




        $result_data["adjacency_matrix"] = $adjacency_matrix;
        $result_data["sum_by_row"] = $sum_by_row;
        $result_data["p"] = $p;

        return $result_data;
    }

//print priority_setting_calculate($data) . PHP_EOL;
}
