<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// MODELS
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;

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
                    'battery_size_categories' => BatterySizeCategoryModel::all()->toArray(),
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
    public function edit($id = null)
    {
        $vehicle = VehicleModel::find($id);

        if (is_null($vehicle)) {
            return redirect()->route('vehicle.index');
        }

        if (is_null($id)) {
            return redirect()->route('vehicle.index');
        }
        $batteryCategories = VehicleModel::find($id)->batterySizeCategories()->pluck('battery_size_category_id')->toArray();

        if (!empty($batteryCategories)) {
            $primaryBattery = $batteryCategories;
        } else {
            $primaryBattery = null;
        }

        return view(
            'MasterData/Vehicle/create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'battery_size_categories' => BatterySizeCategoryModel::all()->toArray(),
                    'profile' => VehicleModel::find($id)->toArray(),
                    'primary_battery' => $primaryBattery,
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
            $row[] = $key->brand->name ?? '-';
            $row[] = "<a href='$key->url'>" . $key->url . "</a>";
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
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
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                    'brand' => 'required',
                    'newbrand' => 'required_if:brand,new',
                ],
                [
                    'name.required' => 'Vehicle name is required!',
                    'brand.required' => 'Vehicle brand is required!',
                    'newbrand.required_if' => 'Vehicle brand is required!'
                ]
            );

            $vehicle = new VehicleModel();
            $vehicle->name = $validatedData['name'];

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new VehicleBrandModel();
                $brand->name = $validatedData['newbrand'];
                $status = $brand->save();

                $vehicle->brand_id = $brand->id;
            } else {
                $vehicle->brand_id = $validatedData['brand'];
            }

            $vehicle->url = $request->url;
            $status = $vehicle->save();

            // Store the list of all vehicles' suitable battery.
            // Set primary battery type to 1.

            if (!is_null($request->batteryprimary)) {
                $batteries = [];
                foreach ($request->batteryprimary as $battery) {
                    $batteries[$battery] = [];
                }
            }
            $vehicle->batterySizeCategories()->attach($batteries);

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new vehicle was successfully created!" : "Failed to create the new vehicle!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
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
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                    'brand' => 'required',
                    'newbrand' => 'required_if:brand,new',
                ],
                [
                    'name.required' => 'Vehicle name is required!',
                    'brand.required' => 'Vehicle brand is required!',
                    'newbrand.required_if' => 'Vehicle brand is required!'
                ]
            );

            $vehicle = VehicleModel::find($request->id);
            $vehicle->name = $validatedData['name'];

            // Check if the brand is newly added or not.
            if ($request->brand === "new") {
                // Store the newly added vehicle brand.
                $brand = new VehicleBrandModel();
                $brand->name = $validatedData['newbrand'];
                $status = $brand->save();

                $vehicle->brand_id = $brand->id;
            } else {
                $vehicle->brand_id = $validatedData['brand'];
            }

            $vehicle->url = $request->url;
            $status = $vehicle->save();

            // Update the list of all vehicles' suitable batteries.
            if (!is_null($request->batteryprimary)) {
                $batteries = [];
                foreach ($request->batteryprimary as $battery) {
                    $batteries[$battery] = [];
                }
            }
            $vehicle->batterySizeCategories()->sync($batteries);

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The vehicle was successfully updated!" : "Failed to update the vehicle!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
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
        try {
            $vehicle = VehicleModel::find($request->id);
            $vehicle->status = $vehicle->status ? 0 : 1;
            $status = $vehicle->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected vehicle was successfully updated!" : "Failed to update the selected vehicle!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

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
            // Logging error message.
            Log::error($e->getMessage());

            return getResponseData(
                false,
                "Error importing data excell format or data is not suitable"
            );
        }
    }
}
