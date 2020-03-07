@extends('layouts.app_dmt')

@section('content')
    {{--    container--}}
    <div class="">
        <div class="row no-container">
            @include('dmt.dmt_sidebar')

            <div class="col-10">
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
                                    <th scope="col">Критерій</th>
                                    <th scope="col">Вага критерію</th>
                                    @for($i = 0; $i < $dmt_spreadsheet_column_count - 2; $i++)
                                        <th scope="col">Стратегія {{chr(65 + $i)}}</th>
                                    @endfor
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
                        $lab4 = new \App\DMT\Lab4($dmt_spreadsheet);
                        $result_data = array();
                        $result_data = $lab4->analysis_of_hierarchies_calculate($lab4->dmt_spreadsheet, $data["alternative"] == "better");
                    @endphp
                    <div class="card">
                        <h5 class="card-header">Результат:</span></h5>
                        <div class="card-body">
                            @foreach($result_data["strategy_array"] as $key => $strategy)
                                <p class="card-text">Університет {{chr(65 + $key)}}:
                                    @foreach($strategy as $index => $addition_element)
                                        @foreach($addition_element as $index_elem => $element)
                                            {{$element}}
                                            @if($index_elem != count($addition_element) - 1)
                                                {{' * '}}
                                            @endif
                                        @endforeach
                                        @if($index != count($strategy) - 1)
                                            {{' + '}}
                                        @endif
                                    @endforeach
                                    {{' = ' . $result_data["strategy_sum"][$key]}}
                                </p>
                            @endforeach
                            <p class="card-text">Значення: {{$result_data["val"]}}</p>
                            <p class="card-text">Альтернатива: {{$result_data["alternative"] + 1}}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
