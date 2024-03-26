<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

// MODELS
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Battery\BatteryModel;

// IMPORT CLASS
use App\Imports\VehicleImport;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Vehicle extends Controller
{
    private $title = "Vehicle";
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
                $this->title,
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
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'batteries' => BatteryModel::getBatteryWithSize(),
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
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'batteries' => BatteryModel::getBatteryWithSize(),
                    'profile' => VehicleModel::find($id)->toArray(),
                    'primary_battery' => VehicleModel::find($id)->batteries()->where('type', 1)->pluck('battery_id')->toArray(),
                    'secondary_batteries' => VehicleModel::find($id)->batteries()->where('type', 0)->pluck('battery_id')->toArray(),
                )
            )
        );
    }

    /**
     * Display all resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get vehicle data (rows and count).
        $data = VehicleModel::allForDataTables($request);

        // Set rows to be displayed in vehicle table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->brand->name ?? '-';
            $row[] = "<a href='" . $key->url . "'>" . $key->url . "</a>";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => VehicleModel::count(),
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
            $vehicle = new VehicleModel();
            $vehicle->name = $request->name;

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new VehicleBrandModel();
                $brand->name = $request->newbrand;
                $status = $brand->save();

                $vehicle->brand_id = $brand->id;
            } else {
                $vehicle->brand_id = $request->brand;
            }

            $vehicle->url = $request->url;
            $status = $vehicle->save();

            // Store the list of all vehicles' suitable battery.
            // Set primary battery type to 1.
            $batteries[$request->batteryprimary] = ["type" => "1"];

            // Set secondary battery type to 0.
            if (!is_null($request->batterysecondary)) {
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
        } catch (Exception $e) {
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
    public function update(Request $request)
    {
        try {
            $vehicle = VehicleModel::find($request->id);
            $vehicle->name = $request->name;

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new VehicleBrandModel();
                $brand->name = $request->newbrand;
                $status = $brand->save();

                $vehicle->brand_id = $brand->id;
            } else {
                $vehicle->brand_id = $request->brand;
            }

            $vehicle->url = $request->url;
            $status = $vehicle->save();

            // Update the list of all vehicles' suitable batteries.
            // Set primary battery type to 1.
            $batteries[$request->batteryprimary] = ["type" => "1"];

            // Set secondary battery type to 0.
            if (!is_null($request->batterysecondary)) {
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
                // Delete the specific vehicle.
                $vehicle = VehicleModel::find($id);
                $status &= $vehicle->delete();

                // Detach suitable batteries from the pivot table
                $vehicle->batteries()->detach();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected customer was successfully deleted!" : "Failed to delete the selected customer!"
            );
        } catch (Exception $e) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * 
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $path1 = $request->file('file')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        try {
            Excel::import(new VehicleImport, $path);
            return getResponseData(
                true,
                "Data imported successfully!"
            );
        } catch (\Exception $e) {
            return getResponseData(
                false,
                "Error importing data: " . $e->getMessage()
            );
        }
    }
}
