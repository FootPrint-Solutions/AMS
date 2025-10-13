<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

// Models
use App\Models\Orders\SalesConsignment\SalesConsignmentModel;
use App\Models\Orders\SalesConsignment\SalesConsignmentBatteriesModel;
use App\Models\Orders\SalesInvoice\SalesInvoiceModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\Settings\PaymentMethodModel;

class SalesConsignment extends Controller
{
    private $title = "Sales Consignment";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Orders.SalesConsignment.index',
            getIndexData(
                $this->title,
                'Orders/SalesConsignment/index',
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($ids = null)
    {
        if (!$ids) {
            return redirect()->route('sales-invoice.index')->with('error', 'No Sales Invoice IDs provided.');
        }

        try {
            // Decode the base64 encoded IDs
            $salesInvoiceIds = explode(',', base64_decode($ids));

            // Get sales invoices with related data
            $salesInvoices = SalesInvoiceModel::with([
                'customer',
                'shop.distributor',
                'technician',
                'batteries.battery',
                'vehicle'
            ])->whereIn('id', $salesInvoiceIds)->get();

            if ($salesInvoices->isEmpty()) {
                return redirect()->route('sales-invoice.index')->with('error', 'No valid Sales Invoices found.');
            }

            // Group by distributor for organization
            $groupedByDistributor = [];
            foreach ($salesInvoices as $invoice) {
                $distributorId = $invoice->shop->distributor->id ?? 'unknown';
                $distributorName = $invoice->shop->distributor->name ?? 'Unknown Distributor';

                if (!isset($groupedByDistributor[$distributorId])) {
                    $groupedByDistributor[$distributorId] = [
                        'distributor_name' => $distributorName,
                        'distributor_id' => $distributorId,
                        'sales_invoices' => [],
                    ];
                }

                $groupedByDistributor[$distributorId]['sales_invoices'][] = $invoice->toArray();
            }

            $data = [
                'title' => 'Create Sales Consignment',
                'breadcrumb' => 'Orders/SalesConsignment/create',
                'grouped_data' => $groupedByDistributor,
                'consignment_number' => SalesConsignmentModel::newCode(),
                'consignment_date' => date('Y-m-d'),
                'company' => CompanyModel::first(),
                'payment_methods' => PaymentMethodModel::where('status', 1)->get()->toArray(),
                'type' => 'create'
            ];

            return view('Orders.SalesInvoice.consignment.create', compact('data'));
        } catch (Exception $e) {
            Log::error('Sales Consignment Create Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ids' => $ids
            ]);

            return redirect()->route('sales-invoice.index')->with('error', 'Error processing sales invoices: ' . $e->getMessage());
        }
    }

