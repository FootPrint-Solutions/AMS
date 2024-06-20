<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

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

class SalesOrder extends Controller
{
    private $title = "Quotation";

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
                $this->title,
            )
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
            $row[] = "<span class='badge $paymentStatusBadgeClass'>$key->payment_status</span>";
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
            Log::error($e->getMessage());

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
            $salesOrder = SalesOrderModel::find($request->id);

            if ($salesOrder->status !== 'draft') {
                return getResponseData(false, "Unable to post posted and completed sales order.");
            }

            $salesOrder->status = "posted";
            $status = $salesOrder->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected sales order was successfully posted!" : "Failed to post the selected sales order!"
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
    public function destroy(Request $request)
    {
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
}
