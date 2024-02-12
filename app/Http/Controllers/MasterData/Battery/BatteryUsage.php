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
            "MasterData.Battery.BatteryUsage.index",
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
            'MasterData.Battery.BatteryUsage.create',
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
            "MasterData.Battery.BatteryUsage.create",
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
    public function show(Request $request, $id = null)
    {
        if ($id == null) {
            $result = BatteryUsageTypeModel::get()->toArray();

            // Set a new array for table rows.
            $tableRows = array();
            $number = 1;

            // Iterate through each row in table.
            foreach ($result as $i) {
                // Set a new row for the table.
                $row = array();
                $row[] = number_format($number, 0); // #
                $row[] = $i["name"]; // Name
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
     * Store a newly created Battery Usage Type resource in database.
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
     * Update the specified Battery Usage Type resource in storage.
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
