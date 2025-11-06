<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

// MODELS
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderBatteryModel;
use App\Models\MasterData\Supplier\SupplierModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryRecycleModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Settings\TaxModel;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\Inventory\InventoryModel;
use App\Models\Inventory\InventoryDetailModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Inventory\InventoryRecycleDetailModel;

class PurchaseOrder extends Controller
{
    private $title = "Purchase Order";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name']),
            'distributorShops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name']),
        ];

        return view('Orders.PurchaseOrder.index', getIndexData(
            $this->title,
            $data
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
            'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
            'number' => PurchaseOrderModel::generatePurchaseOrderNumber(),
            'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
            'shops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address']),
        ];

        return view(
            'Orders.PurchaseOrder.create',
            getIndexData(
                $this->title,
                $data
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create purchase order
            $purchaseOrder = PurchaseOrderModel::create([
                'purchase_order_number' => $request->purchaseordernumber,
                'date' => $request->date,
                'supplier_id' => $request->supplier,
                'ship_to' => $request->shop,
                'address' => $request->Address,
                'latitude' => $request->Latitude ?? 0,
                'longitude' => $request->Longitude ?? 0,
                'discount_price' => (int)str_replace(['Rp', '.', ' '], '', $request->discountprice ?? '0'),
                'subtotal' => (int)str_replace(['Rp', '.', ' '], '', $request->subtotal ?? '0'),
                'total' => (int)str_replace(['Rp', '.', ' '], '', $request->total ?? '0'),
                'payment_status' => $request->status,
                'status' => 'draft',
                'invoice_number' => $request->InvoiceNumber
            ]);

            // Create purchase order batteries
            if ($request->batteriesid && is_array($request->batteriesid)) {
                foreach ($request->batteriesid as $index => $batteryId) {
                    $battery = BatteryModel::find($batteryId);
                    $batteryType = $request->batteriestype[$index] ?? ($battery ? 'regular' : 'recycle');

                    $batteryName = $request->batteriesname[$index] ?? ($battery ? $battery->name : null);

                    if (!$battery && $batteryType === 'recycle') {
                        $batteryRecycle = BatteryRecycleModel::find($batteryId);
                        $batteryName = $batteryName ?? ($batteryRecycle ? $batteryRecycle->name : null);
                        $batteryType = 'recycle';
                    } else {
                        $batteryType = 'regular';
                    }

                    if ($battery || isset($batteryRecycle)) {
                        $batteryPriceRetail = (int)str_replace(['Rp', '.', ' '], '', $request->batteriespriceretail[$index] ?? '0');
                        $tax = $request->batteriestax[$index] ?? 0;
                        $taxPrice = $batteryPriceRetail * $tax / 100;
                        $discountPrice = (int)str_replace(['Rp', '.', ' '], '', $request->batteriesdiscountprice[$index] ?? '0');
                        $discount = $batteryPriceRetail > 0 ? ($discountPrice / $batteryPriceRetail) * 100 : 0;
                        $priceNet = $batteryPriceRetail + $taxPrice - $discountPrice;

                        PurchaseOrderBatteryModel::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'source' => $batteryType,
                            'battery_id' => $batteryId,
                            'battery_name' => $batteryName,
                            'battery_price_retail' => $batteryPriceRetail,
                            'tax' => $tax,
                            'tax_price' => $taxPrice,
                            'discount' => $discount,
                            'discount_price' => $discountPrice,
                            'price_net' => $priceNet,
                            'quantity' => 1,
                            'battery_production_code' => $request->batteriescode[$index] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return getResponseData(
                true,
                'Purchase order created successfully!'
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Store Error: ' . $e->getMessage());

            return getResponseData(
                false,
                'An error occurred while creating purchase order.'
            );
        }
    }

    /**
     * Display all resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get DataTables parameters
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get purchase order data (rows and count)
        $data = PurchaseOrderModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Payment status badge
            if ($key->payment_status == "paid") {
                $paymentStatusBadgeClass = "badge-success";
            } else if ($key->payment_status == "pending") {
                $paymentStatusBadgeClass = "badge-warning";
            } else {
                $paymentStatusBadgeClass = "badge-danger";
            }

            // Status badge
            if ($key->status == "draft") {
                $statusBadgeClass = "badge-secondary text-dark";
            } else if ($key->status == "posted") {
                $statusBadgeClass = "badge-success";
            } else {
                $statusBadgeClass = "badge-info";
            }

            // Action buttons (edit/delete)
            $action = '
                <a href="' . route('purchase-order.edit', $key->id) . '" class="btn btn-sm btn-primary">Edit</a>
                <button data-id="' . $key->id . '" class="btn btn-sm btn-danger btn-delete">Delete</button>
            ';

            $row = [];
            $row[] = $key->id;
            $row[] = $key->purchase_order_number;
            $row[] = $key->invoice_number ?? "<p class='text-center'>-</p>";
            $row[] = formatDate($key->date);
            $row[] = $key->supplier_name ?? "<p class='text-center'>-</p>";
            $row[] = $key->shop_name ?? "<p class='text-center'>-</p>";
            $row[] = formatPrice($key->subtotal);
            $row[] = formatPrice($key->discount_price);
            $row[] = formatPrice($key->total);
            $row[] = "<span class='badge $paymentStatusBadgeClass'>$key->payment_status</span>";
            $row[] = "<span class='badge $statusBadgeClass'>$key->status</span>";
            $row[] = $action;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => PurchaseOrderModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrderModel::with('supplier', 'batteries.battery')->findOrFail($id);
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-order.index')->with('error', 'Only draft purchase orders can be edited.');
        }

        $data = [
            'profile' => $purchaseOrder->toArray(),
            'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
            'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
            'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
            'shops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address']),
        ];

        // Prepare batteries data for the form
        $data['profile']['batteries'] = $purchaseOrder->batteries->map(function ($battery) {
            return [
                'id' => $battery->id,
                'battery_id' => $battery->battery_id,
                'battery_name' => $battery->battery_name,
                'battery_price_retail' => $battery->battery_price_retail,
                'tax' => $battery->tax,
                'tax_price' => $battery->tax_price,
                'discount' => $battery->discount,
                'discount_price' => $battery->discount_price,
                'price_net' => $battery->price_net,
                'quantity' => $battery->quantity,
                'battery_production_code' => $battery->battery_production_code,
                'type' => $battery->source ?? 'regular',
            ];
        })->toArray();

        return view('Orders.PurchaseOrder.create', getIndexData(
            $this->title,
            $data
        ));
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
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrderModel::findOrFail($request->id);

            $purchaseOrder->update([
                'purchase_order_number' => $request->purchaseordernumber,
                'date' => $request->date,
                'supplier_id' => $request->supplier,
                'ship_to' => $request->shop,
                'address' => $request->Address,
                'latitude' => $request->Latitude ?? 0,
                'longitude' => $request->Longitude ?? 0,
                'discount_price' => (int)str_replace(['Rp', '.', ' '], '', $request->discountprice ?? '0'),
                'subtotal' => (int)str_replace(['Rp', '.', ' '], '', $request->subtotal ?? '0'),
                'total' => (int)str_replace(['Rp', '.', ' '], '', $request->total ?? '0'),
                'payment_status' => $request->status,
                'invoice_number' => $request->InvoiceNumber
            ]);

            if ($request->detailid && is_array($request->detailid)) {
                foreach ($request->detailid as $index => $detailId) {
                    if (empty($detailId)) {
                        continue;
                    }

                    $detail = PurchaseOrderBatteryModel::find($detailId);
                    if (!$detail) {
                        continue;
                    }

                    $batteryIdInput = $request->batteriesid[$index] ?? null;
                    $battery = null;
                    $batteryRecycle = null;
                    if ($batteryIdInput) {
                        $battery = BatteryModel::find($batteryIdInput);
                        if (!$battery) {
                            $batteryRecycle = BatteryRecycleModel::find($batteryIdInput);
                        }
                    }

                    $batteryIdToSave = $battery ? $battery->id : ($batteryRecycle ? $batteryRecycle->id : $detail->battery_id);
                    $batteryNameToSave = $battery ? $battery->name : ($batteryRecycle ? $batteryRecycle->name : $detail->battery_name);

                    $batteryPriceRetailRaw = $request->batteriespriceretail[$index] ?? $request->batteriesprice[$index] ?? null;
                    $batteryPriceRetail = $batteryPriceRetailRaw !== null
                        ? (int)str_replace(['Rp', '.', ' '], '', $batteryPriceRetailRaw)
                        : ($detail->battery_price_retail ?? 0);

                    $batteryType = $request->batteriestype[$index] ?? $detail->source ?? 'regular';
                    $tax = isset($request->batteriestax[$index]) ? (float)$request->batteriestax[$index] : ($detail->tax ?? 0);
                    $taxPrice = $batteryPriceRetail * $tax / 100;

                    $discountPrice = isset($request->batteriesdiscountprice[$index])
                        ? (int)str_replace(['Rp', '.', ' '], '', $request->batteriesdiscountprice[$index])
                        : ($detail->discount_price ?? 0);

                    $discount = $batteryPriceRetail > 0 ? ($discountPrice / $batteryPriceRetail) * 100 : 0;
                    $priceNet = $batteryPriceRetail + $taxPrice - $discountPrice;

                    $batteryCode = $request->batteriescode[$index] ?? $detail->battery_production_code ?? null;

                    $detail->update([
                        'source' => $batteryType,
                        'battery_id' => $batteryIdToSave,
                        'battery_name' => $batteryNameToSave,
                        'battery_price_retail' => $batteryPriceRetail,
                        'tax' => $tax,
                        'tax_price' => $taxPrice,
                        'discount' => $discount,
                        'discount_price' => $discountPrice,
                        'price_net' => $priceNet,
                        'quantity' => $detail->quantity ?? 1,
                        'battery_production_code' => $batteryCode,
                    ]);
                }
            }

            DB::commit();

            return getResponseData(
                true,
                'Purchase order updated successfully!'
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Update Error: ' . $e->getMessage());

            return getResponseData(
                false,
                'An error occurred while updating purchase order.'
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            DB::beginTransaction();
            foreach ($ids as $id) {
                $purchaseOrder = PurchaseOrderModel::find($id);
                if ($purchaseOrder) {
                    if ($purchaseOrder->status !== 'draft') {
                        continue;
                    }
                    $purchaseOrder->batteries()->forceDelete();
                    $purchaseOrder->forceDelete();
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase order(s) deleted successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Order Delete Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting purchase order.'
            ], 500);
        }
    }

    public function getPrint(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!$ids) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter ids is required.'
                ], 400);
            }

            $purchaseOrderIds = explode(',', $ids);
            $purchaseOrders = PurchaseOrderModel::with('batteries.battery', 'supplier')
                ->whereIn('id', $purchaseOrderIds)
                ->get();

            if ($purchaseOrders->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Purchase Order(s) not found.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $purchaseOrders
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase Order not found.'
            ], 404);
        } catch (Exception $e) {
            Log::error('Get Purchase Order Print Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve purchase order for printing: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($ids)
    {
        $ids = explode(",", $ids);
        $purchaseOrders = PurchaseOrderModel::with([
            'batteries.battery',
            'supplier'
        ])->whereIn('id', $ids)->get();
        // dd($purchaseOrders);

        foreach ($purchaseOrders as $order) {
            if ($order->status === 'draft') {
                $order->status = 'posted';
                $order->save();
            }
        }

        // Get the first purchase order for filename
        $firstOrder = $purchaseOrders->first();
        $supplierName = $firstOrder->supplier ? $firstOrder->supplier->name : 'Unknown Supplier';
        $poNumber = $firstOrder->purchase_order_number;
        $fileName = 'PO ' . $supplierName . ' ' . $poNumber;

        $view = view(
            'Orders.PurchaseOrder.print.multiple',
            getIndexData(
                $this->title,
                [
                    "profile" => $purchaseOrders->toArray(),
                    "company" => CompanyModel::first(),
                    "fileName" => $fileName,
                ]
            )
        );

        $view->with('fileName', $fileName);

        return $view;
    }

    public function post(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        try {
            DB::beginTransaction();
            foreach ($ids as $id) {
                $purchaseOrder = PurchaseOrderModel::with('batteries')->find($id);
                if ($purchaseOrder && $purchaseOrder->status === 'draft') {
                    $purchaseOrder->status = 'posted';
                    $purchaseOrder->save();

                    foreach ($purchaseOrder->batteries as $battery) {
                        $batteryId = $battery->battery_id;
                        $batteryType = $battery->source ?? 'regular';
                        $batteryCode = $battery->battery_production_code ?? null;
                        $shopId = $purchaseOrder->ship_to;
                        if ($batteryType == 'regular') {
                            $inventory = InventoryModel::where('battery_id', $batteryId)->first();
                            if ($inventory) {
                                $inventory->stock += $battery->quantity;
                                $inventory->save();
                            } else {
                                $inventory = InventoryModel::create([
                                    'battery_id' => $batteryId,
                                    'code' => $batteryCode,
                                    'stock' => $battery->quantity,
                                ]);
                            }

                            InventoryDetailModel::create([
                                'inventory_id' => $inventory->id,
                                'distributor_shop_id' => $shopId,
                                'battery_id' => $batteryId,
                                'type' => 'in',
                                'reference' => 'purchase_order',
                                'quantity' => $battery->quantity,
                                'sold' => 0,
                                'note' => null,
                                'reference_id' => $purchaseOrder->id,
                                'reference_type' => PurchaseOrderModel::class,
                            ]);
                        } else if ($batteryType == 'recycle') {
                            $inventoryRecycle = InventoryRecycleModel::where('battery_recycle_id', $batteryId)->first();
                            if ($inventoryRecycle) {
                                $inventoryRecycle->stock += $battery->quantity;
                                $inventoryRecycle->save();
                            } else {
                                $inventoryRecycle = InventoryRecycleModel::create([
                                    'battery_id' => NULL,
                                    'battery_recycle_id' => $batteryId,
                                    'code' => $batteryCode,
                                    'stock' => $battery->quantity,
                                ]);
                            }

                            InventoryRecycleDetailModel::create([
                                'inventory_id' => $inventoryRecycle->id,
                                'distributor_shop_id' => $shopId,
                                'battery_id' => NULL,
                                'battery_recycle_id' => $batteryId,
                                'type' => 'in',
                                'reference' => 'purchase_order',
                                'quantity' => $battery->quantity,
                                'reference_id' => $purchaseOrder->id,
                                'reference_type' => PurchaseOrderModel::class,
                            ]);
                        } else {
                            Log::warning('Unknown battery type for purchase order battery', [
                                'purchase_order_id' => $purchaseOrder->id,
                                'battery_id' => $batteryId,
                                'type' => $batteryType,
                            ]);
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Unknown battery type found.'
                            ], 400);
                        }
                    }
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase order(s) posted successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Post Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while posting purchase order.'
            ], 500);
        }
    }
}
