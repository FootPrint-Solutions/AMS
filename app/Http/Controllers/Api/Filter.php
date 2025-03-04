<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MasterData\Battery\Battery;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Vehicle\VehicleBatterySizeCategoryModel;
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

    public function modelFind($vehicle_id)
    {
        try {
            $vehicleBatterySizeCategory = VehicleBatterySizeCategoryModel::where('vehicle_id', $vehicle_id)->get();
            $vehicleModel = VehicleModel::where('id', $vehicle_id)->first();
            $data = $vehicleBatterySizeCategory->toArray();

            $details = [];
            foreach ($data as $row) {
                $battery = BatterySizeCategoryModel::where('id', $row['battery_size_category_id'])->with('batteries')->first();

                $batteries = $battery ? $battery->batteries : collect();

                $maxPriceBattery = $batteries->sortByDesc('cca')->first();
                $minPriceBattery = $batteries->sortBy('price_retail')->first();

                foreach ($batteries as $battery) {
                    $battery['is_max_price'] = false;
                    $battery['is_min_price'] = false;
                    $details[] = $battery;
                }

                if ($maxPriceBattery) {
                    $maxPriceBattery['is_max_price'] = true;
                }

                if ($minPriceBattery) {
                    $minPriceBattery['is_min_price'] = true;
                }
            }

            $details = collect($details)->unique('id')->values()->all();
            foreach ($details as $key => $value) {
                $details[$key]['is_max_price'] = false;
                $details[$key]['is_min_price'] = false;
            }

            $maxPriceBattery = collect($details)->sortByDesc('price_retail')->first();
            $minPriceBattery = collect($details)->sortBy('price_retail')->first();

            if ($maxPriceBattery) {
                $maxPriceBattery['is_max_price'] = true;
            }

            if ($minPriceBattery) {
                $minPriceBattery['is_min_price'] = true;
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
                'data' => ['batteries' => $details, 'vehicle' => $vehicleModel]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
                'data' => $e->getMessage()
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

    function searchBattery($batteryName)
    {
        try {
            $batteries = BatteryModel::where('name', 'like', '%' . $batteryName . '%')->where('status', 1)->with('batteryUrl')->limit(3)->get();

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
}
