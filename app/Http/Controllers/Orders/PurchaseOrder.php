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
use App\Models\Settings\PaymentMethodModel;
use App\Models\Settings\TaxModel;

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
            'suppliers' => SupplierModel::active()->orderBy('name')->get(['id', 'name']),
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
            'batteries' => BatteryModel::orderBy('name')->get(['id', 'name', 'price_retail', 'type']),
            'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
            'number' => PurchaseOrderModel::generatePurchaseOrderNumber(),
            'tax' => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
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
        // Validate the request
        $request->validate([
            'salesordernumber' => 'required|string|unique:purchase_orders,purchase_order_number',
            'date' => 'required|date',
            'supplier' => 'required|exists:suppliers,id',
            'Address' => 'required|string',
            'batteriesid.*' => 'required|exists:batteries,id',
            'batteriespriceretail.*' => 'required|numeric|min:0',
            'batteriesdiscountprice.*' => 'required|numeric|min:0',
            'batteriesprice.*' => 'required|numeric|min:0',
            'batteriesname.*' => 'required|string',
            'batteriestax.*' => 'required|numeric|min:0',
            'batteriescode.*' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'discountprice' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:paid,pending,failed',
        ]);

        try {
            DB::beginTransaction();

            // Create purchase order
            $purchaseOrder = PurchaseOrderModel::create([
                'purchase_order_number' => $request->salesordernumber,
                'date' => $request->date,
                'supplier_id' => $request->supplier,
                'address' => $request->Address,
                'latitude' => $request->Latitude ?? 0,
                'longitude' => $request->Longitude ?? 0,
                'discount_price' => (int)str_replace(['Rp', '.', ' '], '', $request->discountprice ?? '0'),
                'subtotal' => (int)str_replace(['Rp', '.', ' '], '', $request->subtotal ?? '0'),
                'total' => (int)str_replace(['Rp', '.', ' '], '', $request->total ?? '0'),
                'payment_status' => $request->status,
                'status' => 'draft',
            ]);

            // Create purchase order batteries
            if ($request->batteriesid && is_array($request->batteriesid)) {
                foreach ($request->batteriesid as $index => $batteryId) {
                    $battery = BatteryModel::find($batteryId);
                    if ($battery) {
                        $batteryPriceRetail = (int)str_replace(['Rp', '.', ' '], '', $request->batteriespriceretail[$index] ?? '0');
                        $tax = $request->batteriestax[$index] ?? 0;
                        $taxPrice = $batteryPriceRetail * $tax / 100;
                        $discountPrice = (int)str_replace(['Rp', '.', ' '], '', $request->batteriesdiscountprice[$index] ?? '0');
                        $discount = $batteryPriceRetail > 0 ? ($discountPrice / $batteryPriceRetail) * 100 : 0;
                        $priceNet = $batteryPriceRetail + $taxPrice - $discountPrice;

                        PurchaseOrderBatteryModel::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'battery_id' => $batteryId,
                            'battery_name' => $request->batteriesname[$index] ?? $battery->name,
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
            $row[] = $no++;
            $row[] = $key->purchase_order_number;
            $row[] = $key->invoice_number ?? "<p class='text-center'>-</p>";
            $row[] = formatDate($key->date);
            $row[] = $key->supplier_name ?? "<p class='text-center'>-</p>";
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

        $data = [
            'profile' => $purchaseOrder->toArray(),
            'suppliers' => SupplierModel::active()->orderBy('name')->get(['id', 'name', 'address', 'contact', 'email']),
            'batteries' => BatteryModel::orderBy('name')->get(['id', 'name', 'price_retail', 'type']),
            'payment_methods' => PaymentMethodModel::orderBy('name')->get(['id', 'name']),
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
                'type' => $battery->battery->type ?? 'regular',
            ];
        })->toArray();

        return view('Orders.PurchaseOrder.create', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|exists:purchase_orders,id',
            'purchase_order_number' => 'required|string',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_status' => 'required|string',
            'status' => 'required|in:draft,posted,completed',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrderModel::findOrFail($request->id);

            // Update purchase order
            $purchaseOrder->update([
                'purchase_order_number' => $request->purchase_order_number,
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'discount_price' => (int)str_replace(['Rp', '.', ' '], '', $request->discount_price ?? '0'),
                'subtotal' => (int)str_replace(['Rp', '.', ' '], '', $request->subtotal ?? '0'),
                'total' => (int)str_replace(['Rp', '.', ' '], '', $request->total ?? '0'),
                'payment_status' => $request->payment_status,
                'status' => $request->status,
                'address' => $request->address,
            ]);

            // Delete existing batteries and recreate them
            $purchaseOrder->batteries()->delete();

            // Create purchase order batteries
            if ($request->battery_id && is_array($request->battery_id)) {
                foreach ($request->battery_id as $index => $batteryId) {
                    $battery = BatteryModel::find($batteryId);
                    if ($battery) {
                        $batteryPriceRetail = (int)str_replace(['Rp', '.', ' '], '', $request->battery_price_retail[$index] ?? '0');
                        $tax = $request->battery_tax[$index] ?? 0;
                        $taxPrice = $batteryPriceRetail * $tax / 100;
                        $discountPrice = (int)str_replace(['Rp', '.', ' '], '', $request->battery_discount_price[$index] ?? '0');
                        $discount = $batteryPriceRetail > 0 ? ($discountPrice / $batteryPriceRetail) * 100 : 0;
                        $priceNet = $batteryPriceRetail + $taxPrice - $discountPrice;

                        PurchaseOrderBatteryModel::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'battery_id' => $batteryId,
                            'battery_name' => $battery->name,
                            'battery_price_retail' => $batteryPriceRetail,
                            'tax' => $tax,
                            'tax_price' => $taxPrice,
                            'discount' => $discount,
                            'discount_price' => $discountPrice,
                            'price_net' => $priceNet,
                            'quantity' => $request->battery_quantity[$index] ?? 1,
                            'battery_production_code' => $request->battery_production_code[$index] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase order updated successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating purchase order.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $purchaseOrder = PurchaseOrderModel::findOrFail($id);
            $purchaseOrder->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase order deleted successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Order Delete Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting purchase order.'
            ], 500);
        }
    }
}
