<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PromoBatteryModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

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
            // Set status indicator color based on status.
            if ($key->status == 0) {
                $statusIndicatorColor = "text-danger";
            } else {
                $statusIndicatorColor = "text-success";
            }

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = formatDate($key->period_start);
            $row[] = $key->period_end ? formatDate($key->period_end) : "-";
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->status;
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
     * Display the specified resource in Dashboard.
     *
     * @param  int   $id
     * @param string $type Promo type to be displayed (limited, unlimited or all).
     * @return \Illuminate\Http\Response
     */
    public function showDashboard(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $type = $request->input("type");

        // Get tax data (rows and count).
        $data = PromoModel::allForDataTablesDashboard($request, $type);

        // Set rows to be displayed in tax table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $isEndingToday = $key->period_end == date('Y-m-d');

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name . ($isEndingToday ? "<span class='badge badge-danger mx-2'>Ending today</span>" : "");
            $row[] = $key->battery_list;
            $row[] = $key->period_end ? formatDate($key->period_end) : "-";
            $row[] = $key->id;
            $row[] = $isEndingToday;
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
        DB::beginTransaction();

        try {
            // Save promo parent data.
            $promo = new PromoModel();
            $promo->name = $request->name;
            $promo->period_start = $request->periodstart;
            $promo->period_end = $request->periodend;
            $status = $promo->save();

            // Save promo detail data.
            for ($i = 0; $i < count($request->detailid); $i++) {
                $battery = new PromoBatteryModel();
                $battery->promo_id = $promo->id;
                $battery->battery_id = $request->detailid[$i];
                $battery->price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->discount = $request->batteriesdisc[$i];
                $battery->discount_price = $request->batteriesdiscprice[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $battery->save();

                // Set battery price.
                $price = BatteryPriceModel::where('battery_id', $request->detailid[$i])->first();
                if ($price) {
                    $price->promo_id = $promo->id;
                    $price->discount = $request->batteriesdisc[$i];
                    $price->discount_price = $request->batteriesdiscprice[$i];
                    $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                    $status &= $price->save();
                } else {
                    $price = new BatteryPriceModel();
                    $price->battery_id = $request->detailid[$i];
                    $price->promo_id = $promo->id;
                    $price->price_retail = BatteryModel::find($request->detailid[$i])->price_retail;
                    $price->discount = $request->batteriesdisc[$i];
                    $price->discount_price = $request->batteriesdiscprice[$i];
                    $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                    $status &= $price->save();
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new promo was successfully created!" : "Failed to create the new promo!"
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            // Fetch the existing promo data
            $promo = PromoModel::find($request->id);
            $promo->name = $request->name;
            $promo->period_start = $request->periodstart;
            $promo->period_end = $request->periodend;
            $status = $promo->save();

            // Update promo detail data
            // Save the new promo battery details
            for ($i = 0; $i < count($request->detailid); $i++) {
                $battery = PromoBatteryModel::where('promo_id', $promo->id)->where('battery_id', $request->detailid[$i])->first();

                if (!$battery) {
                    $battery = new PromoBatteryModel();
                }
                $battery->promo_id = $promo->id;
                $battery->battery_id = $request->detailid[$i];
                $battery->price_retail = (float) str_replace(".", "", $request->batteriespriceretail[$i]);
                $battery->discount = $request->batteriesdisc[$i];
                $battery->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                $status &= $battery->save();

                // Set battery price.
                if ($promo->status) {
                    $price = BatteryPriceModel::where('battery_id', $request->detailid[$i])->first();
                    if ($price) {
                        $price->discount = $request->batteriesdisc[$i];
                        $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                        $status &= $price->save();
                    } else {
                        $price = new BatteryPriceModel();
                        $price->battery_id = $request->detailid[$i];
                        $price->promo_id = $promo->id;
                        $price->price_retail = BatteryModel::find($request->detailid[$i])->price_retail;
                        $price->discount = $request->batteriesdisc[$i];
                        $price->price_net = (float) str_replace(".", "", $request->batteriespricenet[$i]);
                        $status &= $price->save();
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent
            return getResponseData(
                $status,
                $status ? "The promo was successfully updated!" : "Failed to update the promo!"
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
    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $promo = PromoModel::find($request->id);
            $promo->status = $promo->status ? 0 : 1;
            $status = $promo->save();

            // Update price set for each batteries.
            $currentStatus = $promo->status;
            $batteries = PromoBatteryModel::where('promo_id', $promo->id)->get();
            if ($currentStatus) {
                // Activate the current promo price.
                foreach ($batteries as $battery) {
                    // Retrieve the corresponding battery_prices record
                    $price = BatteryPriceModel::where('battery_id', $battery->battery_id)->where('promo_id', 0)->first();

                    if ($price) {
                        $price->promo_id = $promo->id;
                        $price->discount = $battery->discount;
                        $price->price_net = $battery->price_net;
                        $status &= $price->save();
                    } else {
                        $price = new BatteryPriceModel();
                        $price->battery_id = $battery->battery_id;
                        $price->promo_id = $promo->id;
                        $price->price_retail = BatteryModel::find($battery->battery_id)->price_retail;
                        $price->discount = $battery->discount;
                        $price->price_net = (float) str_replace(".", "", $battery->price_net);
                        $status &= $price->save();
                    }
                }
            } else {
                // Deactivate the current promo price.
                foreach ($batteries as $battery) {
                    // Retrieve the corresponding battery_prices record
                    $price = BatteryPriceModel::where('battery_id', $battery->battery_id)->where('promo_id', $promo->id)->first();

                    if ($price) {
                        $price->promo_id = 0;
                        $price->discount = 0.0;
                        $price->price_net = 0;
                        $status &= $price->save();
                    }
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The selected promo was successfully updated!" : "Failed to update the selected promo!"
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
}
