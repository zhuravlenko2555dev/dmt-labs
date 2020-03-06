<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Str;

class DMTController extends Controller {
    public function index(Request $request, $lab = "1") {
        $data = array();
        $data["lab"] = $lab;
        $data["showResult"] = $request->get('submit', '') == "showResult";
        $data["criterion"] = $request->get('criterion', 'maxymax');
        $data["alternative"] = $request->get('alternative', 'better');
        $data["probability"] = $request->get('probability', array());
        $data["coefficient"] = $request->get('coefficient', array());
        $data["maximize_params"] = $request->get('maximize_params', array());
        $data["x"] = $request->get('x', array());
        $data["coefficient-of-pessimism"] = $request->get('coefficient-of-pessimism', 0);

        $data["c1"] = $request->get('c1', '');
        $data["c2"] = $request->get('c2', '');
        $data["n"] = $request->get('n', '');

        $data["i1"] = $request->get('i1', '');
        $data["i2"] = $request->get('i2', '');
        $data["a1"] = $request->get('a1', '');
        $data["a2"] = $request->get('a2', '');
        if ($lab == "lab2_limit_level") {

        } else {
            $data["files"] = array();
            if ($request->session()->has("files_" . $lab) && !empty($request->session()->has("files_" . $lab))) {
                $data["files"] = $request->session()->get("files_" . $lab);
                $data["file_selected"] = $request->get('file_selected', array_values($request->session()->get("files_" . $lab))[0]["uuid"]);//uuid of file
            } else {
                $data["file_selected"] = $request->get('file_selected', "");//uuid of file
            }
        }

        switch ($lab) {
            case "lab1":
                $return_view = 'dmt.lab1';
                break;
            case "lab2_savage":
                $return_view = 'dmt.lab2_savage';
                break;
            case "lab2_expected_value":
                $return_view = 'dmt.lab2_expected_value';
                break;
            case "lab2_expected_value_dispersion":
                $return_view = 'dmt.lab2_expected_value_dispersion';
                break;
            case "lab2_average_expected_value":
                $return_view = 'dmt.lab2_average_expected_value';
                break;
            case "lab2_limit_level":
                $return_view = 'dmt.lab2_limit_level';
                break;
            case "lab3":
                $return_view = 'dmt.lab3';
                break;
            case "lab4":
                $return_view = 'dmt.lab4';
                break;
            case "lab5":
                $return_view = 'dmt.lab5';
                break;
            case "lab6":
                $return_view = 'dmt.lab6';
                break;
            default:
                return redirect("/dmt/lab1");
                break;
        }

        return view($return_view, ['data' => $data]);
    }
    public function uploadFile(Request $request, $lab = "1") {
        $data = array();
        $dataFile = array();

        $file = $request->file('excel');
        $destinationPath = 'uploads';
        $uuid_of_file = (string) Str::uuid();
        $file_extension = $file->getClientOriginalExtension();
        $file->move($destinationPath, $uuid_of_file . "." . $file_extension);

        $dataFile["original_name"] = $file->getClientOriginalName();
        $dataFile["real_path"] = public_path() . "\\". $destinationPath . "\\" . $uuid_of_file . "." . $file_extension;
        $dataFile["uuid"] = $uuid_of_file;
        $request->session()->put("files_" . $lab . "." . $dataFile["uuid"], $dataFile);

        $data["lab"] = $lab;
        if ($lab == "lab2_limit_level") {

        } else {
            $data["file_selected"] = $dataFile["uuid"];
            $data["files"] = array();
            if ($request->session()->has("files_" . $lab)) {
                $data["files"] = $request->session()->get("files_" . $lab);
            }
        }

        switch ($lab) {
            case "lab1":
                $return_view = 'dmt.lab1';
                break;
            case "lab2_savage":
                $return_view = 'dmt.lab2_savage';
                break;
            case "lab2_expected_value":
                $return_view = 'dmt.lab2_expected_value';
                break;
            case "lab2_expected_value_dispersion":
                $return_view = 'dmt.lab2_expected_value_dispersion';
                break;
            case "lab2_average_expected_value":
                $return_view = 'dmt.lab2_average_expected_value';
                break;
            case "lab2_limit_level":
                $return_view = 'dmt.lab2_limit_level';
                break;
            case "lab3":
                $return_view = 'dmt.lab3';
                break;
            case "lab4":
                $return_view = 'dmt.lab4';
                break;
            case "lab5":
                $return_view = 'dmt.lab5';
                break;
            case "lab6":
                $return_view = 'dmt.lab6';
                break;
        }

        return view($return_view, ['data' => $data]);
    }
//    public function deleteFile(Request $request) {
//        $data = array();
//        $uuid = $request->get('uuid', "");
//
//        $dataFile = $request->session()->get("files." . $uuid);
//        File::delete($dataFile["real_path"]);
//        $request->session()->forget("files." . $uuid);
//
//        $data["files"] = array();
//        if ($request->session()->has("files") && !empty($request->session()->has("files"))) {
//            $data["files"] = $request->session()->get("files");
//            $data["file_selected"] = $request->get('file_selected', array_values($request->session()->get("files"))[0]["uuid"]);//uuid of file
//        } else {
//            $data["file_selected"] = $request->get('file_selected', "");//uuid of file
//        }
//
//        return view('dmt', ['data' => $data]);
//    }
}
