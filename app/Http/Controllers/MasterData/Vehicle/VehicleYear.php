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
use App\Models\MasterData\Vehicle\VehicleYearModel;

class VehicleYear extends Controller
{
    private $title = "Vehicle Year";

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData.Vehicle.Year.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Vehicle Year profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Vehicle.Year.create',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for editing Vehicle Year resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('vehicle.Year.index');
        }
        $Year = VehicleYearModel::find($id);
        if ($Year == null) {
            return redirect()->route('vehicle.Year.index');
        }

        return view(
            'MasterData.Vehicle.Year.create',
            getIndexData(
                $this->title,
                array(
                    'profile' => $Year->toArray(),
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

        // Get vehicle Year data (rows and count).
        $data = VehicleYearModel::allForDataTables($request);

        // Set rows to be displayed in vehicle Year table.
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
            $row[] = $key->start_year;
            $row[] = $key->end_year;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
            $no++;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => VehicleYearModel::count(),
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
                    'start_year' => 'required|int',
                    'end_year' => 'required|int',
                ],
                [
                    'start_year.required' => 'Vehicle Start Year name is required!',
                    'end_year.required' => 'Vehicle End Year name is required!',
                ]
            );

            $Year = new VehicleYearModel();
            $Year->start_year = $validatedData['start_year'];
            $Year->end_year = $validatedData['end_year'];
            $status = $Year->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The new vehicle Year was successfully created!' : 'Failed to create the new vehicle Year!'
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
                    'start_year' => 'required|int',
                    'end_year' => 'required|int',
                ],
                [
                    'start_year.required' => 'Vehicle Start Year name is required!',
                    'end_year.required' => 'Vehicle End Year name is required!',
                ]
            );

            $Year = VehicleYearModel::find($request->id);
            $Year->start_year = $validatedData['start_year'];
            $Year->end_year = $validatedData['end_year'];
            $status = $Year->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The vehicle Year was successfully updated!' : 'Failed to update the vehicle Year!'
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
            $Year = VehicleYearModel::find($request->id);
            $Year->status = $Year->status ? 0 : 1;
            $status = $Year->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected vehicle Year was successfully updated!" : "Failed to update the selected vehicle Year!"
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
