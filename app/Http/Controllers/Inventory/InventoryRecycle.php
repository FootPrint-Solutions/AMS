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
use App\Models\Inventory\InventoryRecycleDetailModel;
use App\Models\MasterData\Distributor\DistributorShopModel;


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
                $this->title,
                array(
                    "distributorShops" => DistributorShopModel::all()
                )
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
        $brand = InventoryRecycleDetailModel::find($id);
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
        $data = InventoryRecycleDetailModel::allForDataTables($request);

        // Set rows to be displayed in battery brand table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {

            if ($key->reference === 'Sales Order Battery') {
                if ($key->salesOrderBattery->salesOrder->type === 'recycle') {
                    $date = isset($key->salesOrderBattery->salesOrder) ? formatDate($key->salesOrderBattery->salesOrder->date) : '-';
                    $orderNumber = $key->salesOrderBattery->salesOrder->sales_order_number ?? '-';

                    if ($key->battery && $key->battery->trashed()) {
                        $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                    } elseif ($key->batteryRecycle && $key->batteryRecycle->trashed()) {
                        $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $battery = $key->battery->name ?? $key->batteryRecycle->name ?? '-';
                    }

                    $batteryPrice = isset($key->salesOrderBattery) ? formatPrice($key->salesOrderBattery->price_net) : '-';
                    $batteryProductionCode = $key->salesOrderBattery->battery_production_code ?? '-';

                    if ($key->salesOrderBattery->salesOrder->vendorData && $key->salesOrderBattery->salesOrder->vendorData->trashed()) {
                        $vendor = ($key->salesOrderBattery->salesOrder->vendorData->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $vendor = $key->salesOrderBattery->salesOrder->vendorData->name ?? '-';
                    }

                    if ($key->salesOrderBattery->salesOrder->shipToData && $key->salesOrderBattery->salesOrder->shipToData->trashed()) {
                        $distributorShop = ($key->salesOrderBattery->salesOrder->shipToData->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $distributorShop = $key->salesOrderBattery->salesOrder->shipToData->name ?? '-';
                    }
                } else {
                    $date = isset($key->salesOrderBattery->salesOrder) ? formatDate($key->salesOrderBattery->salesOrder->date) : '-';
                    $orderNumber = $key->salesOrderBattery->salesOrder->sales_order_number ?? '-';

                    if ($key->battery && $key->battery->trashed()) {
                        $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                    } elseif ($key->batteryRecycle && $key->batteryRecycle->trashed()) {
                        $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $battery = $key->battery->name ?? $key->batteryRecycle->name ?? '-';
                    }

                    $batteryPrice = isset($key->salesOrderBattery) ? formatPrice($key->salesOrderBattery->price_net) : '-';
                    $batteryProductionCode = $key->salesOrderBattery->battery_production_code ?? '-';

                    if ($key->salesOrderBattery->salesOrder->customer && $key->salesOrderBattery->salesOrder->customer->trashed()) {
                        $vendor = ($key->salesOrderBattery->salesOrder->customer->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $vendor = $key->salesOrderBattery->salesOrder->customer->name ?? '-';
                    }

                    if ($key->salesOrderBattery->salesOrder->distributorShop && $key->salesOrderBattery->salesOrder->distributorShop->trashed()) {
                        $distributorShop = ($key->salesOrderBattery->salesOrder->distributorShop->name ?? '-') . ' (Was Deleted)';
                    } else {
                        $distributorShop = $key->salesOrderBattery->salesOrder->distributorShop->name ?? '-';
                    }
                }
            } elseif ($key->reference === 'Purchase Order' || $key->reference === 'Purchase Order Battery') {
                $date = isset($key->purchaseOrder) ? formatDate($key->purchaseOrder->date) : '-';

                if ($key->purchaseOrder && method_exists($key->purchaseOrder, 'trashed') && $key->purchaseOrder->trashed()) {
                    $orderNumber = ($key->purchaseOrder->purchase_order_number ?? '-') . ' (Was Deleted)';
                } else {
                    $orderNumber = $key->purchaseOrder->purchase_order_number ?? '-';
                }

                if ($key->battery && $key->battery->trashed()) {
                    $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                } elseif ($key->batteryRecycle && $key->batteryRecycle->trashed()) {
                    $battery = ($key->battery->name ?? $key->batteryRecycle->name ?? '-') . ' (Was Deleted)';
                } else {
                    $battery = $key->batteryRecycle->name ?? $key->battery->name ?? '-';
                }

                $batteryPrice = isset($key->purchaseOrder) ? formatPrice($key->purchaseOrder->batteries->firstWhere('battery_id', $key->battery_recycle_id)->price_net ?? '0') : '0';
                $batteryProductionCode = isset($key->purchaseOrder) ? $key->purchaseOrder->batteries->firstWhere('battery_id', $key->battery_recycle_id)->battery_production_code ?? '-' : '-';

                if ($key->purchaseOrder && $key->purchaseOrder->supplier && $key->purchaseOrder->supplier->trashed()) {
                    $vendor = ($key->purchaseOrder->supplier->name ?? '-') . ' (Was Deleted)';
                } elseif ($key->purchaseOrder && $key->purchaseOrder->supplier) {
                    $vendor = $key->purchaseOrder->supplier->name ?? '-';
                } else {
                    $vendor = '-';
                }

                if ($key->purchaseOrder && $key->purchaseOrder->shipTo && $key->purchaseOrder->shipTo->trashed()) {
                    $distributorShop = ($key->purchaseOrder->shipTo->name ?? '-') . ' (Was Deleted)';
                } elseif ($key->purchaseOrder && $key->purchaseOrder->shipTo) {
                    $distributorShop = $key->purchaseOrder->shipTo->name ?? '-';
                } else {
                    $distributorShop = '-';
                }
            } else {
                $date = '-';
                $orderNumber = '-';
                $distributorShop = '-';
                $battery = '-';
                $batteryPrice = '-';
                $batteryProductionCode = '-';

                $vendor = '-';
            }

            $row = [];
            $row[] = $no++;
            $row[] = $date;
            $row[] = $orderNumber;
            $row[] = $vendor;
            $row[] = $distributorShop;
            $row[] = $battery;
            $row[] = $batteryProductionCode;

            if ($key->type === 'in') {
                $row[] = '<span style="color:green;"><i class="fas fa-arrow-down"></i> IN</span>';
            } elseif ($key->type === 'out') {
                $row[] = '<span style="color:red;"><i class="fas fa-arrow-up"></i> OUT</span>';
            } elseif ($key->type === 'adjustment') {
                $row[] = '<span style="color:orange;"><i class="fas fa-exchange-alt"></i> ADJ</span>';
            } else {
                $row[] = '-';
            }
            $row[] = $key->quantity ?? '-';
            $row[] = $batteryPrice;
            $row[] = $key->id ?? '-';
            $row[] = $key->sold ?? '-';
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => InventoryRecycleDetailModel::count(),
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

            $brand = new InventoryRecycleDetailModel();
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

            $brand = InventoryRecycleDetailModel::find($request->id);
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
                $brand = InventoryRecycleDetailModel::find($id);
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

    /**
     * Mark the selected battery brand as sold out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function soldOut(Request $request)
    {
        DB::beginTransaction();

        try {
            $status = true;
            $ids = $request->input('ids', []);
            foreach ($ids as $id) {
                $brand = InventoryRecycleDetailModel::find($id);
                if ($brand) {
                    $brand->sold = 1;
                    $brand->sold_at = now(); // Use Laravel's helper for current timestamp
                    $status = $brand->save();
                    if (!$status) {
                        DB::rollBack();
                        return getResponseData(false, "Failed to mark brand with ID {$id} as sold out!");
                    }
                } else {
                    DB::rollBack();
                    return getResponseData(false, "Brand with ID {$id} not found!");
                }
            }

            DB::commit();

            // Set a new response data to be sent.
            return getResponseData(
                true,
                "The selected brands were successfully marked as sold out!"
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
