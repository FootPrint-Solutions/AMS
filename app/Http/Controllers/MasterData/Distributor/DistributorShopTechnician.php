<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

// MODELS
use App\Models\MasterData\Distributor\DistributorShopModel;
use App\Models\MasterData\Distributor\DistributorShopTechnicianModel;

class DistributorShopTechnician extends Controller
{
    private $title = "Shop Technician";
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
            'MasterData.Distributor.Technician.index',
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
            'MasterData.Distributor.Technician.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "shops" => DistributorShopModel::with("distributor")->get()->toArray()
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
            'MasterData.Distributor.Technician.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => DistributorShopTechnicianModel::find($id)->toArray(),
                    "shops" => DistributorShopModel::with("distributor")->get()->toArray()
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

        // Get technician data (rows and count).
        $data = DistributorShopTechnicianModel::allForDataTables($request);

        // Set rows to be displayed in technician table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->shop->name ?? "-";
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => DistributorShopTechnicianModel::count(),
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
            $technician = new DistributorShopTechnicianModel();
            $technician->name = $request->name;
            $technician->distributor_shop_id = $request->shop;
            $technician->contact = $request->contact;
            $technician->email = $request->email;
            $technician->note = $request->note;
            $status = $technician->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully created!" : "Failed to create the new technician!"
            );
        } catch (Exception $e) {
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
            $technician = DistributorShopTechnicianModel::find($request->id);
            $technician->name = $request->name;
            $technician->distributor_shop_id = $request->shop;
            $technician->contact = $request->contact;
            $technician->email = $request->email;
            $technician->note = $request->note;
            $status = $technician->save();

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully updated!" : "Failed to update the new technician!"
            );
        } catch (Exception $e) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $technician = DistributorShopTechnicianModel::find($id);
                $status = $technician->delete();
            }

            // Set a new response data to be sent.
            return getResponseData(
                $status,
                $status ? "The new technician was successfully deleted!" : "Failed to delete the new technician!"
            );
        } catch (Exception $e) {
            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}
