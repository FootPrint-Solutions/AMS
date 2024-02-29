<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        // DataTables configuration data.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = DistributorShopTechnicianModel::with(["shop" => function ($query) {
            $query->withTrashed();
        }]);

        $selectColumns = ['id', 'name', 'distributor_shop_id', 'contact', 'email'];
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
            $row[] = $key->name; // Name
            $row[] = $key->shop->name ?? "-"; // Shop
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact; // Contact
            $row[] = $key->email ?? "-"; // Email
            $row[] = $key->id;
            $data[] = $row;
        }

        $recordTotal  = DistributorShopTechnicianModel::count();
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
        $technician = DistributorShopTechnicianModel::find($request->id);
        $technician->name = $request->name;
        $technician->id_shop = $request->shop;
        $technician->contact = $request->contact;
        $technician->email = $request->email;
        $technician->note = $request->note;
        $status = $technician->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new technician was successfully updated!" : "Failed to update the new technician!"
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
        $technician = DistributorShopTechnicianModel::find($request->id);
        $status = $technician->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new technician was successfully deleted!" : "Failed to delete the new technician!"
        );
    }
}
