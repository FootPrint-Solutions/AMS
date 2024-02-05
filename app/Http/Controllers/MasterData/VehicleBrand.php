<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\VehicleBrandModel;

class VehicleBrand extends Controller
{
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
                2,
                3
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
                2,
                3
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
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new vehicle brand was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new vehicle brand!';
        }

        return getResponseData($status, $message);
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
        if ($status) {
            // The deleting process is succeeded.
            $message = 'The selected brand was successfully deleted!';
        } else {
            // The deleting process is failed.
            $message = 'Failed to delete the selected brand!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }
}
