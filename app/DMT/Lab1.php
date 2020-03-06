<?php


namespace App\DMT;


class Lab1 {
    public $dmt_spreadsheet = array();

    public function __construct($data)
    {
        $this->dmt_spreadsheet = $data;
    }

    public function maxymax_calculate($data, $best_alternative = true) {
        $val = $data[0][0];
        $number_of_row_of_alternative = 0;
        $result_data = array();
        foreach ($data as $index => $row) {
            foreach ($row as $column) {
                if ($best_alternative) {
                    if ($column > $val) {
                        $val = $column;
                        $number_of_row_of_alternative = $index;
                    }
                } else {
                    if ($column < $val) {
                        $val = $column;
                        $number_of_row_of_alternative = $index;
                    }
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;

        return $result_data;
    }

    public function valda_calculate($data, $best_alternative = true) {
        $val = -1;
        $number_of_row_of_alternative = 0;
        $result_data = array();
        $values_from_each_row = array();
        foreach ($data as $row) {
            $suitable_value_from_row = $row[0];
            foreach ($row as $column) {
                if ($best_alternative) {
                    if ($column < $suitable_value_from_row) $suitable_value_from_row = $column;
                } else {
                    if ($column > $suitable_value_from_row) $suitable_value_from_row = $column;
                }
            }
            array_push($values_from_each_row, $suitable_value_from_row);
        }
        $val = $values_from_each_row[0];
        foreach ($values_from_each_row as $index => $suitable_value) {
            if ($best_alternative) {
                if ($suitable_value > $val) {
                    $val = $suitable_value;
                    $number_of_row_of_alternative = $index;
                }
            } else {
                if ($suitable_value < $val) {
                    $val = $suitable_value;
                    $number_of_row_of_alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;

        return $result_data;
    }

    public function gurvitsa_calculate($data, $q, $best_alternative = true) {
        $val = -1;
        $number_of_row_of_alternative = 0;
        $result_data = array();
        $min_values_from_each_row = array();
        $max_values_from_each_row = array();
        foreach ($data as $row) {
            $suitable_min_value_from_row = $row[0];
            $suitable_max_value_from_row = $row[0];
            foreach ($row as $column) {
                if ($best_alternative) {
                    if ($column < $suitable_min_value_from_row) $suitable_min_value_from_row = $column;
                    if ($column > $suitable_max_value_from_row) $suitable_max_value_from_row = $column;
                } else {
                    if ($column > $suitable_min_value_from_row) $suitable_min_value_from_row = $column;
                    if ($column < $suitable_max_value_from_row) $suitable_max_value_from_row = $column;
                }
            }
            array_push($min_values_from_each_row, $suitable_min_value_from_row);
            array_push($max_values_from_each_row, $suitable_max_value_from_row);
        }

        $val = $q * $min_values_from_each_row[0] + (1 - $q) * $max_values_from_each_row[0];
        for ($i = 0; $i < count($min_values_from_each_row); $i++) {
            $calculated_values = $q * $min_values_from_each_row[$i] + (1 - $q) * $max_values_from_each_row[$i];
            if ($best_alternative) {
                if ($calculated_values > $val) {
                    $val = $calculated_values;
                    $number_of_row_of_alternative = $i;
                }
            } else {
                if ($calculated_values < $val) {
                    $val = $calculated_values;
                    $number_of_row_of_alternative = $i;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;

        return $result_data;
    }

    public function bayesa_laplasa_calculate($data, $probability, $best_alternative = true) {
        $val = -1;
        $number_of_row_of_alternative = 0;
        $result_data = array();
        $new_data_array = array();
        foreach ($data as $row) {
            $new_row_data = array();
            foreach ($row as $index => $column) {
                array_push($new_row_data, $column * $probability[$index]);
            }
            array_push($new_data_array, $new_row_data);
        }

        $val = array_sum($data[0]);
        foreach ($new_data_array as $index => $row) {
            if ($best_alternative) {
                if (array_sum($row) > $val) {
                    $val = array_sum($row);
                    $number_of_row_of_alternative = $index;
                }
            } else {
                if (array_sum($row) < $val) {
                    $val = array_sum($row);
                    $number_of_row_of_alternative = $index;
                }
            }
        }

        $result_data["val"] = $val;
        $result_data["number_of_row_of_alternative"] = $number_of_row_of_alternative;

        return $result_data;
    }

//print maxymax_calculate($data) . PHP_EOL;
//print valda_calculate($data) . PHP_EOL;
//print gurvitsa_calculate($data, 0.4) . PHP_EOL;
//print bayesa_laplasa_calculate($data, array(0.7, 0.4, 0.5, 0.8));
}
