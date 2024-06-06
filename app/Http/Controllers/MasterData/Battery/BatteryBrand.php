<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\MasterData\Battery\BatteryBrandModel;

class BatteryBrand extends Controller
{
    private $title = "Battery Brand";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Brand.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
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
            "MasterData.Battery.Brand.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Battery Brand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route("battery.brand.index");
        }
        $brand = BatteryBrandModel::find($id);
        if ($brand == null) {
            return redirect()->route("battery.brand.index");
        }

        return view(
            "MasterData.Battery.Brand.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => $brand->toArray()
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
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        // Get battery brand data (rows and count).
        $data = BatteryBrandModel::allForDataTables($request);

        // Set rows to be displayed in battery brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => BatteryBrandModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Vehicle Brand resource in database.
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
                    'name.required' => 'Battery brand name is required!',
                ]
            );

            $brand = new BatteryBrandModel();
            $brand->name = $validatedData['name'];
            $status = $brand->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new battery brand was successfully created!" : "Failed to create the new battery brand!"
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
     * Update the specified Battery Brand resource in storage.
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
                    'name.required' => 'Battery brand name is required!',
                ]
            );

            $brand = BatteryBrandModel::find($request->id);
            $brand->name = $validatedData['name'];
            $status = $brand->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery brand was successfully updated!" : "Failed to update the battery brand!"
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
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $brand = BatteryBrandModel::find($id);
                $status = $brand->delete();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected brand was successfully deleted!" : "Failed to delete the selected brand!"
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
