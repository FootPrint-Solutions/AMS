<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\Inventory\InventoryDetailModel;


class InventoryRecycle extends Controller
{
    private $title = "Inventory Recycle";

    /**
     * Show the Inventory index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Inventory.InventoryRecycle.index",
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating Inventory Recycle profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "Inventory.InventoryRecycle.create",
            getIndexData(
                $this->title
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
        $brand = InventoryDetailModel::find($id);
        if ($brand == null) {
            return redirect()->route("battery.brand.index");
        }

        return view(
            "Inventory.InventoryRecycle.create",
            getIndexData(
                $this->title,
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
        $data = InventoryDetailModel::allForDataTables($request);

        // Set rows to be displayed in battery brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->salesOrderBattery->salesOrder->date;
            $row[] = $key->salesOrderBattery->salesOrder->sales_order_number;
            $row[] = $key->salesOrderBattery->salesOrder->customer->name;
            $row[] = $key->battery->name;
            $row[] = $key->quantity;
            $row[] = $key->salesOrderBattery->price_net;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => InventoryDetailModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Inventory Recycle resource in database.
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

            $brand = new InventoryDetailModel();
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

            $brand = InventoryDetailModel::find($request->id);
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
                $brand = InventoryDetailModel::find($id);
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
