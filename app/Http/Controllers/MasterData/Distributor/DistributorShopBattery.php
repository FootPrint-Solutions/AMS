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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $profile = DistributorShopBatteryModel::find($id)->toArray();
        $shopId = $profile["distributor_shop_id"];
        $distributorId = DistributorShopModel::find($shopId)->toArray()["id"];

        return view(
            'MasterData.Distributor.Shop.Battery.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => $profile,
                    "shop" => DistributorShopModel::find($shopId)->toArray(),
                    "shopId" => $shopId,
                    "distributor" => DistributorModel::find($distributorId)->toArray(),
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
            $row[] = "<a href='" . $key->url . "'>" . $key->url . "</a>";
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

    /**
     * Store batch created resources in storage.
     *
     * @param  int  $shopId Id of the shop.
     * @return \Illuminate\Http\Response
     */
    public function storeBatch(Request $request, $shopId)
    {
        $status = true;
        $batteries = BatteryModel::all();

        foreach ($batteries as $battery) {
            // Check if a record already exists
            $existingRecord = DistributorShopBatteryModel::where('battery_id', $battery->id)
                ->where('distributor_shop_id', $shopId)
                ->first();

            if (!$existingRecord) {
                $shopDetail = new DistributorShopBatteryModel();
                $shopDetail->battery_id = $battery->id;
                $shopDetail->distributor_shop_id = $shopId;
                $shopDetail->price = $battery->price_retail;
                $status &= $shopDetail->save();
            } else {
                if ($request->replace == '1') {
                    // Replace all
                    $existingRecord->battery_id = $battery->id;
                    $existingRecord->distributor_shop_id = $shopId;
                    $existingRecord->price = $battery->price_retail;
                    $existingRecord->url = null;
                    $status &= $existingRecord->save();
                }
            }
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The batch job was successful!" : "Failed to do the batch job!"
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $shopDetail = DistributorShopBatteryModel::find($request->id);
        $shopDetail->distributor_shop_id = $request->shopid;
        $shopDetail->battery_id = $request->battery;
        $shopDetail->price = (float) str_replace(",", "", $request->price);
        $shopDetail->url = $request->url;
        $status = $shopDetail->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected battery detail was successfully updated!" : "Failed to update the selected battery detail!"
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $status = true;
        $ids = $request->id;

        foreach ($ids as $id) {
            $distributor = DistributorShopBatteryModel::find($id);
            $status = $distributor->delete();
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected battery detail was successfully deleted!" : "Failed to delete the selected battery detail!"
        );
    }
}
