<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryTechnologyModel;

class BatteryTechnology extends Controller
{
    private $title = "Battery Technology";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Technology index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Technology.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Battery Technology profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Technology.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Battery Technolgoy resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "MasterData.Battery.Technology.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => BatteryTechnologyModel::find($id)->toArray(),
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
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');

        // Get battery technology data (rows and count).
        $data = BatteryTechnologyModel::allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => BatteryTechnologyModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Battery Technology resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $technology = new BatteryTechnologyModel();
        $technology->name = $request->name;
        $status = $technology->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new battery technology was successfully created!" : "Failed to create the new battery technology!"
        );
    }

    /**
     * Update the specified Battery Technology resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $technology = BatteryTechnologyModel::find($request->id);
        $technology->name = $request->name;
        $status = $technology->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The battery technology was successfully updated!" : "Failed to update the battery technology!"
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
        $technology = BatteryTechnologyModel::find($request->id);
        $status = $technology->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected technology was successfully deleted!" : "Failed to delete the selected technology!"
        );
    }
}
