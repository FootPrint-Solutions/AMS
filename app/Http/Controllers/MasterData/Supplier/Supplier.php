<?php

namespace App\Http\Controllers\MasterData\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\MasterData\Supplier\SupplierModel;
use App\Models\MasterData\Vehicle\VehicleModel;

class Supplier extends Controller
{
    private $title = "Supplier";

    /**
     * Show the Supplier index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Supplier.index",
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Supplier profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "MasterData.Supplier.create",
            getIndexData(
                $this->title,
                array(
                    "vehicles" => VehicleModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing Supplier profile resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "MasterData.Supplier.create",
            getIndexData(
                $this->title,
                array(
                    "profile" => SupplierModel::find($id)->toArray(),
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

        // Get supplier data (rows and count).
        $data = SupplierModel::allForDataTables($request);

        // Set rows to be displayed in supplier table.
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
            "recordsTotal" => SupplierModel::count(),
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

            $supplier = new SupplierModel();
            $supplier->name = $validatedData['name'];
            $supplier->address = $validatedData['address'];
            $supplier->contact = $validatedData['contact'];
            $supplier->email = $validatedData['email'];
            $supplier->latitude = $request->Latitude;
            $supplier->longitude = $request->Longitude;
            $status = $supplier->save();


            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new supplier was successfully created!" : "Failed to create the new supplier!"
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

            $supplier = SupplierModel::find($request->id);
            $supplier->name = $validatedData['name'];
            $supplier->address = $validatedData['address'];
            $supplier->contact = $validatedData['contact'];
            $supplier->email = $validatedData['email'];
            $supplier->latitude = $request->Latitude;
            $supplier->longitude = $request->Longitude;
            $status = $supplier->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The supplier was successfully updated!" : "Failed to update the supplier!"
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
            $supplier = SupplierModel::find($request->id);
            $supplier->status = $supplier->status ? 0 : 1;
            $status = $supplier->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected supplier was successfully updated!" : "Failed to update the selected supplier!"
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
     * Validates the incoming request data for a supplier.
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
                'name.required' => 'Supplier name is required!',
                'address.required' => 'Supplier address is required!',
                'contact.required' => 'Supplier contact is required!',
                'email.email' => 'Supplier email must be a valid email address!',
            ]
        );
    }
}
