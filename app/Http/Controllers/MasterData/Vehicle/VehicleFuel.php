<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\MasterData\Vehicle\VehicleFuelModel;

class VehicleFuel extends Controller
{
    private $title = "Vehicle Fuel";

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData.Vehicle.Fuel.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Vehicle Fuel profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Vehicle.Fuel.create',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for editing Vehicle Fuel resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('vehicle.Fuel.index');
        }
        $Fuel = VehicleFuelModel::find($id);
        if ($Fuel == null) {
            return redirect()->route('vehicle.Fuel.index');
        }

        return view(
            'MasterData.Vehicle.Fuel.create',
            getIndexData(
                $this->title,
                array(
                    'profile' => $Fuel->toArray(),
                )
            )
        );
    }

    /**
     * Display all resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get vehicle Fuel data (rows and count).
        $data = VehicleFuelModel::allForDataTables($request);

        // Set rows to be displayed in vehicle Fuel table.
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
            $row[] = number_format($no, 0);
            $row[] = $key->name;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
            $no++;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => VehicleFuelModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                ],
                [
                    'name.required' => 'Vehicle Fuel name is required!',
                ]
            );

            $Fuel = new VehicleFuelModel();
            $Fuel->name = $validatedData['name'];
            $status = $Fuel->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The new vehicle Fuel was successfully created!' : 'Failed to create the new vehicle Fuel!'
            );
        } catch (ValidationException $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                ],
                [
                    'name.required' => 'Vehicle Fuel name is required!',
                ]
            );

            $Fuel = VehicleFuelModel::find($request->id);
            $Fuel->name = $validatedData['name'];
            $status = $Fuel->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The vehicle Fuel was successfully updated!' : 'Failed to update the vehicle Fuel!'
            );
        } catch (ValidationException $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $Fuel = VehicleFuelModel::find($request->id);
            $Fuel->status = $Fuel->status ? 0 : 1;
            $status = $Fuel->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected vehicle Fuel was successfully updated!" : "Failed to update the selected vehicle Fuel!"
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
}
