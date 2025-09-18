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
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

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

                        if ($request->has('selected_valuex')) { // ini tidak terpakai, sengaja di ubah untuk menghindari error
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

                if ($battery && isset($battery->batteries)) {
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

            if ($request->has('selected_valuex')) { // ini tidak terpakai, sengaja di ubah untuk menghindari error
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

            // Apply pagination to final results
            $paginatedDetails = $sortedDetails->skip($offset)->take($limit);

            if ($paginatedDetails->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => [
                    'batteries' => $paginatedDetails->values(),
                    'vehicle' => $vehicleModel,
                    'pagination' => [
                        'total' => $sortedDetails->count(),
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => ($offset + $limit) < $sortedDetails->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
                'data' => $e->getMessage()
            ], 500);
        }
    }


    public function modelFindNew(Request $request, $vehicle_id)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $offset = (int) $request->input('offset', 0);

            // Ambil vehicleModel dan vehicleBatterySizeCategory sekaligus
            if ($vehicle_id == 'ALL') {
                $vehicleBatterySizeCategory = VehicleBatterySizeCategoryModel::select('battery_size_category_id')->distinct()->get();
                $vehicleModel = VehicleModel::limit(1)->get();
            } else {
                $vehicleBatterySizeCategory = VehicleBatterySizeCategoryModel::where('vehicle_id', $vehicle_id)
                    ->select('battery_size_category_id')->distinct()->get();
                $vehicleModel = VehicleModel::where('id', $vehicle_id)->first();
            }

            $batterySizeCategoryIds = $vehicleBatterySizeCategory->pluck('battery_size_category_id')->unique()->toArray();

            // Query batteries langsung dengan filter dan eager loading
            $batteryQuery = BatteryModel::whereIn('size_category_id', $batterySizeCategoryIds)
                ->where('status', 1)
                ->with(['batteryPrices', 'batteryImages']); // eager load prices & images

            if ($request->has('min_price')) {
                $batteryQuery->where('price_retail', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $batteryQuery->where('price_retail', '<=', $request->max_price);
            }
            if ($request->has('min_capacity')) {
                $batteryQuery->where('capacity', '>=', $request->min_capacity);
            }
            if ($request->has('max_capacity')) {
                $batteryQuery->where('capacity', '<=', $request->max_capacity);
            }
            if ($request->has('search') && $request->search != 'ALL') {
                $batteryQuery->where('name', 'like', '%' . $request->search . '%');
            }

            // Sorting
            if ($request->has('selected_valuex')) {
                if ($request->selected_value == 'max-price-order') {
                    $batteryQuery->orderBy('price_retail', 'desc');
                } elseif ($request->selected_value == 'min-price-order') {
                    $batteryQuery->orderBy('price_retail', 'asc');
                } elseif ($request->selected_value == 'max-capacity-order') {
                    $batteryQuery->orderBy('capacity', 'desc');
                } elseif ($request->selected_value == 'min-capacity-order') {
                    $batteryQuery->orderBy('capacity', 'asc');
                }
            }

            // Ambil semua batteries
            $allBatteries = $batteryQuery->get();

            // Cari min price dan max capacity
            $minPriceBattery = $allBatteries->sortBy('price_retail')->first();
            $highestCCABattery = $allBatteries->sortByDesc('capacity')->first();

            // Format result sesuai kebutuhan frontend
            $details = $allBatteries->map(function ($item) use ($minPriceBattery, $highestCCABattery) {
                // Ambil gambar pertama jika ada
                $image = null;
                if ($item->batteryImages && $item->batteryImages->count() > 0) {
                    $image = $item->batteryImages[0]->image ?? null;
                }

                // Ambil harga retail/net dari relasi batteryPrices
                $battery_prices = [];
                if ($item->batteryPrices && $item->batteryPrices->count() > 0) {
                    foreach ($item->batteryPrices as $price) {
                        $battery_prices[] = [
                            'price_retail' => $price->price_retail,
                            'price_net' => $price->price_net,
                        ];
                    }
                } else {
                    $battery_prices[] = [
                        'price_retail' => $item->price_retail,
                        'price_net' => $item->price_net,
                    ];
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'image' => $image,
                    'price_retail' => $item->price_retail,
                    'price_net' => $item->price_net,
                    'battery_prices' => $battery_prices,
                    'warranty' => $item->warranty,
                    'dimension_length' => $item->dimension_length,
                    'dimension_width' => $item->dimension_width,
                    'dimension_height' => $item->dimension_height,
                    'capacity' => $item->capacity,
                    'standard_cca' => $item->standard_cca,
                    'is_min_price' => ($minPriceBattery && $item->id === $minPriceBattery->id),
                    'is_max_price' => ($highestCCABattery && $item->id === $highestCCABattery->id),
                ];
            });

            // Sorting jika tidak ada selected_valuex
            if (!$request->has('selected_valuex')) {
                $details = $details->sortByDesc('is_max_price')->sortByDesc('is_min_price')->values();
            }

            // Pagination
            $paginatedDetails = $details->slice($offset, $limit)->values();

            if ($paginatedDetails->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => [
                    'batteries' => $paginatedDetails,
                    'vehicle' => $vehicleModel,
                    'pagination' => [
                        'total' => $details->count(),
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => ($offset + $limit) < $details->count()
                    ]
                ]
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
            $battery = BatteryModel::where('id', $battery)->with('brand', 'subbrandCategory', 'sizeCategory', 'technology', 'code', 'batteryPrices', 'vehicleBattery', 'batteryUrl', 'batteryImages')->get();
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
