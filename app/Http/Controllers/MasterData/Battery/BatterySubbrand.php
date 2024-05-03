<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// MODELS
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;

class BatterySubbrand extends Controller
{
    private $title = "Battery Subbrand Category";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Subbrand Category index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Subbrand.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Battery Subbrand Category profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Subbrand.create',
            getIndexData(
                $this->title,
                2,
                4
            )
        );
    }

    /**
     * Show the form for editing Battery Subbrand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) return redirect()->route("battery.subbrand.index");
        $subbrand = BatterySubbrandCategoryModel::find($id);
        if ($subbrand == null) return redirect()->route("battery.subbrand.index");
        return view(
            "MasterData.Battery.Subbrand.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => $subbrand->toArray()
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

        // Get battery subbrand data (rows and count).
        $data = BatterySubbrandCategoryModel::allForDataTables($request);

        // Set rows to be displayed in battery subbrand table.
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
            "recordsTotal" => BatterySubbrandCategoryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Battery Subbrand Category resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string'
                ],
                [
                    'name.required' => 'Subbrand name is required!'
                ]
            );

            $subbrand = new BatterySubbrandCategoryModel();
            $subbrand->name = $validatedData['name'];
            $status = $subbrand->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new battery subbrand category was successfully created!" : "Failed to create the new battery subbrand category!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Update the specified Battery Subbrand Category resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                ],
                [
                    'name.required' => 'Subbrand name is required!',
                ]
            );

            $subbrand = BatterySubbrandCategoryModel::find($request->id);
            $subbrand->name = $validatedData['name'];
            $status = $subbrand->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery subbrand category was successfully updated!" : "Failed to update the battery subbrand category!"
            );
        } catch (ValidationException $e) {
            // Tangani pengecualian jika validasi gagal
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
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
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $subbrand = BatterySubbrandCategoryModel::find($id);
                $status = $subbrand->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected subbrand category was successfully deleted!" : "Failed to delete the selected subbrand category!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
