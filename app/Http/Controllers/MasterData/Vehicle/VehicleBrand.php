<?php

namespace App\Http\Controllers\MasterData\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Vehicle\VehicleBrandModel;

class VehicleBrand extends Controller
{
    private $title = "Vehicle Brand";
    private $menu = 2;
    private $submenu = 3;

    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            'MasterData.Vehicle.Brand.index',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Vehicle Brand profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for editing Vehicle Brand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            'MasterData.Vehicle.Brand.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    'profile' => VehicleBrandModel::find($id)->toArray(),
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show(Request $request)
    {
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = VehicleBrandModel::select('*');

        $selectColumns = $query->getModel()->getFillable(); // ini udah otomatis ambil fillable dari model / query yang di panggil
        $query->select($selectColumns); // ini udah otomatis ambil fillable dari model / query yang di panggil

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
            $row[] = number_format($no, 0);
            $row[] = $key->name;
            $row[] = $key->id;
            $data[] = $row;
            $no++;
        }

        $recordTotal  = VehicleBrandModel::count();

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
     * Store a newly created Vehicle Brand resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $brand = new VehicleBrandModel();
        $brand->name = $request->name;
        $status = $brand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The new vehicle brand was successfully created!' : 'Failed to create the new vehicle brand!'
        );
    }

    /**
     * Update the specified Vehicle Brand resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $brand = VehicleBrandModel::find($request->id);
        $brand->name = $request->name;
        $status = $brand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The vehicle brand was successfully updated!' : 'Failed to update the vehicle brand!'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $vehicle = VehicleBrandModel::find($request->id);
        $status = $vehicle->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? 'The selected brand was successfully deleted!' : 'Failed to delete the selected brand!'
        );
    }
}
