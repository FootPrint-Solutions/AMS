<?php

namespace App\Http\Controllers\MasterData\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        // DataTables configuration data.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = DistributorShopModel::with(["distributor" => function ($query) {
            $query->withTrashed();
        }]);

        $selectColumns = ['id', 'name', 'address', 'contact_person', 'contact', 'email', 'id_distributor'];
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
            $row[] = $key->distributor->name ?? '-'; // Distributor
            $row[] = $key->address; // Address
            $row[] = $key->contact_person; // Contact Person
            $row[] = "<span class='text-secondary'>+62</span> " . $key->contact; // Contact
            $row[] = $key->email ?? "-"; // Email
            $row[] = $key->id;
            $data[] = $row;
        }

        $recordTotal  = DistributorShopModel::count();
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
        $shop = new DistributorShopModel();
        $shop->name = $request->name;
        $shop->id_distributor = $request->distributor;
        $shop->address = $request->address;
        $shop->contact_person = $request->contactperson;
        $shop->contact = $request->contact;
        $shop->email = $request->email;
        $shop->note = $request->note;
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
        $shop->id_distributor = $request->distributor;
        $shop->address = $request->address;
        $shop->contact_person = $request->contactperson;
        $shop->contact = $request->contact;
        $shop->email = $request->email;
        $shop->note = $request->note;
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
