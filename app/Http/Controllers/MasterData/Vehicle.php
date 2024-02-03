<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\VehicleModel;
use App\Models\MasterData\VehicleBrandModel;

class Vehicle extends Controller
{
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
                'Vehicle',
                2,
                3
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
                'Vehicle',
                2,
                3,
                array(
                    'brands' => VehicleBrandModel::all()->toArray()
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
                'Vehicle',
                2,
                3,
                array(
                    'brands' => VehicleBrandModel::all()->toArray(),
                    'profile' => VehicleModel::find($id)->toArray()
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
            $result = VehicleModel::with('brand')->get()->toArray();

            // Set a new array for table rows.
            $tableRows = array();
            $number = 1;

            // Iterate through each row in table.
            foreach ($result as $i) {
                // Set a new row for the table.
                $row = array();
                $row[] = number_format($number, 0); // #
                $row[] = $i["name"]; // Name
                $row[] = $i["brand"]["name"] ?? '-'; // Brand
                $row[] = "<a href='" . $i['url'] . "'>" . $i["url"] . "</a>"; // URL
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
     * Store a newly created Vehicle resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $vehicle = new VehicleModel();
        $vehicle->name = $request->name;
        $vehicle->id_brand = $request->vehiclebrand;
        $vehicle->url = '';
        $status = $vehicle->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new vehicle was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new vehicle!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
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
        $vehicle->id_brand = $request->vehiclebrand;
        $vehicle->url = '';
        $status = $vehicle->save();

        // Set a new response data to be sent.
        if ($status) {
            // The updating process is succeeded.
            $message = 'The vehicle was successfully updated!';
        } else {
            // The updating process is failed.
            $message = 'Failed to update the vehicle!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
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

        // Set a new response data to be sent.
        if ($status) {
            // The deleting process is succeeded.
            $message = 'The selected customer was successfully deleted!';
        } else {
            // The deleting process is failed.
            $message = 'Failed to delete the selected customer!';
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }
}
