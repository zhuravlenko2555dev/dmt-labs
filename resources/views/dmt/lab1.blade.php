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
                                    <th scope="col">#</th>
                                    @for($i = 0; $i < $dmt_spreadsheet_column_count; $i++)
                                        <th scope="col">F{{$i + 1}}</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dmt_spreadsheet as $key => $row)
                                    <tr>
                                        <th scope="row">A{{++$key}}</th>
                                        @foreach($row as $column)
                                            <td>{{$column}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <th scope="row"></th>
                                    @for($i = 0; $i < $dmt_spreadsheet_column_count; $i++)
                                        <td class="probability" style="display: @if(isset($data["criterion"])) {{$data["criterion"] == "bayesa_laplasa" ? "" : "none"}} @else none @endif"><input form="showResult" class="form-control" style="width: 90px;"@if(isset($data["criterion"])) {{$data["criterion"] == "bayesa_laplasa" ? "required value=" . $data["probability"][$i] : "disabled"}} @else disabled @endif type="number" step="any" name="probability[]" placeholder="ймов. {{$i+1}}"></td>
                                    @endfor
                                </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <script>
                    function onSelectCriterion(element) {
                        if (element.value === "bayesa_laplasa") {
                            Array.from(document.getElementsByClassName("probability")).forEach(
                                function(element, index, array) {
                                    element.style.display = "table-cell";
                                    element.getElementsByClassName("form-control")[0].required = true;
                                    element.getElementsByClassName("form-control")[0].disabled = false;
                                }
                            );

                            document.getElementsByClassName("coefficient-of-pessimism")[0].style.display = "none";
                            document.getElementsByClassName("coefficient-of-pessimism")[0].required = false;
                            document.getElementsByClassName("coefficient-of-pessimism")[0].disabled = true;
                        } else if (element.value === "gurvitsa") {
                            document.getElementsByClassName("coefficient-of-pessimism")[0].style.display = "inline-block";
                            document.getElementsByClassName("coefficient-of-pessimism")[0].required = true;
                            document.getElementsByClassName("coefficient-of-pessimism")[0].disabled = false;

                            Array.from(document.getElementsByClassName("probability")).forEach(
                                function(element, index, array) {
                                    element.style.display = "none";
                                    element.getElementsByClassName("form-control")[0].required = false;
                                    element.getElementsByClassName("form-control")[0].disabled = true;
                                }
                            );
                        } else {
                            Array.from(document.getElementsByClassName("probability")).forEach(
                                function(element, index, array) {
                                    element.style.display = "none";
                                    element.getElementsByClassName("form-control")[0].required = false;
                                    element.getElementsByClassName("form-control")[0].disabled = true;
                                }
                            );

                            document.getElementsByClassName("coefficient-of-pessimism")[0].style.display = "none";
                            document.getElementsByClassName("coefficient-of-pessimism")[0].required = false;
                            document.getElementsByClassName("coefficient-of-pessimism")[0].disabled = true;
                        }
                    }
                </script>

                @if(!empty($data["file_selected"]))
                    <form id="showResult" class="form-inline mt-4 mb-4" action={{"/dmt/" . $data["lab"]}}>
                        <input class="form-control mr-2" type="hidden" name="file_selected" value="{{$data["file_selected"]}}">
                        <input class="form-control mr-2 coefficient-of-pessimism" style="width: 110px; display: @if(isset($data["criterion"])) {{$data["criterion"] == "gurvitsa" ? "" : "none"}} @else none @endif" @if(isset($data["criterion"])) {{$data["criterion"] == "gurvitsa" ? "required value=" . $data["coefficient-of-pessimism"] : "disabled"}} @else disabled @endif type="number" step="any" name="coefficient-of-pessimism" placeholder="коеф. песим.">
                        <label class="my-1 mr-2" for="selectCriterion">Виберіть критерій:</label>
                        <select class="custom-select my-1 mr-sm-2" id="selectCriterion" name="criterion" required onchange="onSelectCriterion(this)">
                            <option value="maxymax" selected>Максимасу</option>
                            <option value="valda" @if(isset($data["criterion"])) {{$data["criterion"] == "valda" ? "selected" : ""}} @endif>Вальда</option>
                            <option value="gurvitsa" @if(isset($data["criterion"])) {{$data["criterion"] == "gurvitsa" ? "selected" : ""}} @endif>Песимізму-оптимізму Гурвиця</option>
                            <option value="bayesa_laplasa" @if(isset($data["criterion"])) {{$data["criterion"] == "bayesa_laplasa" ? "selected" : ""}} @endif>Байєса-Лапласа</option>
                        </select>

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
                        $lab1 = new \App\DMT\Lab1($dmt_spreadsheet);
                        $result_data = array();
                        switch ($data["criterion"]) {
                            case "maxymax":
                                $result_data = $lab1->maxymax_calculate($lab1->dmt_spreadsheet, $data["alternative"] == "better");
                            break;
                            case "valda":
                                $result_data = $lab1->valda_calculate($lab1->dmt_spreadsheet, $data["alternative"] == "better");
                            break;
                            case "gurvitsa":
                                $q = floatval($data["coefficient-of-pessimism"]);
                                $result_data = $lab1->gurvitsa_calculate($lab1->dmt_spreadsheet, $q, $data["alternative"] == "better");
                            break;
                            case "bayesa_laplasa":
                                $probability = array_map(function ($v) {return floatval($v);}, $data["probability"]);
                                $result_data = $lab1->bayesa_laplasa_calculate($lab1->dmt_spreadsheet, $probability, $data["alternative"] == "better");
                            break;
                        }
                    @endphp
                    <div class="card">
                        <h5 class="card-header" style="color: green"><b>Результат:</b></h5>
                        <div class="card-body">
{{--                            <p class="card-text">Значення: {{$result_data["val"]}}</p>--}}`
                            <h5 class="card-text"><b>{{$data["alternative"] == "worse" ? "Гірша" : "Краща"}} альтернатива: A{{$result_data["number_of_row_of_alternative"] + 1}}</b></h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
