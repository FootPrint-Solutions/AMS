<?php

namespace App\Http\Controllers\MasterData\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = "+62 $key->contact";
            $row[] = $key->email ?? "-";
            $row[] = $key->address;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
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
        DB::beginTransaction();

        try {
            $validatedData = $this->_validateData($request);

            $customer = new CustomerModel();
            $customer->name = $validatedData['name'];
            $customer->address = $validatedData['address'];
            $customer->contact = $validatedData['contact'];
            $customer->email = $validatedData['email'];
            $customer->latitude = $request->Latitude;
            $customer->longitude = $request->Longitude;
            $status = $customer->save();

            // Store the list of customers" owned vehicles.
            $customer->vehicles()->attach($request->vehicle);

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new customer was successfully created!" : "Failed to create the new customer!"
            );
        } catch (ValidationException $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian lainnya
            Log::error($e->getMessage());
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
        DB::beginTransaction();

        try {
            $validatedData = $this->_validateData($request);

            $customer = CustomerModel::find($request->id);
            $customer->name = $validatedData['name'];
            $customer->address = $validatedData['address'];
            $customer->contact = $validatedData['contact'];
            $customer->email = $validatedData['email'];
            $customer->latitude = $request->Latitude;
            $customer->longitude = $request->Longitude;
            $status = $customer->save();

            // Update the list of customers" owned vehicles.
            $customer->vehicles()->sync($request->vehicle);

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The customer was successfully updated!" : "Failed to update the customer!"
            );
        } catch (ValidationException $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian lainnya
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $customer = CustomerModel::find($request->id);
            $customer->status = $customer->status ? 0 : 1;
            $status = $customer->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected customer was successfully updated!" : "Failed to update the selected customer!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Validates the incoming request data for a customer.
     * 
     * @param Request $request The incoming input request to be validated.
     * @return array The validated data..
     */
    private function _validateData(Request $request)
    {
        return $request->validate(
            [
                'name' => 'required|string',
                'address' => 'required|string',
                'contact' => 'required|string',
                'email' => 'nullable|email',
            ],
            [
                'name.required' => 'Customer name is required!',
                'address.required' => 'Customer address is required!',
                'contact.required' => 'Customer contact is required!',
                'email.email' => 'Customer email must be a valid email address!',
            ]
        );
    }
}
