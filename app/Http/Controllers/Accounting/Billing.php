<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Http\Controllers\Controller;
use App\Models\Accounting\BillingModel;
use App\Models\Accounting\BillingInvoiceModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Supplier\SupplierModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorModel;

class Billing extends Controller
{
    private $title = "Billing";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            "Accounting.Billing.index",
            getIndexData($this->title)
        );
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            "Accounting.Billing.create",
            getIndexData(
                $this->title,
                [
                    'billing_number' => BillingModel::generateSalesBillingNumber(),
                    'distributorShops' => DistributorShopModel::all(),
                    'customers' => CustomerModel::all(),
                ]
            )
        );
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPurchase()
    {
        return view(
            "Accounting.Billing.purchase.create",
            getIndexData(
                $this->title,
                [
                    'billing_number' => BillingModel::generatePurchaseBillingNumber(),
                    'distributorShops' => DistributorShopModel::all(),
                    'customers' => CustomerModel::all(),
                ]
            )
        );
    }

    public function edit($id)
    {
        $billing = BillingModel::with([
            'vendor',
            'shipTo',
            'invoices'
        ])->find($id);

        if (!$billing) {
            return redirect()->route('billing.index')
                ->with('error', 'Billing not found.');
        }

        if ($billing->status === 'posted') {
            return redirect()->route('billing.index')
                ->with('error', 'Posted Billing cannot be edited.');
        }

        return view(
            "Accounting.Billing.create",
            getIndexData(
                $this->title,
                [
                    'type' => 'edit',
                    'billing' => $billing,
                    'distributorShops' => DistributorShopModel::all(),
                    'customers' => CustomerModel::all(),
                ]
            )
        );
    }

    /**
     * Display all resources for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $draw = $request->input("draw");
        $start = intval($request->input("start", 0));
        $length = intval($request->input("length", 10));
        $searchValue = $request->input('search.value');
        $order = $request->input('order', []);
        $status = $request->input('status');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $query = BillingModel::with(['vendor', 'shipTo']);

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($dateStart) {
            $query->whereDate('date', '>=', $dateStart);
        }
        if ($dateEnd) {
            $query->whereDate('date', '<=', $dateEnd);
        }

        // Search
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('billing_number', 'like', "%{$searchValue}%")
                    ->orWhereHas('vendor', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('shipTo', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        $recordsTotal = BillingModel::count();
        $recordsFiltered = $query->count();

        // Ordering
        if (!empty($order)) {
            $columns = [
                0 => null, // dt-control
                1 => null, // #
                2 => 'billing_number',
                3 => null, // vendor name
                4 => null, // shipTo name
                5 => 'date',
                6 => 'subtotal',
                7 => 'discount_price',
                8 => 'total',
                9 => 'status',
                10 => 'id' // hidden
            ];
            $orderColIdx = $order[0]['column'] ?? 5;
            $orderDir = $order[0]['dir'] ?? 'desc';
            $orderCol = $columns[$orderColIdx] ?? 'date';
            if ($orderCol) {
                $query->orderBy($orderCol, $orderDir);
            }
        } else {
            $query->orderBy('date', 'desc');
        }

        $data = $query->skip($start)->take($length)->get();

        $rows = [];
        $no = $start + 1;
        foreach ($data as $item) {
            $status =  $item->status;
            if ($status === "draft") {
                $statusBadgeClass = "badge-secondary text-dark";
            } elseif ($status === "posted") {
                $statusBadgeClass = "badge-success";
            } else {
                $statusBadgeClass = "badge-info";
            }

            $rows[] = [
                '', // dt-control
                $no++,
                $item->billing_number,
                $item->vendor ? $item->vendor->name : '',
                $item->shipTo ? $item->shipTo->name : '',
                formatDate($item->date),
                number_format($item->subtotal, 0, ',', '.'),
                number_format($item->discount_price, 0, ',', '.'),
                number_format($item->total, 0, ',', '.'),
                '<span class="badge ' . $statusBadgeClass . '">' . ucfirst($item->status) . '</span>',
                $item->id // hidden
            ];
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $rows
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            list($shipToId, $shipToType) = explode('-', $request->input('ship_to'));

            $billing = BillingModel::create([
                'billing_number' => $request->input('billingnumber'),
                'vendor_id' => $request->input('vendor'),
                'vendor_type' => DistributorShopModel::class,
                'ship_to_id' => $shipToId,
                'ship_to_type' => $shipToType,
                'date' => $request->input('date'),
                'discount' => 0,
                'discount_price' => $request->input('discountprice', 0),
                'subtotal' => $request->input('subtotal', 0),
                'total' => $request->input('total', 0),
                'status' => $request->input('status', 'draft'),
            ]);

            // Save BillingInvoice(s)
            $invoiceIds = $request->input('invoice_ids', []);
            $orderTypes = $request->input('order_types', []);
            $orderSources = $request->input('order_sources', []);
            $orderNumbers = $request->input('order_numbers', []);
            $notes = $request->input('notes', []);
            $discounts = $request->input('discounts', []);
            $subtotals = $request->input('subtotals', []);
            $totals = $request->input('totals', []);

            foreach ($invoiceIds as $idx => $invoiceId) {
                BillingInvoiceModel::create([
                    'billing_id' => $billing->id,
                    'invoice_id' => $invoiceId,
                    'invoice_type' => $orderSources[$idx] ?? null,
                    'invoice_number' => $orderNumbers[$idx] ?? null,
                    'date' => $request->input('date'),
                    'discount' => 0,
                    'discount_price' => $discounts[$idx] ?? 0,
                    'subtotal' => $subtotals[$idx] ?? 0,
                    'total' => $totals[$idx] ?? 0,
                    'note' => $notes[$idx] ?? null,
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Billing created successfully',
                'data' => $billing
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Billing: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $billing = BillingModel::find($request->input('id'));
            if (!$billing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Billing not found'
                ], 404);
            }

            list($shipToId, $shipToType) = explode('-', $request->input('ship_to'));

            $billing->update([
                'billing_number' => $request->input('billingnumber'),
                'vendor_id' => $request->input('vendor'),
                'vendor_type' => DistributorShopModel::class,
                'ship_to_id' => $shipToId,
                'ship_to_type' => $shipToType,
                'date' => $request->input('date'),
                'discount' => 0,
                'discount_price' => $request->input('discountprice', 0),
                'subtotal' => $request->input('subtotal', 0),
                'total' => $request->input('total', 0),
                'status' => $request->input('status', 'draft'),
            ]);

            // Delete old invoices
            BillingInvoiceModel::where('billing_id', $billing->id)->delete();

            // Save BillingInvoice(s)
            $invoiceIds = $request->input('invoice_ids', []);
            $orderTypes = $request->input('order_types', []);
            $orderSources = $request->input('order_sources', []);
            $orderNumbers = $request->input('order_numbers', []);
            $notes = $request->input('notes', []);
            $discounts = $request->input('discounts', []);
            $subtotals = $request->input('subtotals', []);
            $totals = $request->input('totals', []);

            foreach ($invoiceIds as $idx => $invoiceId) {
                BillingInvoiceModel::create([
                    'billing_id' => $billing->id,
                    'invoice_id' => $invoiceId,
                    'invoice_type' => $orderSources[$idx] ?? null,
                    'invoice_number' => $orderNumbers[$idx] ?? null,
                    'date' => $request->input('date'),
                    'discount' => 0,
                    'discount_price' => $discounts[$idx] ?? 0,
                    'subtotal' => $subtotals[$idx] ?? 0,
                    'total' => $totals[$idx] ?? 0,
                    'note' => $notes[$idx] ?? null,
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Billing updated successfully',
                'data' => $billing
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update Billing: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Post the specified Billing(s).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request)
    {
        $billingIds = $request->input('ids', []);

        try {
            BillingModel::whereIn('id', $billingIds)
                ->update(['status' => 'posted']);

            return response()->json([
                'status' => 'success',
                'message' => 'Billing(s) posted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to post Billing(s): ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Billing Items by Billing ID
     */
    public function getBillingItems($id)
    {
        $billing = BillingModel::with(['invoices'])->find($id);

        if (!$billing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Billing not found'
            ], 404);
        }

        $items = [];
        foreach ($billing->invoices as $invoice) {
            $items[] = [
                'billing_id' => $invoice->billing_id,
                'invoice_id' => $invoice->invoice_id,
                'invoice_type' => $invoice->invoice_type,
                'invoice_number' => $invoice->invoice_number,
                'date' => formatDate($invoice->date),
                'discount' => $invoice->discount,
                'discount_price' => number_format($invoice->discount_price, 0, ',', '.'),
                'subtotal' => number_format($invoice->subtotal, 0, ',', '.'),
                'total' => number_format($invoice->total, 0, ',', '.'),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }


    /**
     * Get Ship To (Customers and Suppliers) for Select2
     */
    public function getShipTo(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', null);

        $results = [];

        if ($type === 'customer') {
            $customerQuery = CustomerModel::query();
            if (!empty($search)) {
                $customerQuery->where('name', 'like', '%' . $search . '%');
            }
            $customers = $customerQuery->where('status', 1)
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
        } else if ($type === 'supplier') {
            $supplierQuery = SupplierModel::query();
            if (!empty($search)) {
                $supplierQuery->where('name', 'like', '%' . $search . '%');
            }
            $suppliers = $supplierQuery->where('status', 1)
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
        } else if ($type === 'distributorshop') {
            $shopQuery = DistributorShopModel::query();
            if (!empty($search)) {
                $shopQuery->where('name', 'like', '%' . $search . '%');
            }
            $shops = $shopQuery->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($shops as $s) {
                $results[] = [
                    'id' => $s->id,
                    'text' => $s->name,
                    'type' => 'distributorshop',
                    'reference_type' => DistributorShopModel::class,
                ];
            }
        } else {
            // All types
            $customerQuery = CustomerModel::query();
            $supplierQuery = SupplierModel::query();

            if (!empty($search)) {
                $customerQuery->where('name', 'like', '%' . $search . '%');
                $supplierQuery->where('name', 'like', '%' . $search . '%');
            }

            $customers = $customerQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
            $suppliers = $supplierQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);

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
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ship To retrieved successfully.',
            'data' => $results
        ]);
    }

    /**
     * Get Vendors for Select2
     */
    public function destroy(Request $request)
    {
        try {
            $billingIds = $request->input('ids', []);

            if (empty($billingIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No Billing IDs provided'
                ], 400);
            }

            foreach ($billingIds as $billingId) {
                $billing = BillingModel::find($billingId);
                if ($billing && $billing->status === 'posted') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Posted Billing(s) cannot be deleted'
                    ], 400);
                }
            }

            BillingModel::whereIn('id', $billingIds)->delete();

            BillingInvoiceModel::whereIn('billing_id', $billingIds)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Billing(s) deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete Billing(s): ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get Orders Data for Ship To
     */
    public function getOrdersData(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');

        $shipToId = explode('-', $request->input('ship_to_id'))[0];
        $shipToType = $request->input('ship_to_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'regular');

        $rows = [];
        $totalRecords = 0;
        $filteredRecords = 0;

        if ($shipToType === 'customer' || $shipToType === 'App\Models\MasterData\Customer\CustomerModel') {

            $query = SalesOrderModel::with(['customer', 'shop.distributor'])
                ->where('customer_id', $shipToId)
                ->whereIn('status', ['posted', 'completed'])
                ->where('type', $type);

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sales_order_number', 'like', '%' . $search . '%')
                        ->orWhere('invoice_number', 'like', '%' . $search . '%');
                });
            }

            $totalQuery = clone $query;
            $totalRecords = $totalQuery->count();
            $filteredRecords = $totalRecords;

            $orders = $query->skip($start)->take($length)->get();

            $no = $start + 1;
            foreach ($orders as $order) {
                $shopName = $order->shop ? $order->shop->distributor->name . ' / ' . $order->shop->name : '-';
                $rows[] = [
                    'checkbox' => '<input type="checkbox" class="form-check-input select-order" data-id="' . $order->id . '" data-type="sales_order">',
                    'number' => $no++,
                    'order_number' => $order->sales_order_number,
                    'date' => formatDate($order->date),
                    'customer_supplier_name' => $order->customer->name ?? '-',
                    'shop_name' => $shopName,
                    'total' => formatPrice($order->total)
                ];
            }
        } else if ($shipToType === 'supplier' || $shipToType === 'App\Models\MasterData\Supplier\SupplierModel') {
            // Get Purchase Orders for supplier  
            // Using vendor_id/vendor_type (polymorphic relation)
            $query = PurchaseOrderModel::with(['vendor', 'shipTo'])
                ->where(function ($q) use ($shipToId) {
                    $q->where(function ($subQ) use ($shipToId) {
                        $subQ->where('vendor_id', $shipToId)
                            ->where('vendor_type', 'App\\Models\\MasterData\\Supplier\\SupplierModel');
                    })->orWhere('vendor_id', $shipToId);
                })
                ->where('status', 'posted');

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('purchase_order_number', 'like', '%' . $search . '%')
                        ->orWhere('invoice_number', 'like', '%' . $search . '%');
                });
            }

            $totalQuery = clone $query;
            $totalRecords = $totalQuery->count();
            $filteredRecords = $totalRecords;

            $orders = $query->skip($start)->take($length)->get();

            $no = $start + 1;
            foreach ($orders as $order) {
                // Get supplier/vendor name from vendor relation (polymorphic)
                $supplierName = $order->vendor ? $order->vendor->name : '-';

                $shipToName = $order->shipTo ? $order->shipTo->name : '-';
                $rows[] = [
                    'checkbox' => '<input type="checkbox" class="form-check-input select-order" data-id="' . $order->id . '" data-type="purchase_order">',
                    'number' => $no++,
                    'order_number' => $order->purchase_order_number,
                    'date' => formatDate($order->date),
                    'customer_supplier_name' => $supplierName,
                    'shop_name' => $shipToName,
                    'total' => formatPrice($order->total)
                ];
            }
        } else if ($shipToType === 'distributorshop' || $shipToType === 'App\Models\MasterData\Distributor\DistributorShopModel') {
            $query = SalesOrderModel::with(['vendorData', 'shipToData'])
                ->where('vendor', $shipToId);

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sales_order_number', 'like', '%' . $search . '%')
                        ->orWhere('invoice_number', 'like', '%' . $search . '%');
                });
            }

            $totalQuery = clone $query;
            $totalRecords = $totalQuery->count();
            $filteredRecords = $totalRecords;

            $orders = $query->skip($start)->take($length)->get();

            $no = $start + 1;
            foreach ($orders as $order) {
                $rows[] = [
                    'checkbox' => '<input type="checkbox" class="form-check-input select-order" data-id="' . $order->id . '" data-type="sales_order">',
                    'number' => $no++,
                    'order_number' => $order->sales_order_number,
                    'date' => formatDate($order->date),
                    'customer_supplier_name' => $order->vendorData ? $order->vendorData->name : '-',
                    'shop_name' => $order->shipToData ? $order->shipToData->name : '-',
                    'total' => formatPrice($order->total)
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows
        ]);
    }


    public function getPurchaseOrdersData(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');

        $shipToId = explode('-', $request->input('ship_to_id'))[0];
        $shipToType = $request->input('ship_to_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'regular');

        $rows = [];
        $totalRecords = 0;
        $filteredRecords = 0;

        $query = PurchaseOrderModel::with(['vendor', 'shipTo'])
            ->where(function ($q) use ($shipToId, $shipToType) {
                $q->where(function ($subQ) use ($shipToId, $shipToType) {
                    $subQ->where('vendor_id', $shipToId)
                        ->where('vendor_type', $shipToType);
                })->orWhere('vendor_id', $shipToId);
            })
            ->where('status', 'posted')
            ->where('type', $type);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%');
            });
        }

        $totalQuery = clone $query;
        $totalRecords = $totalQuery->count();
        $filteredRecords = $totalRecords;

        $orders = $query->skip($start)->take($length)->get();

        $no = $start + 1;
        foreach ($orders as $order) {
            // Get supplier/vendor name from vendor relation (polymorphic)
            $supplierName = $order->vendor ? $order->vendor->name : '-';

            $shipToName = $order->shipTo ? $order->shipTo->name : '-';
            $rows[] = [
                'checkbox' => '<input type="checkbox" class="form-check-input select-order" data-id="' . $order->id . '" data-type="purchase_order">',
                'number' => $no++,
                'order_number' => $order->purchase_order_number,
                'date' => formatDate($order->date),
                'customer_supplier_name' => $supplierName,
                'shop_name' => $shipToName,
                'total' => formatPrice($order->total)
            ];
        }


        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows
        ]);
    }


    public function getSalesPurchaseOrdersData(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');

        $shipToId = explode('-', $request->input('ship_to_id'))[0];
        $shipToType = $request->input('ship_to_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'regular');

        $rows = [];
        $totalRecords = 0;
        $filteredRecords = 0;

        $query = PurchaseOrderModel::with(['vendor', 'shipTo'])
            ->where(function ($q) use ($shipToId, $shipToType) {
                $q->where(function ($subQ) use ($shipToId, $shipToType) {
                    $subQ->where('vendor_id', $shipToId)
                        ->where('vendor_type', $shipToType);
                })->orWhere('vendor_id', $shipToId);
            })
            ->where('status', 'posted')
            ->where('type', $type);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%');
            });
        }

        $totalQuery = clone $query;
        $totalRecords = $totalQuery->count();
        $filteredRecords = $totalRecords;

        $orders = $query->skip($start)->take($length)->get();

        $no = $start + 1;
        foreach ($orders as $order) {
            // Get supplier/vendor name from vendor relation (polymorphic)
            $supplierName = $order->vendor ? $order->vendor->name : '-';

            $shipToName = $order->shipTo ? $order->shipTo->name : '-';
            $rows[] = [
                'checkbox' => '<input type="checkbox" class="form-check-input select-order" data-id="' . $order->id . '" data-type="purchase_order">',
                'number' => $no++,
                'order_number' => $order->purchase_order_number,
                'date' => formatDate($order->date),
                'customer_supplier_name' => $supplierName,
                'shop_name' => $shipToName,
                'total' => formatPrice($order->total)
            ];
        }


        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows
        ]);
    }

    public function addOrdersToTemp(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids', []);
            $orderTypes = $request->input('order_types', []);

            $tempData = [];

            foreach ($orderIds as $index => $orderId) {
                $orderType = $orderTypes[$index] ?? null;

                if ($orderType === 'sales_order') {
                    $order = SalesOrderModel::with(['customer', 'shop.distributor', 'vendorData', 'shipToData'])
                        ->find($orderId);

                    if ($order) {
                        $shopName = $order->shop ? $order->shop->distributor->name . ' / ' . $order->shop->name : '-';
                        $tempData[] = [
                            'id' => $order->id,
                            'type' => 'sales_order',
                            'source' => SalesOrderModel::class,
                            'order_number' => $order->sales_order_number,
                            'invoice_number' => $order->invoice_number ?? '-',
                            'date' => formatDate($order->date),
                            'customer_supplier_name' => $order->customer->name ?? $order->vendorData->name ?? '-',
                            'shop_name' => $shopName ?? $order->shipToData->name ?? '-',
                            'total' => $order->total,
                            'formatted_total' => formatPrice($order->total)
                        ];
                    }
                } else if ($orderType === 'purchase_order') {
                    $order = PurchaseOrderModel::with(['vendor', 'shipTo'])
                        ->find($orderId);

                    if ($order) {
                        $supplierName = $order->vendor ? $order->vendor->name : '-';
                        $shipToName = $order->shipTo ? $order->shipTo->name : '-';

                        $tempData[] = [
                            'id' => $order->id,
                            'type' => 'purchase_order',
                            'source' => PurchaseOrderModel::class,
                            'order_number' => $order->purchase_order_number,
                            'invoice_number' => $order->invoice_number ?? '-',
                            'date' => formatDate($order->date),
                            'customer_supplier_name' => $supplierName,
                            'shop_name' => $shipToName,
                            'total' => $order->total,
                            'formatted_total' => formatPrice($order->total)
                        ];
                    }
                }
            }

            // Store in session for temporary use
            session()->put('temp_billing_orders', $tempData);

            return response()->json([
                'status' => 'success',
                'message' => 'Orders added to billing successfully',
                'data' => $tempData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Billing Data for Edit
     */
    public function getBillingData($id)
    {
        try {
            $billing = BillingModel::with([
                'vendor',
                'shipTo',
                'invoices' => function ($query) {
                    $query->select('billing_id', 'invoice_id', 'invoice_type', 'invoice_number', 'date', 'discount', 'discount_price', 'subtotal', 'total', 'note');
                }
            ])->find($id);

            if (!$billing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Billing not found'
                ], 404);
            }

            $billingData = [
                'id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'date' => $billing->date,
                'vendor' => [
                    'id' => $billing->vendor_id,
                    'name' => $billing->vendor ? $billing->vendor->name : '',
                    'type' => $billing->vendor_type
                ],
                'ship_to' => [
                    'id' => $billing->ship_to_id,
                    'name' => $billing->shipTo ? $billing->shipTo->name : '',
                    'type' => $billing->ship_to_type
                ],
                'discount' => $billing->discount,
                'discount_price' => $billing->discount_price,
                'subtotal' => $billing->subtotal,
                'total' => $billing->total,
                'invoices' => $billing->invoices->map(function ($invoice) {
                    $name = '';
                    if ($invoice->invoice_type === SalesOrderModel::class) {
                        $order = SalesOrderModel::select('id', 'customer_id')
                            ->with(['customer:id,name'])
                            ->find($invoice->invoice_id);
                        $name = $order && $order->customer ? $order->customer->name : '';
                    } else if ($invoice->invoice_type === PurchaseOrderModel::class) {
                        $order = PurchaseOrderModel::select('id', 'vendor_id', 'vendor_type')
                            ->with(['vendor'])
                            ->find($invoice->invoice_id);
                        if ($order && $order->vendor) {
                            $name = $order->vendor->name;
                        } else {
                            $name = '';
                        }
                    }
                    return [
                        'invoice_id' => $invoice->invoice_id,
                        'invoice_type' => $invoice->invoice_type,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_name' => $name,
                        'date' => $invoice->date,
                        'discount' => $invoice->discount,
                        'discount_price' => $invoice->discount_price,
                        'subtotal' => $invoice->subtotal,
                        'total' => $invoice->total,
                        'note' => $invoice->note,
                    ];
                })
            ];

            return response()->json([
                'status' => 'success',
                'data' => $billingData
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get billing data: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get Vendors for Select2
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVendors(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', null);

        $results = [];

        if ($type === 'distributorshop' || $type === null) {
            // Fetch Distributor Shops
            $distributorShopQuery = DistributorShopModel::query();
            if (!empty($search)) {
                $distributorShopQuery->where('name', 'like', '%' . $search . '%');
            }
            $distributors = $distributorShopQuery->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($distributors as $d) {
                $results[] = [
                    'id' => $d->id,
                    'text' => $d->name,
                    'type' => 'distributorshop',
                    'reference_type' => DistributorShopModel::class,
                ];
            }
        } else if ($type === 'distributor') {

            // fetch distributors
            $distributorQuery = DistributorModel::query();
            if (!empty($search)) {
                $distributorQuery->where('name', 'like', '%' . $search . '%');
            }

            $distributors = $distributorQuery->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name']);

            foreach ($distributors as $d) {
                $results[] = [
                    'id' => $d->id,
                    'text' => $d->name,
                    'type' => 'distributor',
                    'reference_type' => DistributorModel::class,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vendors retrieved successfully.',
            'data' => $results
        ]);
    }

    public function print($id)
    {
        $billing = BillingModel::with([
            'vendor',
            'shipTo',
            'invoices.invoice.details'
        ])->find($id);

        if (!$billing) {
            return redirect()->route('billing.index')
                ->with('error', 'Billing not found.');
        }

        // Example: Use vendor name/type to determine which view to use
        $vendorName = $billing->vendor ? $billing->vendor->name : '';
        $vendorId = $billing->vendor ? $billing->vendor->id : null;
        return view(
            'Accounting.Billing.print',
            getIndexData(
                $this->title,
                [
                    "profile" => $billing->toArray(),
                ]
            )
        );
    }

    /**
     * Print receipt (kwitansi) for a billing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printReceipt($id)
    {
        $billing = BillingModel::with([
            'vendor',
            'shipTo',
            'invoices.invoice.details'
        ])->find($id);

        if (!$billing) {
            return redirect()->route('billing.index')
                ->with('error', 'Billing not found.');
        }

        // Example: Use vendor name/type to determine which view to use
        $vendorName = $billing->vendor ? $billing->vendor->name : '';
        $vendorId = $billing->vendor ? $billing->vendor->id : null;
        // dd($billing->toArray());
        return view(
            'Accounting.Billing.print-receipt',
            getIndexData(
                $this->title,
                [
                    "profile" => $billing->toArray(),
                ]
            )
        );
    }
}
