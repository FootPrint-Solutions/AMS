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
            'distributors' => DistributorModel::where('status', 1)->get()->toArray()
        ];

        return view('Orders.SalesConsignment.create', compact('data'));
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
            $salesInvoiceIds = $request->input('sales_invoice_ids', $request->input('sales_invoice_ids', []));
            if (!is_array($salesInvoiceIds)) {
                $salesInvoiceIds = [$salesInvoiceIds];
            }
            $request->merge(['sales_invoice_ids' => $salesInvoiceIds]);

            // Validate request
            $request->validate([
                '_token' => 'required',
                'salesconsignmentnumber' => 'required|string',
                'salesconsignmentdate' => 'required|date',
                'paymentmethod' => 'required|exists:payment_methods,id',
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
            $salesInvoices = SalesInvoiceModel::whereIn('id', $request->sales_invoice_ids)->get();

            $subtotal = $salesInvoices->sum('subtotal');
            $discountPrice = floatval(str_replace('.', '', $request->discount_price)) ?? 0;
            $totalExpenses = $salesInvoices->sum('total_expenses');
            $total = $subtotal - $discountPrice + $totalExpenses;

            // Generate consignment number
            $consignmentNumber = SalesConsignmentModel::newCode();

            // Create sales consignment
            $salesConsignment = SalesConsignmentModel::create([
                'sales_consignment_number' => $consignmentNumber,
                'date' => $request->salesconsignmentdate,
                'discount' => $request->discount ?? 0,
                'discount_price' => $discountPrice,
                'subtotal' => $subtotal,
                'total_expenses' => $totalExpenses,
                'total' => $total,
                'payment_status' => $request->payment_status ?? 'paid',
                'status' => 'draft'
            ]);

            foreach ($request->sales_invoice_ids as $invoiceId) {
                $invoice = SalesInvoiceModel::find($invoiceId);
                if ($invoice) {
                    SalesConsignmentBatteriesModel::create([
                        'sales_consignment_id' => $salesConsignment->id,
                        'sales_invoice_number' => $invoice->sales_invoice_number,
                        'invoice_number' => $invoice->invoice_number,
                        'date' => $invoice->date,
                        'customer_id' => $invoice->customer_id,
                        'vehicle_id' => $invoice->vehicle_id,
                        'distributor_shop_id' => $invoice->distributor_shop_id,
                        'distributor_shop_technician_id' => $invoice->distributor_shop_technician_id,
                        'discount' => $invoice->discount,
                        'discount_price' => $invoice->discount_price,
                        'subtotal' => $invoice->subtotal,
                        'total' => $invoice->total,
                        'payment_status' => $invoice->payment_status,
                        'status' => $invoice->status,
                        'address' => $invoice->address,
                        'alternative_address' => $invoice->alternative_address,
                        'latitude' => $invoice->latitude,
                        'longitude' => $invoice->longitude,
                        'payment_method_id' => $invoice->payment_method_id,
                        'midtrans_invoice_number' => $invoice->midtrans_invoice_number,
                        'midtrans_payment_link' => $invoice->midtrans_payment_link,
                        'source_platform' => $invoice->source_platform,
                        'source_id' => $invoice->source_id,
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
        $start = $request->input("start");

        $data = SalesConsignmentModel::allForDataTables($request);

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

            $row = [];
            $row[] = $no++;
            $row[] = $key->sales_consignment_number;
            $row[] = formatDate($key->date);
            $row[] = $key->total;
            $row[] = "<span class='badge $paymentStatusBadgeClass'>$key->payment_status</span>";
            $row[] = "<span class='badge $statusBadgeClass'>$key->status</span>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => SalesConsignmentModel::count(),
            "recordsFiltered" => $data["count"],
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
            SalesConsignmentInvoiceModel::where('sales_consignment_id', $salesConsignment->id)->delete();

            // Delete the consignment
            $salesConsignment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Consignment deleted successfully!'
            ]);
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
}
