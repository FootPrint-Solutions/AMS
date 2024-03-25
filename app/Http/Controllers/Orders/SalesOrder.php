<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;

class SalesOrder extends Controller
{
    private $title = "Quotation";
    private $menu = 4;
    private $submenu = 1;

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
                    "customers" => CustomerModel::all()->toArray(),
                    "shops" => DistributorShopModel::with(['distributor'])->get()->toArray()
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
            $row[] = $key->quotation_number;
            $row[] = "<a href='javascript:void()'>$key->customer_name</a><button type='button' class='btn btn-sm btn-primary mx-2'><i class='fa fa-map-marker'></i></button>";
            $row[] = $key->shop_name ? "<a href='javascript:void()'>$key->distributor_name</a>/<a href='javascript:void()'>$key->shop_name</a>" : "<p class='text-center'>-</p>";
            $row[] = $key->technician_name ? "<a href='javascript:void()'>$key->technician_name</a>" : "<p class='text-center'>-</p>";
            $row[] = number_format($key->total);
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
     * Update the specified resource status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $quotation = SalesOrderModel::find($request->id);
        $quotation->status = $request->status;
        $status = $quotation->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The quotation status was successfully updated!" : "Failed to update the quotation status!"
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $quotation = new SalesOrderModel();
        $quotation->quotation_number = $request->quotationnumber;
        $quotation->customer_id = $request->customer;
        $quotation->distributor_shop_id = $request->shop;
        $quotation->distributor_shop_technician_id = $request->technician;
        $quotation->tax = $request->tax;
        $quotation->discount = $request->discount;
        $quotation->extra_discount = $request->extradiscount;
        $quotation->total = $request->total;
        $status = $quotation->save();

        // Save quotation detail.

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new quotation was successfully created!" : "Failed to create the new quotation!"
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $status = true;
        $ids = $request->id;

        foreach ($ids as $id) {
            $quotation = SalesOrderModel::find($id);
            $status &= $quotation->delete();
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected quotation was successfully deleted!" : "Failed to delete the selected quotation!"
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
}
