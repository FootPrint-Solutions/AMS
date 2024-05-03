<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// MODELS
use App\Models\MasterData\Battery\BatteryUsageTypeModel;

class BatteryUsage extends Controller
{
    private $title = "Battery Usage Type";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Usage Type index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Usage.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }
    /**
     * Show the form for creating Vehicle Usage Type profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Usage.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Battery Usage Type resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) return redirect()->route("battery.usage.index");
        $usage = BatteryUsageTypeModel::find($id);
        if ($usage == null) return redirect()->route("battery.usage.index");
        return view(
            "MasterData.Battery.Usage.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => $usage->toArray()
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

        // Get battery usage data (rows and count).
        $data = BatteryUsageTypeModel::allForDataTables($request);

        // Set rows to be displayed in battery usage table.
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
            "recordsTotal" => BatteryUsageTypeModel::count(),
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
        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string'
                ],
                [
                    'name.required' => 'The battery usage type name is required!',
                    'name.string' => 'The battery usage type name must be a string!',
                ]
            );
            $usage = new BatteryUsageTypeModel();
            $usage->name = $validatedData['name'];
            $status = $usage->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new battery usage type was successfully created!" : "Failed to create the new battery usage type!"
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string'
                ],
                [
                    'name.required' => 'The battery usage type name is required!',
                    'name.string' => 'The battery usage type name must be a string!',
                ]
            );
            $usage = BatteryUsageTypeModel::find($request->id);
            $usage->name = $validatedData['name'];
            $status = $usage->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The battery usage type was successfully updated!" : "Failed to update the battery usage type!"
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
                $usage = BatteryUsageTypeModel::find($id);
                $status = $usage->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected usage type was successfully deleted!" : "Failed to delete the selected usage type!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
