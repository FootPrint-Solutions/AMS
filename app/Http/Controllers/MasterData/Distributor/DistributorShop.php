<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

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
            $row[] = $key->distributor->name ?? '-';
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = "<i class='fa-solid fa-circle $statusIndicatorColor'></i>";
            $row[] = $key->id;
            $row[] = $key->distributor_id;
            $row[] = $key->status;
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
        try {
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
        } catch (Exception) {
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
        try {
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
        } catch (Exception) {
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
        try {
            $shop = DistributorShopModel::find($request->id);

            // Check whether distributor is active or inactive.
            $distributorStatus = DistributorModel::find($shop->distributor_id)->status;
            if ($distributorStatus !== null && $distributorStatus == 0) {
                // If inactive, the shop status cannot be changed.
                return getResponseData(
                    false,
                    "Failed to update the selected shop status as the distributor is inactive!"
                );
            } else {
                $shop->status = $shop->status ? 0 : 1;
                $status = $shop->save();

                // Set a new response data to be sent.
                return getResponseData(
                    $status,
                    $status ? "The selected shop was successfully updated!" : "Failed to update the selected shop!"
                );
            }
        } catch (Exception) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
