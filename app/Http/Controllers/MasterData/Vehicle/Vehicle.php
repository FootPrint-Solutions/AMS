<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Vehicle\VehicleBrandModel;
use App\Models\MasterData\Battery\BatteryModel;

// IMPORT CLASS
use App\Imports\VehicleImport;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

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
                $this->title,
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
    public function show(Request $request)
    {
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = VehicleModel::with(['brand' => function ($query) {
            $query->withTrashed();
        }]);

        $selectColumns = $query->getModel()->getFillable(); // ini udah otomatis ambil fillable dari model / query yang di panggil
        $query->select($selectColumns); // ini udah otomatis ambil fillable dari model / query yang di panggil

        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
                    $query->orWhere($column, "like", "%" . $searchValue . "%");
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumnIndex] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        $ListData = $query->orderBy("name", "asc")
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $no = $start + 1;

        foreach ($ListData as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->brand->name ?? '-';
            $row[] = "<a href='" . $key->url . "'>" . $key->url . "</a>";
            $row[] = $key->id;
            $data[] = $row;
        }

        $recordTotal  = VehicleModel::count();


        $recordFiltered = ($searchValue != null) ? $query->count() : $recordTotal;

        $output = [
            "draw" => $draw,
            "recordsTotal" => $recordTotal,
            "recordsFiltered" => $recordFiltered,
            "data" => $data
        ];

        return response()->json($output);
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $import = new VehicleImport();
        try {
            Excel::import($import, $file->getRealPath());
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
