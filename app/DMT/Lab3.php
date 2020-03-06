<?php


namespace App\DMT;


class Lab3 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function summation_of_ranks_calculate($data, $best_alternative = true) {
        $result_data = array();
        $ranks_array = array();
        foreach ($data as $row) {
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    if (!isset($ranks_array[$column])) {
                        $ranks_array[$column] = array();
                    }
                    array_push($ranks_array[$column], $index);
                }
            }
        }

        $val = array_sum(array_values($ranks_array)[0]);
        $alternative = array_keys($ranks_array)[0];
        foreach ($ranks_array as $index => $rank) {
            if ($best_alternative) {
                if (array_sum($rank) < $val) {
                    $val = array_sum($rank);
                    $alternative = $index;
                }
            } else {
                if (array_sum($rank) > $val) {
                    $val = array_sum($rank);
                    $alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["alternative"] = $alternative;
        $result_data["ranks_array"] = $ranks_array;

        return $result_data;
    }

//print summation_of_ranks_calculate($data)["val"] . PHP_EOL;
}
