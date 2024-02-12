<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryAlias;
use App\Models\MasterData\Battery\BatteryBrandModel;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;

class Battery extends Controller
{
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
                'Battery',
                2,
                4
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
                'Battery',
                2,
                4,
                array(
                    'brands' => BatteryBrandModel::all()->toArray(),
                    'subbrand_categories' => BatterySubbrandCategoryModel::all()->toArray(),
                    'usage_types' => BatteryUsageTypeModel::all()->toArray(),
                    'technologies' => BatteryTechnologyModel::all()->toArray(),
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
                'Customer',
                2,
                2,
                array(
                    'profile' => BatteryModel::find($id)->toArray(),
                    'brands' => BatteryBrandModel::all()->toArray(),
                    'subbrand_categories' => BatterySubbrandCategoryModel::all()->toArray(),
                    'usage_types' => BatteryUsageTypeModel::all()->toArray(),
                    'technologies' => BatteryTechnologyModel::all()->toArray(),
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show(Request $request, $id = null)
    {
        if ($id == null) {
            $result = BatteryModel::with(["brand", "subbrandCategory", "usageType", "sizeCategory", "technology"])
                ->get()
                ->toArray();

            // Set a new array for table rows.
            $tableRows = array();
            $number = 1;

            // Iterate through each row in table.
            foreach ($result as $i) {
                // Set a new row for the table.
                $row = array();
                $row[] = number_format($number, 0); // #
                $row[] = $i["name"]; // Name
                $row[] = $i["brand"]["name"] ?? "-"; // Brand
                $row[] = $i["subbrand_category"]["name"] ?? "-"; // Subbrand Category
                $row[] = $i["usage_type"]["name"] ?? "-"; // Usage Type
                $row[] = $i["size_category"]["name"] ?? "-"; // Size Category
                $row[] = $i["technology"]["name"] ?? "-"; // Technology
                $row[] = $i["dimension_length"] . " x " . $i["dimension_width"] . " x " . $i["dimension_height"]; // Dimensions

                $row[] = $i["standard_cca"]; // Standard CCa
                $row[] = $i["capacity"]; // Capacity
                $row[] = $i["warranty"]; // Warranty
                $row[] = number_format($i["price_retail"]); // Retail Price
                $row[] = "<a type='button' class='btn btn-primary' onclick=edit(" . $i["id"] . ")><i class='fa-solid fa-pencil'></i></a>"; // Edit
                $row[] = "<a type='button' class='btn btn-danger' onclick=destroy(" . $i["id"] . ")><i class='fa-solid fa-trash'></i></a>"; // Delete
                $tableRows[] = $row;
                $number++;
            }

            // Save data in array.
            $output = array(
                // "draw" => $_POST['draw'],
                "data" => $tableRows,
            );

            // Output data in JSON.
            return json_encode($output);
        }
    }

    /**
     * Store a newly created Customer resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $battery = new BatteryModel();
        $battery->name = $request->name;

        // Check if the brand is newly added or not.
        if ($request->brand === "new") {
            // Store the newly added vehicle brand.
            $brand = new BatteryBrandModel();
            $brand->name = $request->newbrand;
            $status = $brand->save();

            $battery->id_brand = $brand->id;
        } else {
            $battery->id_brand = $request->brand;
        }

        // Check if the subbrand category is newly added or not.
        if ($request->subbrandcategory === "new") {
            // Store the newly added vehicle brand.
            $subbrand = new BatterySubbrandCategoryModel();
            $subbrand->name = $request->newsubbrandcategory;
            $status = $subbrand->save();

            $battery->id_subbrand_category = $subbrand->id;
        } else {
            $battery->id_subbrand_category = $request->subbrandcategory;
        }

        // Check if the subbrand category is newly added or not.
        if ($request->usagetype === "new") {
            // Store the newly added vehicle brand.
            $usagetype = new BatteryUsageTypeModel();
            $usagetype->name = $request->newusagetype;
            $status = $usagetype->save();

            $battery->id_usage_type = $usagetype->id;
        } else {
            $battery->id_usage_type = $request->usagetype;
        }

        // Check if the technology is newly added or not.
        if ($request->technology === "new") {
            // Store the newly added vehicle brand.
            $technology = new BatteryTechnologyModel();
            $technology->name = $request->newtechnology;
            $status = $technology->save();

            $battery->id_technology = $technology->id;
        } else {
            $battery->id_technology = $request->technology;
        }

        $battery->id_size_category = 0;
        $battery->dimension_length = $request->dimension[0];
        $battery->dimension_width = $request->dimension[1];
        $battery->dimension_height = $request->dimension[2];
        $battery->standard_cca = $request->standardcca;
        $battery->capacity = $request->capacity;
        $battery->warranty = $request->warranty;
        $battery->price_retail = (float) str_replace(',', '', $request->price);
        $battery->image = '';
        $status = $battery->save();

        // Store the list of batteries' aliases.
        // $aliases = explode(',', $request->altname);
        // foreach ($aliases as $i) {
        //     $alias = new BatteryAlias();
        //     $alias->id_battery = $battery->id;
        //     $alias->name = $i;
        //     $alias->save();
        // }

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new customer was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new customer!';
        }

        return getResponseData($status, $message);
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
        $battery = BatteryModel::find($request->id);
        $battery->name = $request->name;

        // Check if the brand is newly added or not.
        if ($request->brand === "new") {
            // Store the newly added vehicle brand.
            $brand = new BatteryBrandModel();
            $brand->name = $request->newbrand;
            $status = $brand->save();

            $battery->id_brand = $brand->id;
        } else {
            $battery->id_brand = $request->brand;
        }

        // Check if the subbrand category is newly added or not.
        if ($request->subbrandcategory === "new") {
            // Store the newly added vehicle brand.
            $subbrand = new BatterySubbrandCategoryModel();
            $subbrand->name = $request->newsubbrandcategory;
            $status = $subbrand->save();

            $battery->id_subbrand_category = $subbrand->id;
        } else {
            $battery->id_subbrand_category = $request->subbrandcategory;
        }

        // Check if the subbrand category is newly added or not.
        if ($request->usagetype === "new") {
            // Store the newly added vehicle brand.
            $usagetype = new BatteryUsageTypeModel();
            $usagetype->name = $request->newusagetype;
            $status = $usagetype->save();

            $battery->id_usage_type = $usagetype->id;
        } else {
            $battery->id_usage_type = $request->usagetype;
        }

        // Check if the technology is newly added or not.
        if ($request->technology === "new") {
            // Store the newly added vehicle brand.
            $technology = new BatteryTechnologyModel();
            $technology->name = $request->newtechnology;
            $status = $technology->save();

            $battery->id_technology = $technology->id;
        } else {
            $battery->id_technology = $request->technology;
        }

        $battery->id_size_category = 0;
        $battery->dimension_length = $request->dimension[0];
        $battery->dimension_width = $request->dimension[1];
        $battery->dimension_height = $request->dimension[2];
        $battery->standard_cca = $request->standardcca;
        $battery->capacity = $request->capacity;
        $battery->warranty = $request->warranty;
        $battery->price_retail = $request->price;
        $battery->image = '';
        $status = $battery->save();

        // Update the list of customers' owned vehicles.
        // $battery->aliases()->sync($request->vehicle);

        // Set a new response data to be sent.
        if ($status) {
            // The updating process is succeeded.
            $message = 'The battery was successfully updated!';
        } else {
            // The updating process is failed.
            $message = 'Failed to update the battery!';
        }

        return getResponseData($status, $message);
    }

    /**
     * Remove the specified Battery resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $battery = BatteryModel::find($request->id);

        // Detach associated vehicles from the pivot table
        // $battery->aliases()->detach();

        // Delete customer data in storage.
        $status = $battery->delete();

        // Set a new response data to be sent.
        if ($status) {
            // The deleting process is succeeded.
            $message = 'The selected battery was successfully deleted!';
        } else {
            // The deleting process is failed.
            $message = 'Failed to delete the selected battery!';
        }

        return getResponseData($status, $message);
    }
}
