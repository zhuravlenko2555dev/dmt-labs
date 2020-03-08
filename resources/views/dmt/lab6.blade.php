@extends('layouts.app_dmt')

@section('content')
    {{--    container--}}
    <div class="">
        <div class="row no-container">
            <div class="col-md-12 col-lg-10">
                @include('dmt.instruction')
                <form id="showResult" class="form-inline mt-4 mb-4" action={{"/dmt/" . $data["lab"]}}>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["x"]) && !empty($data["x"])) {{'value=' . $data["x"][0]}} @endif type="number" step="any" name="x[]" placeholder="X1" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["x"]) && !empty($data["x"])) {{'value=' . $data["x"][1]}} @endif type="number" step="any" name="x[]" placeholder="X2" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["x"]) && !empty($data["x"])) {{'value=' . $data["x"][2]}} @endif type="number" step="any" name="x[]" placeholder="X3" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["x"]) && !empty($data["x"])) {{'value=' . $data["x"][3]}} @endif type="number" step="any" name="x[]" placeholder="X4" required>

                    <button type="submit" name="submit" value="showResult" class="btn btn-primary my-1">Вивести результат</button>
                </form>

                @if(!empty($data["showResult"]))
                    @php
                        $lab6 = new \App\DMT\Lab6($data["x"]);
                        $result_data = array();
                        $result_data = $lab6->priority_setting_calculate($lab6->dmt_spreadsheet);

                    @endphp
                    <div class="card">
                        <h5 class="card-header">Результат:</h5>
                        <div class="card-body">
                            <p class="card-text">Квадратна матриця суміжності:</p>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">I \ J</th>
                                    @for($i = 0; $i < count($result_data["adjacency_matrix"][0]); $i++)
                                        <th scope="col">X<sub>{{$i + 1}}</sub></th>
                                    @endfor
                                    <th scope="col">&sum;a<sub>ij</sub></th>
                                    <th scope="col">P<sub>i</sub><sup>0(1)</sup></th>
                                    <th scope="col">P<sub>i</sub><sup>(2)</sup></th>
                                    <th scope="col">P<sub>i</sub><sup>0(2)</sup></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($result_data["adjacency_matrix"] as $index => $row)
                                    <tr>
                                        <th scope="col">X<sub>{{$index + 1}}</sub></th>
                                        @foreach($row as $column)
                                            <td>{{$column}}</td>
                                        @endforeach
                                        <td>{{$result_data["sum_by_row"][$index]}}</td>
                                        <td>{{$result_data["p"][0][$index]}}</td>
                                        <td>{{$result_data["p"][1][$index]}}</td>
                                        <td>{{$result_data["p"][2][$index]}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="{{count($result_data["adjacency_matrix"][0]) + 1}}"></td>
                                    <td>{{array_sum($result_data["sum_by_row"])}}</td>
                                    <td>{{array_sum($result_data["p"][0])}}</td>
                                    <td>{{array_sum($result_data["p"][1])}}</td>
                                    <td>{{array_sum($result_data["p"][2])}}</td>
                                </tr>
                                </tbody>
                            </table>
                            @foreach($result_data["adjacency_matrix"] as $key => $row)
                                <p class="card-text">P<sub>{{$key + 1}}</sub>(2) =
                                    @foreach($row as $index => $column)
                                        {{$column}}*{{$result_data["sum_by_row"][$index]}}
                                        @if($index != count($row) - 1)
                                            {{' + '}}
                                        @endif
                                    @endforeach
                                    {{' = ' . $result_data["p"][1][$key]}}
                                </p>
                            @endforeach
                            <br>
                            @foreach($result_data["p"][1] as $key => $row)
                                <p class="card-text">P<sub>{{$key + 1}}</sub><sup>0</sup> = {{$row}}/{{array_sum($result_data["p"][1])}}
                                    {{' = ' . $row / array_sum($result_data["p"][1])}}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
