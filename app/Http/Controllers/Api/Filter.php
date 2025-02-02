<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use Illuminate\Http\Request;

class Filter extends Controller
{
    // brand, model, starting_year, ending_year, fuel_type, transmission, battery_size_category, alternate_battery_size_category


    public function brand(Request $request)
    {
        try {
            $brands = VehicleBrandModel::where('status', 1)->get()->toArray();
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
            $models = VehicleModel::where(['brand_id' => $brand, 'status' => 1])->with('batterySizeCategories', 'year', 'fuelVehicle', 'transmission')->get()->toArray();
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
                'data' => $e->getMessage()
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
                if ($row[0] == $model) {
                    $battery = BatterySizeCategoryModel::where('name', $row[6])->with('batteries')->first();
                    $alternateBattery = BatterySizeCategoryModel::where('name', $row[7])->with('batteries')->first();

                    $batteries = $battery ? $battery->batteries : collect();
                    $alternateBatteries = $alternateBattery ? $alternateBattery->batteries : collect();

                    $allBatteries = $batteries->merge($alternateBatteries);
                    $maxPriceBattery = $allBatteries->sortByDesc('price_retail')->first();
                    $minPriceBattery = $allBatteries->sortBy('price_retail')->first();

                    $details[] = [
                        'model' => $row[0],
                        'brand' => $row[5],
                        'starting_year' => $row[1],
                        'ending_year' => $row[2],
                        'fuel_type' => $row[3],
                        'transmission' => $row[4],
                        'battery_size_category' => $row[6],
                        'batteries' => $batteries,
                        'alternate_battery_size_category' => $row[7],
                        'alternate_batteries' => $alternateBatteries,
                        'max_price_battery' => $maxPriceBattery,
                        'min_price_battery' => $minPriceBattery
                    ];
                }
            }

            foreach ($details as $key => $detail) {
                foreach ($detail['batteries'] as $batteryKey => $battery) {
                    $details[$key]['batteries'][$batteryKey]['is_max_price'] = $battery['id'] == $detail['max_price_battery']['id'];
                    $details[$key]['batteries'][$batteryKey]['is_min_price'] = $battery['id'] == $detail['min_price_battery']['id'];
                }

                foreach ($detail['alternate_batteries'] as $altBatteryKey => $altBattery) {
                    $details[$key]['alternate_batteries'][$altBatteryKey]['is_max_price'] = $altBattery['id'] == $detail['max_price_battery']['id'];
                    $details[$key]['alternate_batteries'][$altBatteryKey]['is_min_price'] = $altBattery['id'] == $detail['min_price_battery']['id'];
                }
            }

            foreach ($details as $key => $detail) {
                unset($details[$key]['max_price_battery']);
                unset($details[$key]['min_price_battery']);
            }

            if (empty($details)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
                'data' => []
            ], 500);
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
