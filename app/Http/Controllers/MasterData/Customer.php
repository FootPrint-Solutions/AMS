<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\CustomerModel;
use App\Models\MasterData\VehicleModel;

class Customer extends Controller
{
    /**
     * Show the Customer index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData/Customer/index',
            getIndexData(
                'Customer',
                2,
                2
            )
        );
    }

    /**
     * Show the form for creating Customer profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData/Customer/create',
            getIndexData(
                'Customer',
                2,
                2,
                array(
                    'vehicles' => VehicleModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing Customer profile resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData/Customer/create',
            array(
                'title' => 'Customer | ' . config('app.name'),
                'subtitle' => 'List',
                'active' => 2,
                'profile' => CustomerModel::find($id)->toArray()
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
            $result = CustomerModel::all()->toArray();

            // Set a new array for table rows.
            $tableRows = array();
            $number = 1;

            // Iterate through each row in table.
            foreach ($result as $i) {
                // Set a new row for the table.
                $row = array();
                $row[] = number_format($number, 0); // #
                $row[] = $i["name"]; // Name
                $row[] = $i["contact"]; // Contact
                $row[] = $i["email"]; // E-mail
                $row[] = $i["address"]; // Address
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
     * Store a newly created Customer resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $customer = new CustomerModel();
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->contact = $request->contact;
        $customer->email = $request->email;
        $status = $customer->save();

        // Set a new response data to be sent.
        if ($status) {
            // The inserting process is succeeded.
            $message = 'The new customer was successfully created!';
        } else {
            // The inserting process is failed.
            $message = 'Failed to create the new customer!';
        }

        return getResponseData($status, $message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $customer = CustomerModel::find($request->id);
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->contact = $request->contact;
        $customer->email = $request->email;
        $status = $customer->save();

        // Set a new response data to be sent.
        if ($status) {
            // The updating process is succeeded.
            $message = 'The customer was successfully updated!';
        } else {
            // The updating process is failed.
            $message = 'Failed to update the customer!';
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
        $customer = CustomerModel::find($request->id);
        $status = $customer->delete();

        // Set a new response data to be sent.
        if ($status) {
            // The deleting process is succeeded.
            $message = 'The selected customer was successfully deleted!';
        } else {
            // The deleting process is failed.
            $message = 'Failed to delete the selected customer!';
        }

        return getResponseData($status, $message);
    }
}
