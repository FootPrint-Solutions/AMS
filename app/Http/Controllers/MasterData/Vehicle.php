<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\VehicleModel;
use App\Models\MasterData\VehicleBrandModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\VehicleBatteryModel;

use function PHPUnit\Framework\isNull;

class Vehicle extends Controller
{
    private $menu = 2;
    private $submenu = 3;

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData/Vehicle/index',
            getIndexData(
                'Vehicle',
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Vehicle resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData/Vehicle/create',
            getIndexData(
                'Vehicle',
                $this->menu,
                $this->submenu,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'batteries' => BatteryModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing Vehicle resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData/Vehicle/create',
            getIndexData(
                'Vehicle',
                $this->menu,
                $this->submenu,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'batteries' => BatteryModel::all()->toArray(),
                    'profile' => VehicleModel::find($id)->toArray(),
                    'primary_battery' => VehicleModel::find($id)->batteries()->where('type', 1)->pluck('id_battery')->first(),
                    'secondary_batteries' => VehicleModel::find($id)->batteries()->where('type', 0)->pluck('id_battery')->toArray(),
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
            $result = VehicleModel::with('brand')->get()->toArray();

            // Set a new array for table rows.
            $tableRows = array();
            $number = 1;

            // Iterate through each row in table.
            foreach ($result as $i) {
                // Set a new row for the table.
                $row = array();
                $row[] = number_format($number, 0); // #
                $row[] = $i["name"]; // Name
                $row[] = $i["brand"]["name"] ?? '-'; // Brand
                $row[] = "<a href='" . $i['url'] . "'>" . $i["url"] . "</a>"; // URL
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
     * Store a newly created Vehicle resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $vehicle = new VehicleModel();
        $vehicle->name = $request->name;

        // Check if the brand is newly added or not.
        if ($request->brand === "new") {
            // Store the newly added vehicle brand.
            $brand = new VehicleBrandModel();
            $brand->name = $request->newbrand;
            $status = $brand->save();

            $vehicle->id_brand = $brand->id;
        } else {
            $vehicle->id_brand = $request->brand;
        }

        $vehicle->url = $request->url;
        $status = $vehicle->save();

        // Store the list of all vehicles' suitable battery.
        // Set primary battery type to 1.
        $batteries[$request->batteryprimary] = ["type" => "1"];

        // Set secondary battery type to 0.
        if (!isNull($request->batterysecondary)) {
            foreach ($request->batterysecondary as $battery) {
                $batteries[$battery] = ["type" => "0"];
            }
        }
        $vehicle->batteries()->attach($batteries);

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new vehicle was successfully created!" : "Failed to create the new vehicle!"
        );
    }

    /**
     * Update the specified Vehicle resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $vehicle = VehicleModel::find($request->id);
        $vehicle->name = $request->name;
        $vehicle->id_brand = $request->brand;
        $vehicle->url = $request->url;
        $status = $vehicle->save();

        // Update the list of all vehicles' suitable batteries.
        // Set primary battery type to 1.
        $batteries[$request->batteryprimary] = ["type" => "1"];

        // Set secondary battery type to 0.
        if (!isNull($request->batterysecondary)) {
            foreach ($request->batterysecondary as $battery) {
                $batteries[$battery] = ["type" => "0"];
            }
        }
        $vehicle->batteries()->sync($batteries);

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The vehicle was successfully updated!" : "Failed to update the vehicle!"
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $vehicle = VehicleModel::find($request->id);
        $status = $vehicle->delete();

        // Detach suitable batteries from the pivot table
        $vehicle->batteries()->detach();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected customer was successfully deleted!" : "Failed to delete the selected customer!"
        );
    }
}
