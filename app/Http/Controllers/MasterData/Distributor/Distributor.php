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
                $this->submenu
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
        // DataTables configuration data.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = DistributorModel::query();

        $selectColumns = ['id', 'name', 'address', 'contact_person', 'contact', 'email'];
        $query->select($selectColumns);

        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
                    $query->orWhere($column, "like", "%" . $searchValue . "%");
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumnIndex] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        $ListData = $query->orderBy("name", "asc")
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $no = $start + 1;

        foreach ($ListData as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->address;
            $row[] = $key->contact_person;
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact;
            $row[] = $key->email ?? "-";
            $row[] = $key->id;
            $data[] = $row;
        }

        $recordTotal  = DistributorModel::count();
        $recordFiltered = ($searchValue != null) ? $query->count() : $recordTotal;

        $output = [
            "draw" => $draw,
            "recordsTotal" => $recordTotal,
            "recordsFiltered" => $recordFiltered,
            "data" => $data
        ];

        return response()->json($output);
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
            $shop->id_distributor = $distributor->id;
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
            $shop = DistributorShopModel::where('id_distributor', $distributor->id)->where("type", 1)->first();
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
            $shop = DistributorShopModel::where('id_distributor', $distributor->id)->where("type", 1)->first();
            if (!$shop) {
                // Add a new shop for the distributor.
                $shop = new DistributorShopModel();
                $shop->name = "Distributor Main Shop";
                $shop->id_distributor = $distributor->id;
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
            $shop = DistributorShopModel::where('id_distributor', $distributor->id)->where("type", 1)->first();
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
        $distributor = DistributorModel::find($request->id);
        $status = $distributor->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected distributor was successfully deleted!" : "Failed to delete the selected distributor!"
        );
    }
}
