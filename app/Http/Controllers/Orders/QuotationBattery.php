<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Orders\Quotation\QuotationBatteryModel;

class QuotationBattery extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get shop data (rows and count).
        $data = QuotationBatteryModel::whereForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = "<a href='javascript:void(0)'>$key->battery_name</a>";
            $row[] = number_format($key->quantity);
            $row[] = number_format($key->battery_price);
            $row[] = number_format($key->quantity * $key->battery_price);
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => QuotationBatteryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }
}
