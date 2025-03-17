<?php

namespace App\Http\Controllers\Orders;

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
                    "customers" => CustomerModel::all()->toArray(),
                    "vehicles" => VehicleModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                    "tax" => TaxModel::where('status', 1)->first()->percentage ?? "0.00",
                    "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
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
        return view(
            'Orders.SalesOrder.create',
            getIndexData(
                $this->title,
                array(
                    "profile" => SalesOrderModel::with(["batteries"])->find($id)->toArray(),
                    "customers" => CustomerModel::all()->toArray(),
                    "vehicles" => VehicleModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                    "payment_methods" => PaymentMethodModel::where('status', 1)->get()->toArray(),
                )
            )
        );
    }

    /**
     * Show the invoice for specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
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
                $statusBadgeClass = "badge-secondary text-dark";
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

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->sales_order_number;
            $row[] = formatDate($key->date);
            $row[] = $key->customer_name;
            $row[] = $key->vehicle_name;
            $row[] = $key->shop_name ? "$key->distributor_name/$key->shop_name" : "<p class='text-center'>-</p>";
            $row[] = $key->technician_name ?? "<p class='text-center'>-</p>";
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
            $status = $salesOrder->save();

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesprice); $i++) {
                $battery = SalesOrderBatteryModel::find($request->detailid[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
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
            if (isset($request->id)) {
                $salesOrder = SalesOrderModel::find($request->id);

                if ($salesOrder->status === 'complete') {
                    return getResponseData(false, "Unable to post completed sales order.");
                }

                if ($salesOrder->status === 'posted') {
                    $salesOrder->status = 'draft';
                    $status = $salesOrder->save();

                    // Delete work order.
                    WorkOrderModel::where('sales_order_id', $request->id)->delete();

                    $successMessage = "The selected sales order was successfully unposted!";
                    $failedMessage = "Failed to unpost the selected sales order!";
                } else {
                    $salesOrder->payment_status = "paid";
                    $salesOrder->status = "posted";
                    $status = $salesOrder->save();

                    $successMessage = "The selected sales order was successfully posted!";
                    $failedMessage = "Failed to upost the selected sales order!";
                }

                if ($status)
                    DB::commit();
                else
                    DB::rollBack();

                // Set a new response data to be sent.
                return getResponseData(
                    $status,
                    $status ? $successMessage : $failedMessage
                );
            } else {
                $ids = explode(",", $request->ids);
                foreach ($ids as $id) {
                    $salesOrder = SalesOrderModel::find($id);

                    if ($salesOrder->status !== 'draft')
                        break;

                    $salesOrder->payment_status = "paid";
                    $salesOrder->status = "posted";
                    $status = $salesOrder->save();
                }

                if ($status)
                    DB::commit();
                else
                    DB::rollBack();

                // Set a new response data to be sent.
                $successMessage = "The selected sales order was successfully posted!";
                $failedMessage = "Failed to post the selected sales order!";
                if (count($ids) > 1) {
                    $successMessage = "The selected sales orders were successfully posted!";
                    $failedMessage = "Failed to post the selected sales orders!";
                }

                return getResponseData(
                    $status,
                    $status ? $successMessage : $failedMessage
                );
            }
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
        $poNumber = str_replace('AK', 'KP', $salesOrder->sales_order_number);
        return response()->json([
            'status' => "success",
            'message' => "Success get purchase order number",
            'data' => $poNumber
        ]);
    }
}
