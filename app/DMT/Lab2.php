<?php


namespace App\DMT;


class Lab2 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function savage_calculate($data, $best_alternative = true) {
        $number_of_row_of_alternative = 0;
        $result_data = array();
        $risk_data_array = array();
        $max_values_from_each_column = $data[0];
        $max_values_from_each_row = array();
        foreach ($data as $row) {
            foreach ($row as $index => $column) {
                if ($column > $max_values_from_each_column[$index]) {
                    $max_values_from_each_column[$index] = $column;
                }
            }
        }

        foreach ($data as $row) {
            $risk_row_data = array();
            foreach ($row as $index => $column) {
                array_push($risk_row_data, $max_values_from_each_column[$index] - $column);
            }
            array_push($risk_data_array, $risk_row_data);
        }

        foreach ($risk_data_array as $row) {
            $suitable_max_values_from_row = $row[0];
            foreach ($row as $column) {
                if ($column > $suitable_max_values_from_row) $suitable_max_values_from_row = $column;
            }
            array_push($max_values_from_each_row, $suitable_max_values_from_row);
        }

        $val = $max_values_from_each_row[0];
        foreach ($max_values_from_each_row as $index => $max_value) {
            if ($best_alternative) {
                if ($max_value < $val) {
                    $val = $max_value;
                    $number_of_row_of_alternative = $index;
                }
            } else {
                if ($max_value > $val) {
                    $val = $max_value;
                    $number_of_row_of_alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;
        $result_data["risk_data_array"] = $risk_data_array;

        return $result_data;
    }

    public function expected_value_calculate($data, $c1, $c2, $n) {
        $number_of_row_of_alternative = 0;
        $result_data = array();
        foreach ($data as $index => $row) {
            $p_sum = 0;
            if ($index != 0) {
                for ($i = 0; $i < $index; $i++) {
                    $p_sum += $data[$i][0];
                }
            }
            array_push($data[$index], $p_sum);
            array_push($data[$index], round($n * ($c1 * $p_sum + $c2) / ($index + 1), 1));
        }

        $val = 0;
        foreach ($data as $index => $row) {
            if ($index != 0) {
                if (($data[$index - 1][2] >= $data[$index][2]) && ($data[$index + 1][2] >= $data[$index][2])) {
                    $val = $data[$index][2];
                    $number_of_row_of_alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;
        $result_data["new_data"] = $data;

        return $result_data;
    }

    public function expected_value_dispersion_calculate($data, $c1, $c2, $n) {
        $number_of_row_of_alternative = 0;
        $result_data = array();
        foreach ($data as $index => $row) {
            array_push($data[$index], pow($data[$index][0], 2));
            $p_sum = 0;
            $p_pow_2_sum = 0;
            if ($index != 0) {
                for ($i = 0; $i < $index; $i++) {
                    $p_sum += $data[$i][0];
                }
            }
            if ($index != 0) {
                for ($i = 0; $i < $index; $i++) {
                    $p_pow_2_sum += $data[$i][1];
                }
            }
            array_push($data[$index], $p_sum);
            array_push($data[$index], $p_pow_2_sum);
            array_push($data[$index], round($n / ($index + 1) * ($c1 * $data[$index][2] + $c2) + $n * pow($c1 / ($index + 1), 2) * ( $data[$index][2] -  $data[$index][3]), 2));
        }

        $val = $data[0][4];
        foreach ($data as $index => $row) {
            if ($row[4] < $val) {
                $val = $row[4];
                $number_of_row_of_alternative = $index;
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;
        $result_data["new_data"] = $data;

        return $result_data;
    }

    public function average_expected_value_calculate($data, $best_alternative = true) {
        $number_of_row_of_alternative = 0;
        $result_data = array();
        $cases_count_array = array();
        $average_expected_value_array = array();
        foreach ($data as $row) {
            $cases_count = 0;
            foreach ($row as $column) {
                $cases_count += $column[1];
            }
            array_push($cases_count_array, $cases_count);
        }

        foreach ($data as $index => $row) {
            $average_expected_value = 0;
            foreach ($row as $index2 => $column) {
                $average_expected_value += $column[0] * $column[1] / $cases_count_array[$index];
                $data[$index][$index2][2] = $column[1] / $cases_count_array[$index];
            }
            array_push($average_expected_value_array, $average_expected_value);
        }

        $val = $average_expected_value_array[0];
        foreach ($average_expected_value_array as $index => $expected_value) {
            if ($best_alternative) {
                if ($expected_value > $val) {
                    $val = $expected_value;
                    $number_of_row_of_alternative = $index;
                }
            } else {
                if ($expected_value < $val) {
                    $val = $expected_value;
                    $number_of_row_of_alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;
        $result_data["average_expected_value_array"] = $average_expected_value_array;
        $result_data["new_data"] = $data;

        return $result_data;
    }

    public function limit_level_calculate($i1, $i2, $a1, $a2) {
        $result_data = array();
        $expected_deficit = array();
        $expected_surpluses = array();
        $expected_deficit_limit = round(log($i2) - ($a1 / $i2) - 1, 3);
        $expected_surpluses_limit = round(log($i1) - ($a2 / $i2) - 1, 3);

        for ($i = $i1; $i <= $i2; $i++) {
            array_push($expected_deficit, round(log($i) - ($i / $i2), 2));
            array_push($expected_surpluses, round(log($i) - ($i / $i1), 2));
        }

        for ($i = 0; $i < count($expected_deficit); $i++) {
            if ($expected_deficit[$i] >= $expected_deficit_limit && $expected_surpluses[$i] >= $expected_surpluses_limit) {
                $suitable_start_interval_index = $i;
                break;
            }
        }

        for ($i = count($expected_deficit) - 1; $i >= 0; $i--) {
            if ($expected_deficit[$i] >= $expected_deficit_limit && $expected_surpluses[$i] >= $expected_surpluses_limit) {
                $suitable_end_interval_index = $i;
                break;
            }
        }

        $result_data["expected_deficit"] = $expected_deficit;
        $result_data["expected_surpluses"] = $expected_surpluses;
        $result_data["expected_deficit_limit"] = $expected_deficit_limit;
        $result_data["expected_surpluses_limit"] = $expected_surpluses_limit;
        $result_data["suitable_start_interval_index"] = $suitable_start_interval_index;
        $result_data["suitable_end_interval_index"] = $suitable_end_interval_index;

        return $result_data;
    }

//print savage_calculate($data)["val"] . PHP_EOL;
//print expected_value_calculate($data2, 100, 10, 50)["val"] . PHP_EOL;
//print expected_value_dispersion_calculate($data2, 100, 10, 50)["val"] . PHP_EOL;
//print average_expected_value_calculate($data3)["val"] . PHP_EOL;
//print limit_level_calculate(10, 20, 2, 4) . PHP_EOL;
}
