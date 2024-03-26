<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;

class SalesOrderBattery extends Controller
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
        $data = SalesOrderBatteryModel::whereForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set production code field.
            $productionCodeField = 'Click to assign production code';
            if ($key->battery_production_code && !empty($key->battery_production_code))
                $productionCodeField = $key->battery_production_code;

            $row = [];
            $row[] = $no++;
            $row[] =  "<a href='javascript:void(0)' class='battery-production-code' data-id='$key->id' data-code='$key->battery_production_code' data-toggle='tooltip' data-placement='bottom' title='Edit battery production code'>$productionCodeField</a>";
            $row[] = $key->battery_name;
            $row[] = number_format($key->quantity);
            $row[] = number_format($key->battery_price);
            $row[] = number_format($key->quantity * $key->battery_price);
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => SalesOrderBatteryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function updateProductionCode(Request $request)
    {
        $battery = SalesOrderBatteryModel::find($request->id);
        $battery->battery_production_code = $request->productioncode;
        $status = $battery->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The battery production code was successfully updated!" : "Failed to update the battery production code!"
        );
    }
}
