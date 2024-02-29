<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatteryUsageTypeModel;

class BatteryUsage extends Controller
{
    private $title = "Battery Usage Type";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Usage Type index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Usage.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }
    /**
     * Show the form for creating Vehicle Usage Type profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Usage.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Battery Usage Type resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "MasterData.Battery.Usage.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => BatteryUsageTypeModel::find($id)->toArray(),
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

        // Get battery usage data (rows and count).
        $data = BatteryUsageTypeModel::allForDataTables($start, $length, $searchValue, $orderColumn, $orderDirection);

        // Set rows to be displayed in battery usage table.
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
            "recordsTotal" => BatteryUsageTypeModel::count(),
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
        $usage = new BatteryUsageTypeModel();
        $usage->name = $request->name;
        $status = $usage->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new battery usage type was successfully created!" : "Failed to create the new battery usage type!"
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
        $usage = BatteryUsageTypeModel::find($request->id);
        $usage->name = $request->name;
        $status = $usage->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The battery usage type was successfully updated!" : "Failed to update the battery usage type!"
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
        $usage = BatteryUsageTypeModel::find($request->id);
        $status = $usage->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected usage type was successfully deleted!" : "Failed to delete the selected usage type!"
        );
    }
}
