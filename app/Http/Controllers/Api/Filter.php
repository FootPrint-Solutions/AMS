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

    public function modelFind(Request $request, $vehicle_id)
    {
        try {
            if ($vehicle_id == 'ALL') {
                $vehicleBatterySizeCategory = VehicleBatterySizeCategoryModel::get();
                $vehicleModel = VehicleModel::limit(1)->get();
            } else {
                $vehicleBatterySizeCategory = VehicleBatterySizeCategoryModel::where('vehicle_id', $vehicle_id)->get();
                $vehicleModel = VehicleModel::where('id', $vehicle_id)->first();
            }

            $details = [];

            foreach ($vehicleBatterySizeCategory as $row) {
                $battery = BatterySizeCategoryModel::where('id', $row->battery_size_category_id)
                    ->with(['batteries' => function ($query) use ($request) {
                        if ($request->has('min_price')) {
                            $query->where('price_retail', '>=', $request->min_price);
                        }
                        if ($request->has('max_price')) {
                            $query->where('price_retail', '<=', $request->max_price);
                        }
                        if ($request->has('min_capacity')) {
                            $query->where('capacity', '>=', $request->min_capacity);
                        }
                        if ($request->has('max_capacity')) {
                            $query->where('capacity', '<=', $request->max_capacity);
                        }

                        if ($request->has('selected_value')) {
                            if ($request->selected_value == 'max-price-order') {
                                $query->orderBy('price_retail', 'desc');
                            } elseif ($request->selected_value == 'min-price-order') {
                                $query->orderBy('price_retail', 'asc');
                            } elseif ($request->selected_value == 'max-capacity-order') {
                                $query->orderBy('capacity', 'desc');
                            } elseif ($request->selected_value == 'min-capacity-order') {
                                $query->orderBy('capacity', 'asc');
                            }
                        }

                        if ($request->has('search') && $request->search != 'ALL') {
                            $query->where(function ($q) use ($request) {
                                $q->where('name', 'like', '%' . $request->search . '%');
                            });
                        }
                    }])
                    ->first();

                if ($battery) {
                    foreach ($battery->batteries as $batt) {
                        $details[] = $batt;
                    }
                }
            }

            $details = collect($details)->unique('id')->values();

            $minPriceBattery = $details->sortBy('price_retail')->first();
            $highestCCABattery = $details->sortByDesc('capacity')->first();

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

            if ($request->has('selected_value')) {
                if ($request->selected_value == 'max-price-order') {
                    $sortedDetails = $details->sortByDesc('price_retail')->values();
                } elseif ($request->selected_value == 'min-price-order') {
                    $sortedDetails = $details->sortBy('price_retail')->values();
                } elseif ($request->selected_value == 'max-capacity-order') {
                    $sortedDetails = $details->sortByDesc('capacity')->values();
                } elseif ($request->selected_value == 'min-capacity-order') {
                    $sortedDetails = $details->sortBy('capacity')->values();
                } else {
                    $sortedDetails = $details;
                }
            } else {
                $sortedDetails = $details
                    ->sortByDesc('is_max_price')
                    ->sortByDesc('is_min_price')
                    ->values();
            }
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
