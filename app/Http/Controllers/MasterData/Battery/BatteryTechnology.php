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
use App\Models\MasterData\Battery\BatteryTechnologyModel;

class BatteryTechnology extends Controller
{
    private $title = "Battery Technology";

    /**
     * Show the Battery Technology index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Technology.index",
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Battery Technology profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Technology.create',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for editing Battery Technolgoy resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id == null) return redirect()->route("Battery.Technology.index");
        $technology = BatteryTechnologyModel::find($id);
        if ($technology == null) return redirect()->route("Battery.Technology.index");
        return view(
            "MasterData.Battery.Technology.create",
            getIndexData(
                $this->title,
                array(
                    "profile" => $technology->toArray()
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

        // Get battery technology data (rows and count).
        $data = BatteryTechnologyModel::allForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => BatteryTechnologyModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Battery Technology resource in database.
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
                    'name' => 'required|string|'
                ],
                [
                    'name.required' => 'The battery technology name is required!',
                    'name.string' => 'The battery technology name must be a string!'
                ]
            );
            $technology = new BatteryTechnologyModel();
            $technology->name = $validatedData["name"];
            $status = $technology->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new battery technology was successfully created!" : "Failed to create the new battery technology!"
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
     * Update the specified Battery Technology resource in storage.
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
                    'name.required' => 'The battery technology name is required!',
                    'name.string' => 'The battery technology name must be a string!',
                ]
            );

            $technology = BatteryTechnologyModel::find($request->id);
            $technology->name = $validatedData['name'];
            $status = $technology->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery technology was successfully updated!" : "Failed to update the battery technology!"
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
                $technology = BatteryTechnologyModel::find($id);
                $status = $technology->delete();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected technology was successfully deleted!" : "Failed to delete the selected technology!"
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
