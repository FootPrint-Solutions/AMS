<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// MODELS
use App\Models\MasterData\Distributor\DistributorModel;
use App\Models\MasterData\Distributor\DistributorShopModel;

class Distributor extends Controller
{
    private $title = "Distributor";
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
            'MasterData.Distributor.index',
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
            'MasterData.Distributor.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "shops" => DistributorShopModel::where("type", 1)->get()->toArray()
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
            'MasterData.Distributor.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => DistributorModel::find($id)->toArray()
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

        // Get distributor data (rows and count).
        $data = DistributorModel::allForDataTables($request);

        // Set rows to be displayed in table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorModel::count(),
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
        $distributor = new DistributorModel();
        $distributor->name = $request->name;
        $distributor->is_shop = $request->isshop;
        $distributor->address = $request->address;
        $distributor->contact_person = $request->contactperson;
        $distributor->contact = $request->contact;
        $distributor->email = $request->email;
        $distributor->note = "";
        $status = $distributor->save();

        // Check if is shop is checked or not.
        if ($request->isshop == 1) {
            // Add a new shop for the distributor.
            $shop = new DistributorShopModel();
            $shop->name = "Distributor Main Shop";
            $shop->distributor_id = $distributor->id;
            $shop->type = 1;
            $shop->address = $request->address;
            $shop->contact_person = $request->contactperson;
            $shop->contact = $request->contact;
            $shop->email = $request->email;
            $shop->note = $request->note;
            $shop->latitude = $request->Latitude;
            $shop->longitude = $request->Longitude;
            $status &= $shop->save();
        } else {
            // Delete saved distributor shop.
            $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
            if ($shop) {
                $shop->delete();
            }
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new distributor was successfully created!" : "Failed to create the new distributor!"
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
        $distributor = DistributorModel::find($request->id);
        $distributor->name = $request->name;
        $distributor->is_shop = $request->isshop;
        $distributor->address = $request->address;
        $distributor->contact_person = $request->contactperson;
        $distributor->contact = $request->contact;
        $distributor->email = $request->email;
        $distributor->note = $request->note;
        $status = $distributor->save();

        // Check if is shop is checked or not.
        if ($request->isshop == 1) {
            $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
            if (!$shop) {
                // Add a new shop for the distributor.
                $shop = new DistributorShopModel();
                $shop->name = "Distributor Main Shop";
                $shop->distributor_id = $distributor->id;
                $shop->type = 1;
                $shop->address = $request->address;
                $shop->contact_person = $request->contactperson;
                $shop->contact = $request->contact;
                $shop->email = $request->email;
                $shop->note = "";
                $shop->latitude = $request->Latitude;
                $shop->longitude = $request->Longitude;
                $status &= $shop->save();
            }
        } else {
            // Delete saved distributor shop.
            $shop = DistributorShopModel::where('distributor_id', $distributor->id)->where("type", 1)->first();
            if ($shop) {
                $shop->delete();
            }
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected distributor was successfully updated!" : "Failed to update the selected distributor!"
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
            $distributor = DistributorModel::find($id);
            $status = $distributor->delete();
        }

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected distributor was successfully deleted!" : "Failed to delete the selected distributor!"
        );
    }
}
