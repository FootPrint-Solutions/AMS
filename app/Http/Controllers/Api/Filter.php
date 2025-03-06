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

            $details = [];

            foreach ($vehicleBatterySizeCategory as $row) {
                $battery = BatterySizeCategoryModel::where('id', $row->battery_size_category_id)->with('batteries')->first();

                if ($battery) {
                    foreach ($battery->batteries as $batt) {
                        $details[] = $batt;
                    }
                }
            }

            $details = collect($details)->unique('id')->values();

            $minPriceBattery = $details->sortBy('price_retail')->first();
            $highestCCABattery = $details->sortByDesc('cca')->first();

            if ($minPriceBattery) {
                $details = $details->map(function ($item) use ($minPriceBattery) {
                    if ($item['id'] === $minPriceBattery['id']) {
                        $item['is_min_price'] = true;
                    }
                    return $item;
                });
            }

            if ($highestCCABattery) {
                $details = $details->map(function ($item) use ($highestCCABattery) {
                    if ($item['id'] === $highestCCABattery['id']) {
                        $item['is_max_price'] = true;
                    }
                    return $item;
                });
            }

            $sortedDetails = $details
                ->sortByDesc('is_max_price')
                ->sortByDesc('is_min_price')
                ->values();

            if ($sortedDetails->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => ['batteries' => $sortedDetails, 'vehicle' => $vehicleModel]
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
