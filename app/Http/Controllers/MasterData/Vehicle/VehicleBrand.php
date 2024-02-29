<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Vehicle\VehicleBrandModel;

class VehicleBrand extends Controller
{
    private $title = "Vehicle Brand";
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
            'MasterData.Vehicle.Brand.index',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Vehicle Brand profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Vehicle Brand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'profile' => VehicleBrandModel::find($id)->toArray(),
                )
            )
        );
    }

    /**
     * Display all resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        // Get vehicle brand data (rows and count).
        $data = VehicleBrandModel::allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection);

        // Set rows to be displayed in vehicle brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = number_format($no, 0);
            $row[] = $key->name;
            $row[] = $key->id;
            $rows[] = $row;
            $no++;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => VehicleBrandModel::count(),
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
        $brand = new VehicleBrandModel();
        $brand->name = $request->name;
        $status = $brand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The new vehicle brand was successfully created!' : 'Failed to create the new vehicle brand!'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $brand = VehicleBrandModel::find($request->id);
        $brand->name = $request->name;
        $status = $brand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The vehicle brand was successfully updated!' : 'Failed to update the vehicle brand!'
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
        $vehicle = VehicleBrandModel::find($request->id);
        $status = $vehicle->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The selected brand was successfully deleted!' : 'Failed to delete the selected brand!'
        );
    }
}