    public function createNoIds()
    {
        $data = [
            'title' => 'Create Sales Consignment',
            'breadcrumb' => 'Orders/SalesConsignment/create',
            'grouped_data' => [],
            'consignment_number' => SalesConsignmentModel::newCode(),
            'consignment_date' => date('Y-m-d'),
            'company' => CompanyModel::first(),
            'payment_methods' => PaymentMethodModel::where('status', 1)->get()->toArray(),
            'type' => 'create',
            'distributors' => DistributorModel::where('status', 1)->get()->toArray(),
            'shops' => DistributorShopModel::where('status', 1)->get()->toArray(),
        ];

        return view(
            'Orders.SalesConsignment.create',
            getIndexData('Create Sales Consignment', $data)
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
        DB::beginTransaction();

        try {
            // Normalize sales_invoice_ids for validation
            $salesInvoiceIds = $request->input('sales_invoice_ids', []);
            if (!is_array($salesInvoiceIds)) {
                $salesInvoiceIds = [$salesInvoiceIds];
            }

            // Validate request
            $request->validate([
                '_token' => 'required',
                'vendor_id' => 'required|exists:distributors,id',
                'ship_to_id' => 'required|exists:distributor_shops,id',
                'salesconsignmentnumber' => 'required|string',
                'salesconsignmentdate' => 'required|date',
                'status' => 'required|in:draft,posted,completed',
                'discount' => 'nullable|numeric|min:0|max:100',
                'discountprice' => 'nullable|numeric|min:0',
                'sales_invoice_ids' => 'required|array|min:1',
                'sales_invoice_ids.*' => 'exists:sales_invoices,id',
                'subtotal' => 'required|numeric|min:0',
                'totalexpenses' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0'
            ]);

            // Calculate totals from selected invoices
            $subtotal = floatval(str_replace(['.', ','], '', $request->subtotal)) ?? 0;
            $discountPrice = floatval(str_replace(['.', ','], '', $request->discountprice)) ?? 0;
            $totalExpenses = floatval(str_replace(['.', ','], '', $request->totalexpenses)) ?? 0;
            $total = floatval(str_replace(['.', ','], '', $request->total)) ?? 0;

            // Generate consignment number
            $consignmentNumber = $request->salesconsignmentnumber ?: SalesConsignmentModel::newCode();

            // Create sales consignment
            $salesConsignment = SalesConsignmentModel::create([
                'sales_consignment_number' => $consignmentNumber,
                'vendor_id' => $request->vendor_id,
                'vendor_name' => DistributorModel::find($request->vendor_id)->name ?? '',
                'ship_to_id' => $request->ship_to_id,
                'ship_to_name' => DistributorShopModel::find($request->ship_to_id)->name ?? '',
                'date' => $request->salesconsignmentdate,
                'discount' => $request->discount ?? 0,
                'discount_price' => $discountPrice,
                'subtotal' => $subtotal,
                'total_expenses' => $totalExpenses,
                'total' => $total,
                'payment_status' => 'paid', // Default to paid
                'status' => $request->status ?? 'draft'
            ]);

            foreach ($salesInvoiceIds as $invoiceId) {
                $invoice = SalesInvoiceModel::find($invoiceId);
                if ($invoice) {
                    SalesConsignmentBatteriesModel::create([
                        'sales_consignment_id' => $salesConsignment->id,
                        'sales_invoice_id' => $invoice->id,
                        'sales_invoice_number' => $invoice->sales_invoice_number,
                        'invoice_number' => $invoice->invoice_number,
                        'date' => $invoice->date,
                        'discount' => $invoice->discount,
                        'discount_price' => $invoice->discount_price,
                        'subtotal' => $invoice->subtotal,
                        'total' => $invoice->total,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Consignment created successfully!',
                'data' => [
                    'consignment_id' => $salesConsignment->id,
                    'consignment_number' => $salesConsignment->sales_consignment_number
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sales Consignment Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Sales Consignment: ' . $e->getMessage()
            ], 500);
        }
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

        $query = SalesConsignmentModel::query();

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
                $q->where('sales_consignment_number', 'like', "%{$searchValue}%")
                    ->orWhere('vendor_name', 'like', "%{$searchValue}%")
                    ->orWhere('ship_to_name', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = SalesConsignmentModel::count();
        $recordsFiltered = $query->count();

        // Ordering
        if (!empty($order)) {
            $columns = [
                0 => null, // No
                1 => 'sales_consignment_number',
                2 => 'date',
                3 => 'vendor_name',
                4 => 'ship_to_name',
                5 => 'subtotal',
                6 => 'discount_price',
                7 => 'total',
                8 => 'status',
                9 => 'id' // Hidden ID for internal use
            ];
            $orderColIdx = $order[0]['column'] ?? 2;
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
            // Status badge
            if ($item->status == "draft") {
                $statusBadge = "<span class='badge badge-secondary text-dark'>Draft</span>";
            } else if ($item->status == "posted") {
                $statusBadge = "<span class='badge badge-success'>Printed</span>";
            } else if ($item->status == "completed") {
                $statusBadge = "<span class='badge badge-info'>Completed</span>";
            } else {
                $statusBadge = "<span class='badge badge-light'>{$item->status}</span>";
            }

            $rows[] = [
                $no++,
                $item->sales_consignment_number,
                formatDate($item->date),
                $item->vendor_name,
                $item->ship_to_name,
                $item->subtotal,
                $item->discount_price,
                $item->total,
                $statusBadge,
                $item->id // Hidden ID for internal use
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
     * Display the specified resource details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $salesConsignment = SalesConsignmentModel::with([
            'salesInvoices.customer',
            'salesInvoices.shop.distributor',
            'salesInvoices.batteries',
            'salesInvoices.vehicle'
        ])->findOrFail($id);

        return view('Orders.SalesConsignment.detail', [
            'title' => $this->title,
            'breadcrumb' => 'Orders/SalesConsignment/detail',
            'salesConsignment' => $salesConsignment
        ]);
    }

    /**
     * Update the specified resource status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request)
    {
        DB::beginTransaction();

        try {
            $salesConsignment = SalesConsignmentModel::findOrFail($request->id);

            if ($salesConsignment->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft consignments can be posted.'
                ], 422);
            }

            $salesConsignment->update(['status' => 'posted']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Consignment posted successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sales Consignment Post Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to post Sales Consignment: ' . $e->getMessage()
            ], 500);
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
            $salesConsignment = SalesConsignmentModel::findOrFail($request->id);

            if ($salesConsignment->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft consignments can be deleted.'
                ], 422);
            }

            // Delete pivot records first
            SalesConsignmentBatteriesModel::where('sales_consignment_id', $salesConsignment->id)->delete();

            // Delete the consignment
            $salesConsignment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Consignment deleted successfully!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sales Consignment not found.'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sales Consignment Delete Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Sales Consignment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getItems($id)
    {
        try {
            $items = SalesConsignmentBatteriesModel::where('sales_consignment_id', $id)->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (Exception $e) {
            Log::error('Get Sales Consignment Items Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'consignment_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve items: ' . $e->getMessage()
            ], 500);
        }
    }
}
