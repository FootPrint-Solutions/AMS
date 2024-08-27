<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Exports\BatteryExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;
use App\Models\MasterData\Battery\BatteryBrandModel;
use App\Models\MasterData\Battery\BatteryUrlModel;

// IMPORT CLASS
use App\Imports\BatteryImport;
use App\Imports\BatteryPriceImport;
use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class Battery extends Controller
{
    private $title = "Battery";

    /**
     * Show the Battery index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData.Battery.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Battery profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.create',
            getIndexData(
                $this->title,
                array(
                    'brands' => BatteryBrandModel::all()->toArray(),
                    'subbrand_categories' => BatterySubbrandCategoryModel::all()->toArray(),
                    'usage_types' => BatteryUsageTypeModel::all()->toArray(),
                    'technologies' => BatteryTechnologyModel::all()->toArray(),
                    'sizes' => BatterySizeCategoryModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing Battery profile resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData.Battery.create',
            getIndexData(
                $this->title,
                array(
                    'profile' => BatteryModel::with(['urls', 'code'])->find($id)->toArray(),
                    'brands' => BatteryBrandModel::all()->toArray(),
                    'subbrand_categories' => BatterySubbrandCategoryModel::all()->toArray(),
                    'usage_types' => BatteryUsageTypeModel::all()->toArray(),
                    'technologies' => BatteryTechnologyModel::all()->toArray(),
                    'sizes' => BatterySizeCategoryModel::all()->toArray()
                )
            )
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $path1 = $request->file('file')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        try {
            Excel::import(new BatteryImport, $path);
            return getResponseData(
                true,
                "Data imported successfully!"
            );
        } catch (\Exception $e) {
            return getResponseData(
                false,
                "Error importing data Error importing data excell format or data is not suitable"
            );
        }
    }

    public function importPrice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xlsx,xls,csv',
            ]);
            if ($validator->fails())
                throw new Exception('Invalid file format.');

            // Proceed with the file storage and import
            $path1 = $request->file('file')->store('temp');
            $path = storage_path('app') . '/' . $path1;

            $import = new BatteryPriceImport();
            Excel::import($import, $path);
            return view('import', [
                'status' => true,
                'totalRows' => $import->getTotalRows(),
                'totalChangedRows' => $import->getTotalChangedRows(),
                'totalUnchangedRows' => $import->getTotalUnchangedRows(),
                'unimportedRows' => $import->getUnimportedRows()
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return view('import', [
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new BatteryExport, 'batteries.xlsx');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        // Get battery data (rows and count).
        $data = BatteryModel::allForDataTables($request);

        // Set rows to be displayed in battery table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->brand->name ?? "-";
            $row[] = $key->subbrandCategory->name ?? "-";
            $row[] = $key->usageType->name ?? "-";
            $row[] = $key->sizeCategory->name ?? "-";
            $row[] = $key->technology->name ?? "-";
            $row[] = $key->dimension_length . " x " . $key->dimension_width . " x " . $key->dimension_height;
            $row[] = $key->standard_cca;
            $row[] = $key->capacity;
            $row[] = $key->warranty;
            $row[] = formatPrice($key->price_retail);
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => BatteryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $battery = new BatteryModel();
            $battery->name = $request->name;
            $battery->name_alternate = $request->altname;

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new BatteryBrandModel();
                $brand->name = $request->newbrand;
                $status = $brand->save();

                $battery->brand_id = $brand->id;
            } else {
                $battery->brand_id = $request->brand;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->subbrandcategory === "new" && isset($request->newsubbrandcategory)) {
                // Store the newly added vehicle brand.
                $subbrand = new BatterySubbrandCategoryModel();
                $subbrand->name = $request->newsubbrandcategory;
                $status = $subbrand->save();

                $battery->subbrand_category_id = $subbrand->id;
            } else {
                if (isset($request->subbrandcategory) && $request->subbrandcategory !== "new")
                    $battery->subbrand_category_id = $request->subbrandcategory;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->usagetype === "new" && isset($request->newusagetype)) {
                // Store the newly added vehicle brand.
                $usagetype = new BatteryUsageTypeModel();
                $usagetype->name = $request->newusagetype;
                $status = $usagetype->save();

                $battery->usage_type_id = $usagetype->id;
            } else {
                if (isset($request->usagetype) && $request->usagetype !== "new")
                    $battery->usage_type_id = $request->usagetype;
            }

            // Check if the technology is newly added or not.
            if ($request->technology === "new" && isset($request->newtechnology)) {
                // Store the newly added vehicle brand.
                $technology = new BatteryTechnologyModel();
                $technology->name = $request->newtechnology;
                $status = $technology->save();

                $battery->technology_id = $technology->id;
            } else {
                if (isset($request->technology) && $request->technology !== "new")
                    $battery->technology_id = $request->technology;
            }

            // Check if the size category is newly added or not.
            if ($request->size === "new" && isset($request->newsize)) {
                // Store the newly added vehicle brand.
                $size = new BatterySizeCategoryModel();
                $size->name = $request->newsize;
                $status = $size->save();

                $battery->size_category_id = $size->id;
            } else {
                if (isset($request->size) && $request->size !== "new")
                    $battery->size_category_id = $request->size;
            }

            $battery->dimension_length = $request->dimension[0];
            $battery->dimension_width = $request->dimension[1];
            $battery->dimension_height = $request->dimension[2];

            if (isset($request->standardcca))
                $battery->standard_cca = $request->standardcca;

            $battery->capacity = $request->capacity;

            if (isset($request->warranty))
                $battery->warranty = $request->warranty;

            $battery->price_retail = (float) str_replace(".", "", $request->price);

            // Check if an image has been uploaded or not.
            if ($request->hasFile('image')) {
                $battery->image = basename($request->file("image")->store("public/image/battery"));
            }

            $status = $battery->save();

            // Store the list of battery's urls.
            for ($i = 0; $i < count($request->url); $i++) {
                if ($request->platform[$i] != '' && $request->url[$i] !== '')
                    $battery->urls()->create([
                        'platform' => $request->platform[$i],
                        'url' => $request->url[$i],
                    ]);
            }

            // Store battery price.
            $price = new BatteryPriceModel();
            $price->promo_id = null;
            $price->battery_id = $battery->id;
            $price->price_retail = $battery->price_retail;
            $status &= $price->save();

            // Store battery code.
            $code = new BatteryCodeModel();
            $code->code = $request->code;
            $code->battery_id = $battery->id;
            $status &= $code->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new battery was successfully created!" : "Failed to create the new battery!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $battery = BatteryModel::find($request->id);
            $battery->name = $request->name;
            $battery->name_alternate = $request->altname;

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new BatteryBrandModel();
                $brand->name = $request->newbrand;
                $status = $brand->save();

                $battery->brand_id = $brand->id;
            } else {
                $battery->brand_id = $request->brand;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->subbrandcategory === "new" && isset($request->newsubbrandcategory)) {
                // Store the newly added vehicle brand.
                $subbrand = new BatterySubbrandCategoryModel();
                $subbrand->name = $request->newsubbrandcategory;
                $status = $subbrand->save();

                $battery->subbrand_category_id = $subbrand->id;
            } else {
                if (isset($request->subbrandcategory) && $request->subbrandcategory !== "new")
                    $battery->subbrand_category_id = $request->subbrandcategory;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->usagetype === "new" && isset($request->newusagetype)) {
                // Store the newly added vehicle brand.
                $usagetype = new BatteryUsageTypeModel();
                $usagetype->name = $request->newusagetype;
                $status = $usagetype->save();

                $battery->usage_type_id = $usagetype->id;
            } else {
                if (isset($request->usagetype) && $request->usagetype !== "new")
                    $battery->usage_type_id = $request->usagetype;
            }

            // Check if the technology is newly added or not.
            if ($request->technology === "new" && isset($request->newtechnology)) {
                // Store the newly added vehicle brand.
                $technology = new BatteryTechnologyModel();
                $technology->name = $request->newtechnology;
                $status = $technology->save();

                $battery->technology_id = $technology->id;
            } else {
                if (isset($request->technology) && $request->technology !== "new")
                    $battery->technology_id = $request->technology;
            }

            // Check if the size category is newly added or not.
            if ($request->size === "new" && isset($request->newsize)) {
                // Store the newly added vehicle brand.
                $size = new BatterySizeCategoryModel();
                $size->name = $request->newsize;
                $status = $size->save();

                $battery->size_category_id = $size->id;
            } else {
                if (isset($request->size) && $request->size !== "new")
                    $battery->size_category_id = $request->size;
            }

            $battery->dimension_length = $request->dimension[0];
            $battery->dimension_width = $request->dimension[1];
            $battery->dimension_height = $request->dimension[2];

            if (isset($request->standardcca))
                $battery->standard_cca = $request->standardcca;
            $battery->capacity = $request->capacity;

            if (isset($request->warranty))
                $battery->warranty = $request->warranty;
            $battery->price_retail = (float) str_replace(".", "", $request->price);

            // Check if an image has been uploaded or not.
            if ($request->hasFile('image')) {
                $battery->image = basename($request->file("image")->store("public/image/battery"));
            }
            $status = $battery->save();

            // Delete rows that are not in the request.
            if ($request->url_id != null) {
                $urlsToDelete = $battery->urls->pluck('id')->diff($request->url_id);
                if ($urlsToDelete->isNotEmpty()) {
                    BatteryUrlModel::destroy($urlsToDelete);
                }

                // Store the list of battery's urls.
                for ($i = 0; $i < count($request->url); $i++) {
                    if ($request->platform[$i] != '' && $request->url[$i] !== '')
                        $battery->urls()->updateOrCreate(
                            [
                                'id' => $request->url_id[$i],
                            ],
                            [
                                'platform' => $request->platform[$i],
                                'url' => $request->url[$i],
                            ]
                        );
                }
            } else {
                // Store the list of battery's urls.
                for ($i = 0; $i < count($request->url); $i++) {
                    if ($request->platform[$i] != '' && $request->url[$i] !== '')
                        $battery->urls()->create([
                            'platform' => $request->platform[$i],
                            'url' => $request->url[$i],
                        ]);
                }
            }

            // Store battery price.
            $price = BatteryPriceModel::where("battery_id", $battery->id)->first();
            if ($price) {
                $price->price_retail = $battery->price_retail;
                $status &= $price->save();
            } else {
                $price = new BatteryPriceModel();
                $price->promo_id = null;
                $price->battery_id = $battery->id;
                $price->price_retail = $battery->price_retail;
                $status &= $price->save();
            }

            // Store battery code.
            $code = BatteryCodeModel::where("battery_id", $battery->id)->first();
            if ($code) {
                $code->code = $request->code;
                $status &= $code->save();
            } else {
                $code = new BatteryCodeModel();
                $code->code = $request->code;
                $code->battery_id = $battery->id;
                $status &= $code->save();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery was successfully updated!" : "Failed to update the battery!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $battery = BatteryModel::find($request->id);
            $battery->status = $battery->status ? 0 : 1;
            $status = $battery->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected battery was successfully updated!" : "Failed to update the selected battery!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Get the list of batteries based on keyword.
     * 
     * @param  int  $keyword The search keyword
     */
    public function getBatteriesByKeyword($keyword)
    {
        $inventoryController = new Inventory();
        $exceptions = $inventoryController->getNonZeroStockInventory();

        return BatteryModel::allForAutocomplete(
            $keyword,
            [
                "battery_prices.price_retail", // retail price
                "battery_prices.discount", // discount
            ],
            $exceptions
        );
    }

    /**
     * Get the list of batteries based on size category.
     * 
     * @param  int  $sizeId The size category id
     */
    public function getBatteriesBySizeCategory(Request $request)
    {
        try {
            $sizeId = $request->sizeId;
            $name = $request->name;

            $query = BatteryModel::query();
            if (!is_null($sizeId))
                $query->where('size_category_id', $sizeId);

            if (!is_null($name))
                $query->where('name', 'like', '%' . $name . '%');

            $query->join('battery_prices', 'batteries.id', '=', 'battery_prices.battery_id');
            $query->where(function ($query) {
                $query->whereNull('battery_prices.promo_id')
                    ->orWhere('battery_prices.promo_id', '=', 0);
            });

            return $query->get()->toArray();
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());
        }
    }
}
