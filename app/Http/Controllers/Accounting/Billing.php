<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\Accounting\BillingModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Supplier\SupplierModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;

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
                    'billing_number' => BillingModel::generateBillingNumber(),
                    'distributorShops' => DistributorShopModel::all(),
                    'customers' => CustomerModel::all(),
                ]
            )
        );
    }

    public function getShipTo(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', null);

        $results = [];
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

        if ($type === 'distributor') {
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

        // If no type specified, return both suppliers and customers
        $supplierQuery = SupplierModel::query();
        $customerQuery = CustomerModel::query();

        if (!empty($search)) {
            $supplierQuery->where('name', 'like', '%' . $search . '%');
            $customerQuery->where('name', 'like', '%' . $search . '%');
        }

        $suppliers = $supplierQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $customers = $customerQuery->where('status', 1)->orderBy('name')->get(['id', 'name']);
        foreach ($suppliers as $s) {
            $results[] = [
                'id' => $s->id,
                'text' => $s->name,
                'type' => 'supplier',
                'reference_type' =>  SupplierModel::class,
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

        $rows = [];
        $totalRecords = 0;
        $filteredRecords = 0;

        if ($shipToType === 'customer') {

            $query = SalesOrderModel::with(['customer', 'shop.distributor'])
                ->where('customer_id', $shipToId);

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
        } else if ($shipToType === 'supplier') {
            // Get Purchase Orders for supplier  
            // Using either vendor_id/vendor_type (polymorphic) or supplier_id (direct relation)
            $query = PurchaseOrderModel::with(['supplier', 'vendor', 'shipTo'])
                ->where(function ($q) use ($shipToId) {
                    $q->where(function ($subQ) use ($shipToId) {
                        $subQ->where('vendor_id', $shipToId)
                            ->where('vendor_type', 'App\\Models\\MasterData\\Supplier\\SupplierModel');
                    })->orWhere('supplier_id', $shipToId);
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
                // Get supplier name from either relation
                $supplierName = $order->supplier ? $order->supplier->name : ($order->vendor ? $order->vendor->name : '-');

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
            $orderType = $request->input('order_type'); // 'sales_order' or 'purchase_order'

            $tempData = [];

            if ($orderType === 'sales_order') {
                $orders = SalesOrderModel::with(['customer', 'shop.distributor'])
                    ->whereIn('id', $orderIds)->get();

                foreach ($orders as $order) {
                    $shopName = $order->shop ? $order->shop->distributor->name . ' / ' . $order->shop->name : '-';
                    $tempData[] = [
                        'id' => $order->id,
                        'type' => 'sales_order',
                        'order_number' => $order->sales_order_number,
                        'invoice_number' => $order->invoice_number ?? '-',
                        'date' => formatDate($order->date),
                        'customer_supplier_name' => $order->customer->name ?? '-',
                        'shop_name' => $shopName,
                        'total' => $order->total,
                        'formatted_total' => formatPrice($order->total)
                    ];
                }
            } else if ($orderType === 'purchase_order') {
                $orders = PurchaseOrderModel::with(['supplier', 'vendor', 'shipTo'])
                    ->whereIn('id', $orderIds)->get();

                foreach ($orders as $order) {
                    // Get supplier name from either relation
                    $supplierName = $order->supplier ? $order->supplier->name : ($order->vendor ? $order->vendor->name : '-');

                    $tempData[] = [
                        'id' => $order->id,
                        'type' => 'purchase_order',
                        'order_number' => $order->purchase_order_number,
                        'invoice_number' => $order->invoice_number ?? '-',
                        'date' => formatDate($order->date),
                        'customer_supplier_name' => $supplierName,
                        'shop_name' => $order->shipTo->name ?? '-',
                        'total' => $order->total,
                        'formatted_total' => formatPrice($order->total)
                    ];
                }
            }

            // Store in session for temporary use
            session()->put('temp_billing_orders', $tempData);

            return response()->json([
                'status' => 'success',
                'message' => 'Orders added to billing successfully',
                'data' => $tempData,
                'html' => view('Accounting.Billing.partials.selected-orders-table', ['orders' => $tempData])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVendors(Request $request)
    {
        $search = $request->input('q', '');

        $results = [];

        // Fetch Suppliers
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

        return response()->json([
            'status' => 'success',
            'message' => 'Vendors retrieved successfully.',
            'data' => $results
        ]);
    }
}
