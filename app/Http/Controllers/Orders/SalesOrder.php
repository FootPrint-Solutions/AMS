<?php

namespace App\Http\Controllers\Orders;


// LIBRARY EXCEL
use App\Exports\SalesOrderExport;
use App\Exports\SalesOrderDetailsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use GuzzleHttp\Client;

// MODELS
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\Orders\WorkOrder\WorkOrderModel;
use App\Models\Settings\PaymentMethodModel;
use App\Models\Settings\TaxModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Inventory\InventoryRecycleDetailModel;
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\Inventory\InventoryDetailModel;
use App\Models\Inventory\InventoryModel;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\Accounting\ExpenseModel;
use App\Models\Accounting\BillingInvoiceExpenseModel;
use App\Models\Accounting\BillingModel;
use App\Models\Accounting\BillingInvoiceModel;

// Midtrans 
use App\Services\Midtrans\Transaction;

class SalesOrder extends Controller
{
    private $title = "Sales Order";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Orders.SalesOrder.index',
            getIndexData(
                $this->title
            )
        );
    }

    public function getSalesOrders($status = 'all', $filter = '')
    {
        $query = SalesOrderModel::with('customer')->join("customers", "sales_orders.customer_id", "customers.id")
            ->select("sales_orders.*")->orderBy("sales_orders.date", "DESC");

        // Filter status
        if ($status != 'all')
            $query->where(function ($q) use ($status) {
                $q->where('payment_status', $status)
                    ->orWhere('sales_orders.status', $status);
            });

        // Filter
        if ($filter != '')
            $query->where(function ($q) use ($filter) {
                $q->where('sales_order_number', 'like', '%' . $filter . '%')
                    ->orWhere('customers.name', 'like', '%' . $filter . '%');
            });

        return $query->get()->toArray();
    }

    public function getSalesOrderDetail($id)
    {
        return SalesOrderModel::with(['vehicle', 'technician', 'shop.distributor', 'batteries'])->find($id)->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'Orders.SalesOrder.create',
            getIndexData(
                $this->title,
                array(
                    "number" => SalesOrderModel::newCode(),
                    "customers" => CustomerModel::where('status', 1)->orderBy('name')->get()->toArray(),
                    "vehicles" => VehicleModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                    "tax" => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
                    "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
                    "expenses" => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $salesOrder = SalesOrderModel::find($id);
        if (!$salesOrder) {
            return redirect()->route('sales-order.index')->with('error', 'Sales Order not found.');
        }

        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-order.index')->with('error', 'Unable to edit posted Sales Order.');
        }

        if ($salesOrder->type == 'recycle') {
            return view(
                'Orders.SalesOrder.recycle.create',
                getIndexData(
                    $this->title,
                    array(
                        "profile" => SalesOrderModel::with(["batteries", "billingInvoiceExpenses"])->find($id)->toArray(),
                        "customers" => CustomerModel::where('status', 1)->orderBy('name')->get()->toArray(),
                        "vehicles" => VehicleModel::all()->toArray(),
                        "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                        "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
                        "DistributorShop" => DistributorShopModel::get()->toArray(),
                        "Distributor" => DistributorModel::get()->toArray(),
                        "expenses" => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
                    )
                )
            );
        } else {
            return view(
                'Orders.SalesOrder.create',
                getIndexData(
                    $this->title,
                    array(
                        "profile" => SalesOrderModel::with(["batteries", "billingInvoiceExpenses"])->find($id)->toArray(),
                        "customers" => CustomerModel::all()->toArray(),
                        "vehicles" => VehicleModel::all()->toArray(),
                        "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                        "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
                        "expenses" => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
                    )
                )
            );
        }
    }

    /**
     * Show the invoice for specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $checkType = SalesOrderModel::find($id)->type;
        if ($checkType == 'recycle') {
            return getResponseData(false, "Invoice is not available for recycle type Sales Orders.");
        }

        return view(
            'Orders.SalesOrder.invoice',
            getIndexData(
                $this->title,
                array(
                    "profile" => SalesOrderModel::with(['customer', 'shop', 'technician', 'batteries'])->find($id)->toArray(),
                    "company" => CompanyModel::first(),
                )
            )
        );
    }

    /**
     * Show the purchase order for specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseOrder($id)
    {
        $salesOrder = SalesOrderModel::with(['customer', 'shop', 'technician', 'batteries'])->find($id);
        $shopName = $salesOrder->shop->name;
        $shopId = $salesOrder->shop->id;

        if ($shopName !== 'Distributor Main Shop') {
            return view(
                'Orders.SalesOrder.print.purchase-order-replacement',
                getIndexData(
                    $this->title,
                    array(
                        "profile" => $salesOrder->toArray(),
                        "company" => CompanyModel::first(),
                    )
                )
            );
        } else if ($shopId == 16) {
            return view(
                'Orders.SalesOrder.print.purchase-order',
                getIndexData(
                    $this->title,
                    array(
                        "profile" => $salesOrder->toArray(),
                        "company" => CompanyModel::first(),
                    )
                )
            );
        } else {
            return view(
                'Orders.SalesOrder.print.purchase-order',
                getIndexData(
                    $this->title,
                    array(
                        "profile" => $salesOrder->toArray(),
                        "company" => CompanyModel::first(),
                    )
                )
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
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get customer data (rows and count).
        $data = SalesOrderModel::allForDataTables($request);

        // Set rows to be displayed in customer table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set the payment status badge class name depending on the status.
            if ($key->payment_status == "paid") {
                $paymentStatusBadgeClass = "badge-success";
            } else if ($key->payment_status == "pending") {
                $paymentStatusBadgeClass = "badge-warning";
            } else {
                $paymentStatusBadgeClass = "badge-danger";
            }

            // Set the payment status badge class name depending on the status.
            if ($key->status == "draft") {
                $statusBadgeClass = "badge-secondary";
            } else if ($key->status == "posted") {
                $statusBadgeClass = "badge-success";
            } else {
                $statusBadgeClass = "badge-info";
            }

            // payment method badge
            $paymentMethodBadge = " <span class='badge badge-info'>$key->payment_method_name</span>";

            // source platfrom badge
            if ($key->source_platform == 'woocommerce') {
                $sourcePlatformBadge = " <span class='badge badge-info text-dark'>$key->source_platform - $key->source_id</span>";
            } else {
                $sourcePlatformBadge = "";
            }

            // badge type sales order
            if ($key->type == 'recycle') {
                $typeBadge = " <span class='badge badge-warning text-dark'>Recycle</span>";
            } else {
                $typeBadge = " <span class='badge badge-success'>Regular</span>";
            }

            // Sales Billing Number badge
            if ($key->billing_number) {
                if (substr($key->billing_number, 0, 2) === 'PB') {
                    $billingNumberBadge = "<br><span class='badge badge-warning text-dark'>Billing: $key->billing_number</span>";
                } else {
                    $billingNumberBadge = "<br><span class='badge badge-success'>Billing: $key->billing_number</span>";
                }
            } else {
                $billingNumberBadge = "";
            }

            // Set an array for each row.
            $row = [];
            $row[] = "";
            $row[] = $no++;
            $row[] = $key->sales_order_number . $typeBadge . $billingNumberBadge;
            $row[] = $key->invoice_number ?? "<p class='text-center'>-</p>";
            $row[] = formatDate($key->date);
            $row[] = $key->customer_name ?? $key->vendor_name ?? "<p class='text-center'>-</p>";
            $row[] = $key->vehicle_name ?? "<p class='text-center'>-</p>";
            if ($key->type == 'recycle') {
                $row[] = $key->ship_to_name ? "$key->ship_to_name" : "<p class='text-center'>-</p>";
            } else {
                $row[] = $key->shop_name ? "$key->shop_name" : "<p class='text-center'>-</p>";
            }
            $row[] = formatPrice($key->total);
            $row[] = "<span class='badge $paymentStatusBadgeClass'>$key->payment_status</span>" . $paymentMethodBadge . $sourcePlatformBadge;
            $row[] = "<span class='badge $statusBadgeClass'>$key->status</span>";
            $row[] = $key->id;
            $row[] = $key->status;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => SalesOrderModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Store sales order data.
            $salesOrder = new SalesOrderModel();
            $salesOrder->sales_order_number = $request->salesordernumber;
            $salesOrder->invoice_number = $request->invoicenumber;
            $salesOrder->date = $request->date;

            if ($request->customer === "new") {
                // Store the newly added vehicle brand.
                $customer = new CustomerModel();
                $customer->name = $request->customername;
                $customer->contact = $request->customercontact;
                $customer->email = $request->customeremail;
                $customer->address = $request->Address;
                $customer->latitude = $request->Latitude;
                $customer->longitude = $request->Longitude;
                $status = $customer->save();

                // Store the list of customers" owned vehicles.
                $customer->vehicles()->attach($request->customervehicle);

                // Set customer to the newly added customer.
                $salesOrder->customer_id = $customer->id;
            } else {
                $salesOrder->customer_id = $request->customer;
            }

            $salesOrder->vehicle_id = $request->vehicle;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->discount = $request->discount;
            $salesOrder->discount_price = (float) str_replace(".", "", $request->discountprice);
            $salesOrder->subtotal = (float) str_replace(".", "", $request->subtotal);
            $salesOrder->total = (float) str_replace(".", "", $request->total);
            $salesOrder->payment_method_id = $request->paymentmethod;
            $salesOrder->payment_status = $request->status;
            $status = $salesOrder->save();

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesid); $i++) {
                $battery = new SalesOrderBatteryModel();
                $battery->sales_order_id = $salesOrder->id;
                $battery->battery_id = $request->batteriesid[$i];
                $battery->battery_name = $request->batteriesname[$i];
                $battery->battery_price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->tax = (float) $request->batteriestax[$i];
                $battery->tax_price = (float) $request->batteriestaxprice[$i];
                $battery->discount = (float) $request->batteriesdiscount[$i];
                $battery->discount_price = (float) $request->batteriesdiscountprice[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriesprice[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            $billing = BillingModel::create([
                'billing_number' => BillingModel::generateSalesBillingNumber(),
                'vendor_id' => $salesOrder->shop->distributor_id,
                'vendor_type' => DistributorShopModel::class,
                'ship_to_id' => $salesOrder->customer_id,
                'ship_to_type' => CustomerModel::class,
                'date' => date('Y-m-d'),
                'discount' => $salesOrder->discount,
                'discount_price' => $salesOrder->discount_price,
                'subtotal' => $salesOrder->subtotal,
                'total' => $salesOrder->total,
                'status' => 'draft',
            ]);

            if ($billing ?? false) {
                $billingInvoice = BillingInvoiceModel::create([
                    'billing_id' => $billing->id,
                    'invoice_id' => $salesOrder->id,
                    'invoice_type' => SalesOrderModel::class,
                    'invoice_number' => $salesOrder->sales_order_number,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'note' => 'Battery Regular From Sales Order ' . $salesOrder->sales_order_number,
                ]);
            }

            // store expenses
            if ($request->has('ExpenseIds')) {
                $expenseIds = $request->input('ExpenseIds');
                $expenseAmounts = $request->input('ExpenseAmounts');

                for ($i = 0; $i < count($expenseIds); $i++) {
                    $expenseId = $expenseIds[$i];
                    $expenseModel = ExpenseModel::find($expenseId);

                    if ($expenseModel) {
                        $expense = new BillingInvoiceExpenseModel();
                        $expense->billing_invoice_id = $billingInvoice->id;
                        $expense->expense_id = $expenseId;
                        $expense->sales_order_id = $salesOrder->id;
                        $expense->debit_account_id = $expenseModel->chartOfAccount->id;
                        $expense->credit_account_id = NULL;
                        $expense->description = $expenseModel->name;
                        $expense->amount = parseNumber($expenseAmounts[$i]);
                        $status &= $expense->save();
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new sales order was successfully created!" : "Failed to create the new sales order!"
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            // Update sales order data.
            $salesOrder = SalesOrderModel::find($request->id);

            if ($salesOrder->status !== 'draft') {
                return getResponseData(false, "Unable to edit posted Sales Order.");
            }

            $salesOrder->sales_order_number = $request->salesordernumber;
            $salesOrder->invoice_number = $request->invoicenumber;
            $salesOrder->date = $request->date;

            if ($request->customer === "new") {
                // Store the newly added vehicle brand.
                $customer = new CustomerModel();
                $customer->name = $request->customername;
                $customer->contact = $request->customercontact;
                $customer->email = $request->customeremail;
                $customer->address = $request->Address;
                $customer->latitude = $request->Latitude;
                $customer->longitude = $request->Longitude;
                $status = $customer->save();

                // Store the list of customers" owned vehicles.
                $customer->vehicles()->attach($request->customervehicle);

                // Set customer to the newly added customer.
                $salesOrder->customer_id = $customer->id;
            } else {
                $salesOrder->customer_id = $request->customer;
            }

            $salesOrder->vehicle_id = $request->vehicle;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->payment_method_id = $request->paymentmethod;
            $salesOrder->payment_status = $request->status;
            $salesOrder->discount = $request->discount;
            $salesOrder->discount_price = (float) str_replace(".", "", $request->discountprice);
            $salesOrder->subtotal = (float) str_replace(".", "", $request->subtotal);
            $salesOrder->total = (float) str_replace(".", "", $request->total);
            $status = $salesOrder->save();

            // Delete the existing sales order batteries.
            $existingBatteries = SalesOrderBatteryModel::where('sales_order_id', $salesOrder->id)->get();
            foreach ($existingBatteries as $existingBattery) {
                $existingBattery->delete();
            }

            $billingNumber = BillingInvoiceModel::where('invoice_id', $salesOrder->id)->where('invoice_type', SalesOrderModel::class)->first()->billing->billing_number ?? null;

            // Update or create billing and billing invoice
            $billing = BillingModel::updateOrCreate(
                ['billing_number' => $billingNumber],
                [
                    'vendor_id' => $salesOrder->shop->distributor_id,
                    'vendor_type' => DistributorShopModel::class,
                    'ship_to_id' => $salesOrder->customer_id,
                    'ship_to_type' => CustomerModel::class,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'status' => 'draft',
                ]
            );

            // Update or create billing invoice
            $billingInvoice = BillingInvoiceModel::updateOrCreate(
                ['invoice_id' => $salesOrder->id, 'invoice_type' => SalesOrderModel::class],
                [
                    'billing_id' => $billing->id,
                    'invoice_number' => $salesOrder->sales_order_number,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'note' => 'Battery Regular From Sales Order ' . $salesOrder->sales_order_number,
                ]
            );

            // Delete the existing billing invoice expenses.
            $existingExpenses = BillingInvoiceExpenseModel::where('sales_order_id', $salesOrder->id)->get();
            foreach ($existingExpenses as $existingExpense) {
                $existingExpense->delete();
            }

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesprice); $i++) {
                $battery = new SalesOrderBatteryModel();
                $battery->sales_order_id = $salesOrder->id;
                $battery->battery_id = $request->batteriesid[$i];
                $battery->battery_name = $request->batteriesname[$i];
                $battery->battery_price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->tax = (float) $request->batteriestax[$i];
                $battery->tax_price = (float) $request->batteriestaxprice[$i];
                $battery->discount = (float) $request->batteriesdiscount[$i];
                $battery->discount_price = (float) $request->batteriesdiscountprice[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriesprice[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            // store expenses
            if ($request->has('ExpenseIds')) {
                $expenseIds = $request->input('ExpenseIds');
                $expenseAmounts = $request->input('ExpenseAmounts');
                // remove 2 decimal value ex : 18000.00 -> 18000
                $expenseAmounts = array_map(function ($amount) {
                    return parseNumber($amount);
                }, $expenseAmounts);

                for ($i = 0; $i < count($expenseIds); $i++) {
                    $expenseId = $expenseIds[$i];
                    $expenseModel = ExpenseModel::find($expenseId);

                    if ($expenseModel) {
                        $expense = new BillingInvoiceExpenseModel();
                        $expense->expense_id = $expenseId;
                        $expense->billing_invoice_id = $billingInvoice->id;
                        $expense->sales_order_id = $salesOrder->id;
                        $expense->debit_account_id = $expenseModel->chartOfAccount->id;
                        $expense->credit_account_id = NULL;
                        $expense->description = $expenseModel->name;
                        $expense->amount = (float) $expenseAmounts[$i];
                        $status &= $expense->save();
                    } else {
                        // If the expense model is not found, rollback the transaction and return an error response.
                        DB::rollBack();
                        return getResponseData(false, "Expense with ID $expenseId not found.");
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The sales order was successfully updated!" : "Failed to update the sales order!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e);

            // Set an error response data to be sent.
            return getResponseData(false);
        }
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
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return getResponseData(false, "No sales orders selected.");
            }

            $status = true;

            foreach ($ids as $id) {
                $salesOrder = SalesOrderModel::find($id);

                if (!$salesOrder) {
                    continue;
                }

                if ($salesOrder->status === 'complete') {
                    return getResponseData(false, "Unable to post completed sales order.");
                }

                if ($salesOrder->status === 'posted') {
                    return getResponseData(false, "Unable to unpost this sales order as it has been already recorded in the inventory.");
                }

                if ($salesOrder->status === 'draft') {
                    $salesOrder->payment_status = "paid";
                    $salesOrder->status = "posted";
                    $status &= $salesOrder->save();

                    // Send to inventory system sales billing
                    SalesOrderModel::sendToInventorySystemSalesBilling([$id]);
                }
            }

            if ($status) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            $successMessage = count($ids) > 1 ? "The selected sales orders were successfully posted!" : "The selected sales order was successfully posted!";
            $failedMessage = count($ids) > 1 ? "Failed to post the selected sales orders!" : "Failed to post the selected sales order!";

            return getResponseData(
                $status,
                $status ? $successMessage : $failedMessage
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (isset($request->id)) {
            $salesOrder = SalesOrderModel::find($request->id)->first();

            if ($salesOrder->status !== 'draft') {
                return getResponseData(false, "Unable to delete posted and completed sales order.");
            }

            $status = $salesOrder->delete();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected sales order was successfully deleted!" : "Failed to deleted the selected sales order!"
            );
        } else {
            $ids = explode(",", $request->ids);
            foreach ($ids as $id) {
                $salesOrder = SalesOrderModel::find($id);

                if ($salesOrder->status !== 'draft')
                    break;

                $status = $salesOrder->delete();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            $successMessage = "The selected sales order was successfully deleted!";
            $failedMessage = "Failed to delete the selected sales order!";
            if (count($ids) > 1) {
                $successMessage = "The selected sales orders were successfully deleted!";
                $failedMessage = "Failed to delete the selected sales orders!";
            }

            return getResponseData(
                $status,
                $status ? $successMessage : $failedMessage
            );
        }
    }

    /**
     * Get the list of technicians based on selectd shop.
     * 
     * @param  int  $shopId The id of the selected shop
     */
    public function getTechnicianByShop($shopId)
    {
        return DistributorShopTechnicianModel::where("distributor_shop_id", $shopId)->get()->toArray();
    }

    /**
     * Get the list of batteries based on selectd shop.
     * 
     * @param  int  $shopId The id of the selected shop
     */
    public function workOrderCreate($id)
    {
        try {
            // Check if sales order is posted or not.
            $salesOrder = SalesOrderModel::find($id);

            $checkType = $salesOrder->type;
            if ($checkType == "recycle") {
                return getResponseData(false, "Work Order creation is not available for recycle type Sales Orders.");
            }

            if ($salesOrder->status == "draft") {
                return getResponseData(false, "Unable to create Work Order of an unposted Sales Order.");
            }

            // check if the work order is already created.
            $workOrder = WorkOrderModel::where('sales_order_id', $id)->first();
            if ($workOrder) {
                // Set a new response data to be sent.
                return getResponseData(
                    false,
                    "The work order has already been created!"
                );
            } else {
                // Create work order.
                $status = SalesOrderModel::CreateWorkOrder($id);

                if ($status) {
                    // Set a new response data to be sent.
                    return getResponseData(
                        $status,
                        $status ? "The work order was successfully created!" : "Failed to create the work order!"
                    );
                } else {
                    // Set an error response data to be sent.
                    return getResponseData(false);
                }
            }
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }


    public function recreatePaymentLink($id)
    {
        try {
            // Check if sales order is posted or not.
            $salesOrder = SalesOrderModel::find($id);
            if ($salesOrder->status == "posted") {
                return getResponseData(false, "Unable to create Payment Link of an posted Sales Order.");
            }

            // if type recycle
            if ($salesOrder->type == "recycle") {
                return getResponseData(false, "Payment Link recreation is not available for recycle type Sales Orders.");
            }

            // get detail sales order
            $salesOrder = SalesOrderModel::with(['customer', 'shop', 'technician', 'batteries'])->find($id);

            // cancle the previous payment link midtrans
            $paymentMethod = PaymentMethodModel::find($salesOrder->payment_method_id);

            if (!$salesOrder->midtrans_invoice_number) {
                $MidtransOrderId = $salesOrder->sales_order_number . '-' . time();
            } else {
                $MidtransOrderId = $salesOrder->midtrans_invoice_number;
            }
            if ($paymentMethod->name == 'Midtrans') {

                // Create new payment link.
                $transaction = new Transaction($MidtransOrderId);
                $response = $transaction->status($MidtransOrderId);
                $response = json_decode(json_encode($response), true);

                if (isset($response['status_code']) && $response['status_code'] == '404') {
                    // Set a new response data to be sent.
                    return getResponseData(
                        false,
                        "Payment Link not found! Please create a new one."
                    );
                } else {
                    if ($response['transaction_status'] == 'pending') {


                        // cancel the previous payment link
                        $response = $transaction->cancel($MidtransOrderId);
                        $response = json_decode(json_encode($response), true);

                        if ($response['status_code'] == '200') {

                            // Create new payment link.

                            $transaction_details = array(
                                'order_id' => $salesOrder->sales_order_number,
                                'gross_amount' => $salesOrder->total,
                            );

                            $item_details = array();
                            foreach ($salesOrder->batteries as $key) {
                                $item_details[] = array(
                                    'id' => $key->battery_id,
                                    'price' => $key->price_net,
                                    'quantity' => 1,
                                    'name' => $key->battery_name,
                                );
                            }

                            $customer_details = array(
                                'first_name' => $salesOrder->customer->name,
                                'last_name' => "",
                                'email' => $salesOrder->customer->email,
                                'phone' => $salesOrder->customer->contact,
                                'billing_address' => array(
                                    'first_name' => $salesOrder->customer->name,
                                    'last_name' => "",
                                    'address' => $salesOrder->address,
                                    'city' => "",
                                    'postal_code' => "",
                                    'phone' => $salesOrder->customer->contact,
                                    'country_code' => 'IDN'
                                ),
                                'shipping_address' => array(
                                    'first_name' => $salesOrder->customer->name,
                                    'last_name' => "",
                                    'address' => $salesOrder->address,
                                    'city' => "",
                                    'postal_code' => "",
                                    'phone' => $salesOrder->customer->contact,
                                    'country_code' => 'IDN'
                                )
                            );

                            $params = array(
                                'transaction_details' => $transaction_details,
                                'item_details' => $item_details,
                                'customer_details' => $customer_details,
                            );

                            $response = $transaction->createTransaction($params);
                        } else {

                            return getResponseData(
                                false,
                                $response['status_message']
                            );
                        }
                    } else if ($response['transaction_status'] == 'settlement') {

                        return getResponseData(
                            false,
                            "The payment link has been successfully paid!"
                        );
                    } else if ($response['transaction_status'] == 'expire') {

                        return getResponseData(
                            false,
                            "The payment link has been expired!"
                        );
                    } else {

                        return getResponseData(
                            false,
                            "The payment link has been failed!"
                        );
                    }
                }
            } else {
                return getResponseData(false, "Payment Method not supported.");
            }
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            $transaction = new Transaction($MidtransOrderId);
            $transaction_details = array(
                'order_id' => $salesOrder->sales_order_number,
                'gross_amount' => $salesOrder->total,
            );

            $item_details = array();
            foreach ($salesOrder->batteries as $key) {
                $item_details[] = array(
                    'id' => $key->battery_id,
                    'price' => $key->price_net,
                    'quantity' => 1,
                    'name' => $key->battery_name,
                );
            }

            $customer_details = array(
                'first_name' => $salesOrder->customer->name,
                'last_name' => "",
                'email' => $salesOrder->customer->email,
                'phone' => $salesOrder->customer->contact,
                'billing_address' => array(
                    'first_name' => $salesOrder->customer->name,
                    'last_name' => "",
                    'address' => $salesOrder->address,
                    'city' => "",
                    'postal_code' => "",
                    'phone' => $salesOrder->customer->contact,
                    'country_code' => 'IDN'
                ),
                'shipping_address' => array(
                    'first_name' => $salesOrder->customer->name,
                    'last_name' => "",
                    'address' => $salesOrder->address,
                    'city' => "",
                    'postal_code' => "",
                    'phone' => $salesOrder->customer->contact,
                    'country_code' => 'IDN'
                )
            );

            $params = array(
                'transaction_details' => $transaction_details,
                'item_details' => $item_details,
                'customer_details' => $customer_details,
            );

            $response = $transaction->createTransaction($params);

            // update data sales order 
            $salesOrder->midtrans_invoice_number = $MidtransOrderId;
            $salesOrder->midtrans_payment_link = $response;
            $salesOrder->save();

            return getResponseData(
                true,
                "The payment link was successfully created! " . $response
            );
        }
    }

    public function copyPaymentLink($id)
    {
        try {
            // check payment method is midtrans
            $salesOrder = SalesOrderModel::find($id);

            if ($salesOrder->type == "recycle") {
                return getResponseData(false, "Payment Link is not available for recycle type Sales Orders.");
            }

            $paymentMethod = PaymentMethodModel::find($salesOrder->payment_method_id);

            if ($paymentMethod->name == 'Midtrans') {
                $paymenMethodLink = $salesOrder->midtrans_payment_link;
                return getResponseData(true, $paymenMethodLink);
            } else {
                return getResponseData(false, "Payment Method not supported.");
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getSalesOrderStatus(Request $request)
    {
        $allDrafts = true;
        $ids = $request->ids;
        foreach ($ids as $id) {
            $order = SalesOrderModel::find($id);

            if ($order->status != 'draft') {
                $allDrafts = false;
                break;
            }
        }
        return response()->json(['allDrafts' => $allDrafts]);
    }

    public function getPurchaseOrderNumber($id)
    {
        $salesOrder = SalesOrderModel::find($id);
        $checkType = $salesOrder->type;
        if ($checkType == 'recycle') {
            return response()->json([
                'status' => "error",
                'message' => "Purchase Order Number is not available for recycle type Sales Orders.",
                'data' => null
            ]);
        }

        $poNumber = str_replace('AK', 'KP', $salesOrder->sales_order_number);
        return response()->json([
            'status' => "success",
            'message' => "Success get purchase order number",
            'data' => $poNumber
        ]);
    }

    public function multiplePurchaseOrder()
    {
        $ids = request()->input('salesOrderIds', []);
        $salesOrders = SalesOrderModel::whereIn('id', $ids)->get();
        $poNumbers = [];

        foreach ($salesOrders as $salesOrder) {
            $poNumber = str_replace('AK', 'KP', $salesOrder->sales_order_number);
            $poNumbers[] = [
                'id' => $salesOrder->id,
                'po_number' => $poNumber
            ];
        }

        return response()->json([
            'status' => "success",
            'message' => "Success get purchase order numbers",
            'data' => $poNumbers
        ]);
    }

    public function multiplePrintPurchaseOrder($ids)
    {
        $ids = explode(",", $ids);
        $salesOrders = SalesOrderModel::with(['customer', 'shop', 'technician', 'batteries'])->whereIn('id', $ids)->get();
        foreach ($salesOrders as $salesOrder) {
            $shopName = $salesOrder->shop->name ?? null;
            $shopId = $salesOrder->shop->id ?? null;
        }

        return view(
            'Orders.SalesOrder.print.multiple-purchase-order',
            getIndexData(
                $this->title,
                array(
                    "profile" => $salesOrders->toArray(),
                    "company" => CompanyModel::first(),
                )
            )
        );
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new SalesOrderExport, 'sales-orders.xlsx');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data ' . $e->getMessage()
            ]);
        }
    }

    public function exportDetails(Request $request)
    {
        try {
            $dateStart = $request->input('dateStart');
            $dateEnd = $request->input('dateEnd');

            return Excel::download(new SalesOrderDetailsExport($dateStart, $dateEnd), 'sales-orders-details ' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data ' . $e->getMessage()
            ]);
        }
    }

    public function checkPost(Request $request)
    {
        $salesOrder = SalesOrderModel::find($request->id);
        if ($salesOrder->status === 'draft') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sales Order is in draft status, please post it first.'
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Sales Order is already posted or completed.'
            ]);
        }
    }

    public function createRecycle()
    {
        return view(
            'Orders.SalesOrder.recycle.create',
            getIndexData(
                "Sales Order Recycle",
                array(
                    "number" => SalesOrderModel::newCode(),
                    "tax" => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
                    "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
                    "DistributorShop" => DistributorShopModel::get()->toArray(),
                    "Distributor" => DistributorModel::get()->toArray(),
                    "expenses" => ExpenseModel::with('chartOfAccount')->where('is_active', 1)->orderBy('name', 'asc')->get()->toArray()
                )
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function storeRecycle(Request $request)
    {
        DB::beginTransaction();

        try {
            // Store sales order data.
            $salesOrder = new SalesOrderModel();
            $salesOrder->sales_order_number = $request->salesordernumber;
            $salesOrder->invoice_number = $request->invoicenumber;
            $salesOrder->date = $request->date;
            $salesOrder->vehicle_id = $request->vehicle;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->discount = $request->discount;
            $salesOrder->discount_price = (float) str_replace(".", "", $request->discountprice);
            $salesOrder->subtotal = (float) str_replace(".", "", $request->subtotal);
            $salesOrder->total = (float) str_replace(".", "", $request->total);
            $salesOrder->payment_method_id = $request->paymentmethod;
            $salesOrder->payment_status = $request->status;
            $salesOrder->vendor = $request->vendor;
            $salesOrder->ship_to = $request->ship_to;
            $salesOrder->type = 'recycle';
            $status = $salesOrder->save();

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesid); $i++) {
                $battery = new SalesOrderBatteryModel();
                $battery->sales_order_id = $salesOrder->id;
                $battery->battery_id = $request->batteriesid[$i];
                $battery->battery_name = $request->batteriesname[$i];
                $battery->battery_price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->type = 'recycle';
                $battery->tax = (float) $request->batteriestax[$i];
                $battery->tax_price = (float) $request->batteriestaxprice[$i];
                $battery->discount = (float) $request->batteriesdiscount[$i];
                $battery->discount_price = (float) $request->batteriesdiscountprice[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriesprice[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            $billing = BillingModel::create([
                'billing_number' => BillingModel::generateSalesBillingNumber(),
                'vendor_id' => $salesOrder->vendor,
                'vendor_type' => DistributorShopModel::class,
                'ship_to_id' => $salesOrder->ship_to,
                'ship_to_type' => DistributorModel::class,
                'date' => date('Y-m-d'),
                'discount' => $salesOrder->discount,
                'discount_price' => $salesOrder->discount_price,
                'subtotal' => $salesOrder->subtotal,
                'total' => $salesOrder->total,
                'status' => 'draft',
            ]);

            if ($billing ?? false) {
                $billingInvoice = BillingInvoiceModel::create([
                    'billing_id' => $billing->id,
                    'invoice_id' => $salesOrder->id,
                    'invoice_type' => SalesOrderModel::class,
                    'invoice_number' => $salesOrder->sales_order_number,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'note' => 'Battery Recycle From Sales Order Recycle ' . $salesOrder->sales_order_number,
                ]);
            }

            // store expenses
            if ($request->has('ExpenseIds')) {
                $expenseIds = $request->input('ExpenseIds');
                $expenseAmounts = $request->input('ExpenseAmounts');

                for ($i = 0; $i < count($expenseIds); $i++) {
                    $expenseId = $expenseIds[$i];
                    $expenseModel = ExpenseModel::find($expenseId);

                    if ($expenseModel) {
                        $expense = new BillingInvoiceExpenseModel();
                        $expense->expense_id = $expenseId;
                        $expense->billing_invoice_id = $billingInvoice->id;
                        $expense->sales_order_id = $salesOrder->id;
                        $expense->debit_account_id = $expenseModel->chartOfAccount->id;
                        $expense->credit_account_id = NULL;
                        $expense->description = $expenseModel->name;
                        $expense->amount = parseNumber($expenseAmounts[$i]);
                        $status &= $expense->save();
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new sales order was successfully created!" : "Failed to create the new sales order!"
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRecycle(Request $request)
    {
        DB::beginTransaction();

        try {
            // Update sales order data.
            $salesOrder = SalesOrderModel::find($request->id);

            $salesOrder->invoice_number = $request->invoicenumber;
            $salesOrder->date = $request->date;
            $salesOrder->vehicle_id = $request->vehicle;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->discount = $request->discount;
            $salesOrder->discount_price = (float) str_replace(".", "", $request->discountprice);
            $salesOrder->subtotal = (float) str_replace(".", "", $request->subtotal);
            $salesOrder->total = (float) str_replace(".", "", $request->total);
            $salesOrder->payment_method_id = $request->paymentmethod;
            $salesOrder->payment_status = $request->status;
            $salesOrder->vendor = $request->vendor;
            $salesOrder->ship_to = $request->ship_to;
            $salesOrder->type = 'recycle';
            $status = $salesOrder->save();

            // Delete the existing sales order batteries.
            $existingBatteries = SalesOrderBatteryModel::where('sales_order_id', $salesOrder->id)->get();
            foreach ($existingBatteries as $existingBattery) {
                $existingBattery->delete();
            }

            $billingNumber = BillingInvoiceModel::where('invoice_id', $salesOrder->id)->where('invoice_type', SalesOrderModel::class)->first()->billing->billing_number ?? null;

            // Update or create billing and billing invoice
            $billing = BillingModel::updateOrCreate(
                ['billing_number' => $billingNumber],
                [
                    'vendor_id' => $salesOrder->vendor,
                    'vendor_type' => DistributorShopModel::class,
                    'ship_to_id' => $salesOrder->ship_to,
                    'ship_to_type' => DistributorModel::class,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'status' => 'draft',
                ]
            );

            // Update or create billing invoice
            $billingInvoice = BillingInvoiceModel::updateOrCreate(
                ['invoice_id' => $salesOrder->id, 'invoice_type' => SalesOrderModel::class],
                [
                    'billing_id' => $billing->id,
                    'invoice_number' => $salesOrder->sales_order_number,
                    'date' => date('Y-m-d'),
                    'discount' => $salesOrder->discount,
                    'discount_price' => $salesOrder->discount_price,
                    'subtotal' => $salesOrder->subtotal,
                    'total' => $salesOrder->total,
                    'note' => 'Battery Regular From Sales Order Recycle ' . $salesOrder->sales_order_number,
                ]
            );

            // Delete the existing billing invoice expenses.
            $existingExpenses = BillingInvoiceExpenseModel::where('sales_order_id', $salesOrder->id)->get();
            foreach ($existingExpenses as $existingExpense) {
                $existingExpense->delete();
            }

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesprice); $i++) {
                $battery = new SalesOrderBatteryModel();
                $battery->sales_order_id = $salesOrder->id;
                $battery->battery_id = $request->batteriesid[$i];
                $battery->battery_name = $request->batteriesname[$i];
                $battery->battery_price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->tax = (float) $request->batteriestax[$i];
                $battery->tax_price = (float) $request->batteriestaxprice[$i];
                $battery->discount = (float) $request->batteriesdiscount[$i];
                $battery->discount_price = (float) $request->batteriesdiscountprice[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriesprice[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            // store expenses
            if ($request->has('ExpenseIds')) {
                $expenseIds = $request->input('ExpenseIds');
                $expenseAmounts = $request->input('ExpenseAmounts');

                for ($i = 0; $i < count($expenseIds); $i++) {
                    $expenseId = $expenseIds[$i];
                    $expenseModel = ExpenseModel::find($expenseId);

                    if ($expenseModel) {
                        $expense = new BillingInvoiceExpenseModel();
                        $expense->expense_id = $expenseId;
                        $expense->billing_invoice_id = $billingInvoice->id;
                        $expense->sales_order_id = $salesOrder->id;
                        $expense->debit_account_id = $expenseModel->chartOfAccount->id;
                        $expense->credit_account_id = NULL;
                        $expense->description = $expenseModel->name;
                        $expense->amount = parseNumber($expenseAmounts[$i]);
                        $status &= $expense->save();
                    } else {
                        // If the expense model is not found, rollback the transaction and return an error response.
                        DB::rollBack();
                        return getResponseData(false, "Expense with ID $expenseId not found.");
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The sales order was successfully updated!" : "Failed to update the sales order!"
            );
        } catch (Exception $e) {
            // Rollback if any of the database processes failed.
            DB::rollBack();

            // Logging error message.
            Log::error($e);

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Get sales order items for subgrid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getItems($id)
    {
        try {
            $salesOrder = SalesOrderModel::with('batteries')->find($id);

            if (!$salesOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales order not found.'
                ]);
            }

            $items = $salesOrder->batteries->map(function ($battery) {
                return [
                    'battery_name' => $battery->battery_name,
                    'type' => $battery->type,
                    'battery_price' => $battery->price_net,
                    'quantity' => $battery->quantity,
                    'battery_production_code' => $battery->battery_production_code
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sales order items.'
            ]);
        }
    }

    /**
     * Get sales order summary.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try {
            $summary = SalesOrderModel::getSalesOrderSummary($request->dateStart, $request->dateEnd, $request->salesOrderType);
            return response()->json([
                'status' => 'success',
                'message' => 'Sales order summary fetched successfully.',
                'data' => $summary
            ]);
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales order summary.'
            ]);
        }
    }
}
