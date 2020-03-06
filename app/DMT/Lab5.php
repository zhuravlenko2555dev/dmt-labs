<?php


namespace App\DMT;


class Lab5 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function theory_of_game_calculate($data, $coefficient, $maximize_params, $best_alternative = true) {
        $result_data = array();
        $efficiency_matrix = array();
        $normalize_matrix = array();
        $weight_coefficient_matrix = array();
        $values_by_params = array();
        $min_or_max_values_by_params = array();
        $sum_of_minimize_params = array();
        $sum_of_maximize_params = array();
        $efficiency_matrix = $data;
        foreach ($efficiency_matrix as $key => $row) {
            $Ec = $row[count($row) - 1];
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    switch ($index) {
                        case 1:
                            $efficiency_matrix[$key][$index] = 1 / ($efficiency_matrix[$key][$index] * $Ec);
                            break;
                        case 2:
                            $efficiency_matrix[$key][$index] = $efficiency_matrix[$key][$index] / $Ec;
                            break;
                        case 3:
                            $efficiency_matrix[$key][$index] = 1 / ($efficiency_matrix[$key][$index] * $Ec);
                            break;
                        case 4:
                            $efficiency_matrix[$key][$index] = $efficiency_matrix[$key][$index] / $Ec;
                            break;
                        case 5:
                            break;
                    }
                }
            }
        }

        foreach ($efficiency_matrix as $key => $row) {
            if ($key == 0) {
                for ($i = 0; $i < count($row) - 1; $i++) {
                    array_push($values_by_params, array());
                }
            }
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    array_push($values_by_params[$index - 1], $column);
                }
            }
        }

        foreach ($values_by_params as $key => $row) {
            $min_or_max_values = $row[0];
            foreach ($row as $index => $column) {
                if ($maximize_params[$key]) {
                    if ($column > $min_or_max_values) $min_or_max_values = $column;
                } else {
                    if ($column < $min_or_max_values) $min_or_max_values = $column;
                }
            }
            array_push($min_or_max_values_by_params, $min_or_max_values);
        }

        $normalize_matrix = $efficiency_matrix;
        foreach ($normalize_matrix as $key => $row) {
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    if ($maximize_params[$key]) {
                        $normalize_matrix[$key][$index] /= $min_or_max_values_by_params[$index - 1];
                    } else {
                        $normalize_matrix[$key][$index] = $min_or_max_values_by_params[$index - 1] / $efficiency_matrix[$key][$index];
                    }
                }
            }
        }

        $weight_coefficient_matrix = $normalize_matrix;
        foreach ($weight_coefficient_matrix as $key => $row) {
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    $weight_coefficient_matrix[$key][$index] = $weight_coefficient_matrix[$key][$index] * $coefficient[$index - 1];
                }
            }
        }

        foreach ($weight_coefficient_matrix as $key => $row) {
            $sum_of_max = 0;
            $sum_of_min = 0;
            foreach ($row as $index => $column) {
                if ($index != 0) {
                    if ($maximize_params[$index - 1]) {
                        $sum_of_max += $column;
                    } else {
                        $sum_of_min += $column;
                    }
                }
            }
            array_push($sum_of_maximize_params, $sum_of_max);
            array_push($sum_of_minimize_params, $sum_of_min);
        }

        $val = $sum_of_maximize_params[0] / $sum_of_minimize_params[0];
        $alternative = 0;
        for ($i = 0; $i < count($weight_coefficient_matrix); $i++) {
            if ($best_alternative) {
                if ($sum_of_maximize_params[$i] / $sum_of_minimize_params[$i] > $val) {
                    $val = $sum_of_maximize_params[$i] / $sum_of_minimize_params[$i];
                    $alternative = $i;
                }
            } else {
                if ($sum_of_maximize_params[$i] / $sum_of_minimize_params[$i] < $val) {
                    $val = $sum_of_maximize_params[$i] / $sum_of_minimize_params[$i];
                    $alternative = $i;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["alternative"] = $alternative;
        $result_data["efficiency_matrix"] = $efficiency_matrix;
        $result_data["normalize_matrix"] = $normalize_matrix;
        $result_data["weight_coefficient_matrix"] = $weight_coefficient_matrix;
        $result_data["min_or_max_values_by_params"] = $min_or_max_values_by_params;
        $result_data["sum_of_minimize_params"] = $sum_of_minimize_params;
        $result_data["sum_of_maximize_params"] = $sum_of_maximize_params;

        return $result_data;
    }

//print theory_of_game_calculate($data, array(0.2, 0.2, 0.2, 0.25, 0.15), array(false, true, false, true, false))["val"] . PHP_EOL;
}
