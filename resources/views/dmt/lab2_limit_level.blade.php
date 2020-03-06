@extends('layouts.app_dmt')

@section('content')
    {{--    container--}}
    <div class="">
        <div class="row">
            <div class="col-10">
                <form id="showResult" class="form-inline mt-4 mb-4" action={{"/dmt/" . $data["lab"]}}>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["i1"]) && !empty($data["i1"])) {{'value=' . $data["i1"]}} @endif type="number" step="any" name="i1" placeholder="I1 значення" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["i2"]) && !empty($data["i2"])) {{'value=' . $data["i2"]}} @endif type="number" step="any" name="i2" placeholder="I2 значення" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["a1"]) && !empty($data["a1"])) {{'value=' . $data["a1"]}} @endif type="number" step="any" name="a1" placeholder="A1 значення" required>
                    <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["a2"]) && !empty($data["a2"])) {{'value=' . $data["a2"]}} @endif type="number" step="any" name="a2" placeholder="A2 значення" required>

                    <button type="submit" name="submit" value="showResult" class="btn btn-primary my-1">Вивести результат</button>
                </form>

                @if(!empty($data["showResult"]))
                    @php
                        $lab2 = new \App\DMT\Lab2(null);
                        $result_data = array();
                        $result_data = $lab2->limit_level_calculate($data["i1"], $data["i2"], $data["a1"], $data["a2"]);
                    @endphp
                    <div class="card">
                        <h5 class="card-header">Результат:</h5>
                        <div class="card-body">
                            <p class="card-text">ln(I) - (I/{{$data["a2"]}}) = {{$result_data["expected_deficit_limit"]}}</p>
                            <p class="card-text">ln(I) - (I/{{$data["a1"]}}) = {{$result_data["expected_surpluses_limit"]}}</p>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">I</th>
                                    @for($i = $data["i1"]; $i <= $data["i2"]; $i++)
                                        <th scope="col">{{$i}}</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="col">ln(I) - (I/{{$data["a2"]}})</th>
                                    @foreach($result_data["expected_deficit"] as $column)
                                        <td>{{$column}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th scope="col">ln(I) - (I/{{$data["a1"]}})</th>
                                    @foreach($result_data["expected_surpluses"] as $column)
                                        <td>{{$column}}</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                            <p class="card-text">З таблиці видно, що обидві умови виконуються для I, з інтервалу ({{($result_data["suitable_start_interval_index"] + intval($data["i1"])) . "," . ($result_data["suitable_end_interval_index"] + intval($data["i1"]))}})</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
