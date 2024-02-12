<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\VehicleBrandModel;

class VehicleBrand extends Controller
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
            'MasterData.VehicleBrand.index',
            getIndexData(
                'Vehicle Brand',
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
            'MasterData.VehicleBrand.create',
            getIndexData(
                'Vehicle Brand',
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
            'MasterData.VehicleBrand.create',
            getIndexData(
                'Vehicle',
                $this->menu,
                $this->submenu,
                array(
                    'profile' => VehicleBrandModel::find($id)->toArray(),
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
            $result = VehicleBrandModel::get()->toArray();

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
     * Store a newly created Vehicle Brand resource in database.
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
     * Update the specified Vehicle Brand resource in storage.
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
