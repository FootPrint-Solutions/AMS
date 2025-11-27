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
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopBatteryModel;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRecycle()
    {
        $data = [
            'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
            'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
            'number' => PurchaseOrderModel::generatePurchaseOrderNumber(),
            'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
            'shops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address']),
        ];

        return view(
            'Orders.PurchaseOrder.recycle.create',
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

            $vendor = explode('-', $request->vendor);
            $shipTo = explode('-', $request->ship_to);

            // Create purchase order
            $purchaseOrder = PurchaseOrderModel::create([
                'purchase_order_number' => $request->purchaseordernumber,
                'date' => $request->date,
                'vendor_id' => $vendor[0] ?? null,
                'vendor_type' => $vendor[1] ?? null,
                'ship_to_id' => $shipTo[0] ?? null,
                'ship_to_type' => $shipTo[1] ?? null,
                'address' => $request->Address,
                'latitude' => $request->Latitude ?? 0,
                'longitude' => $request->Longitude ?? 0,
                'discount_price' => (int)str_replace(['Rp', '.', ' '], '', $request->discountprice ?? '0'),
                'subtotal' => (int)str_replace(['Rp', '.', ' '], '', $request->subtotal ?? '0'),
                'total' => (int)str_replace(['Rp', '.', ' '], '', $request->total ?? '0'),
                'payment_status' => $request->status,
                'status' => 'draft',
                'invoice_number' => $request->InvoiceNumber,
                'type' => $request->type ?? 'regular',
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
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);

        $status = $request->input('status', null);
        $vendorId = $request->input('vendor_id', null);
        $shipToId = $request->input('ship_to_id', null);
        $dateStart = $request->input('dateStart', null);
        $dateEnd = $request->input('dateEnd', null);
        $search = $request->input('search.value', null);
        $orderColumnIndex = $request->input('order.0.column', null);
        $orderDirection = $request->input('order.0.dir', 'asc');

        $query = PurchaseOrderModel::with(['vendor', 'shipTo']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($vendorId)) {
            $query->where('vendor_id', $vendorId);
        }

        if (!empty($shipToId)) {
            $query->where('ship_to_id', $shipToId);
        }

        if (!empty($dateStart) && !empty($dateEnd)) {
            $query->whereBetween('date', [$dateStart, $dateEnd]);
        } elseif (!empty($dateStart)) {
            $query->where('date', '>=', $dateStart);
        } elseif (!empty($dateEnd)) {
            $query->where('date', '<=', $dateEnd);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('shipTo', function ($shipToQuery) use ($search) {
                        $shipToQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($orderColumnIndex !== null) {
            $columns = [
                0 => 'id',
                1 => 'purchase_order_number',
                2 => 'invoice_number',
                3 => 'date',
                4 => 'vendor_id',
                5 => 'ship_to_id',
                6 => 'subtotal',
                7 => 'discount_price',
                8 => 'total',
                9 => 'payment_status',
                10 => 'status',
            ];

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $data = $query->get()->toArray();


        $rows = [];
        $no = $start + 1;
        foreach ($data as $item) {
            // payment status badge class
            $paymentStatus = $item['payment_status'] ?? '';
            if ($paymentStatus === "paid") {
                $paymentStatusBadgeClass = "badge-success";
            } elseif ($paymentStatus === "pending") {
                $paymentStatusBadgeClass = "badge-warning";
            } else {
                $paymentStatusBadgeClass = "badge-danger";
            }

            // status badge class
            $status = $item['status'] ?? '';
            if ($status === "draft") {
                $statusBadgeClass = "badge-secondary text-dark";
            } elseif ($status === "posted") {
                $statusBadgeClass = "badge-success";
            } else {
                $statusBadgeClass = "badge-info";
            }

            $type = $item['type'] ?? '';
            if ($type === "regular") {
                $typeBadgeClass = "<span class='badge badge-success'>Regular</span>";
            } elseif ($type === "recycle") {
                $typeBadgeClass = "<span class='badge badge-warning text-dark'>Recycle</span>";
            } else {
                $typeBadgeClass = "<span class='badge badge-secondary'>Unknown</span>";
            }

            $id = $item['id'] ?? null;

            $action = '
                <a href="' . route('purchase-order.edit', $id) . '" class="btn btn-sm btn-primary">Edit</a>
                <button data-id="' . $id . '" class="btn btn-sm btn-danger btn-delete">Delete</button>
            ';

            $vendorName = $item['vendor']['name'] ?? "<p class='text-center'>-</p>";
            $shopName = $item['ship_to']['name'] ?? "<p class='text-center'>-</p>";

            $row = [];
            $row[] = $id;
            $row[] = $item['purchase_order_number'] . " " . $typeBadgeClass;
            $row[] = $item['invoice_number'] ?? "<p class='text-center'>-</p>";
            $row[] = isset($item['date']) ? formatDate($item['date']) : '';
            $row[] = $vendorName;
            $row[] = $shopName;
            $row[] = formatPrice($item['subtotal'] ?? 0);
            $row[] = formatPrice($item['discount_price'] ?? 0);
            $row[] = formatPrice($item['total'] ?? 0);
            $row[] = "<span class='badge $paymentStatusBadgeClass'>" . ($paymentStatus ?: '-') . "</span>";
            $row[] = "<span class='badge $statusBadgeClass'>" . ($status ?: '-') . "</span>";
            $row[] = $action;

            $rows[] = $row;
            $no++;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => PurchaseOrderModel::count(),
            "recordsFiltered" => count($data),
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
        $purchaseOrder = PurchaseOrderModel::with('vendor', 'shipTo', 'batteries.battery')->findOrFail($id);
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-order.index')->with('error', 'Only draft purchase orders can be edited.');
        }

        if ($purchaseOrder->type === 'recycle') {
            $data = [
                'profile' => $purchaseOrder->toArray(),
                'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
                'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
                'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
                'shops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address']),
            ];

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
                    'type' => $battery->source ?? 'recycle',
                ];
            })->toArray();

            return view('Orders.PurchaseOrder.recycle.create', getIndexData(
                $this->title,
                $data
            ));
        } else {
            $data = [
                'profile' => $purchaseOrder->toArray(),
                'suppliers' => SupplierModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
                'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
                'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
                'shops' => DistributorShopModel::where('status', 1)->orderBy('name')->get(['id', 'name', 'address']),
            ];

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
            $vendor = explode('-', $request->vendor);
            $shipTo = explode('-', $request->ship_to);

            $purchaseOrder->update([
                'purchase_order_number' => $request->purchaseordernumber,
                'date' => $request->date,
                'vendor_id' => $vendor[0] ?? null,
                'vendor_type' => $vendor[1] ?? null,
                'ship_to_id' => $shipTo[0] ?? null,
                'ship_to_type' => $shipTo[1] ?? null,
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

    /**
     * Print the specified purchase order(s).
     *
     * @param  string  $ids
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Post the specified purchase order(s).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
                                'reference' => 'Purchase Order',
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

    public function getVendor(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', null);

        $results = [];

        if ($type === 'customer') {
            $query = CustomerModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $customers = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($customers as $c) {
                $results[] = [
                    'id' => $c->id,
                    'text' => $c->name,
                    'type' => 'customer',
                    'reference_type' => CustomerModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        if ($type === 'supplier') {
            $query = SupplierModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $suppliers = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($suppliers as $s) {
                $results[] = [
                    'id' => $s->id,
                    'text' => $s->name,
                    'type' => 'supplier',
                    'reference_type' => SupplierModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        if ($type === 'shop') {
            $query = DistributorShopModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $shops = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($shops as $s) {
                $results[] = [
                    'id' => $s->id,
                    'text' => $s->name,
                    'type' => 'shop',
                    'reference_type' => DistributorShopModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        // If no type specified, return both suppliers and customers
        $supplierQuery = SupplierModel::query();
        $customerQuery = CustomerModel::query();
        $shopQuery = DistributorShopModel::query();

        if (!empty($search)) {
            $supplierQuery->where('name', 'like', '%' . $search . '%');
            $customerQuery->where('name', 'like', '%' . $search . '%');
            $shopQuery->where('name', 'like', '%' . $search . '%');
        }

        $suppliers = $supplierQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $customers = $customerQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $shops = $shopQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);

        foreach ($suppliers as $s) {
            $results[] = [
                'id' => $s->id,
                'text' => $s->name,
                'type' => 'supplier',
                'reference_type' => SupplierModel::class,
            ];
        }

        foreach ($customers as $c) {
            $results[] = [
                'id' => $c->id,
                'text' => $c->name,
                'type' => 'customer',
                'reference_type' => CustomerModel::class,
            ];
        }

        foreach ($shops as $s) {
            $results[] = [
                'id' => $s->id,
                'text' => $s->name,
                'type' => 'shop',
                'reference_type' => DistributorShopModel::class,
            ];
        }

        // optional: sort combined results by text
        $results = collect($results)->sortBy('text')->values()->all();

        return response()->json(['status' => 'success', 'message' => 'Vendors retrieved successfully.', 'data' => $results]);
    }

    public function getShipTo(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', null);

        $results = [];

        if ($type === 'shop') {
            $query = DistributorShopModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $customers = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($customers as $c) {
                $results[] = [
                    'id' => $c->id,
                    'text' => $c->name,
                    'type' => 'shop',
                    'reference_type' => DistributorShopModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        if ($type === 'distributor') {
            $query = DistributorModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $suppliers = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($suppliers as $s) {
                $results[] = [
                    'id' => $s->id,
                    'text' => $s->name,
                    'type' => 'distributor',
                    'reference_type' => DistributorModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        if ($type === 'customer') {
            $query = CustomerModel::query();
            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%');
            }
            $customers = $query->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($customers as $c) {
                $results[] = [
                    'id' => $c->id,
                    'text' => $c->name,
                    'type' => 'customer',
                    'reference_type' => CustomerModel::class,
                ];
            }

            return response()->json(['results' => $results]);
        }

        // If no type specified, return both suppliers and customers
        $distributorQuery = DistributorModel::query();
        $shopQuery = DistributorShopModel::query();
        $customerQuery = CustomerModel::query();

        if (!empty($search)) {
            $distributorQuery->where('name', 'like', '%' . $search . '%');
            $shopQuery->where('name', 'like', '%' . $search . '%');
            $customerQuery->where('name', 'like', '%' . $search . '%');
        }

        $distributors = $distributorQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $shops = $shopQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $customers = $customerQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);

        foreach ($distributors as $s) {
            $results[] = [
                'id' => $s->id,
                'text' => $s->name,
                'type' => 'distributor',
                'reference_type' =>  DistributorModel::class,
            ];
        }

        foreach ($shops as $c) {
            $results[] = [
                'id' => $c->id,
                'text' => $c->name,
                'type' => 'shop',
                'reference_type' => DistributorShopModel::class,
            ];
        }

        foreach ($customers as $c) {
            $results[] = [
                'id' => $c->id,
                'text' => $c->name,
                'type' => 'customer',
                'reference_type' => CustomerModel::class,
            ];
        }

        // optional: sort combined results by text
        $results = collect($results)->sortBy('text')->values()->all();

        return response()->json(['status' => 'success', 'message' => 'Vendors retrieved successfully.', 'data' => $results]);
    }
}
