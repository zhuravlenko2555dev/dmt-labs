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
                            @endphp
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">T</th>
                                    <th scope="col">p<sub>t</sub></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dmt_spreadsheet as $key => $row)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$row[0]}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                @if(!empty($data["file_selected"]))
                    <form id="showResult" class="form-inline mt-4 mb-4" action={{"/dmt/" . $data["lab"]}}>
                        <input class="form-control mr-2" type="hidden" name="file_selected" value="{{$data["file_selected"]}}">

                        <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["c1"]) && !empty($data["c1"])) {{'value=' . $data["c1"]}} @endif type="number" step="any" name="c1" placeholder="C1 значення" required>
                        <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["c2"]) && !empty($data["c2"])) {{'value=' . $data["c2"]}} @endif type="number" step="any" name="c2" placeholder="C2 значення" required>
                        <input class="form-control mr-2 coefficient-of-pessimism" style="width: 130px" @if(isset($data["n"]) && !empty($data["n"])) {{'value=' . $data["n"]}} @endif type="number" step="any" name="n" placeholder="n значення" required>

                        <button type="submit" name="submit" value="showResult" class="btn btn-primary my-1">Вивести результат</button>
                    </form>
                @endif

                @if(!empty($data["showResult"]))
                    @php
                        $lab2 = new \App\DMT\Lab2($dmt_spreadsheet);
                        $result_data = array();
                        $result_data = $lab2->expected_value_calculate($lab2->dmt_spreadsheet, $data["c1"], $data["c2"], $data["n"]);
                    @endphp
                    <div class="card">
                        <h5 class="card-header">Результат:</span></h5>
                        <div class="card-body">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">T</th>
                                    <th scope="col">p<sub>t</sub></th>
                                    <th scope="col">&sum;<sub>t=1</sub><sup>T-1</sup>p<sub>t</sub></th>
                                    <th scope="col">OB(T)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($result_data["new_data"] as $key => $row)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$row[0]}}</td>
                                        <td>{{$row[1]}}</td>
                                        <td>{{$row[2]}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <p class="card-text">Значення: {{$result_data["val"]}}</p>
                            <p class="card-text">Номер рядка альтернативи: {{$result_data["number_of_row_of_alternative"] + 1}}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
