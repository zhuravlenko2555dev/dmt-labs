<?php


namespace App\DMT;


class Lab4 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function analysis_of_hierarchies_calculate($data, $best_alternative = true) {
        $result_data = array();
        $strategy_array = array();
        $strategy_sum = array();
        foreach ($data as $key => $row) {
            foreach ($row as $index => $column) {
                if ($index > 1) {
                    if (!isset($strategy_array[$index - 2])) {
                        $strategy_array[$index - 2] = array();
                    }
                    $addition_element = array();
                    array_push($addition_element, $column/100);
                    array_push($addition_element, $data[$key][1]/100);
                    array_push($strategy_array[$index - 2], $addition_element);
                }
            }
        }

        $val = 0;
        $alternative = 0;
        foreach ($strategy_array as $index => $strategy) {
            $addition_sum = 0;
            foreach ($strategy as $addition_element) {
                $addition_sum += $addition_element[0] * $addition_element[1];
            }
            array_push($strategy_sum, $addition_sum);
            if ($index == 0) {
                $val = $addition_sum;
            }
            if ($best_alternative) {
                if ($addition_sum > $val) {
                    $val = $addition_sum;
                    $alternative = $index;
                }
            } else {
                if ($addition_sum < $val) {
                    $val = $addition_sum;
                    $alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["alternative"] = $alternative;
        $result_data["strategy_array"] = $strategy_array;
        $result_data["strategy_sum"] = $strategy_sum;

        return $result_data;
    }

//print analysis_of_hierarchies_calculate($data)["val"] . PHP_EOL;
}
