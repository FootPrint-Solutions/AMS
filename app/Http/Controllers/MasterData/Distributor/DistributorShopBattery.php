<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Battery\BatteryModel;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Distributor\DistributorShopBatteryModel;
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorModel;

class DistributorShopBattery extends Controller
{
    private $title = "Distributor Shop Battery";
    private $menu = 2;
    private $submenu = 5;

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $shopId
     * @param  int  $distributorId
     * @return \Illuminate\Http\Response
     */
    public function create($shopId, $distributorId)
    {
        return view(
            'MasterData.Distributor.Shop.Battery.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "shop" => DistributorShopModel::find($shopId)->toArray(),
                    "shopId" => $shopId,
                    "distributor" => DistributorModel::find($shopId)->toArray(),
                    "distributorId" => $distributorId,
                    "batteries" => BatteryModel::all()->toArray(),
                )
            )
        );
    }

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
        $data = DistributorShopBatteryModel::whereForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = "<a href='javascript:void(0)'>$key->name</a>";
            $row[] = number_format($key->price);
            $row[] = $key->url;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopBatteryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shopDetail = new DistributorShopBatteryModel();
        $shopDetail->distributor_shop_id = $request->shopid;
        $shopDetail->battery_id = $request->battery;
        $shopDetail->price = (float) str_replace(",", "", $request->price);
        $shopDetail->url = $request->url;
        $status = $shopDetail->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new battery detail was successfully created!" : "Failed to create the new battery detail!"
        );
    }
}
