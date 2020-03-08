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
                                    <th scope="col">Прибуток (грн)</th>
                                    <th scope="col">Кількість випадків</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $approach_number = 0;
                                @endphp
                                @php $approach_number++; @endphp
                                <td colspan="2">Захід № {{$approach_number}}.</td>
                                @foreach($dmt_spreadsheet as $key => $row)
                                    <tr>
                                        @if($row[0] == '-' && $row[1] == '-')
                                            @php $approach_number++; @endphp
                                            <td colspan="2">Захід № {{$approach_number}}.</td>
                                        @else
                                            <td>{{$row[0]}}</td>
                                            <td>{{$row[1]}}</td>
                                        @endif
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
                        $lab2 = new \App\DMT\Lab2($dmt_spreadsheet);
                        $result_data = array();
                        $approach_array = array();
                        $approach_count = count($dmt_spreadsheet);
                        $approach = array();
                        foreach ($dmt_spreadsheet as $key => $row) {
                            if (($row[0] != '-' && $row[1] != '-')) {
                                array_push($approach, array($row[0], $row[1]));
                            } else {
                                array_push($approach_array, $approach);
                                $approach = array();
                            }
                            if ($key == $approach_count - 1) {
                                array_push($approach_array, $approach);
                                $approach = array();
                            }
                        }
                        $result_data = $lab2->average_expected_value_calculate($approach_array, $data["alternative"] == "better");
                    @endphp
                    <div class="card">
                        <h5 class="card-header">Результат:</span></h5>
                        <div class="card-body">
                            @foreach($result_data["new_data"] as $key => $approach)
                                <p class="card-text">Захід № {{$key + 1}}. Середній прибуток склав:
                                @php $approach_count = count($approach) @endphp
                                @foreach($approach as $index => $row)
                                    {{$row[0] . ' x ' . $row[2]}}
                                    @if($index != $approach_count - 1)
                                        {{' + '}}
                                    @endif
                                @endforeach
                                    {{' = ' . $result_data["average_expected_value_array"][$key]}}
                                </p>
                            @endforeach
                            <p class="card-text">Значення: {{$result_data["val"]}}</p>
                            <p class="card-text">Номер рядка альтернативи: {{$result_data["number_of_row_of_alternative"] + 1}}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
