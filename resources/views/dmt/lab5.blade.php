@extends('layouts.app_dmt')

@section('content')
    {{--    container--}}
    <div class="">
        <div class="row no-container">
            @include('dmt.dmt_sidebar')

            <div class="col-md-12 col-lg-10">
                @include('dmt.instruction')
                <div class="card">
                    @if(!empty($data["file_selected"]))
                        <h5 class="card-header">Файл: <span class="text-muted">{{$data["files"][$data["file_selected"]]["original_name"]}}</span></h5>
                    @endif
                    <div class="card-body">
                        @if(!empty($data["files"]))
                            @php
                                $dmt_excel = Excel::toArray(new \App\Imports\DMTImport, $data["files"][$data["file_selected"]]["real_path"]);
                                $dmt_spreadsheet = $dmt_excel[0];
                                $dmt_spreadsheet_column_count = count($dmt_spreadsheet[0]);
                            @endphp
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Тип ІМС</th>
                                    <th scope="col">Струм живлення I, mA</th>
                                    <th scope="col">Вихідна потужність N, Вт</th>
                                    <th scope="col">Коефіцієнт гармонік K</th>
                                    <th scope="col">Ймовірність безвідмовної роботи</th>
                                    <th scope="col">Вартість</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dmt_spreadsheet as $key => $row)
                                    <tr>
                                        @foreach($row as $index => $column)
                                            <td>{{$column}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="row"></th>
                                    @for($i = 0; $i < $dmt_spreadsheet_column_count - 1; $i++)
                                        <td><input form="showResult" class="form-control" style="width: 90px;"@if(isset($data["coefficient"]) && !empty($data["coefficient"])) {{"value=" . $data["coefficient"][$i]}} @endif type="number" step="any" required name="coefficient[]" placeholder="коеф. {{$i+1}}"></td>
                                    @endfor
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    @for($i = 0; $i < $dmt_spreadsheet_column_count - 1; $i++)
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" form="showResult" class="custom-control-input" name="maximize_params[]" value="{{$i}}" id="switch{{$i}}">
                                                <label class="custom-control-label" for="switch{{$i}}">Toggle to maximize</label>
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                @if(isset($data["maximize_params"]) && !empty($data["maximize_params"]))
                    @php
                        $switches = array();
                        for ($i = 0; $i < $dmt_spreadsheet_column_count - 1; $i++) {
                            $switches[$i] = false;
                        }
                        foreach (array_values($data["maximize_params"]) as $key) {
                            $switches[$key] = true;
                        }
                    @endphp
                @endif
                @if(isset($data["maximize_params"]) && !empty($data["maximize_params"]))
                    <script>
                        $(document).ready(function () {
                            @foreach($switches as $i => $switch)
                                @if($switch)
                                    document.getElementById("switch{{$i}}").checked = true;
                                @else
                                    document.getElementById("switch{{$i}}").checked = false;
                                @endif
                            @endforeach
                        });
                    </script>
                @endif

                @if(!empty($data["file_selected"]))
                    <form id="showResult" class="form-inline mt-4 mb-4" action={{"/dmt/" . $data["lab"]}}>
                        <input class="form-control mr-2" type="hidden" name="file_selected" value="{{$data["file_selected"]}}">

                        <div class="custom-control custom-radio">
                            <input type="radio" id="radioBetter" name="alternative" value="better" class="custom-control-input" required checked>
                            <label class="custom-control-label mr-2" for="radioBetter">Краща альтернатива</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="radioWorse" name="alternative" value="worse" class="custom-control-input" required @if(isset($data["alternative"])) {{$data["alternative"] == "worse" ? "checked" : ""}} @endif>
                            <label class="custom-control-label mr-2" for="radioWorse">Гірша альтернатива</label>
                        </div>

                        <button type="submit" name="submit" value="showResult" class="btn btn-primary my-1">Вивести результат</button>
                    </form>
                @endif

                @if(!empty($data["showResult"]))
                    @php
                        $lab5 = new \App\DMT\Lab5($dmt_spreadsheet);
                        $result_data = array();
                        $coefficient = array_map(function ($v) {return floatval($v);}, $data["coefficient"]);
                        $result_data = $lab5->theory_of_game_calculate($lab5->dmt_spreadsheet, $coefficient, $switches, $data["alternative"] == "better");
                        $count_of_minimize = 0;
                        $count_of_maximize = 0;
                        $param_names = array(
                            "Струм живлення I, mA",
                            "Вихідна потужність N, Вт",
                            "Коефіцієнт гармонік K",
                            "Ймовірність безвідмовної роботи",
                            "Вартість"
                        );
                        foreach ($switches as $switch) {
                            if ($switch) {
                                $count_of_maximize++;
                            } else {
                                $count_of_minimize++;
                            }
                        }
                    @endphp
                    <div class="card">
                        <h5 class="card-header" style="color: green"><b>Результат:</b></h5>
                        <div class="card-body">
                            <h5 class="card-text"><b>Матриця показників ефективності:</b></h5>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Варіант рішення</th>
                                    <th scope="col" colspan="{{$count_of_minimize}}" style="text-align: center; background-color: #6cbbff">Параметри, що мінімізуються</th>
                                    <th scope="col" colspan="{{$count_of_maximize}}" style="text-align: center; background-color: #ffac55">Параметри, що максимізуються</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="col">Тип ІМС</th>
                                    @foreach($switches as $i => $switch)
                                        @if(!$switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                    @foreach($switches as $i => $switch)
                                        @if($switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                </tr>
                                @foreach($result_data["efficiency_matrix"] as $key => $row)
                                    <tr>
                                        <td>{{$row[0]}}</td>
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && !$switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && $switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>min E</td>
                                    @foreach($result_data["min_or_max_values_by_params"] as $key => $row)
                                        @if(!$switches[$key])
                                            <td style="background-color: #6cbbff"">{{$row}}</td>
                                        @endif
                                    @endforeach
                                    <td colspan="{{$count_of_maximize}}"></td>
                                </tr>
                                <tr>
                                    <td>max E</td>
                                    <td colspan="{{$count_of_minimize}}"></td>
                                    @foreach($result_data["min_or_max_values_by_params"] as $key => $row)
                                        @if($switches[$key])
                                            <td style="background-color: #ffac55">{{$row}}</td>
                                        @endif
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>

                            <br>
                            <h5 class="card-text"><b>Нормована ігрова матриця:</b></h5>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Варіант рішення</th>
                                    <th scope="col" colspan="{{$count_of_minimize}}" style="text-align: center; background-color: #6cbbff">Параметри, що мінімізуються</th>
                                    <th scope="col" colspan="{{$count_of_maximize}}" style="text-align: center; background-color: #ffac55">Параметри, що максимізуються</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="col">Тип ІМС</th>
                                    @foreach($switches as $i => $switch)
                                        @if(!$switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                    @foreach($switches as $i => $switch)
                                        @if($switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                </tr>
                                @foreach($result_data["normalize_matrix"] as $key => $row)
                                    <tr>
                                        <td>{{$row[0]}}</td>
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && !$switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && $switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <br>
                            <h5 class="card-text"><b>Ігрова матриця з врахуванням вагових коефіцієнтів:</b></h5>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Варіант рішення</th>
                                    <th scope="col" colspan="{{$count_of_minimize}}" style="text-align: center; background-color: #6cbbff">Параметри, що мінімізуються</th>
                                    <th scope="col">Сума</th>
                                    <th scope="col" colspan="{{$count_of_maximize}}" style="text-align: center; background-color: #ffac55">Параметри, що максимізуються</th>
                                    <th scope="col">Сума</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="col">Тип ІМС</th>
                                    @foreach($switches as $i => $switch)
                                        @if(!$switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                    <th scope="col">&sum;</th>
                                    @foreach($switches as $i => $switch)
                                        @if($switch)
                                            <th scope="col">{{$param_names[$i]}}</th>
                                        @endif
                                    @endforeach
                                    <th scope="col">&sum;</th>
                                </tr>
                                @foreach($result_data["weight_coefficient_matrix"] as $key => $row)
                                    <tr>
                                        <td>{{$row[0]}}</td>
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && !$switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                        <td>{{$result_data["sum_of_minimize_params"][$key]}}</td>
                                        @foreach($row as $index => $column)
                                            @if($index != 0 && $switches[$index - 1])
                                                <td>{{$column}}</td>
                                            @endif
                                        @endforeach
                                        <td>{{$result_data["sum_of_maximize_params"][$key]}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <br>
                            @foreach($result_data["weight_coefficient_matrix"] as $key => $row)
                                <p class="card-text">{{$row[0]}} = {{$result_data["sum_of_maximize_params"][$key]}} / {{$result_data["sum_of_minimize_params"][$key]}}
                                    {{' = ' . $result_data["sum_of_maximize_params"][$key] / $result_data["sum_of_minimize_params"][$key]}}
                                </p>
                            @endforeach
{{--                            <p class="card-text">Значення: {{$result_data["val"]}}</p>--}}
                            <h5 class="card-text"><b>{{$data["alternative"] == "worse" ? "Гірша" : "Краща"}} альтернатива: {{$result_data["weight_coefficient_matrix"][$result_data["alternative"]][0]}}</b></h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
