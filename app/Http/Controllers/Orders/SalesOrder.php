<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

// MODELS
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;
use App\Models\Settings\TaxModel;

class SalesOrder extends Controller
{
    private $title = "Quotation";
    private $menu = 3;
    private $submenu = 2;

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
                $this->menu,
                $this->submenu
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
                $this->menu,
                $this->submenu,
                array(
                    "number" => SalesOrderModel::newCode(),
                    "customers" => CustomerModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray(),
                    "tax" => TaxModel::where('status', 'active')->first()->percentage ?? "0.00",
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
                $this->menu,
                $this->submenu,
                array(
                    "profile" => SalesOrderModel::with(["batteries"])->find($id)->toArray(),
                    "customers" => CustomerModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray()
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
                $this->menu,
                $this->submenu,
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
            // Set the status badge class name depending on the status.
            if ($key->status == "paid") {
                $statusBadgeClass = "badge-success";
            } else if ($key->status == "pending") {
                $statusBadgeClass = "badge-warning";
            } else {
                $statusBadgeClass = "badge-danger";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->sales_order_number;
            $row[] = $key->customer_name;
            $row[] = $key->shop_name ? "$key->distributor_name/$key->shop_name" : "<p class='text-center'>-</p>";
            $row[] = $key->technician_name ?? "<p class='text-center'>-</p>";
            $row[] = formatPrice($key->total);
            $row[] = ucwords($key->payment_method);
            $row[] = "<span class='badge $statusBadgeClass'>$key->status</span>";
            $row[] = $key->id;
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
        try {
            // Store sales order data.
            $salesOrder = new SalesOrderModel();
            $salesOrder->sales_order_number = $request->salesordernumber;
            $salesOrder->date = $request->date;
            $salesOrder->customer_id = $request->customer;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->tax = $request->tax;
            $salesOrder->tax_price = (float) str_replace(".", "", $request->taxprice);
            $salesOrder->discount = $request->discount;
            $salesOrder->discount_price = (float) str_replace(".", "", $request->discountprice);
            $salesOrder->subtotal = (float) str_replace(".", "", $request->subtotal);
            $salesOrder->total = (float) str_replace(".", "", $request->total);
            $salesOrder->payment_method = $request->paymentmethod;
            $salesOrder->status = $request->status;
            $status = $salesOrder->save();

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesid); $i++) {
                $battery = new SalesOrderBatteryModel();
                $battery->sales_order_id = $salesOrder->id;
                $battery->battery_id = $request->batteriesid[$i];
                $battery->battery_name = $request->batteriesname[$i];
                $battery->battery_price = (float) str_replace(".", "", $request->batteriesprice[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new quotation was successfully created!" : "Failed to create the new quotation!"
            );
        } catch (Exception $e) {
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
        try {
            // Update sales order data.
            $salesOrder = SalesOrderModel::find($request->id);
            $salesOrder->date = $request->date;
            $salesOrder->customer_id = $request->customer;
            $salesOrder->address = $request->Address;
            $salesOrder->latitude = $request->Latitude;
            $salesOrder->longitude = $request->Longitude;
            $salesOrder->distributor_shop_id = $request->shop;
            $salesOrder->distributor_shop_technician_id = $request->technician;
            $salesOrder->payment_method = $request->paymentmethod;
            $salesOrder->status = $request->status;
            $status = $salesOrder->save();

            // Store sales order detail data.
            for ($i = 0; $i < count($request->batteriesprice); $i++) {
                $battery = SalesOrderBatteryModel::find($request->detailid[$i]);
                $battery->battery_production_code = $request->batteriescode[$i];
                $status &= $battery->save();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The quotation was successfully updated!" : "Failed to update the quotation!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
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
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $salesOrder = SalesOrderModel::find($id);
                $status &= $salesOrder->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected quotation was successfully deleted!" : "Failed to delete the selected quotation!"
            );
        } catch (Exception $e) {
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
    public function updateStatus(Request $request)
    {
        try {
            $quotation = SalesOrderModel::find($request->id);
            $quotation->status = $request->status;
            $status = $quotation->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The quotation status was successfully updated!" : "Failed to update the quotation status!"
            );
        } catch (Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
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
}
