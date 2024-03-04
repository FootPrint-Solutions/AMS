<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// MODELS
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorModel;

class DistributorShop extends Controller
{
    private $title = "Distributor Shop";
    private $menu = 2;
    private $submenu = 5;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'MasterData.Distributor.Shop.index',
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
            'MasterData.Distributor.Shop.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "distributors" => DistributorModel::all()->toArray()
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
            'MasterData.Distributor.Shop.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => DistributorShopModel::find($id)->toArray(),
                    "distributors" => DistributorModel::all()->toArray()
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
        $data = DistributorShopModel::allForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->distributor->name ?? '-';
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopModel::count(),
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
        $shop = new DistributorShopModel();
        $shop->name = $request->name;
        $shop->distributor_id = $request->distributor;
        $shop->address = $request->address;
        $shop->contact_person = $request->contactperson;
        $shop->contact = $request->contact;
        $shop->email = $request->email;
        $shop->note = $request->note;
        $shop->latitude = $request->Latitude;
        $shop->longitude = $request->Longitude;
        $status = $shop->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new shop was successfully created!" : "Failed to create the new shop!"
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
        $shop = DistributorShopModel::find($request->id);
        $shop->name = $request->name;
        $shop->distributor_id = $request->distributor;
        $shop->address = $request->address;
        $shop->contact_person = $request->contactperson;
        $shop->contact = $request->contact;
        $shop->email = $request->email;
        $shop->note = $request->note;
        $shop->latitude = $request->Latitude;
        $shop->longitude = $request->Longitude;
        $status = $shop->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new shop was successfully created!" : "Failed to create the new shop!"
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
        $shop = DistributorShopModel::find($request->id);
        $status = $shop->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected shop was successfully deleted!" : "Failed to delete the selected shop!"
        );
    }
}
