<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PromoBatteryModel;
use Illuminate\Http\Request;

// MODELS
use App\Models\Settings\PromoModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;

class Promo extends Controller
{
    private $title = "Price Manager";
    private $menu = 5;
    private $submenu = 3;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Settings.PromoManager.index',
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
            'Settings.PromoManager.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "batteries" => BatteryModel::all()->toArray(),
                    "battery_categories" => BatterySizeCategoryModel::all()->toArray()
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
        return view(
            'Settings.PromoManager.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => PromoModel::with('batteries')->find($id)->toArray(),
                    "batteries" => BatteryModel::all()->toArray(),
                    "battery_categories" => BatterySizeCategoryModel::all()->toArray()
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

        // Get tax data (rows and count).
        $data = PromoModel::allForDataTables($request);

        // Set rows to be displayed in tax table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = formatDate($key->period_start);
            $row[] = $key->period_end ? formatDate($key->period_end) : "-";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => PromoModel::count(),
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
        // Save promo parent data.
        $promo = new PromoModel();
        $promo->name = $request->name;
        $promo->period_start = $request->periodstart;
        $promo->period_end = $request->periodend;
        $status = $promo->save();

        // Save promo detail data.
        if ($status) {
            for ($i = 0; $i < count($request->detailid); $i++) {
                $battery = new PromoBatteryModel();
                $battery->promo_id = $promo->id;
                $battery->battery_id = $request->detailid[$i];
                $battery->price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->discount = $request->batteriesdisc[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $battery->save();

                // Set battery price.
                $price = BatteryPriceModel::where('battery_id', $request->detailid[$i])->first();
                $price->discount = $request->batteriesdisc[$i];
                $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $price->save();
            }
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new promo was successfully created!" : "Failed to create the new promo!"
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
        // Fetch the existing promo data
        $promo = PromoModel::find($request->id);
        $promo->name = $request->name;
        $promo->period_start = $request->periodstart;
        $promo->period_end = $request->periodend;
        $status = $promo->save();

        // Update promo detail data
        if ($status) {
            // Delete existing promo battery details
            PromoBatteryModel::where('promo_id', $promo->id)->delete();

            // Save the new promo battery details
            for ($i = 0; $i < count($request->detailid); $i++) {
                $battery = new PromoBatteryModel();
                $battery->promo_id = $promo->id;
                $battery->battery_id = $request->detailid[$i];
                $battery->price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->discount = $request->batteriesdisc[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $battery->save();

                // Set battery price.
                $price = BatteryPriceModel::where('battery_id', $request->detailid[$i])->first();
                $price->discount = $request->batteriesdisc[$i];
                $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $price->save();
            }
        }

        // Set a new response data to be sent
        return getResponseData(
            $status,
            $status ? "The promo was successfully updated!" : "Failed to update the promo!"
        );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
