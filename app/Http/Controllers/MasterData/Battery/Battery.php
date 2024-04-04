<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryAlias;
use App\Models\MasterData\Battery\BatteryBrandModel;
use Illuminate\Http\Request;
use Exception;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;

// IMPORT CLASS
use App\Imports\BatteryImport;
use Maatwebsite\Excel\Facades\Excel;


class Battery extends Controller
{
    private $title = "Battery";
    private $menu = 2;
    private $submenu = 4;

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
                $this->title,
                $this->menu,
                $this->submenu
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
                $this->menu,
                $this->submenu,
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
        // Get the specific battery profile.
        $batteryProfile = BatteryModel::find($id);

        return view(
            'MasterData.Battery.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'profile' => $batteryProfile->toArray(),
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
            $row[] = number_format($key->price_retail);
            $row[] = $key->id;
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
            if ($request->subbrandcategory === "new") {
                // Store the newly added vehicle brand.
                $subbrand = new BatterySubbrandCategoryModel();
                $subbrand->name = $request->newsubbrandcategory;
                $status = $subbrand->save();

                $battery->subbrand_category_id = $subbrand->id;
            } else {
                $battery->subbrand_category_id = $request->subbrandcategory;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->usagetype === "new") {
                // Store the newly added vehicle brand.
                $usagetype = new BatteryUsageTypeModel();
                $usagetype->name = $request->newusagetype;
                $status = $usagetype->save();

                $battery->usage_type_id = $usagetype->id;
            } else {
                $battery->usage_type_id = $request->usagetype;
            }

            // Check if the technology is newly added or not.
            if ($request->technology === "new") {
                // Store the newly added vehicle brand.
                $technology = new BatteryTechnologyModel();
                $technology->name = $request->newtechnology;
                $status = $technology->save();

                $battery->technology_id = $technology->id;
            } else {
                $battery->technology_id = $request->technology;
            }

            // Check if the size category is newly added or not.
            if ($request->size === "new") {
                // Store the newly added vehicle brand.
                $size = new BatterySizeCategoryModel();
                $size->name = $request->newsize;
                $status = $size->save();

                $battery->size_category_id = $size->id;
            } else {
                $battery->size_category_id = $request->size;
            }

            $battery->dimension_length = $request->dimension[0];
            $battery->dimension_width = $request->dimension[1];
            $battery->dimension_height = $request->dimension[2];
            $battery->standard_cca = $request->standardcca;
            $battery->capacity = $request->capacity;
            $battery->warranty = $request->warranty;
            $battery->price_retail = (float) str_replace(",", "", $request->price);

            // Check if an image has been uploaded or not.
            if ($request->hasFile('image')) {
                $battery->image = basename($request->file("image")->store("public/image/battery"));
            }

            $status = $battery->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new customer was successfully created!" : "Failed to create the new customer!"
            );
        } catch (Exception $e) {
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
            if ($request->subbrandcategory === "new") {
                // Store the newly added vehicle brand.
                $subbrand = new BatterySubbrandCategoryModel();
                $subbrand->name = $request->newsubbrandcategory;
                $status = $subbrand->save();

                $battery->subbrand_category_id = $subbrand->id;
            } else {
                $battery->subbrand_category_id = $request->subbrandcategory;
            }

            // Check if the subbrand category is newly added or not.
            if ($request->usagetype === "new") {
                // Store the newly added vehicle brand.
                $usagetype = new BatteryUsageTypeModel();
                $usagetype->name = $request->newusagetype;
                $status = $usagetype->save();

                $battery->usage_type_id = $usagetype->id;
            } else {
                $battery->usage_type_id = $request->usagetype;
            }

            // Check if the technology is newly added or not.
            if ($request->technology === "new") {
                // Store the newly added vehicle brand.
                $technology = new BatteryTechnologyModel();
                $technology->name = $request->newtechnology;
                $status = $technology->save();

                $battery->technology_id = $technology->id;
            } else {
                $battery->technology_id = $request->technology;
            }

            // Check if the size category is newly added or not.
            if ($request->size === "new") {
                // Store the newly added vehicle brand.
                $size = new BatterySizeCategoryModel();
                $size->name = $request->newsize;
                $status = $size->save();

                $battery->size_category_id = $size->id;
            } else {
                $battery->size_category_id = $request->size;
            }

            $battery->dimension_length = $request->dimension[0];
            $battery->dimension_width = $request->dimension[1];
            $battery->dimension_height = $request->dimension[2];
            $battery->standard_cca = $request->standardcca;
            $battery->capacity = $request->capacity;
            $battery->warranty = $request->warranty;
            $battery->price_retail = (float) str_replace(",", "", $request->price);

            // Check if an image has been uploaded or not.
            if ($request->hasFile('image')) {
                $battery->image = basename($request->file("image")->store("public/image/battery"));
            }
            $status = $battery->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery was successfully updated!" : "Failed to update the battery!"
            );
        } catch (Exception $e) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $battery = BatteryModel::find($id);
                $status &= $battery->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected battery was successfully deleted!" : "Failed to delete the selected battery!"
            );
        } catch (Exception $e) {
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
        return BatteryModel::allForAutocomplete($keyword, ["price_retail"]);
    }
}
