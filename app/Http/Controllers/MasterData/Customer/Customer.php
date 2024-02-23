<?php

namespace App\Http\Controllers\MasterData\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                    "owned_vehicles" => CustomerModel::find($id)->vehicles()->pluck("id_vehicle")->toArray(),
                    "vehicles" => VehicleModel::all()->toArray()
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
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = CustomerModel::query();  //// tinggal custom disini jika ada relasi atau kondisi lain yang diperlukan, jika tidak ada langsung di panggil saja, seperti contoh dibawah ini :
        /// kalo pengen rapih bisa di pindahin ke model :) 

        $selectColumns = $query->getModel()->getFillable(); // ini udah otomatis ambil fillable dari model / query yang di panggil
        $query->select($selectColumns); // ini udah otomatis ambil fillable dari model / query yang di panggil

        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
                    $query->orWhere($column, "like", "%" . $searchValue . "%");
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumnIndex] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        $ListData = $query->orderBy("name", "asc")
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $no = $start + 1;

        foreach ($ListData as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->contact;
            $row[] = $key->email;
            $row[] = $key->address;
            $row[] = $key->id;
            $data[] = $row;
        }


        $recordTotal = CustomerModel::count();

        $recordFiltered = ($searchValue != null) ? $query->count() : $recordTotal;

        $output = [
            "draw" => $draw,
            "recordsTotal" => $recordTotal,
            "recordsFiltered" => $recordFiltered,
            "data" => $data
        ];

        return response()->json($output);
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
    }

    /**
     * Remove the specified Customer resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customer = CustomerModel::find($request->id);

        // Detach associated vehicles from the pivot table
        $customer->vehicles()->detach();

        // Delete customer data in storage.
        $status = $customer->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected customer was successfully deleted!" : "Failed to delete the selected customer!"
        );
    }
}
