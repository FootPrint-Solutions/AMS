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
use App\Models\MasterData\Vehicle\VehicleBrandModel;

class VehicleBrand extends Controller
{
    private $title = "Vehicle Brand";

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData.Vehicle.Brand.index',
            getIndexData(
                $this->title
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
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for editing Vehicle Brand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('vehicle.brand.index');
        }
        $brand = VehicleBrandModel::find($id);
        if ($brand == null) {
            return redirect()->route('vehicle.brand.index');
        }

        return view(
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title,
                array(
                    'profile' => $brand->toArray(),
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

        // Get vehicle brand data (rows and count).
        $data = VehicleBrandModel::allForDataTables($request);

        // Set rows to be displayed in vehicle brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            if ($key->visible == 0) {
                $statusIndicatorColorVisible = "fa-eye-slash text-muted";
            } else {
                $statusIndicatorColorVisible = "fa-eye text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = number_format($no, 0);
            $row[] = $key->name;
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = "<i class='fa-solid $statusIndicatorColorVisible'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
            $no++;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => VehicleBrandModel::count(),
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
                    'name.required' => 'Vehicle brand name is required!',
                ]
            );

            $brand = new VehicleBrandModel();
            $brand->name = $validatedData['name'];
            $status = $brand->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The new vehicle brand was successfully created!' : 'Failed to create the new vehicle brand!'
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
                    'name.required' => 'Vehicle brand name is required!',
                ]
            );

            $brand = VehicleBrandModel::find($request->id);
            $brand->name = $validatedData['name'];
            $status = $brand->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? 'The vehicle brand was successfully updated!' : 'Failed to update the vehicle brand!'
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
            $brand = VehicleBrandModel::find($request->id);
            $brand->status = $brand->status ? 0 : 1;
            $status = $brand->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected vehicle brand was successfully updated!" : "Failed to update the selected vehicle brand!"
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

    public function toggleVisible(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return getResponseData(false, 'No vehicle brands selected!');
            }

            // Toggle visibility for each selected brand
            foreach ($ids as $id) {
                $brand = VehicleBrandModel::find($id);
                if ($brand) {
                    $brand->visible = $brand->visible ? 0 : 1;
                    $status = $brand->save();
                    if (!$status) {
                        DB::rollBack();
                        return getResponseData(false, 'Failed to update visibility for some vehicle brands!');
                    }
                }
            }

            DB::commit();

            return getResponseData(true, 'Visibility status updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false, 'An error occurred while updating visibility status!');
        }
    }
}
