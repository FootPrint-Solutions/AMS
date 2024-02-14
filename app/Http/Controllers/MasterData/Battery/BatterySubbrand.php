<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;

class BatterySubbrand extends Controller
{
    private $title = "Battery Subbrand Category";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Subbrand Category index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Subbrand.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Battery Subbrand Category profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Subbrand.create',
            getIndexData(
                $this->title,
                2,
                4
            )
        );
    }

    /**
     * Show the form for editing Battery Subbrand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "MasterData.Battery.Subbrand.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => BatterySubbrandCategoryModel::find($id)->toArray(),
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
            $result = BatterySubbrandCategoryModel::get()->toArray();

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
     * Store a newly created Battery Subbrand Category resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $subbrand = new BatterySubbrandCategoryModel();
        $subbrand->name = $request->name;
        $status = $subbrand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new battery subbrand category was successfully created!" : "Failed to create the new battery subbrand category!"
        );
    }

    /**
     * Update the specified Battery Subbrand Category resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $subbrand = BatterySubbrandCategoryModel::find($request->id);
        $subbrand->name = $request->name;
        $status = $subbrand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The battery subbrand category was successfully updated!" : "Failed to update the battery subbrand category!"
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
        $subbrand = BatterySubbrandCategoryModel::find($request->id);
        $status = $subbrand->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected subbrand category was successfully deleted!" : "Failed to delete the selected subbrand category!"
        );
    }
}
