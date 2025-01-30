<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use Illuminate\Http\Request;

class Filter extends Controller
{
    // brand, model, starting_year, ending_year, fuel_type, transmission, battery_size_category, alternate_battery_size_category


    public function brand(Request $request)
    {
        try {
            $file = public_path('template/excel/Detailed_Vehicle_Database.xlsx');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->rangeToArray('A2:J' . $sheet->getHighestRow());

            $brands = [];
            foreach ($data as $row) {
                if (!empty($row[5])) {
                    $brands[] = $row[5];
                }
            }

            $brands = array_unique($brands);
            $brands = array_values($brands); // Re-index the array
            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $brands
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    public function brandFind($brand)
    {
        try {
            $file = public_path('template/excel/Detailed_Vehicle_Database.xlsx');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->rangeToArray('A2:J' . $sheet->getHighestRow());

            $models = [];
            foreach ($data as $row) {
                if ($row[5] == $brand) {
                    $models[] = $row[0];
                }
            }

            $models = array_unique($models);
            $models = array_values($models); // Re-index the array

            // jika data tidak ditemukan
            if (empty($models)) {
                $response = [
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ];

                return response()->json($response, 404);
            }

            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $models
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    public function modelFind($model)
    {
        try {
            $file = public_path('template/excel/Detailed_Vehicle_Database.xlsx');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->rangeToArray('A2:J' . $sheet->getHighestRow());

            $details = [];
            foreach ($data as $row) {
                $battery = BatterySizeCategoryModel::where('name', $row[6])->with('batteries')->first();
                $alternateBattery = BatterySizeCategoryModel::where('name', $row[7])->with('batteries')->first();
                if ($row[0] == $model) {
                    $details[] = [
                        'model' => $row[0],
                        'brand' => $row[5],
                        'starting_year' => $row[1],
                        'ending_year' => $row[2],
                        'fuel_type' => $row[3],
                        'transmission' => $row[4],
                        'battery_size_category' => $row[6],
                        'batteries' => $battery->batteries,
                        'alternate_battery_size_category' => $row[7],
                        'alternate_batteries' => $alternateBattery->batteries
                    ];
                }
            }

            // jika data tidak ditemukan
            if (empty($details)) {
                $response = [
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ];

                return response()->json($response, 404);
            }

            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $details
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    function battery()
    {
        try {
            $batteries = BatteryModel::all();

            $response = [
                'status' => 'success',
                'message' => 'Data found',
                'data' => $batteries
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    function batteryFind($battery)
    {
        try {
            $battery = BatteryModel::where('id', $battery)->with('brand', 'subbrandCategory', 'sizeCategory', 'technology', 'code', 'batteryPrices', 'vehicleBattery', 'batteryUrl')->get();
            // jika data tidak ditemukan
            if (empty($battery) || $battery->isEmpty()) {
                $response = [
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ];

                return response()->json($response, 404);
            } else {

                $response = [
                    'status' => 'success',
                    'message' => 'Data found',
                    'data' => $battery
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }
}
