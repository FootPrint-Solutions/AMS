<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Company\CompanyModel;
use App\Models\MasterData\Customer\CustomerModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use Illuminate\Http\Request;

// MODELS
use App\Models\Orders\Quotation\QuotationModel;

class Quotation extends Controller
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
            'Orders.Quotation.index',
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
            'Orders.Quotation.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "customers" => CustomerModel::all()->toArray(),
                    "shops" => DistributorShopModel::all()->toArray()
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
            'Orders.Quotation.invoice',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => QuotationModel::with(['customer', 'shop', 'technician', 'batteries'])->find($id)->toArray(),
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
        $data = QuotationModel::allForDataTables($request);

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
            "recordsTotal" => QuotationModel::count(),
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
        $quotation = QuotationModel::find($request->id);
        $quotation->status = $request->status;
        $status = $quotation->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The quotation status was successfully updated!" : "Failed to update the quotation status!"
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
            $quotation = QuotationModel::find($id);
            $status &= $quotation->delete();
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected quotation was successfully deleted!" : "Failed to delete the selected quotation!"
        );
    }
}
