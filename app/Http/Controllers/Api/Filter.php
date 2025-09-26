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
            $brands = VehicleBrandModel::where(['status' => 1, 'visible' => 1])->get()->toArray();
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

            // Query batteries langsung dengan filter dan eager loading lengkap
            $batteryQuery = BatteryModel::whereIn('size_category_id', $batterySizeCategoryIds)
                ->where('status', 1)
                ->with(['batteryImages', 'batteryPrices', 'batteryUrl']); // Add all required relations

            // Apply filters
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

            // Apply sorting at database level if selected_value is provided
            if ($request->has('selected_value')) {
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

            // Get all batteries
            $allBatteries = $batteryQuery->get();

            // Convert to collection and create details array with complete database structure
            $details = collect();
            foreach ($allBatteries as $batt) {
                // Get main image from batteryImages relation
                $image = null;
                if ($batt->batteryImages && $batt->batteryImages->count() > 0) {
                    $image = $batt->batteryImages->first()->image ?? $batt->batteryImages->first()->url ?? null;
                }

                // Get battery_url data
                $battery_url = [];
                if ($batt->batteryUrl && $batt->batteryUrl->count() > 0) {
                    $battery_url = $batt->batteryUrl->toArray();
                }

                // Get battery_prices data
                $battery_prices = [];
                if ($batt->batteryPrices && $batt->batteryPrices->count() > 0) {
                    $battery_prices = $batt->batteryPrices->toArray();
                }

                // Create complete battery data structure matching the required format
                $batteryData = [
                    'id' => $batt->id,
                    'name' => $batt->name,
                    'name_alternate' => $batt->name_alternate ?? null,
                    'brand_id' => $batt->brand_id ?? null,
                    'subbrand_category_id' => $batt->subbrand_category_id ?? null,
                    'usage_type_id' => $batt->usage_type_id ?? null,
                    'size_category_id' => $batt->size_category_id ?? null,
                    'technology_id' => $batt->technology_id ?? null,
                    'dimension_length' => $batt->dimension_length ?? null,
                    'dimension_width' => $batt->dimension_width ?? null,
                    'dimension_height' => $batt->dimension_height ?? null,
                    'standard_cca' => $batt->standard_cca ?? null,
                    'capacity' => $batt->capacity,
                    'warranty' => $batt->warranty ?? null,
                    'price_retail' => $batt->price_retail,
                    'image' => $image,
                    'status' => $batt->status ?? 1,
                    'type' => $batt->type ?? 'regular',
                    'editable_price' => $batt->editable_price ?? 0,
                    'created_at' => $batt->created_at,
                    'updated_at' => $batt->updated_at,
                    'deleted_at' => $batt->deleted_at,
                    'battery_url' => $battery_url,
                    'battery_prices' => $battery_prices,
                ];

                $details->push($batteryData);
            }

            // Remove duplicates by id and reindex (matching original logic)
            $details = $details->unique('id')->values();

            // Find min price and max capacity batteries
            $minPriceBattery = $details->sortBy('price_retail')->first();
            $highestCCABattery = $details->sortByDesc('capacity')->first();

            // Add flags for min price and max capacity (matching original logic)
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

            // Apply sorting based on selected_value (matching original logic)
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
                // Default sorting: max_price first, then min_price (matching original)
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

    public function modelFindOptimized(Request $request, $vehicle_id)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $offset = (int) $request->input('offset', 0);

            // Single query untuk mendapatkan battery IDs yang sesuai dengan vehicle
            if ($vehicle_id == 'ALL') {
                $batterySizeCategoryIds = VehicleBatterySizeCategoryModel::distinct()
                    ->pluck('battery_size_category_id')
                    ->toArray();
                $vehicleModel = VehicleModel::limit(1)->get();
            } else {
                $batterySizeCategoryIds = VehicleBatterySizeCategoryModel::where('vehicle_id', $vehicle_id)
                    ->distinct()
                    ->pluck('battery_size_category_id')
                    ->toArray();
                $vehicleModel = VehicleModel::find($vehicle_id);
            }

            if (empty($batterySizeCategoryIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            // Build query dengan subquery untuk menghitung min/max
            $batteryQuery = BatteryModel::whereIn('size_category_id', $batterySizeCategoryIds)
                ->where('status', 1);

            // Apply filters
            if ($request->filled('min_price')) {
                $batteryQuery->where('price_retail', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $batteryQuery->where('price_retail', '<=', $request->max_price);
            }
            if ($request->filled('min_capacity')) {
                $batteryQuery->where('capacity', '>=', $request->min_capacity);
            }
            if ($request->filled('max_capacity')) {
                $batteryQuery->where('capacity', '<=', $request->max_capacity);
            }
            if ($request->filled('search') && $request->search != 'ALL') {
                $batteryQuery->where('name', 'like', '%' . $request->search . '%');
            }

            // Get total count for pagination
            $totalCount = $batteryQuery->count();

            if ($totalCount === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            // Get min price and max capacity in single queries
            $minPriceId = (clone $batteryQuery)->orderBy('price_retail', 'asc')->value('id');
            $maxCapacityId = (clone $batteryQuery)->orderBy('capacity', 'desc')->value('id');

            // Apply sorting and pagination
            if ($request->filled('selected_value')) {
                switch ($request->selected_value) {
                    case 'max-price-order':
                        $batteryQuery->orderBy('price_retail', 'desc');
                        break;
                    case 'min-price-order':
                        $batteryQuery->orderBy('price_retail', 'asc');
                        break;
                    case 'max-capacity-order':
                        $batteryQuery->orderBy('capacity', 'desc');
                        break;
                    case 'min-capacity-order':
                        $batteryQuery->orderBy('capacity', 'asc');
                        break;
                }
            } else {
                // Custom ordering: min price and max capacity first
                $batteryQuery->orderByRaw("CASE WHEN id = ? THEN 0 WHEN id = ? THEN 1 ELSE 2 END", [$maxCapacityId, $minPriceId]);
            }

            // Get paginated results with complete eager loading
            $batteries = $batteryQuery->with(['batteryImages', 'batteryPrices', 'batteryUrl'])
                ->skip($offset)->take($limit)->get();

            // Add flags and format data with complete database structure
            $details = $batteries->map(function ($battery) use ($minPriceId, $maxCapacityId) {
                // Get main image from batteryImages relation
                // dd($battery);
                $image = null;
                if ($battery->batteryImages && $battery->batteryImages->count() > 0) {
                    $image = $battery->batteryImages->first()->image ?? $battery->batteryImages->first()->url ?? null;
                }

                // Get battery_url data
                $battery_url = [];
                if ($battery->batteryUrl && $battery->batteryUrl->count() > 0) {
                    $battery_url = $battery->batteryUrl->toArray();
                }

                // Get battery_prices data
                $battery_prices = [];
                if ($battery->batteryPrices && $battery->batteryPrices->count() > 0) {
                    $battery_prices = $battery->batteryPrices->toArray();
                }

                // Create complete battery data structure
                $batteryData = [
                    'id' => $battery->id,
                    'name' => $battery->name,
                    'name_alternate' => $battery->name_alternate ?? null,
                    'brand_id' => $battery->brand_id ?? null,
                    'subbrand_category_id' => $battery->subbrand_category_id ?? null,
                    'usage_type_id' => $battery->usage_type_id ?? null,
                    'size_category_id' => $battery->size_category_id ?? null,
                    'technology_id' => $battery->technology_id ?? null,
                    'dimension_length' => $battery->dimension_length ?? null,
                    'dimension_width' => $battery->dimension_width ?? null,
                    'dimension_height' => $battery->dimension_height ?? null,
                    'standard_cca' => $battery->standard_cca ?? null,
                    'capacity' => $battery->capacity,
                    'warranty' => $battery->warranty ?? null,
                    'price_retail' => $battery->price_retail,
                    'image' => $battery->image,
                    'status' => $battery->status ?? 1,
                    'type' => $battery->type ?? 'regular',
                    'editable_price' => $battery->editable_price ?? 0,
                    'created_at' => $battery->created_at,
                    'updated_at' => $battery->updated_at,
                    'deleted_at' => $battery->deleted_at,
                    'battery_url' => $battery_url,
                    'battery_prices' => $battery_prices,
                ];

                // Add flags for frontend identification
                if ($battery->id === $minPriceId) {
                    $batteryData['is_min_price'] = true;
                }

                if ($battery->id === $maxCapacityId) {
                    $batteryData['is_max_price'] = true;
                }

                return $batteryData;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data found',
                'data' => [
                    'batteries' => $details->values(),
                    'vehicle' => $vehicleModel,
                    'pagination' => [
                        'total' => $totalCount,
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => ($offset + $limit) < $totalCount
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
