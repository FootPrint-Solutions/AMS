<?php

namespace App\Http\Controllers\MasterData\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

// MODELS
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Vehicle\VehicleModel;

class Customer extends Controller
{
    private $title = "Customer";
    private $menu = 2;
    private $submenu = 2;

    /**
     * Show the Customer index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Customer.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
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
            "MasterData.Customer.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "vehicles" => VehicleModel::all()->toArray()
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
            "MasterData.Customer.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => CustomerModel::find($id)->toArray(),
                    "owned_vehicles" => CustomerModel::find($id)->vehicles()->pluck("vehicle_id")->toArray(),
                    "vehicles" => VehicleModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Display all resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get customer data (rows and count).
        $data = CustomerModel::allForDataTables($request);

        // Set rows to be displayed in customer table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->contact;
            $row[] = $key->email;
            $row[] = $key->address;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => CustomerModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        try {
            $customer = new CustomerModel();
            $customer->name = $request->name;
            $customer->address = $request->address;
            $customer->contact = $request->contact;
            $customer->email = $request->email;
            $customer->latitude = $request->Latitude;
            $customer->longitude = $request->Longitude;
            $status = $customer->save();

            // Store the list of customers" owned vehicles.
            $customer->vehicles()->attach($request->vehicle);

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new customer was successfully created!" : "Failed to create the new customer!"
            );
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $customer = CustomerModel::find($request->id);
            $customer->name = $request->name;
            $customer->address = $request->address;
            $customer->contact = $request->contact;
            $customer->email = $request->email;
            $customer->latitude = $request->Latitude;
            $customer->longitude = $request->Longitude;
            $status = $customer->save();

            // Update the list of customers" owned vehicles.
            $customer->vehicles()->sync($request->vehicle);

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The customer was successfully updated!" : "Failed to update the customer!"
            );
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $customer = CustomerModel::find($id);

                // Detach associated vehicles from the pivot table
                $customer->vehicles()->detach();

                // Delete customer data in storage.
                $status &= $customer->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected customer was successfully deleted!" : "Failed to delete the selected customer!"
            );
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
