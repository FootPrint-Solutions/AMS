<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
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
                $row[] = $i["brand"]["name"] ?? ""; // Brand
                $row[] = $i["subbrandCategory"]["name"] ?? ""; // Subbrand Category
                $row[] = $i["usageType"]["name"] ?? ""; // Usage Type
                $row[] = $i["sizeCategory"]["name"] ?? ""; // Size Category
                $row[] = $i["technology"]["name"] ?? ""; // Technology
                $row[] = $i["dimension_length"] . " x " . $i["dimension_width"] . " x " . $i["dimension_height"]; // Dimensions

                $row[] = $i["standard_cca"]; // Standard CCa
                $row[] = $i["capacity"]; // Capacity
                $row[] = $i["warranty"]; // Warranty
                $row[] = $i["price_retail"]; // Retail Price
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
        $battery->id_brand = 0;
        $battery->id_subbrand_category = 0;
        $battery->id_usage_type = 0;
        $battery->id_size_category = 0;
        $battery->id_technology = 0;
        $battery->dimension_length = $request->dimension[0];
        $battery->dimension_width = $request->dimension[1];
        $battery->dimension_height = $request->dimension[2];
        $battery->standard_cca = $request->standardcca;
        $battery->capacity = $request->capacity;
        $battery->warranty = $request->warranty;
        $battery->price_retail = $request->price;
        $battery->image = '';
        $status = $battery->save();

        // Store the list of batteries' aliases.
        // $battery->aliases()->attach($request->altname);

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
}
